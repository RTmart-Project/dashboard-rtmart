<?php

namespace App\Http\Controllers;

use App\Services\DeliveryOrderService;
use App\Services\DriverService;
use App\Services\HaistarService;
use App\Services\VehicleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use stdClass;

class DeliveryController extends Controller
{
    protected $saveImageUrl;
    protected $baseImageUrl;

    public function __construct()
    {
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function request(DeliveryOrderService $deliveryOrderService, DriverService $driverService, VehicleService $vehicleService)
    {
        return view('delivery.request.index', [
            // 'areas' => $deliveryOrderService->getArea(),
            'vehicles' => $vehicleService->getVehicles()->get(),
            'drivers' => $driverService->getDrivers()->get(),
            'helpers' => $driverService->getHelpers()->get()

        ]);
    }

    public function getRequest(DeliveryOrderService $deliveryOrderService, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $urutanDO = $request->input('urutanDO');
        $depoUser = Auth::user()->Depo;
        $regionalUser = Auth::user()->Regional;

        $sqlDeliveryRequest = $deliveryOrderService->getDeliveryRequest();

        if ($depoUser != "ALL") {
            $sqlDeliveryRequest->where('ms_distributor.Depo', $depoUser);
        }

        if ($regionalUser != NULL && $depoUser == "ALL") {
            $sqlDeliveryRequest->where('ms_distributor.Regional', $regionalUser);
        }

        if ($fromDate != '' && $toDate != '') {
            $sqlDeliveryRequest->whereDate('tmdo.CreatedDate', '>=', $fromDate)
                ->whereDate('tmdo.CreatedDate', '<=', $toDate);
        }

        if ($urutanDO != null) {
            $sqlDeliveryRequest->whereRaw("(SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
                WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
                AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID) = '$urutanDO'");
        }

        // Get data response
        $data = $sqlDeliveryRequest;

        if ($request->ajax()) {
            $searchValue = $request->input('search')['value'];

            if ($searchValue || ($fromDate && $toDate)) {
                return DataTables::of($data)
                    // Rest of the code for columns, filters, and rawColumns
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
            } else {
                return DataTables::of([])->make(true);
            }
        }

        // if ($request->ajax()) {
        //     return DataTables::of($data)
        //         ->addColumn('Empty', function ($data) {
        //             return "";
        //         })
        //         ->editColumn('CreatedDate', function ($data) {
        //             $date = date('d-M-Y H:i', strtotime($data->CreatedDate));
        //             $dateDiff = $data->DueDate;
        //             if ($dateDiff == 0) {
        //                 $dueDate = "<a class='badge badge-danger'>H " . $dateDiff . " (Hari H)</a>";
        //             } elseif (Str::contains($dateDiff, '-')) {
        //                 if ($dateDiff == -1 || $dateDiff == -2) {
        //                     $dueDate = "<a class='badge badge-warning'>H" . $dateDiff . "</a>";
        //                 } else {
        //                     $dueDate = "H" . $dateDiff;
        //                 }
        //             } else {
        //                 $dueDate = "<a class='badge badge-danger'>H+" . $dateDiff . "</a>";
        //             }

        //             return $date . '<br>' . $dueDate;
        //         })
        //         ->addColumn('Checkbox', function ($data) {
        //             // if (date('Y-m-d', strtotime($data->CreatedDate)) > date('Y-m-d')) {
        //             //     $checkbox = "<input type='checkbox' class='larger' disabled />";
        //             // } else {
        //             $checkbox = "<input type='checkbox' class='check-do-id larger' name='confirm[]' value='" . $data->DeliveryOrderID . "' />";
        //             // }
        //             return $checkbox;
        //         })
        //         ->filterColumn('tmdo.CreatedDate', function ($query, $keyword) {
        //             $query->whereRaw("DATE_FORMAT(tmdo.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('DistributorName', function ($query, $keyword) {
        //             $sql = "ms_distributor.DistributorName like ?";
        //             $query->whereRaw($sql, ["%{$keyword}%"]);
        //         })
        //         ->filterColumn('StoreName', function ($query, $keyword) {
        //             $sql = "ms_merchant_account.StoreName like ?";
        //             $query->whereRaw($sql, ["%{$keyword}%"]);
        //         })
        //         ->filterColumn('PhoneNumber', function ($query, $keyword) {
        //             $sql = "ms_merchant_account.PhoneNumber like ?";
        //             $query->whereRaw($sql, ["%{$keyword}%"]);
        //         })
        //         ->filterColumn('Sales', function ($query, $keyword) {
        //             $sql = "CONCAT(ms_sales.SalesCode, ' - ', ms_sales.SalesName) like ?";
        //             $query->whereRaw($sql, ["%{$keyword}%"]);
        //         })
        //         ->rawColumns(['Checkbox', 'CreatedDate', 'Products'])
        //         ->setTotalRecords(10)
        //         ->make(true);
        // }
    }

    public function sumStockProduct($productID, $distributorID, $investorID, $label)
    {
        $sumStockProduct = DB::table('ms_stock_product')
            ->join('ms_investor', function ($join) {
                $join->on('ms_investor.InvestorID', 'ms_stock_product.InvestorID');
                $join->where('ms_investor.IsActive', 1);
            })
            ->where('ms_stock_product.ProductID', $productID)->where('ms_stock_product.DistributorID', $distributorID)
            ->where('ms_stock_product.InvestorID', $investorID)->where('ms_stock_product.ProductLabel', $label)
            ->where('ms_stock_product.ConditionStock', 'GOOD STOCK')
            ->where('ms_stock_product.Qty', '>', 0)
            ->sum('ms_stock_product.Qty');

        $investorName = DB::table('ms_investor')->where('InvestorID', $investorID)->select('InvestorName')->first();

        return "Stok " . $investorName->InvestorName . " " . $label . " : " . $sumStockProduct;
    }

    public function getDeliveryOrderByID(DeliveryOrderService $deliveryOrderService, Request $request)
    {
        $arrayDeliveryOrderID = $request->arrayDeliveryOrderID;

        if (empty($arrayDeliveryOrderID)) {
            return "400";
        }

        $stringDeliveryOrderID = "'" . implode("', '", $arrayDeliveryOrderID) . "'";

        $data = $deliveryOrderService->getMultipleDeliveryOrder($stringDeliveryOrderID, $arrayDeliveryOrderID)->get();

        // foreach ($data as $value) {
        //     $value->IsHaistarProduct = 0;

        //     if ($value->IsHaistar == 1 && $haistarService->haistarGetStock($value->ProductID)->status == "success") {
        //         $value->IsHaistarProduct = 1;
        //     }
        // }

        return view('delivery.request.product-detail', [
            'detailProduct' => $data,
            'investors' => DB::table('ms_investor')->where('IsActive', 1)->get(),
            'firstInvestor' => DB::table('ms_investor')->where('InvestorID', 1)->first()
        ]);
    }

    public function createExpedition(Request $request, DeliveryOrderService $deliveryOrderService)
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
            'CreatedDate' => $createdDate,
            'PhoneNumberValidation' => 1,
            'AddressValidation' => 1
        ];

