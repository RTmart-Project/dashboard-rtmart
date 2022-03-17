<?php

namespace App\Http\Controllers;

use App\Services\DeliveryOrderService;
use App\Services\DriverService;
use App\Services\HaistarService;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class DeliveryController extends Controller
{
    public function request(DeliveryOrderService $deliveryOrderService, DriverService $driverService, VehicleService $vehicleService)
    {
        // dd($deliveryOrderService->getPreviewProduct()->get());
        return view('delivery.request.index', [
            'areas' => $deliveryOrderService->getArea(),
            'vehicles' => $vehicleService->getVehicles()->get(),
            'drivers' => $driverService->getDrivers()->get(),
            'helpers' => $driverService->getHelpers()->get()

        ]);
    }

    public function getRequest(DeliveryOrderService $deliveryOrderService, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $checkboxFilter = $request->checkFilter;

        $sqlDeliveryRequest = $deliveryOrderService->getDeliveryRequest();

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlDeliveryRequest->where('ms_distributor.Depo', '=', $depoUser);
        }
        if ($fromDate != '' && $toDate != '') {
            $sqlDeliveryRequest->whereDate('tx_merchant_delivery_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_merchant_delivery_order.CreatedDate', '<=', $toDate);
        }
        if ($checkboxFilter != "") {
            $sqlDeliveryRequest->whereIn('ms_area.Subdistrict', $checkboxFilter);
        }

        // Get data response
        $data = $sqlDeliveryRequest;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('Empty', function ($data) {
                    return "";
                })
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('DueDate', function ($data) {
                    $dateDiff = $data->DueDate;
                    if ($dateDiff == 0) {
                        $dueDate = "Hari H";
                    } elseif (Str::contains($dateDiff, '-')) {
                        $dueDate = "H" . $dateDiff;
                    } else {
                        $dueDate = "H+" . $dateDiff;
                    }
                    return $dueDate;
                })
                ->addColumn('Checkbox', function ($data) {
                    $checkbox = "<input type='checkbox' class='check-do-id larger' name='confirm[]' value='" . $data->DeliveryOrderID . "' />";
                    return $checkbox;
                })
                ->filterColumn('Area', function ($query, $keyword) {
                    $sql = "CONCAT(ms_area.AreaName, ', ', ms_area.Subdistrict) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('tx_merchant_delivery_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_delivery_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('DistributorName', function ($query, $keyword) {
                    $sql = "ms_distributor.DistributorName like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('StoreName', function ($query, $keyword) {
                    $sql = "ms_merchant_account.StoreName like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('PhoneNumber', function ($query, $keyword) {
                    $sql = "ms_merchant_account.PhoneNumber like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_sales.SalesCode, ' - ', ms_sales.SalesName) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['Checkbox'])
                ->make(true);
        }
    }

    public function getDeliveryOrderByID(DeliveryOrderService $deliveryOrderService, HaistarService $haistarService, Request $request)
    {
        $arrayDeliveryOrderID = $request->arrayDeliveryOrderID;

        if ($arrayDeliveryOrderID == "") {
            return "400";
        }

        $stringDeliveryOrderID = "'" . implode("', '", $arrayDeliveryOrderID) . "'";

        $data = $deliveryOrderService->getMultipleDeliveryOrder($stringDeliveryOrderID, $arrayDeliveryOrderID)->get();

        foreach ($data as $key => $value) {
            $value->IsHaistarProduct = 0;
            if ($value->IsHaistar == 1) {
                $productHaistar = $haistarService->haistarGetStock($value->ProductID);
                if ($productHaistar->status == "success") {
                    $value->IsHaistarProduct = 1;
                }
            }
        }

        return view('delivery.request.product-detail', [
            'detailProduct' => $data
        ]);
    }

    public function createExpedition(Request $request, DeliveryOrderService $deliveryOrderService)
    {
        $dataExpedition = json_decode($request->dataExpedition);

        $newMerchantExpeditionID = $deliveryOrderService->generateExpeditionID();

        $createdDate = str_replace("T", " ", $dataExpedition->createdDate);
        $vehicleLicensePlate = str_replace(" ", "-", $dataExpedition->licensePlate);

        $dataInsertExpedition = [
            'MerchantExpeditionID' => $newMerchantExpeditionID,
            'StatusExpedition' => 'S032',
            'DriverID' => $dataExpedition->driverID,
            'HelperID' => $dataExpedition->helperID,
            'VehicleID' => $dataExpedition->vehicleID,
            'VehicleLicensePlate' => $vehicleLicensePlate,
            'CreatedDate' => $createdDate,
            'Distributor' => $dataExpedition->distributor
        ];

        $dataInsertExpeditionLog = [
            'MerchantExpeditionID' => $newMerchantExpeditionID,
            'StatusExpedition' => 'S032',
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
        ];

        $dataDetailExpedition = [];
        foreach ($dataExpedition->dataDetail as $key => $value) {
            array_push($dataDetailExpedition, [
                'MerchantExpeditionID' => $newMerchantExpeditionID,
                'DeliveryOrderDetailID' => $value->deliveryOrderDetailID
            ]);
        }

        if ($dataExpedition->distributor == "RT MART") {
            $sqlTransaction = true;
            DB::transaction(function () use ($deliveryOrderService, $dataInsertExpedition, $dataDetailExpedition, $dataInsertExpeditionLog, $dataExpedition) {
                $deliveryOrderService->insertTable("tx_merchant_expedition", $dataInsertExpedition);
                $deliveryOrderService->insertTable("tx_merchant_expedition_detail", $dataDetailExpedition);
                $deliveryOrderService->insertTable("tx_merchant_expedition_log", $dataInsertExpeditionLog);
                foreach ($dataExpedition->dataDetail as $key => $value) {
                    $deliveryOrderService->updateDetailDeliveryOrder($value->deliveryOrderDetailID, $value->qtyExpedition, "S030");
                }
            });
        } else {
            $sqlTransaction = false;
        }

        if ($sqlTransaction) {
            $status = "success";
            $message = "Ekspedisi berhasil dibuat";
        } else {
            $status = "failed";
            $message = "Terjadi kesalahan";
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}