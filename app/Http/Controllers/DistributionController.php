<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Yajra\DataTables\Facades\DataTables;

class DistributionController extends Controller
{
    protected $baseImageUrl;

    public function __construct()
    {
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function restock()
    {
        return view('distribution.restock.index');
    }

    public function getRestockByStatus(Request $request, $statusOrder)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $fromShipmentDate = $request->input('fromShipmentDate');
        $toShipmentDate = $request->input('toShipmentDate');

        $sqlGetRestock = DB::table('tx_merchant_order')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->where('tx_merchant_order.StatusOrderID', '=', $statusOrder)
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.ShipmentDate', 'tx_merchant_order.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CancelReasonNote', 'tx_merchant_order.StatusOrderID');

        if (Auth::user()->RoleID == "AD" && Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetRestock->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlGetRestock->whereDate('tx_merchant_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_merchant_order.CreatedDate', '<=', $toDate);
        }
        if ($fromShipmentDate != '' && $toShipmentDate != '') {
            $sqlGetRestock->whereDate('tx_merchant_order.ShipmentDate', '>=', $fromShipmentDate)
                ->whereDate('tx_merchant_order.ShipmentDate', '<=', $toShipmentDate);
        }

        // Get data response
        $data = $sqlGetRestock;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">'.$data->Partner.'</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->editColumn('ShipmentDate', function ($data) {
                    return date('d M Y', strtotime($data->ShipmentDate));
                })
                ->addColumn('Action', function ($data) {
                    if ($data->StatusOrderID == "S009" || $data->StatusOrderID == "S023" || $data->StatusOrderID == "S012") {
                        $btn = "Proses";
                        $btnColor = "secondary";
                    } else {
                        $btn = "Lihat";
                        $btnColor = "info";
                    }
                    $actionBtn = '<a class="btn btn-sm btn-' . $btnColor . '" href="/distribution/restock/detail/' . $data->StockOrderID . '">
                                    ' . $btn . '
                                </a>';
                    return $actionBtn;
                })
                ->filterColumn('tx_merchant_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('tx_merchant_order.ShipmentDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_order.ShipmentDate,'%d-%b-%Y') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['Partner', 'Action'])
                ->make(true);
        }
    }

    public function restockDetail($stockOrderID)
    {
        $merchantOrder = DB::table('tx_merchant_order')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->leftJoin('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderID)
            ->select('ms_merchant_account.StoreImage', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'tx_merchant_order.MerchantID', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.StoreAddressNote', 'ms_merchant_account.Latitude', 'ms_merchant_account.Longitude', 'tx_merchant_order.StockOrderID', 'tx_merchant_order.StatusOrderID', 'tx_merchant_order.PaymentMethodID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.NettPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.ShipmentDate', 'tx_merchant_order.MerchantNote', 'tx_merchant_order.DistributorNote', 'tx_merchant_order.Rating', 'tx_merchant_order.Feedback', 'tx_merchant_order.CancelReasonNote', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName')
            ->first();

        $merchantOrderDetail = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order_detail.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'tx_merchant_order_detail.Quantity', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Price', 'tx_merchant_order_detail.Discount', 'tx_merchant_order_detail.Nett')
            ->get();

        $deliveryOrder = DB::table('tx_merchant_delivery_order')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_delivery_order.StatusDO')
            ->leftJoin('ms_user', 'ms_user.UserID', 'tx_merchant_delivery_order.DriverID')
            ->leftJoin('ms_vehicle', 'ms_vehicle.VehicleID', 'tx_merchant_delivery_order.VehicleID')
            ->where('tx_merchant_delivery_order.StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_delivery_order.*', 'ms_status_order.StatusOrder', 'ms_user.Name', 'ms_vehicle.VehicleName')
            ->get();

        foreach ($deliveryOrder as $key => $value) {
            $deliveryOrderDetail = DB::table('tx_merchant_delivery_order_detail')
                ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_delivery_order_detail.ProductID')
                ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', '=', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
                ->where('tx_merchant_delivery_order_detail.DeliveryOrderID', '=', $value->DeliveryOrderID)
                ->select('tx_merchant_delivery_order_detail.ProductID', 'tx_merchant_delivery_order_detail.Qty', 'tx_merchant_delivery_order_detail.Price', 'ms_product.ProductName', 'ms_product.ProductImage')
                ->get()->toArray();
            $value->DetailProduct = $deliveryOrderDetail;
            $subTotal = 0;
            foreach ($deliveryOrderDetail as $key => $item) {
                $subTotal += $item->Price * $item->Qty;
                $orderQty = DB::table('tx_merchant_order_detail')
                    ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderID)
                    ->where('tx_merchant_order_detail.ProductID', '=', $item->ProductID)
                    ->select('tx_merchant_order_detail.PromisedQuantity')
                    ->first();
                $item->OrderQty = $orderQty->PromisedQuantity;
            }
            $value->SubTotal = $subTotal;
        }

        $productAddDO = DB::table('tx_merchant_order_detail')
            ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order_detail.ProductID', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Nett', 'ms_product.ProductName', 'ms_product.ProductImage')
            ->get();

        $promisedQty = 0;
        $deliveryOrderQty = 0;

        foreach ($productAddDO as $key => $value) {
            $productQtyDO = DB::table('tx_merchant_delivery_order')
                ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderID', '=', 'tx_merchant_delivery_order.DeliveryOrderID')
                ->where('tx_merchant_delivery_order.StockOrderID', '=', $stockOrderID)
                ->where('tx_merchant_delivery_order_detail.ProductID', '=', $value->ProductID)
                ->selectRaw('IFNULL(SUM(tx_merchant_delivery_order_detail.Qty), 0) as Qty')
                ->first();
            $value->QtyDO = $productQtyDO->Qty;

            $promisedQty += $value->PromisedQuantity;
            $deliveryOrderQty += $productQtyDO->Qty;
        }

        $drivers = DB::table('ms_user')
            ->where('RoleID', 'DRV')
            ->where('IsTesting', 0)
            ->select('UserID', 'Name')
            ->orderBy('Name');

        if (Auth::user()->Depo == "ALL") {
            $dataDrivers = $drivers->get();
        } else {
            $dataDrivers = $drivers->where('Depo', Auth::user()->Depo)->get();
        }

        $vehicles = DB::table('ms_vehicle')
            ->select('*')
            ->orderBy('VehicleName')->get();

        return view('distribution.restock.detail', [
            'stockOrderID' => $stockOrderID,
            'merchantOrder' => $merchantOrder,
            'merchantOrderDetail' => $merchantOrderDetail,
            'deliveryOrder' => $deliveryOrder,
            'productAddDO' => $productAddDO,
            'promisedQty' => $promisedQty,
            'deliveryOrderQty' => $deliveryOrderQty,
            'drivers' => $dataDrivers,
            'vehicles' => $vehicles
        ]);
    }

    public function updateStatusRestock(Request $request, $stockOrderID, $status)
    {
        $txMerchantOrder = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->where('StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order.PaymentMethodID', 'tx_merchant_order.DistributorID', 'tx_merchant_order.MerchantID', 'ms_merchant_account.MerchantFirebaseToken', 'ms_distributor.DistributorName', 'ms_payment_method.PaymentMethodCategory')->first();

        $txMerchantOrderDetail = DB::table('tx_merchant_order_detail')
            ->where('StockOrderID', '=', $stockOrderID)
            ->select('*')->get();

        $pesananBaru = "S009";
        $dikonfirmasi = "S010";
        $dalamProses = "S023";
        $dikirim = "S012";
        $selesai = "S018";
        $dibatalkan = "S011";

        $baseImageUrl = config('app.base_image_url');

        if ($status == "reject") {
            $request->validate([
                'cancel_reason' => 'required'
            ]);

            // data untuk update tx merchant order
            $data = [
                'StatusOrderID' => $dibatalkan,
                'CancelReasonID' => 'CO-004',
                'CancelReasonNote' => $request->input('cancel_reason')
            ];

            // data untuk insert tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $dibatalkan,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => 'DISTRIBUTOR'
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $data, $dataLog) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                });

                $fields = array(
                    'registration_ids' => array($txMerchantOrder->MerchantFirebaseToken),
                    'data' => array(
                        "date" => date("Y-m-d H:i:s"),
                        "merchantID" => $txMerchantOrder->MerchantID,
                        "title" => "Pesanan Anda dibatalkan oleh " . $txMerchantOrder->DistributorName,
                        "body" => "Pesanan Restok Dibatalkan",
                        'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                    )
                );

                $headers = array(
                    'Authorization: key=' . config('app.firebase_auth_token'),
                    'Content-Type: application/json'
                );

                $fields = json_encode($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                curl_exec($ch);
                curl_close($ch);

                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil dibatalkan');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } elseif ($status == "approved") {
            if ($txMerchantOrder->PaymentMethodCategory == "CASH") { // kategori cash
                $statusOrder = $dalamProses;
                $titleNotif = "Pesanan Restok Dalam Proses";
                $bodyNotif = "Pesanan Anda sedang diproses " . $txMerchantOrder->DistributorName . " dan akan segera dikirim.";
            } else { // non tunai
                $statusOrder = $dikonfirmasi;
                $titleNotif = "Pesanan Restok Dikonfirmasi dan Menunggu Pembayaran";
                $bodyNotif = "Pesanan Anda telah dikonfirmasi dari " . $txMerchantOrder->DistributorName . ". Silakan periksa kembali pesanan Anda dan segera lakukan pembayaran.";
            }

            // data untuk insert tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $statusOrder,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => 'DISTRIBUTOR'
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $statusOrder, $dataLog) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update([
                            'StatusOrderID' => $statusOrder
                        ]);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                });

                $fields = array(
                    'registration_ids' => array($txMerchantOrder->MerchantFirebaseToken),
                    'data' => array(
                        "date" => date("Y-m-d H:i:s"),
                        "merchantID" => $txMerchantOrder->MerchantID,
                        "title" => $titleNotif,
                        "body" => $bodyNotif,
                        'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                    )
                );

                $headers = array(
                    'Authorization: key=' . config('app.firebase_auth_token'),
                    'Content-Type: application/json'
                );

                $fields = json_encode($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                curl_exec($ch);
                curl_close($ch);

                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil diproses');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } elseif ($status == "send") {
            $request->validate([
                'distributor_note' => 'string|nullable'
            ]);

            // data untuk update tx merchant order
            $data = [
                'StatusOrderID' => $dikirim,
                'DistributorNote' => $request->input('distributor_note')
            ];

            // data untuk update tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $dikirim,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => 'DISTRIBUTOR'
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $data, $dataLog) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                });

                $fields = array(
                    'registration_ids' => array($txMerchantOrder->MerchantFirebaseToken),
                    'data' => array(
                        "date" => date("Y-m-d H:i:s"),
                        "merchantID" => $txMerchantOrder->MerchantID,
                        "title" => "Pesanan Restok Dikirim",
                        "body" => "Pesanan Anda sedang dikirim menuju alamat Anda oleh " . $txMerchantOrder->DistributorName . ".",
                        'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                    )
                );

                $headers = array(
                    'Authorization: key=' . config('app.firebase_auth_token'),
                    'Content-Type: application/json'
                );

                $fields = json_encode($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                curl_exec($ch);
                curl_close($ch);

                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil dikirim');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        }
    }

    public function updateDeliveryOrder($deliveryOrderId)
    {
        $stockOrderID = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', '=', $deliveryOrderId)
            ->select('StockOrderID', 'DriverID', 'VehicleID', 'VehicleLicensePlate')->first();

        try {
            DB::transaction(function () use ($stockOrderID, $deliveryOrderId) {
                DB::table('tx_merchant_delivery_order')
                    ->where('DeliveryOrderID', '=', $deliveryOrderId)
                    ->update([
                        'StatusDO' => 'S025',
                        'FinishDate' => date('Y-m-d H:i:s')
                    ]);

                DB::table('tx_merchant_delivery_order_log')
                    ->insert([
                        'StockOrderID' => $stockOrderID->StockOrderID,
                        'DeliveryOrderID' => $deliveryOrderId,
                        'StatusDO' => 'S025',
                        'DriverID' => $stockOrderID->DriverID,
                        'VehicleID' => $stockOrderID->VehicleID,
                        'VehicleLicensePlate' => $stockOrderID->VehicleLicensePlate,
                        'ActionBy' => 'DISTRIBUTOR'
                    ]);
            });

            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('success', 'Delivery Order telah diselesaikan');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function createDeliveryOrder(Request $request, $stockOrderID)
    {
        $request->validate([
            'created_date_do' => 'required|date',
            'driver' => 'required',
            'vehicle' => 'required',
            'license_plate' => 'required',
            'qty_do' => 'required',
            'qty_do.*' => 'required|numeric|lte:max_qty_do.*|gte:1'
        ]);

        $baseImageUrl = config('app.base_image_url');

        $msMerchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderID)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.MerchantFirebaseToken', 'ms_distributor.DistributorName')
            ->first();

        $max = DB::table('tx_merchant_delivery_order')
            ->selectRaw('MAX(DeliveryOrderID) AS DeliveryOrderID, MAX(CreatedDate) AS CreatedDate')
            ->first();

        $maxMonth = date('m', strtotime($max->CreatedDate));
        $now = date('m');
            
        if ($max->DeliveryOrderID == null || (strcmp($maxMonth, $now) != 0)) {
            $newDeliveryOrderID = "DO-" . date('YmdHis') . '-000001';
        } else {
            $maxDONumber = substr($max->DeliveryOrderID, 18);
            $newDONumber = $maxDONumber + 1;
            $newDeliveryOrderID = "DO-" . date('YmdHis') . "-" . str_pad($newDONumber, 6, '0', STR_PAD_LEFT);
        }

        $createdDateDO = str_replace("T", " ", $request->input('created_date_do'));

        $dataDO = [
            'DeliveryOrderID' => $newDeliveryOrderID,
            'StockOrderID' => $stockOrderID,
            'StatusDO' => 'S024',
            'DriverID' => $request->input('driver'),
            'VehicleID' => $request->input('vehicle'),
            'VehicleLicensePlate' => $request->input('license_plate'),
            'CreatedDate' => $createdDateDO
        ];

        $productId = $request->input('product_id');
        $qty = $request->input('qty_do');
        $price = $request->input('price');

        $dataDetailDO = array_map(function () {
            return func_get_args();
        }, $productId, $qty, $price);
        foreach ($dataDetailDO as $key => $value) {
            $dataDetailDO[$key][] = $newDeliveryOrderID;
        }

        $dataLogDO = [
            'StockOrderID' => $stockOrderID,
            'DeliveryOrderID' => $newDeliveryOrderID,
            'StatusDO' => 'S024',
            'DriverID' => $request->input('driver'),
            'VehicleID' => $request->input('vehicle'),
            'VehicleLicensePlate' => $request->input('license_plate'),
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo
        ];

        try {
            DB::transaction(function () use ($dataDO, $dataDetailDO, $dataLogDO) {
                DB::table('tx_merchant_delivery_order')
                    ->insert($dataDO);
                foreach ($dataDetailDO as &$value) {
                    $value = array_combine(['ProductID', 'Qty', 'Price', 'DeliveryOrderID'], $value);
                    DB::table('tx_merchant_delivery_order_detail')
                        ->insert([
                            'DeliveryOrderID' => $value['DeliveryOrderID'],
                            'ProductID' => $value['ProductID'],
                            'Qty' => $value['Qty'],
                            'Price' => $value['Price']
                        ]);
                }
                DB::table('tx_merchant_delivery_order_log')
                    ->insert($dataLogDO);
            });

            $fields = array(
                'registration_ids' => array($msMerchant->MerchantFirebaseToken),
                'data' => array(
                    "date" => date("Y-m-d H:i:s"),
                    "merchantID" => $msMerchant->MerchantID,
                    "title" => "Pesanan Restok Dikirim",
                    "body" => "Pesanan Anda sedang dikirim menuju alamat Anda oleh " . $msMerchant->DistributorName . " dengan nomor delivery ".$newDeliveryOrderID.".",
                    'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                )
            );

            $headers = array(
                'Authorization: key=' . config('app.firebase_auth_token'),
                'Content-Type: application/json'
            );

            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            curl_exec($ch);
            curl_close($ch);

            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('success', 'Delivery Order berhasil dibuat');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function updateQtyDO(Request $request, $deliveryOrderId)
    {
        $request->validate([
            'driver' => 'required',
            'vehicle' => 'required',
            'license_plate' => 'required'
        ]);

        $stockOrderID = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', '=', $deliveryOrderId)
            ->select('StockOrderID')->first();

        $qty = $request->input('edit_qty_do');
        $productID = $request->input('product_id');

        $dataUpdateDO = array_map(function () {
            return func_get_args();
        }, $productID, $qty);
        foreach ($dataUpdateDO as $key => $value) {
            $dataUpdateDO[$key][] = $deliveryOrderId;
        }

        $dataDriver = [
            'DriverID' => $request->input('driver'),
            'VehicleID' => $request->input('vehicle'),
            'VehicleLicensePlate' => $request->input('license_plate')
        ];

        $dataLogDO = [
            'StockOrderID' => $stockOrderID->StockOrderID,
            'DeliveryOrderID' => $deliveryOrderId,
            'StatusDO' => 'S024',
            'DriverID' => $request->input('driver'),
            'VehicleID' => $request->input('vehicle'),
            'VehicleLicensePlate' => $request->input('license_plate'),
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo
        ];

        try {
            DB::transaction(function () use ($dataUpdateDO, $deliveryOrderId, $dataDriver, $dataLogDO) {
                foreach ($dataUpdateDO as &$value) {
                    $value = array_combine(['ProductID', 'Qty', 'DeliveryOrderID'], $value);
                    DB::table('tx_merchant_delivery_order_detail')
                        ->where('DeliveryOrderID', '=', $value['DeliveryOrderID'])
                        ->where('ProductID', '=', $value['ProductID'])
                        ->update([
                            'Qty' => $value['Qty']
                        ]);
                }
                DB::table('tx_merchant_delivery_order')
                    ->where('DeliveryOrderID', '=', $deliveryOrderId)
                    ->update($dataDriver);
                DB::table('tx_merchant_delivery_order_log')
                    ->insert($dataLogDO);
            });

            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('success', 'Data Delivery Order berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function product()
    {
        return view('distribution.product.index');
    }

    public function getProduct(Request $request)
    {
        $distributorId = $request->input('distributorId');

        $distributorProducts = DB::table('ms_distributor_product_price')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_distributor_product_price.DistributorID')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_distributor_product_price.ProductID')
            ->join('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_product_price.GradeID')
            ->join('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->join('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->select('ms_distributor_product_price.DistributorID', 'ms_distributor.DistributorName', 'ms_distributor_product_price.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_product_uom.ProductUOMName', 'ms_product.ProductUOMDesc', 'ms_distributor_product_price.Price', 'ms_distributor_product_price.GradeID', 'ms_distributor_grade.Grade');

        if (Auth::user()->RoleID == "AD" && Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $distributorProducts->where('ms_distributor.Depo', '=', $depoUser);
        }

        if ($distributorId != null) {
            $distributorProducts->where('ms_distributor.DistributorID', '=', $distributorId);
        }

        $data = $distributorProducts;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="90">';
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade == "Retail") {
                        $grade = '<span class="badge badge-success">' . $data->Grade . '</span>';
                    } elseif ($data->Grade == "SO") {
                        $grade = '<span class="badge badge-warning">' . $data->Grade . '</span>';
                    } elseif ($data->Grade == "WS") {
                        $grade = '<span class="badge badge-primary">' . $data->Grade . '</span>';
                    } else {
                        $grade = $data->Grade;
                    }
                    return $grade;
                })
                ->addColumn('Action', function ($data) {
                    if (Auth::user()->RoleID != "AD") {
                        $actionBtn = '<a href="#" data-distributor-id="' . $data->DistributorID . '" data-product-id="' . $data->ProductID . '" data-grade-id="' . $data->GradeID . '" data-product-name="' . $data->ProductName . '" data-grade-name="' . $data->Grade . '" data-price="' . $data->Price . '" class="btn-edit btn btn-sm btn-warning mr-1">Ubah Harga</a>
                    <a data-distributor-id="' . $data->DistributorID . '" data-product-id="' . $data->ProductID . '" data-grade-id="' . $data->GradeID . '" data-product-name="' . $data->ProductName . '" data-grade-name="' . $data->Grade . '" href="#" class="btn-delete btn btn-sm btn-danger">Delete</a>';   
                    } else {
                        $actionBtn = '';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['Grade', 'ProductImage', 'Action'])
                ->make(true);
        }
    }

    public function addProduct()
    {
        $getDistributor = DB::table('ms_distributor')
            ->where('Ownership', '=', 'RTMart')
            ->where('Email', '!=', NULL)
            ->select('DistributorID', 'DistributorName', 'Email', 'Address', 'CreatedDate')
            ->get();

        if (Auth::user()->RoleID == "AD") {
            $distributorName = DB::table('ms_distributor')
                ->where('Depo', '=', Auth::user()->Depo)
                ->select('DistributorID', 'DistributorName')
                ->first();

            $productNotInDistributor = $getProduct = DB::table('ms_product')
                ->leftJoin('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
                ->leftJoin('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
                ->leftJoin('ms_distributor_product_price', 'ms_distributor_product_price.ProductID', '=', 'ms_product.ProductID')
                ->whereNotIn('ms_product.ProductID', function($query) use ($distributorName){
                    $query->select('ms_distributor_product_price.ProductID')->from('ms_distributor_product_price')->where('ms_distributor_product_price.DistributorID', '=', $distributorName->DistributorID);
                })
                ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_category.ProductCategoryName', 'ms_product_uom.ProductUOMName')
                ->distinct()
                ->get();

            $gradeDistributor = DB::table('ms_distributor_grade')
                ->where('ms_distributor_grade.DistributorID', '=', $distributorName->DistributorID)
                ->select('ms_distributor_grade.GradeID', 'ms_distributor_grade.Grade')
                ->get();
            
            return view('distribution.product.new', [
                'distributor' => $getDistributor,
                'depo' => $distributorName,
                'productNotInDistributor' => $productNotInDistributor,
                'gradeDistributor' => $gradeDistributor
            ]);
        } else {
            return view('distribution.product.new', [
                'distributor' => $getDistributor
            ]);
        }
    }

    public function ajaxGetProduct($distributorId)
    {
        $getProduct = DB::table('ms_product')
            ->leftJoin('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->leftJoin('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->leftJoin('ms_distributor_product_price', 'ms_distributor_product_price.ProductID', '=', 'ms_product.ProductID')
            ->whereNotIn('ms_product.ProductID', function($query) use ($distributorId){
                $query->select('ms_distributor_product_price.ProductID')->from('ms_distributor_product_price')->where('ms_distributor_product_price.DistributorID', '=', $distributorId);
            })
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_category.ProductCategoryName', 'ms_product_uom.ProductUOMName')
            ->distinct()
            ->get();

        return response()->json($getProduct);
    }

    public function insertProduct(Request $request)
    {
        $request->validate([
            'distributor' => 'exists:ms_distributor,DistributorID',
            'product' => 'required|exists:ms_product,ProductID',
            'grade_price' => 'required',
            'grade_price.*' => 'numeric'
        ]);

        $gradeID = $request->input('grade_id');
        $gradePrice = $request->input('grade_price');
        $data = array_map(function () {
            return func_get_args();
        }, $gradeID, $gradePrice);
        foreach ($data as $key => $value) {
            $data[$key][] = $request->input('distributor');
            $data[$key][] = $request->input('product');
        }

        try {
            DB::transaction(function () use ($data) {
                foreach ($data as &$value) {
                    $value = array_combine(['GradeID', 'Price', 'DistributorID', 'ProductID'], $value);
                    DB::table('ms_distributor_product_price')
                        ->insert([
                            'DistributorID' => $value['DistributorID'],
                            'ProductID' => $value['ProductID'],
                            'GradeID' => $value['GradeID'],
                            'Price' => $value['Price']
                        ]);
                }
            });

            return redirect()->route('distribution.product')->with('success', 'Data Produk berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.product')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function updateProduct(Request $request, $distributorId, $productId, $gradeId)
    {
        $request->validate([
            'price' => 'required|integer'
        ]);

        $updateDistributorProduct = DB::table('ms_distributor_product_price')
            ->where('DistributorID', '=', $distributorId)
            ->where('ProductID', '=', $productId)
            ->where('GradeID', '=', $gradeId)
            ->update([
                'Price' => $request->input('price')
            ]);

        if ($updateDistributorProduct) {
            return redirect()->route('distribution.product')->with('success', 'Harga produk telah berhasil diubah');
        } else {
            return redirect()->route('distribution.product')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteProduct($distributorId, $productId, $gradeId)
    {
        $deleteProduct = DB::table('ms_distributor_product_price')
            ->where('DistributorID', '=', $distributorId)
            ->where('ProductID', '=', $productId)
            ->where('GradeID', '=', $gradeId)
            ->delete();

        if ($deleteProduct) {
            return redirect()->route('distribution.product')->with('success', 'Data produk berhasil dihapus');
        } else {
            return redirect()->route('distribution.product')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function merchant()
    {
        return view('distribution.merchant.index');
    }

    public function getMerchant(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributorId = $request->input('distributorId');

        // Get data account, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_merchant_account')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', '=', 'ms_merchant_account.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_merchant_grade.GradeID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.DistributorID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.CreatedDate', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.ReferralCode', 'ms_distributor.DistributorName', 'ms_distributor_grade.Grade', 'ms_distributor_merchant_grade.GradeID');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_merchant_account.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_merchant_account.CreatedDate', '<=', $toDate);
        }

        if (Auth::user()->RoleID == "AD" && Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('ms_distributor.Depo', '=', $depoUser);
        }

        if ($distributorId != null) {
            $sqlAllAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade == null) {
                        $grade = "Retail";
                    } else {
                        $grade = $data->Grade;
                    }
                    return $grade;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="#" data-distributor-id="'.$data->DistributorID.'" data-merchant-id="'.$data->MerchantID.'" data-store-name="'.$data->StoreName.'" data-owner-name="'.$data->OwnerFullName.'" data-grade-id="'.$data->GradeID.'" class="btn btn-sm btn-warning edit-grade">Ubah Grade</a>';
                    return $actionBtn;
                })
                ->filterColumn('ms_merchant_account.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_merchant_account.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['Action'])
                ->make(true);
        }
    }

    public function updateGrade(Request $request, $merchantId)
    {
        $merchantGrade = DB::table('ms_distributor_merchant_grade')
            ->where('MerchantID', '=', $merchantId)
            ->select('MerchantID')->first();

        $request->validate([
            'grade' => 'required|exists:ms_distributor_grade,GradeID'
        ]);

        $dataGrade = [
            'DistributorID' => $request->input('distributor'),
            'MerchantID' => $merchantId,
            'GradeID' => $request->input('grade')
        ];

        if ($merchantGrade) {
            $updateGrade = DB::table('ms_distributor_merchant_grade')
                ->where('MerchantID', '=', $merchantId)
                ->update($dataGrade);
        } else {
            $updateGrade =  DB::table('ms_distributor_merchant_grade')
                ->insert($dataGrade);
        }

        if ($updateGrade) {
            return redirect()->route('distribution.merchant')->with('success', 'Data grade merchant berhasil diubah');
        } else {
            return redirect()->route('distribution.merchant')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }
}