        $dataInsertExpeditionLog = [
            'MerchantExpeditionID' => $newMerchantExpeditionID,
            'StatusExpedition' => 'S032',
            'ActionBy' => $user
        ];

        $previousDeliveryOrderID = null;
        // $dataForHaistar = [];
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
            // $stockHaistarResponse = 200;

            // if ($value->distributor == "HAISTAR") {
            //     $detailDO = $deliveryOrderService->getDOfromDetailDO($value->deliveryOrderDetailID);
            //     $checkStock = $haistarService->haistarGetStock($detailDO->ProductID);

            //     $arrayExistStock = $checkStock->data->detail;

            //     $existStock = array_sum(array_column($arrayExistStock, "exist_quantity"));

            //     if ((int)$value->qtyExpedition > $existStock) {
            //         $stockHaistarResponse = 400;
            //         break;
            //     }

            //     $deliveryOrderID = $detailDO->DeliveryOrderID;

            //     if ($deliveryOrderID !== $previousDeliveryOrderID) {
            //         if ($previousDeliveryOrderID != null) {
            //             array_push($dataForHaistar, $arrayDataDO);
            //         }
            //         $arrayDataDO['Items'] = [];
            //         $arrayDataDO['DeliveryOrderID'] = $deliveryOrderID;
            //         $arrayDataDO['StockOrderID'] = $detailDO->StockOrderID;
            //         $arrayDataDO['PaymentMethodID'] = $detailDO->PaymentMethodID;
            //     }

