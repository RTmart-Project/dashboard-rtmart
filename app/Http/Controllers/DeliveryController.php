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
use stdClass;

class DeliveryController extends Controller
{
    public function request(DeliveryOrderService $deliveryOrderService, DriverService $driverService, VehicleService $vehicleService)
    {
        // $data = $deliveryOrderService->getMultipleDeliveryOrder("'DO-20220107154018-000002'", ['DO-20220107154018-000002'])->get();
        // // dd(($data[0]->TotalPrice - $data[0]->SumPriceCreatedDO) / $data[0]->CountCreatedDO);
        // dd($data);
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
                        $dueDate = "<a class='badge badge-danger'>Hari H</a>";
                    } elseif (Str::contains($dateDiff, '-')) {
                        if ($dateDiff == -1 || $dateDiff == -2) {
                            $dueDate = "<a class='badge badge-warning'>H" . $dateDiff . "</a>";
                        } else {
                            $dueDate = "H" . $dateDiff;
                        }
                        return $dueDate;
                    } else {
                        $dueDate = "<a class='badge badge-danger'>H+" . $dateDiff . "</a>";
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
                ->rawColumns(['Checkbox', 'DueDate'])
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

    public function createExpedition(Request $request, DeliveryOrderService $deliveryOrderService, HaistarService $haistarService)
    {
        $dataExpedition = json_decode($request->dataExpedition);

        $newMerchantExpeditionID = $deliveryOrderService->generateExpeditionID();
        $user = 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name;

        $createdDate = str_replace("T", " ", $dataExpedition->createdDate);
        $vehicleLicensePlate = str_replace(" ", "-", $dataExpedition->licensePlate);

        $dataInsertExpedition = [
            'MerchantExpeditionID' => $newMerchantExpeditionID,
            'StatusExpedition' => 'S032',
            'DriverID' => $dataExpedition->driverID,
            'HelperID' => $dataExpedition->helperID,
            'VehicleID' => $dataExpedition->vehicleID,
            'VehicleLicensePlate' => $vehicleLicensePlate,
            'CreatedDate' => $createdDate
        ];

        $dataInsertExpeditionLog = [
            'MerchantExpeditionID' => $newMerchantExpeditionID,
            'StatusExpedition' => 'S032',
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
        ];

        // $dataDetailExpedition = [];
        // foreach ($dataExpedition->dataDetail as $key => $value) {
        //     array_push($dataDetailExpedition, [
        //         'MerchantExpeditionID' => $newMerchantExpeditionID,
        //         'DeliveryOrderDetailID' => $value->deliveryOrderDetailID
        //     ]);
        // }

        $previousDeliveryOrderID = null;
        $dataForHaistar = [];
        $arrayDataDO = [
            'DeliveryOrderID' => '',
            'StockOrderID' => '',
            'PaymentMethodID' => '',
            'TotalPrice' => '',
            'Items' => []
        ];
        $totalPrice = 0;
        foreach ($dataExpedition->dataDetail as $key => $value) {
            $stockHaistarResponse = 200;
            if ($value->distributor == "HAISTAR") {
                $detailDO = $deliveryOrderService->getDOfromDetailDO($value->deliveryOrderDetailID);
                $checkStock = $haistarService->haistarGetStock($detailDO->ProductID);

                $arrayExistStock = $checkStock->data->detail;

                $existStock = array_sum(array_column($arrayExistStock, "exist_quantity"));

                if ((int)$value->qtyExpedition > $existStock) {
                    $stockHaistarResponse = 400;
                    break;
                }
                $deliveryOrderID = $detailDO->DeliveryOrderID;
                if ($deliveryOrderID !== $previousDeliveryOrderID) {
                    if ($previousDeliveryOrderID != null) {
                        array_push($dataForHaistar, $arrayDataDO);
                    }
                    $arrayDataDO['Items'] = [];
                    $arrayDataDO['DeliveryOrderID'] = $deliveryOrderID;
                    $arrayDataDO['StockOrderID'] = $detailDO->StockOrderID;
                    $arrayDataDO['PaymentMethodID'] = $detailDO->PaymentMethodID;
                }

                $totalPrice += $value->qtyExpedition * $detailDO->Price;
                $arrayDataDO['TotalPrice'] = $totalPrice;
                $objectItems = new stdClass;
                $objectItems->item_code = $detailDO->ProductID;
                $objectItems->unit_price = $detailDO->Price;
                $objectItems->quantity = $value->qtyExpedition;
                array_push($arrayDataDO['Items'], clone $objectItems);

                $previousDeliveryOrderID = $deliveryOrderID;
            }
        }
        array_push($dataForHaistar, $arrayDataDO);

        if ($stockHaistarResponse == 200) {
            foreach ($dataForHaistar as $key => $value) {
                if ($value['DeliveryOrderID'] != "") {
                    if ($value['PaymentMethodID'] == 1) {
                        $codPrice = $value['TotalPrice'];
                    } else {
                        $codPrice = "0";
                    }
                    // Parameter Push Order Haistar
                    $objectParams = new stdClass;
                    $objectParams->code = $value['DeliveryOrderID'];
                    $objectParams->cod_price = $codPrice;
                    $objectParams->total_price = $value['TotalPrice'];
                    $objectParams->total_product_price = $value['TotalPrice'];
                    $objectParams->items = $value['Items'];

                    // $haistarPushOrder = $haistarService->haistarPushOrder($value['StockOrderID'], $objectParams);
                    $haistarPushOrder = 200;
                    if ($haistarPushOrder == 200) {
                        $doID = $value['DeliveryOrderID'];
                        DB::transaction(function () use ($deliveryOrderService, $dataExpedition, $vehicleLicensePlate, $newMerchantExpeditionID, $user) {
                            foreach ($dataExpedition->dataDetail as $key => $detailExpd) {
                                $detailDO = $deliveryOrderService->getDOfromDetailDO($detailExpd->deliveryOrderDetailID);
                                // if ($detailDO->DeliveryOrderID == $doID) {
                                //     $message = $doID;
                                // }
                                // $deliveryOrderService->updateDetailDeliveryOrder($value->deliveryOrderDetailID, $value->qtyExpedition, "S030");
                                // $deliveryOrderService->updateDeliveryOrder($value->deliveryOrderDetailID, "S024", $dataExpedition->driverID, $dataExpedition->helperID, $dataExpedition->vehicleID, $vehicleLicensePlate);
                                // $deliveryOrderService->insertExpeditionDetail($newMerchantExpeditionID, $value->deliveryOrderDetailID);
                            }
                            // $deliveryOrderService->insertDeliveryOrderLog($value['StockOrderID'], $value['DeliveryOrderID'], "S024", $dataExpedition->driverID, $dataExpedition->helperID, $dataExpedition->vehicleID, $vehicleLicensePlate, $user);
                        });
                    }
                    $oke = "ada haistar";
                } else {
                    $oke = $dataExpedition->dataDetail;
                }
            }
            // $deliveryOrderService->insertTable("tx_merchant_expedition", $dataInsertExpedition);
            // $deliveryOrderService->insertTable("tx_merchant_expedition_log", $dataInsertExpeditionLog);
        } else {
            $status = "failed";
            $message = "Stock Haistar tidak mencukupi";
        }

        return $oke;

        // if ($stockHaistarResponse == 200) {

        //     foreach ($dataForHaistar as $key => $value) {
        //         $stockOrder = DB::table('tx_merchant_delivery_order')->where('DeliveryOrderID', $value['DeliveryOrderID'])->select('StockOrderID')->first();

        //     }

        //     $status = "success";
        //     $message = "Delivery Order Haistar berhasil dibuat";
        // } else {
        //     $status = "failed";
        //     $message = "Stock Haistar tidak mencukupi!";
        // }
        // return $stockOrder->StockOrderID;


        // if ($dataExpedition->distributor == "RT MART") {
        //     $sqlTransaction = true;
        //     DB::transaction(function () use ($deliveryOrderService, $dataInsertExpedition, $dataDetailExpedition, $dataInsertExpeditionLog, $dataExpedition) {
        //         $deliveryOrderService->insertTable("tx_merchant_expedition", $dataInsertExpedition);
        //         $deliveryOrderService->insertTable("tx_merchant_expedition_detail", $dataDetailExpedition);
        //         $deliveryOrderService->insertTable("tx_merchant_expedition_log", $dataInsertExpeditionLog);
        //         foreach ($dataExpedition->dataDetail as $key => $value) {
        //             $deliveryOrderService->updateDetailDeliveryOrder($value->deliveryOrderDetailID, $value->qtyExpedition, "S030");
        //         }
        //     });
        // } else {
        //     $sqlTransaction = false;
        // }

        // if ($sqlTransaction) {
        //     $status = "success";
        //     $message = "Ekspedisi berhasil dibuat";
        // } else {
        //     $status = "failed";
        //     $message = "Terjadi kesalahan";
        // }

        // return response()->json([
        //     'status' => $status,
        //     'message' => $message
        // ]);
    }
}