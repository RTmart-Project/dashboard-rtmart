<?php

namespace App\Http\Controllers;

use App\Services\DeliveryOrderService;
use App\Services\DriverService;
use App\Services\HaistarService;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use stdClass;
use Yajra\DataTables\DataTables as DataTablesDataTables;

class DeliveryController extends Controller
{
    public function request(DeliveryOrderService $deliveryOrderService, DriverService $driverService, VehicleService $vehicleService)
    {
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
        $urutanDO = $request->input('urutanDO');

        $sqlDeliveryRequest = $deliveryOrderService->getDeliveryRequest();

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlDeliveryRequest->where('ms_distributor.Depo', '=', $depoUser);
        }
        if ($fromDate != '' && $toDate != '') {
            $sqlDeliveryRequest->whereDate('tmdo.CreatedDate', '>=', $fromDate)
                ->whereDate('tmdo.CreatedDate', '<=', $toDate);
        }
        if ($checkboxFilter != "") {
            $sqlDeliveryRequest->whereIn('ms_area.Subdistrict', $checkboxFilter);
        }
        if ($urutanDO != null) {
            $sqlDeliveryRequest->whereRaw("(SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
                WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
                AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID) = '$urutanDO'");
        }

        // Get data response
        $data = $sqlDeliveryRequest;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('Empty', function ($data) {
                    return "";
                })
                ->editColumn('CreatedDate', function ($data) {
                    $date = date('d-M-Y H:i', strtotime($data->CreatedDate));
                    $dateDiff = $data->DueDate;
                    if ($dateDiff == 0) {
                        $dueDate = "<a class='badge badge-danger'>H " . $dateDiff . " (Hari H)</a>";
                    } elseif (Str::contains($dateDiff, '-')) {
                        if ($dateDiff == -1 || $dateDiff == -2) {
                            $dueDate = "<a class='badge badge-warning'>H" . $dateDiff . "</a>";
                        } else {
                            $dueDate = "H" . $dateDiff;
                        }
                    } else {
                        $dueDate = "<a class='badge badge-danger'>H+" . $dateDiff . "</a>";
                    }

                    return $date . '<br>' . $dueDate;
                })
                ->addColumn('Checkbox', function ($data) {
                    $checkbox = "<input type='checkbox' class='check-do-id larger' name='confirm[]' value='" . $data->DeliveryOrderID . "' />";
                    return $checkbox;
                })
                ->filterColumn('Area', function ($query, $keyword) {
                    $sql = "CONCAT(ms_area.AreaName, ', ', ms_area.Subdistrict) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('tmdo.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tmdo.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
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
                ->rawColumns(['Checkbox', 'CreatedDate', 'Products'])
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
            'VehicleID' => (int)$dataExpedition->vehicleID,
            'VehicleLicensePlate' => $vehicleLicensePlate,
            'CreatedDate' => $createdDate
        ];

        $dataInsertExpeditionLog = [
            'MerchantExpeditionID' => $newMerchantExpeditionID,
            'StatusExpedition' => 'S032',
            'ActionBy' => $user
        ];

        $previousDeliveryOrderID = null;
        $dataForHaistar = [];
        $arrayDataDO = [
            'DeliveryOrderID' => '',
            'StockOrderID' => '',
            'PaymentMethodID' => '',
            'TotalPrice' => '',
            'Items' => []
        ];
        $dataForRTmart = [];
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
            } elseif ($value->distributor == "RT MART") {
                array_push($dataForRTmart, $value);
            } else {
                return "error";
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

                    $haistarPushOrder = $haistarService->haistarPushOrder($value['StockOrderID'], $objectParams);
                    $haistarResponse = $haistarPushOrder->status;

                    if ($haistarResponse == 200) {
                        $statusDetailDO = "S030"; // Dalam Perjalanan
                    } else {
                        $statusDetailDO = "S034"; // Gagal
                    }
                    try {
                        DB::transaction(function () use ($newMerchantExpeditionID, $deliveryOrderService, $value, $statusDetailDO) {
                            foreach ($value['Items'] as $key => $item) {
                                $deliveryOrderService->updateDetailDeliveryOrder($value['DeliveryOrderID'], $item->item_code, $item->quantity, $statusDetailDO, "HAISTAR");
                                $deliveryOrderService->insertExpeditionDetail($newMerchantExpeditionID, $value['DeliveryOrderID'], $item->item_code);
                            }
                        });
                        $sqlTransaction = "success";
                    } catch (\Throwable $th) {
                        $status = "failed";
                        $message = "Terjadi gangguan jaringan";
                    }
                } else {
                    break;
                }
            }
            if (!empty($dataForRTmart)) {
                foreach ($dataForRTmart as $key => $value) {
                    $detailDO = $deliveryOrderService->getDOfromDetailDO($value->deliveryOrderDetailID);
                    try {
                        DB::transaction(function () use ($deliveryOrderService, $detailDO, $value, $newMerchantExpeditionID) {
                            $deliveryOrderService->updateDetailDeliveryOrder($detailDO->DeliveryOrderID, $detailDO->ProductID, $value->qtyExpedition, "S030", "RT MART");
                            $deliveryOrderService->insertExpeditionDetail($newMerchantExpeditionID, $detailDO->DeliveryOrderID, $detailDO->ProductID);
                        });
                        $sqlTransaction = "success";
                    } catch (\Throwable $th) {
                        $status = "failed";
                        $message = "Terjadi gangguan";
                    }
                }
            }
            if ($sqlTransaction == "success") {
                try {
                    DB::transaction(function () use ($dataInsertExpedition, $dataInsertExpeditionLog, $deliveryOrderService, $dataExpedition, $vehicleLicensePlate, $user) {
                        $deliveryOrderService->insertTable("tx_merchant_expedition", $dataInsertExpedition);
                        $deliveryOrderService->insertTable("tx_merchant_expedition_log", $dataInsertExpeditionLog);
                        foreach ($dataExpedition->dataDeliveryOrderID as $key => $value) {
                            $deliveryOrderService->updateDeliveryOrder($value->deliveryOrderID, "S024", $dataExpedition->driverID, $dataExpedition->helperID, $dataExpedition->vehicleID, $vehicleLicensePlate);
                            $deliveryOrderService->insertDeliveryOrderLog($value->deliveryOrderID, "S024", $dataExpedition->driverID, $dataExpedition->helperID, $dataExpedition->vehicleID, $vehicleLicensePlate, $user);
                        }
                        if (!empty($dataExpedition->dataDeliveryOrderDetailNotChecked)) {
                            foreach ($dataExpedition->dataDeliveryOrderDetailNotChecked as $key => $value) {
                                DB::table('tx_merchant_delivery_order_detail')
                                    ->where('DeliveryOrderDetailID', $value->deliveryOrderDetailIDNotChecked)
                                    ->delete();
                            }
                        }
                    });
                    $status = "success";
                    $message = "Data Ekspedisi berhasil dibuat";
                } catch (\Throwable $th) {
                    $status = "failed";
                    $message = "Terjadi kesalahan";
                }
            }
        } else {
            $status = "failed";
            $message = "Stock Haistar tidak mencukupi";
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function expedition()
    {
        return view('delivery.expedition.index');
    }

    public function getExpedition(DeliveryOrderService $deliveryOrderService, Request $request, $status)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlExpedition = $deliveryOrderService->expeditions()->where('expd.StatusExpedition', $status);

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlExpedition->where('ms_distributor.Depo', '=', $depoUser);
        }
        if ($fromDate != '' && $toDate != '') {
            $sqlExpedition->whereDate('expd.CreatedDate', '>=', $fromDate)
                ->whereDate('expd.CreatedDate', '<=', $toDate);
        }

        $data = $sqlExpedition;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('StatusOrder', function ($data) {
                    if ($data->StatusExpedition == "S032") {
                        $color = "warning";
                    } elseif ($data->StatusExpedition == "S035") {
                        $color = "success";
                    } elseif ($data->StatusExpedition == "S036") {
                        $color = "danger";
                    } else {
                        $color = "info";
                    }
                    return '<span class="badge badge-' . $color . '">' . $data->StatusOrder . '</span>';
                })
                ->addColumn('Detail', function ($data) {
                    if ($data->StatusExpedition == "S032") {
                        $link = "on-going";
                    } else {
                        $link = "history";
                    }
                    $btn = '<a class="btn btn-sm btn-secondary" href="/delivery/' . $link . '/detail/' . $data->MerchantExpeditionID . '">Lihat</a>';
                    return $btn;
                })
                ->filterColumn('expd.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(expd.CreatedDate,'%d %b %Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('ms_status_order.StatusOrder', function ($query, $keyword) {
                    $query->whereRaw("ms_status_order.StatusOrder like ?", ["%$keyword%"]);
                })
                ->filterColumn('DriverName', function ($query, $keyword) {
                    $query->whereRaw("driver.Name like ?", ["%$keyword%"]);
                })
                ->filterColumn('HelperName', function ($query, $keyword) {
                    $query->whereRaw("helper.Name like ?", ["%$keyword%"]);
                })
                ->filterColumn('ms_vehicle.VehicleName', function ($query, $keyword) {
                    $query->whereRaw("ms_vehicle.VehicleName like ?", ["%$keyword%"]);
                })
                ->rawColumns(['Detail', 'StatusOrder'])
                ->make(true);
        }
    }

    public function detailExpedition(DeliveryOrderService $deliveryOrderService, $expeditionID)
    {
        // dd($deliveryOrderService->expedition($expeditionID)->get());
        return view('delivery.expedition.detail', [
            'expedition' => $deliveryOrderService->expedition($expeditionID)->get(),
            'countStatus' => $deliveryOrderService->countStatusDeliveryDetail($expeditionID)->first()
        ]);
    }

    public function confirmExpedition($status, $expeditionID)
    {
        $getDOandSO = DB::table('tx_merchant_expedition')
            ->join('tx_merchant_expedition_detail', 'tx_merchant_expedition_detail.MerchantExpeditionID', 'tx_merchant_expedition.MerchantExpeditionID')
            ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID', 'tx_merchant_expedition_detail.DeliveryOrderDetailID')
            ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
            ->where('tx_merchant_expedition_detail.MerchantExpeditionID', $expeditionID)
            ->distinct()
            ->select('tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order.StockOrderID', 'tx_merchant_expedition.DriverID', 'tx_merchant_expedition.HelperID', 'tx_merchant_expedition.VehicleID', 'tx_merchant_expedition.VehicleLicensePlate')
            ->get();

        if ($status == "finish") {
            $statusExpedition = "S035";
            $statusDO = "S025";
            $message = "Ekspedisi berhasil diselesaikan";
        } else {
            $statusExpedition = "S036";
            $statusDO = "S028";
            $message = "Ekspedisi telah dibatalkan";
        }

        $dataUpdateDO = [
            'StatusDO' => $statusDO,
            'FinishDate' => date('Y-m-d H:i:s')
        ];

        $dataUpdateExpedition = [
            'StatusExpedition' => $statusExpedition,
            'FinishDate' => date('Y-m-d H:i:s')
        ];

        $dataExpeditionLog = [
            'MerchantExpeditionID' => $expeditionID,
            'StatusExpedition' => $statusExpedition,
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
        ];

        try {
            DB::transaction(function () use ($status, $expeditionID, $dataUpdateExpedition, $dataExpeditionLog, $getDOandSO, $dataUpdateDO, $statusDO) {
                DB::table('tx_merchant_expedition')
                    ->where('MerchantExpeditionID', $expeditionID)
                    ->update($dataUpdateExpedition);
                DB::table('tx_merchant_expedition_log')->insert($dataExpeditionLog);
                foreach ($getDOandSO as $key => $value) {
                    DB::table('tx_merchant_delivery_order')
                        ->where('DeliveryOrderID', $value->DeliveryOrderID)
                        ->where('StockOrderID', $value->StockOrderID)
                        ->update($dataUpdateDO);
                    DB::table('tx_merchant_delivery_order_log')->insert([
                        'StockOrderID' => $value->StockOrderID,
                        'DeliveryOrderID' => $value->DeliveryOrderID,
                        'StatusDO' => $statusDO,
                        'DriverID' => $value->DriverID,
                        'HelperID' => $value->HelperID,
                        'VehicleID' => $value->VehicleID,
                        'VehicleLicensePlate' => $value->VehicleLicensePlate,
                        'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
                    ]);
                    if ($status == "cancel") {
                        DB::table('tx_merchant_delivery_order_detail')
                            ->where('DeliveryOrderID', $value->DeliveryOrderID)
                            ->update([
                                'StatusExpedition' => 'S029',
                                'Distributor' => NULL
                            ]);
                    }
                }
            });
            return redirect()->route('delivery.expedition')->with('success', $message);
        } catch (\Throwable $th) {
            return redirect()->route('delivery.expedition')->with('failed', 'Terjadi kesalahan');
        }
    }

    public function confirmProduct($status, $deliveryOrderDetailID)
    {
        if ($status == "finish") {
            $statusExpedition = "S031";
            $message = "Produk berhasil diselesaikan";
        } else {
            $statusExpedition = "S037";
            $message = "Produk dibatalkan";
        }

        $confirmProduct = DB::table('tx_merchant_delivery_order_detail')
            ->where('DeliveryOrderDetailID', $deliveryOrderDetailID)
            ->update([
                'StatusExpedition' => $statusExpedition
            ]);

        if ($confirmProduct) {
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('failed', 'Terjadi kesalahan');
        }
    }

    public function resendHaistar($deliveryOrderID, HaistarService $haistarService)
    {
        $items = [];
        $objectItems = new stdClass;
        $stockOrder = DB::table('tx_merchant_delivery_order')
            ->where('tx_merchant_delivery_order.DeliveryOrderID', $deliveryOrderID)
            ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
            ->select('tx_merchant_delivery_order.StockOrderID', 'tx_merchant_order.PaymentMethodID')
            ->first();
        $getItems = DB::table('tx_merchant_delivery_order_detail')
            ->where('DeliveryOrderID', $deliveryOrderID)
            ->where('Distributor', 'HAISTAR')
            ->where('StatusExpedition', 'S034')
            ->select('ProductID', 'Qty', 'Price')
            ->get();
        $totalPrice = 0;
        foreach ($getItems as $key => $value) {
            $objectItems->item_code = $value->ProductID;
            $objectItems->quantity = $value->Qty * 1;
            $objectItems->unit_price = $value->Price * 1;

            array_push($items, clone $objectItems);
            $totalPrice += $value->Price * $value->Qty;
        }

        if ($stockOrder->PaymentMethodID == 1) {
            $codPrice = $totalPrice;
        } else {
            $codPrice = "0";
        }

        // Parameter Push Order Haistar
        $objectParams = new stdClass;
        $objectParams->code = $deliveryOrderID;
        $objectParams->cod_price = $codPrice;
        $objectParams->total_price = $totalPrice;
        $objectParams->total_product_price = $totalPrice;
        $objectParams->items = $items;

        $haistarPushOrder = $haistarService->haistarPushOrder($stockOrder->StockOrderID, $objectParams);

        $haistarResponse = $haistarPushOrder->status;

        if ($haistarResponse == 200) {
            try {
                DB::transaction(function () use ($deliveryOrderID, $items) {
                    foreach ($items as $key => $value) {
                        DB::table('tx_merchant_delivery_order_detail')
                            ->where('DeliveryOrderID', $deliveryOrderID)
                            ->where('ProductID', $value->item_code)
                            ->update([
                                'StatusExpedition' => 'S030'
                            ]);
                    }
                });
                return redirect()->back()->with('success', 'Order Haistar berhasil di-resend');
            } catch (\Throwable $th) {
                return redirect()->back()->with('failed', 'Terjadi kesalahan');
            }
        } else {
            return redirect()->back()->with('failed', $haistarPushOrder->data);
        }
    }

    public function history()
    {
        return view('delivery.history.index');
    }

    public function detailHistory(DeliveryOrderService $deliveryOrderService, $expeditionID)
    {
        return view('delivery.expedition.detail', [
            'expedition' => $deliveryOrderService->expedition($expeditionID)->get(),
            'countStatus' => $deliveryOrderService->countStatusDeliveryDetail($expeditionID)->first()
        ]);
    }
}