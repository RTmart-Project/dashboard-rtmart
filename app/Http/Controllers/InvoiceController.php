<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function invoiceSO($stockOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderId)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order.StatusOrderID')
            ->first();

        $stockOrderById = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('StockOrderID', '=', $stockOrderId)
            ->select('tx_merchant_order_detail.*', 'ms_product.ProductName')->get();

        $subTotal = 0;
        foreach ($stockOrderById as $key => $value) {
            $subTotal += $value->Nett * $value->PromisedQuantity;
        }

        return view('merchant.restock.invoice.invoice_so', [
            'stockOrderId' => $stockOrderId,
            'merchant' => $merchant,
            'stockOrderById' => $stockOrderById,
            'subTotal' => $subTotal
        ]);
    }

    public function invoiceDO($deliveryOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.StockOrderID', '=', 'tx_merchant_order.StockOrderID')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_delivery_order.StatusDO')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->leftJoin('ms_user', 'ms_user.UserID', 'tx_merchant_delivery_order.DriverID')
            ->where('tx_merchant_delivery_order.DeliveryOrderID', '=', $deliveryOrderId)
            ->select('tx_merchant_order.StockOrderID', 'ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order.CreatedDate', 'tx_merchant_delivery_order.FinishDate', 'ms_user.Name', 'tx_merchant_delivery_order.Distributor', 'tx_merchant_order.PaymentMethodID', 'tx_merchant_delivery_order.IsPaid')
            ->first();

        $detailDeliveryOrder = DB::table('tx_merchant_delivery_order_detail')
            ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_delivery_order_detail.ProductID')
            ->where('tx_merchant_delivery_order_detail.DeliveryOrderID', '=', $deliveryOrderId)
            ->select('tx_merchant_delivery_order_detail.Qty', 'tx_merchant_delivery_order_detail.Price', 'ms_product.ProductName')
            ->get();

        $subTotal = 0;
        foreach ($detailDeliveryOrder as $key => $value) {
            $subTotal += $value->Price * $value->Qty;
        }

        $processTime = DB::table('tx_merchant_delivery_order_log')
            ->where('DeliveryOrderID', $deliveryOrderId)
            ->max('ProcessTime');

        return view('merchant.restock.invoice.invoice_do', [
            'merchant' => $merchant,
            'detailDeliveryOrder' => $detailDeliveryOrder,
            'subTotal' => $subTotal,
            'processTime' => $processTime
        ]);
    }
}