            //     $totalPrice += $value->qtyExpedition * $detailDO->Price;
            //     $arrayDataDO['TotalPrice'] = $totalPrice;
            //     $objectItems = new stdClass;
            //     $objectItems->item_code = $detailDO->ProductID;
            //     $objectItems->unit_price = $detailDO->Price;
            //     $objectItems->quantity = $value->qtyExpedition;
            //     array_push($arrayDataDO['Items'], clone $objectItems);

            //     $previousDeliveryOrderID = $deliveryOrderID;
            // }
            array_push($dataForRTmart, $value);
        }

        // array_push($dataForHaistar, $arrayDataDO);

        $dataVoucherDeliveryOrder = $deliveryOrderService->calculateVoucherDObyMultiple($dataExpedition->dataDetail);

        // if ($stockHaistarResponse == 200) {
        try {
            DB::transaction(function () use ($dataInsertExpedition, $dataInsertExpeditionLog, $deliveryOrderService, $dataExpedition, $vehicleLicensePlate, $user, $newMerchantExpeditionID, $dataForRTmart, $createdDate, $dataVoucherDeliveryOrder) {
                // foreach ($dataForHaistar as $key => $value) {
                //     if ($value['DeliveryOrderID'] != "") {
                //         if ($value['PaymentMethodID'] == 1) {
                //             $codPrice = $value['TotalPrice'];
                //         } else {
                //             $codPrice = "0";
                //         }
                //         // Parameter Push Order Haistar
                //         $objectParams = new stdClass;
                //         $objectParams->code = $value['DeliveryOrderID'];
                //         $objectParams->cod_price = $codPrice;
                //         $objectParams->total_price = $value['TotalPrice'];
                //         $objectParams->total_product_price = $value['TotalPrice'];
                //         $objectParams->items = $value['Items'];

                //         $haistarPushOrder = $haistarService->haistarPushOrder($value['StockOrderID'], $objectParams);
                //         $haistarResponse = $haistarPushOrder->status;

                //         if ($haistarResponse == 200) {
                //             $statusDetailDO = "S030"; // Dalam Perjalanan
                //         } else {
                //             $statusDetailDO = "S034"; // Gagal
                //         }
                //         foreach ($value['Items'] as $key => $item) {
                //             $deliveryOrderService->updateDetailDeliveryOrder($value['DeliveryOrderID'], $item->item_code, $item->quantity, $statusDetailDO, "HAISTAR");
                //             $deliveryOrderService->insertExpeditionDetail($newMerchantExpeditionID, $value['DeliveryOrderID'], $item->item_code, $statusDetailDO);
                //         }
                //     } else {
                //         break;
                //     }
                // }

                foreach ($dataForRTmart as $key => $value) {
                    $detailDO = $deliveryOrderService->getDOfromDetailDO($value->deliveryOrderDetailID);
                    $deliveryOrderService->updateDetailDeliveryOrder($detailDO->DeliveryOrderID, $detailDO->ProductID, $value->qtyExpedition, "S030", "RT MART");
                    $merchantExpeditionDetailID = $deliveryOrderService->insertExpeditionDetail($newMerchantExpeditionID, $detailDO->DeliveryOrderID, $detailDO->ProductID, "S030");
                    $deliveryOrderService->reduceStock($detailDO->ProductID, $detailDO->DistributorID, $value->qtyExpedition, $value->deliveryOrderDetailID, $merchantExpeditionDetailID, $value->sourceProduct, $value->sourceProductInvestor);
                }

                $deliveryOrderService->insertTable("tx_merchant_expedition", $dataInsertExpedition);
                $deliveryOrderService->insertTable("tx_merchant_expedition_log", $dataInsertExpeditionLog);

                foreach ($dataExpedition->dataDeliveryOrderID as $key => $value) {
                    $deliveryOrderService->updateDeliveryOrder($value->deliveryOrderID, "S024", $dataExpedition->driverID, $dataExpedition->helperID, $dataExpedition->vehicleID, $vehicleLicensePlate, $createdDate);
                    $deliveryOrderService->insertDeliveryOrderLog($value->deliveryOrderID, "S024", $dataExpedition->driverID, $dataExpedition->helperID, $dataExpedition->vehicleID, $vehicleLicensePlate, $user, $createdDate);
                    $deliveryOrderService->updateStatusStockOrder($value->deliveryOrderID);
                }

                if (!empty($dataExpedition->dataDeliveryOrderDetailNotChecked)) {
                    foreach ($dataExpedition->dataDeliveryOrderDetailNotChecked as $key => $value) {
                        DB::table('tx_merchant_delivery_order_detail')
                            ->where('DeliveryOrderDetailID', $value->deliveryOrderDetailIDNotChecked)
                            ->delete();
                    }
                }

                foreach ($dataVoucherDeliveryOrder as $key => $value) {
                    DB::table('tx_merchant_delivery_order')
                        ->where('DeliveryOrderID', $value['DeliveryOrderID'])
                        ->update([
                            'Discount' => $value['Discount'],
                            'ServiceCharge' => $value['ServiceCharge'],
                            'DeliveryFee' => $value['DeliveryFee']
                        ]);
                }
            });

            $status = "success";
            $message = "Data Ekspedisi berhasil dibuat";
        } catch (\Throwable $th) {
            $status = "failed";
            $message = "Terjadi kesalahan";
        }
        // } else {
        //     $status = "failed";
        //     $message = "Stock Haistar tidak mencukupi";
        // }

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
        $depoUser = Auth::user()->Depo;
        $regionalUser = Auth::user()->Regional;

        $sqlExpedition = $deliveryOrderService->expeditions()->whereRaw("expd.StatusExpedition IN ($status)");

        if ($depoUser != "ALL") {
            $sqlExpedition->where('ms_distributor.Depo', $depoUser);
        }

        if ($regionalUser != NULL && $depoUser == "ALL") {
            $sqlExpedition->where('ms_distributor.Regional', $regionalUser);
        }

        if ($fromDate != '' && $toDate != '') {
            $sqlExpedition->whereDate('expd.CreatedDate', '>=', $fromDate)
                ->whereDate('expd.CreatedDate', '<=', $toDate);
        }

        $data = $sqlExpedition;

        if ($request->ajax()) {
            $searchValue = $request->input('search')['value'];

            if ($searchValue || ($fromDate && $toDate)) {
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
                    ->editColumn('PhoneNumberValidation', function ($data) {
                        if ($data->PhoneNumberValidation == 1) {
                            $phoneNumberValidation = "Valid";
                        } else {
                            $phoneNumberValidation = "";
                        }
                        return $phoneNumberValidation;
                    })
                    ->editColumn('AddressValidation', function ($data) {
                        if ($data->AddressValidation == 1) {
                            $addressValidation = "Valid";
                        } else {
                            $addressValidation = "";
                        }
                        return $addressValidation;
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
                    ->filterColumn('MerchantID', function ($query, $keyword) {
                        $query->whereRaw("ms_merchant_account.MerchantID LIKE ?", ["%$keyword%"]);
                    })
                    ->filterColumn('StoreName', function ($query, $keyword) {
                        $query->whereRaw("ms_merchant_account.StoreName LIKE ?", ["%$keyword%"]);
                    })
                    ->filterColumn('expd.CreatedDate', function ($query, $keyword) {
                        $query->whereRaw("DATE_FORMAT(expd.CreatedDate,'%d %b %Y %H:%i') LIKE ?", ["%$keyword%"]);
                    })
                    ->filterColumn('ms_status_order.StatusOrder', function ($query, $keyword) {
                        $query->whereRaw("ms_status_order.StatusOrder LIKE ?", ["%$keyword%"]);
                    })
                    ->filterColumn('DriverName', function ($query, $keyword) {
                        $query->whereRaw("driver.Name LIKE ?", ["%$keyword%"]);
                    })
                    ->filterColumn('HelperName', function ($query, $keyword) {
                        $query->whereRaw("helper.Name LIKE ?", ["%$keyword%"]);
                    })
                    ->filterColumn('ms_vehicle.VehicleName', function ($query, $keyword) {
                        $query->whereRaw("ms_vehicle.VehicleName LIKE ?", ["%$keyword%"]);
                    })
                    ->rawColumns(['Detail', 'StatusOrder'])
                    ->setTotalRecords(30)
                    ->make(true);
            } else {
                return DataTables::of([])->make(true);
            }
        }

        // if ($request->ajax()) {
        //     return DataTables::of($data)
        //         ->editColumn('CreatedDate', function ($data) {
        //             return date('d M Y H:i', strtotime($data->CreatedDate));
        //         })
        //         ->editColumn('StatusOrder', function ($data) {
        //             if ($data->StatusExpedition == "S032") {
        //                 $color = "warning";
        //             } elseif ($data->StatusExpedition == "S035") {
        //                 $color = "success";
        //             } elseif ($data->StatusExpedition == "S036") {
        //                 $color = "danger";
        //             } else {
        //                 $color = "info";
        //             }
        //             return '<span class="badge badge-' . $color . '">' . $data->StatusOrder . '</span>';
        //         })
        //         ->editColumn('PhoneNumberValidation', function ($data) {
        //             if ($data->PhoneNumberValidation == 1) {
        //                 $phoneNumberValidation = "Valid";
        //             } else {
        //                 $phoneNumberValidation = "";
        //             }
        //             return $phoneNumberValidation;
        //         })
        //         ->editColumn('AddressValidation', function ($data) {
        //             if ($data->AddressValidation == 1) {
        //                 $addressValidation = "Valid";
        //             } else {
        //                 $addressValidation = "";
        //             }
        //             return $addressValidation;
        //         })
        //         ->addColumn('Detail', function ($data) {
        //             if ($data->StatusExpedition == "S032") {
        //                 $link = "on-going";
        //             } else {
        //                 $link = "history";
        //             }
        //             $btn = '<a class="btn btn-sm btn-secondary" href="/delivery/' . $link . '/detail/' . $data->MerchantExpeditionID . '">Lihat</a>';
        //             return $btn;
        //         })
        //         ->filterColumn('MerchantID', function ($query, $keyword) {
        //             $query->whereRaw("ms_merchant_account.MerchantID LIKE ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('StoreName', function ($query, $keyword) {
        //             $query->whereRaw("ms_merchant_account.StoreName LIKE ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('expd.CreatedDate', function ($query, $keyword) {
        //             $query->whereRaw("DATE_FORMAT(expd.CreatedDate,'%d %b %Y %H:%i') LIKE ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('ms_status_order.StatusOrder', function ($query, $keyword) {
        //             $query->whereRaw("ms_status_order.StatusOrder LIKE ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('DriverName', function ($query, $keyword) {
        //             $query->whereRaw("driver.Name LIKE ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('HelperName', function ($query, $keyword) {
        //             $query->whereRaw("helper.Name LIKE ?", ["%$keyword%"]);
        //         })
        //         ->filterColumn('ms_vehicle.VehicleName', function ($query, $keyword) {
        //             $query->whereRaw("ms_vehicle.VehicleName LIKE ?", ["%$keyword%"]);
        //         })
        //         ->rawColumns(['Detail', 'StatusOrder'])
        //         ->setTotalRecords(30)
        //         ->make(true);
        // }
    }

    public function getExpeditionAllProduct($status, Request $request, DeliveryOrderService $deliveryOrderService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $depoUser = Auth::user()->Depo;
        $regionalUser = Auth::user()->Regional;

        $sqlExpeditionAllProduct = $deliveryOrderService->expeditionsAllProduct()->whereRaw("tx_merchant_expedition.StatusExpedition IN ($status)");

        if ($depoUser != "ALL") {
            $sqlExpeditionAllProduct->where('ms_distributor.Depo', $depoUser);
        }

        if ($regionalUser != NULL && $depoUser == "ALL") {
            $sqlExpeditionAllProduct->where('ms_distributor.Regional', $regionalUser);
        }

        if ($fromDate != '' && $toDate != '') {
            $sqlExpeditionAllProduct->whereDate('tx_merchant_expedition.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_merchant_expedition.CreatedDate', '<=', $toDate);
        }

        $data = $sqlExpeditionAllProduct;

        if ($request->ajax()) {
            $searchValue = $request->input('search')['value'];

            if ($searchValue || ($fromDate && $toDate)) {
                return DataTables::of($data)
                    ->editColumn('CreatedDate', function ($data) {
                        return date('d M Y H:i', strtotime($data->CreatedDate));
                    })
                    ->editColumn('StatusOrder', function ($data) {
                        if ($data->StatusExpedition == "S030") {
                            $color = "warning";
                        } elseif ($data->StatusExpedition == "S031") {
                            $color = "success";
                        } elseif ($data->StatusExpedition == "S037") {
                            $color = "danger";
                        } else {
                            $color = "info";
                        }

                        return '<span class="badge badge-' . $color . '">' . $data->StatusOrder . '</span>';
                    })
                    ->filterColumn('tx_merchant_expedition.CreatedDate', function ($query, $keyword) {
                        $query->whereRaw("DATE_FORMAT(tx_merchant_expedition.CreatedDate,'%d %b %Y %H:%i') like ?", ["%$keyword%"]);
                    })
                    ->rawColumns(['StatusOrder'])
                    ->make();
            } else {
                return DataTables::of([])->make(true);
            }
        }
    }

    public function detailExpedition(DeliveryOrderService $deliveryOrderService, $expeditionID)
    {
        return view('delivery.expedition.detail', [
            'expedition' => $deliveryOrderService->expedition($expeditionID)->get(),
            'countStatus' => $deliveryOrderService->countStatusDeliveryDetail($expeditionID)->first()
        ]);
    }

    public function confirmExpedition($status, $expeditionID, $merchantID, DeliveryOrderService $deliveryOrderService)
    {
        $getDOandSO = DB::table('tx_merchant_expedition')
            ->join('tx_merchant_expedition_detail', 'tx_merchant_expedition_detail.MerchantExpeditionID', 'tx_merchant_expedition.MerchantExpeditionID')
            ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID', 'tx_merchant_expedition_detail.DeliveryOrderDetailID')
            ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
            ->where('tx_merchant_expedition_detail.MerchantExpeditionID', $expeditionID)
            ->distinct()
            ->select('tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order.StatusDO', 'tx_merchant_delivery_order.StockOrderID', 'tx_merchant_expedition.DriverID', 'tx_merchant_expedition.HelperID', 'tx_merchant_expedition.VehicleID', 'tx_merchant_expedition.VehicleLicensePlate')
            ->get();

        if ($status == "finish") {
            $statusExpedition = "S035";
            $message = "Ekspedisi berhasil diselesaikan";
        } else {
            $statusExpedition = "S036";
            $message = "Ekspedisi telah dibatalkan";
        }

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
            DB::transaction(function () use ($status, $expeditionID, $merchantID, $dataUpdateExpedition, $dataExpeditionLog, $getDOandSO, $deliveryOrderService) {
                DB::table('tx_merchant_expedition')
                    ->where('MerchantExpeditionID', $expeditionID)
                    ->update($dataUpdateExpedition);
                DB::table('tx_merchant_expedition_log')->insert($dataExpeditionLog);

                $checkMembership = DB::table('ms_history_membership')
                    ->join('ms_merchant_account', 'ms_history_membership.merchant_id', 'ms_merchant_account.MerchantID')
                    ->join('tx_merchant_order', function ($join) {
                        $join->on('ms_history_membership.merchant_id', 'tx_merchant_order.MerchantID');
                        $join->whereRaw("DATE_FORMAT(tx_merchant_order.CreatedDate, '%Y-%m-%d') >= ms_merchant_account.CrowdoApprovedDate");
                        $join->where('tx_merchant_order.PaymentMethodID', 14);
                    })
                    ->where('ms_history_membership.merchant_id', $merchantID)
                    ->where('ms_merchant_account.ValidationStatusMembershipCouple', 3)
                    ->where('ms_history_membership.status_membership', 2)
                    ->first();

                if ($checkMembership) {
                    $productAddDO = DB::table('tx_merchant_order_detail')
                        ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
                        ->where('tx_merchant_order_detail.StockOrderID', '=', $getDOandSO[0]->StockOrderID)
                        ->select('tx_merchant_order_detail.ProductID', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Nett', 'ms_product.ProductName', 'ms_product.ProductImage')
                        ->get();

                    $promisedQty = 0;
                    $deliveryOrderQty = 0;

                    foreach ($productAddDO as $key => $value) {
                        $productQtyDO = DB::table('tx_merchant_delivery_order')
                            ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderID', '=', 'tx_merchant_delivery_order.DeliveryOrderID')
                            ->where('tx_merchant_delivery_order.StockOrderID', '=', $getDOandSO[0]->StockOrderID)
                            ->where('tx_merchant_delivery_order_detail.ProductID', '=', $value->ProductID)
                            ->where('tx_merchant_delivery_order_detail.StatusExpedition', '!=', 'S037')
                            ->selectRaw('IFNULL(SUM(tx_merchant_delivery_order_detail.Qty), 0) as Qty')
                            ->first();

                        $promisedQty += $value->PromisedQuantity;
                        $deliveryOrderQty += $productQtyDO->Qty;
                    }

                    ($promisedQty == $deliveryOrderQty) ? $statusShipment = 3 : $statusShipment = 2;

                    DB::table('ms_history_membership')
                        ->where('id', $checkMembership->id)
                        ->where('ms_history_membership.merchant_id', $merchantID)
                        ->where('ms_history_membership.status_membership', 2)
                        ->update(['status_shipment_id' => $statusShipment]);
                }

                foreach ($getDOandSO as $key => $value) {
                    $getDOdetail = DB::table('tx_merchant_delivery_order')
                        ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID')
                        ->join('tx_merchant_expedition_detail', 'tx_merchant_expedition_detail.DeliveryOrderDetailID', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID')
                        ->where('tx_merchant_delivery_order.DeliveryOrderID', $value->DeliveryOrderID)
                        ->select('tx_merchant_delivery_order_detail.Distributor', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID', 'tx_merchant_delivery_order_detail.Qty', 'tx_merchant_expedition_detail.DeliveryOrderDetailID', 'tx_merchant_expedition_detail.StatusExpeditionDetail', 'tx_merchant_expedition_detail.MerchantExpeditionDetailID');

                    // $countNotBatalorSelesai = (clone $getDOdetail)
                    //     ->where('tx_merchant_delivery_order_detail.Distributor', 'HAISTAR')
                    //     ->where('tx_merchant_expedition_detail.StatusExpeditionDetail', '!=', 'S037')
                    //     ->where('tx_merchant_expedition_detail.StatusExpeditionDetail', '!=', 'S031')
                    //     ->count();

                    // if ($countNotBatalorSelesai > 0) {
                    //     $messageError = "Terdapat Order Haistar yang belum dikonfirmasi";
                    //     throw new Exception($messageError);
                    // }

                    if ($status == "finish") {
                        $statusDO = "S025";
                        DB::table('tx_merchant_delivery_order')
                            ->where('DeliveryOrderID', $value->DeliveryOrderID)
                            ->where('StockOrderID', $value->StockOrderID)
                            ->update([
                                'StatusDO' => $statusDO,
                                'FinishDate' => date('Y-m-d H:i:s')
                            ]);
                    } else {
                        $statusDO = $value->StatusDO;
                        DB::table('tx_merchant_delivery_order_detail')
                            ->where('DeliveryOrderID', $value->DeliveryOrderID)
                            ->update([
                                'StatusExpedition' => 'S037'
                            ]);
                        foreach ($getDOdetail->where('tx_merchant_delivery_order_detail.Distributor', 'RT MART')->get() as $key => $item) {
                            DB::table('tx_merchant_expedition_detail')
                                ->where('MerchantExpeditionID', $expeditionID)
                                ->where('DeliveryOrderDetailID', $item->DeliveryOrderDetailID)
                                ->update([
                                    'StatusExpeditionDetail' => 'S037'
                                ]);
                            if ($item->StatusExpeditionDetail == "S030" || $item->StatusExpeditionDetail == "S037") {
                                // Balikin stok
                                $deliveryOrderService->cancelProductExpedition($item->MerchantExpeditionDetailID, $item->Qty, "GOOD STOCK");
                                DB::table('tx_merchant_delivery_order')
                                    ->whereRaw("DeliveryOrderID = (SELECT DeliveryOrderID FROM `tx_merchant_delivery_order_detail` 
                                        WHERE `DeliveryOrderDetailID` = (SELECT DeliveryOrderDetailID 
                                            FROM `tx_merchant_expedition_detail` 
                                            WHERE `MerchantExpeditionDetailID` = $item->MerchantExpeditionDetailID
                                            )
                                        )
                                    ")
                                    ->update([
                                        'StatusDO' => "S026"
                                    ]);
                            }
                        }
                    }

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
                }
            });

            return redirect()->route('delivery.expedition')->with('success', $message);
        } catch (\Throwable $th) {
            return redirect()->route('delivery.expedition')->with('failed', $th->getMessage());
        }
    }

    public function confirmProduct($status, $expeditionDetailID, DeliveryOrderService $deliveryOrderService, Request $request)
    {
        $deliveryOrderDetailID = DB::table('tx_merchant_expedition_detail')
            ->where('MerchantExpeditionDetailID', $expeditionDetailID)
            ->select('DeliveryOrderDetailID')
            ->first();

        if ($status == "finish") {
            $qtyDiterima = $request->input('receipt_qty');
            $qtyBadStock = $request->input('badstock_qty');
            $imageName = date('YmdHis') . '_' . $expeditionDetailID . '.' . $request->file('receipt_image')->extension();
            $request->file('receipt_image')->move($this->saveImageUrl . 'receipt_image_expedition/', $imageName);
            $statusExpedition = "S031";
            $message = "Produk berhasil diselesaikan";
        } else {
            $qtyDiterima = 0;
            $qtyBadStock = 0;
            $imageName = "";
            $statusExpedition = "S037";
            $message = "Produk berhasil dibatalkan";
        }

        try {
            DB::transaction(function () use ($deliveryOrderDetailID, $statusExpedition, $expeditionDetailID, $status, $deliveryOrderService, $qtyDiterima, $qtyBadStock, $imageName) {
                $deliveryOrderDetail = DB::table('tx_merchant_delivery_order_detail')
                    ->where('DeliveryOrderDetailID', $deliveryOrderDetailID->DeliveryOrderDetailID);
                $expeditionDetail = DB::table('tx_merchant_expedition_detail')
                    ->where('MerchantExpeditionDetailID', $expeditionDetailID);

                $DOdetail = (clone $deliveryOrderDetail)->select('Qty')->first();

                if ($status == "finish" && $qtyDiterima <= $DOdetail->Qty) {
                    (clone $deliveryOrderDetail)->update([
                        'StatusExpedition' => $statusExpedition,
                        'Qty' => $qtyDiterima
                    ]);
                    (clone $expeditionDetail)->update([
                        'StatusExpeditionDetail' => $statusExpedition,
                        'ReceiptImage' => $imageName
                    ]);

                    $qtyTdkDiterima = $DOdetail->Qty - $qtyDiterima - $qtyBadStock;
                    if ($qtyTdkDiterima > 0) {
                        $deliveryOrderService->cancelProductExpedition($expeditionDetailID, $qtyTdkDiterima, "GOOD STOCK");
                    }
                    if ($qtyBadStock > 0) {
                        $deliveryOrderService->cancelProductExpedition($expeditionDetailID, $qtyBadStock, "BAD STOCK", $qtyTdkDiterima);
                    }
                }

                if ($status == "cancel") {
                    (clone $deliveryOrderDetail)->update([
                        'StatusExpedition' => $statusExpedition
                    ]);
                    (clone $expeditionDetail)->update([
                        'StatusExpeditionDetail' => $statusExpedition
                    ]);

                    $deliveryOrderService->cancelProductExpedition($expeditionDetailID, $DOdetail->Qty, "GOOD STOCK");
                }
            });
            return redirect()->back()->with('success', $message);
        } catch (\Throwable $th) {
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
                        DB::table('tx_merchant_expedition_detail')
                            ->whereRaw("DeliveryOrderDetailID = (
                                SELECT tx_merchant_delivery_order_detail.DeliveryOrderDetailID
                                FROM tx_merchant_delivery_order_detail
                                WHERE tx_merchant_delivery_order_detail.DeliveryOrderID = '$deliveryOrderID'
                                AND tx_merchant_delivery_order_detail.ProductID = '$value->item_code'
                            )")
                            ->update([
                                'StatusExpeditionDetail' => 'S030'
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

    public function requestCancelHaistar($deliveryOrderID, $expeditionID, HaistarService $haistarService)
    {
        $haistarCancelOrder = $haistarService->haistarCancelOrder($deliveryOrderID, "Batal");

        if ($haistarCancelOrder->status == 200) {
            try {
                DB::transaction(function () use ($deliveryOrderID, $expeditionID) {
                    DB::table('tx_merchant_delivery_order_detail')
                        ->where('DeliveryOrderID', $deliveryOrderID)
                        ->where('Distributor', 'HAISTAR')
                        ->update(['StatusExpedition' => 'S038']);
                    DB::table('tx_merchant_expedition_detail')
                        ->where('MerchantExpeditionID', $expeditionID)
                        ->whereRaw("DeliveryOrderDetailID IN (
                            SELECT DeliveryOrderDetailID FROM tx_merchant_delivery_order_detail
                            WHERE DeliveryOrderID = '$deliveryOrderID' AND Distributor = 'HAISTAR')")
                        ->update(['StatusExpeditionDetail' => 'S038']);
                });

                return redirect()->back()->with('success', 'Request Cancel berhasil dibuat');
            } catch (\Throwable $th) {
                return redirect()->back()->with('failed', 'Terjadi kesalahan');
            }
        } else {
            return redirect()->back()->with('failed', 'Terjadi kesalahan Haistar');
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
