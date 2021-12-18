<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DistributionController extends Controller
{
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
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.ShipmentDate', 'tx_merchant_order.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CancelReasonNote', 'tx_merchant_order.StatusOrderID');

        if (Auth::user()->RoleID == "AD" && Auth::user()->RoleID != "ALL") {
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
                ->editColumn('ShipmentDate', function ($data) {
                    return date('d M Y', strtotime($data->ShipmentDate));
                })
                ->addColumn('Action', function ($data) {
                    if ($data->StatusOrderID == "S009" || $data->StatusOrderID == "S023") {
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
                ->rawColumns(['Action'])
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

        return view('distribution.restock.detail', [
            'stockOrderID' => $stockOrderID,
            'merchantOrder' => $merchantOrder,
            'merchantOrderDetail' => $merchantOrderDetail
        ]);
    }

    public function updateStatusRestock(Request $request, $stockOrderID, $status)
    {
        $txMerchantOrder = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->where('StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order.PaymentMethodID', 'tx_merchant_order.DistributorID', 'tx_merchant_order.MerchantID', 'ms_merchant_account.MerchantFirebaseToken', 'ms_distributor.DistributorName')->first();

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

            // data untuk update ms visit order
            $dataMsVisit = [
                'IsProcessed' => 3,
                'ProcessedBy' => 'DISTRIBUTOR',
                'ProcessedDate' => date("Y-m-d H:i:s"),
                'CategoryReason' => $request->input('cancel_reason'),
                'CancelReason' => $request->input('cancel_reason')
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $data, $dataLog, $dataMsVisit) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                    DB::table('ms_visit_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($dataMsVisit);
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
            $request->validate(
                [
                    'shipment_date' => 'required|date',
                    'promised_qty' => 'required',
                    'promised_qty.*' => 'required|numeric|min:0',
                    'discount_product' => 'required',
                    'discount_product.*' => 'required|numeric|min:0',
                    'discount_price' => 'required|numeric|min:0'
                ],
                [
                    'promised_qty.*.min' => 'The promised quantity must be at least 0.',
                    'discount_product.*.min' => 'The discount product must be at least 0.',
                ]
            );

            if ($txMerchantOrder->PaymentMethodID == 1) { // Tunai
                $statusOrder = $dalamProses;
                $titleNotif = "Pesanan Restok Dalam Proses";
                $bodyNotif = "Pesanan Anda sedang diproses " . $txMerchantOrder->DistributorName . " dan akan segera dikirim.";
                $isProcessed = 1;
            } else { // non tunai
                $statusOrder = $dikonfirmasi;
                $titleNotif = "Pesanan Restok Dikonfirmasi dan Menunggu Pembayaran";
                $bodyNotif = "Pesanan Anda telah dikonfirmasi dari " . $txMerchantOrder->DistributorName . ". Silakan periksa kembali pesanan Anda dan segera lakukan pembayaran.";
                $isProcessed = 0;
            }

            $productId = $request->input('product_id');
            $promisedQty = $request->input('promised_qty');
            $discountProduct = $request->input('discount_product');

            $productOrderDetail = array_map(function () {
                return func_get_args();
            }, $productId, $promisedQty, $discountProduct);

            $totalPrice = 0;
            $dataDetail = []; // data untuk update tx merchant order detail
            $dataMsVisit = []; // data untuk update ms visit order 
            foreach ($productOrderDetail as $value) {
                $value = array_combine(['ProductID', 'PromisedQuantity', 'Discount'], $value);
                foreach ($txMerchantOrderDetail as $orderDetail) {
                    if ($value['ProductID'] == $orderDetail->ProductID) {
                        $price = $orderDetail->Price;
                        $nett = $price - str_replace('.', '', $value['Discount']);
                        $pricePerProduct = $nett * str_replace('.', '', $value['PromisedQuantity']);
                        $totalPrice += $pricePerProduct;
                        $arrayDetail = [
                            'ProductID' => $value['ProductID'],
                            'PromisedQuantity' => $value['PromisedQuantity'] * 1,
                            'Discount' => str_replace('.', '', $value['Discount']) * 1,
                            'Nett' => $nett
                        ];
                        array_push($dataDetail, $arrayDetail);

                        $msVisitOrder = DB::table('ms_visit_order')
                            ->where('StockOrderID', '=', $stockOrderID)
                            ->where('ProductID', '=', $value['ProductID'])
                            ->select('PurchasePrice')->first();

                        $margin = $nett - $msVisitOrder->PurchasePrice;

                        $arrayMsVisit = [
                            'ProductID' => $value['ProductID'],
                            'Qty' => $value['PromisedQuantity'] * 1,
                            'Price' => $nett,
                            'TotalPrice' => $pricePerProduct,
                            'Margin' => $margin,
                            'MarginInPersen' => number_format((float)($margin / $msVisitOrder->PurchasePrice) * 100, 2, '.', ''),
                            'IsProcessed' => $isProcessed,
                            'ProcessedBy' => 'DISTRIBUTOR',
                            'ProcessedDate' => date("Y-m-d H:i:s")
                        ];
                        array_push($dataMsVisit, $arrayMsVisit);
                    }
                }
            }

            // data untuk update tx merchant order
            $data = [
                'StatusOrderID' => $statusOrder,
                'ShipmentDate' => $request->input('shipment_date'),
                'DiscountPrice' => str_replace('.', '', $request->input('discount_price')) * 1,
                'TotalPrice' => $totalPrice,
                'NettPrice' => $totalPrice - str_replace('.', '', $request->input('discount_price'))
            ];

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
                DB::transaction(function () use ($stockOrderID, $data, $dataDetail, $dataLog, $dataMsVisit) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    foreach ($dataDetail as $value) {
                        DB::table('tx_merchant_order_detail')
                            ->where('StockOrderID', '=', $stockOrderID)
                            ->where('ProductID', '=', $value['ProductID'])
                            ->update($value);
                    }
                    foreach ($dataMsVisit as $value) {
                        DB::table('ms_visit_order')
                            ->where('StockOrderID', '=', $stockOrderID)
                            ->where('ProductID', '=', $value['ProductID'])
                            ->update($value);
                    }
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

            // data untuk update ms visit order
            $dataMsVisit = [
                'IsProcessed' => 1,
                'IsDelivered' => 1,
                'StartTimeDelivery' => date("Y-m-d H:i:s")
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $data, $dataLog, $dataMsVisit) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                    DB::table('ms_visit_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($dataMsVisit);
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
}
