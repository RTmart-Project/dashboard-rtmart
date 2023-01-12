<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function invoiceSO($stockOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_sales', 'ms_merchant_account.ReferralCode', 'ms_sales.SalesCode')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tx_merchant_order.PaymentMethodID')
            ->where('tx_merchant_order.StockOrderID', $stockOrderId)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order.StatusOrderID', 'tx_merchant_order.Type', 'ms_sales.SalesName')
            ->first();

        $stockOrderById = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', 'tx_merchant_order_detail.ProductID')
            ->where('StockOrderID', $stockOrderId)
            ->select('tx_merchant_order_detail.*', 'ms_product.ProductName')
            ->get();

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
            ->leftJoin('tx_merchant_delivery_order_log', function ($join) {
                $join->on('tx_merchant_delivery_order_log.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
                $join->where('tx_merchant_delivery_order_log.StatusDO', '=', 'S024');
            })
            ->leftJoin('ms_user', 'ms_user.UserID', 'tx_merchant_delivery_order.DriverID')
            ->where('tx_merchant_delivery_order.DeliveryOrderID', '=', $deliveryOrderId)
            ->select('tx_merchant_order.StockOrderID', 'ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order.CreatedDate', 'tx_merchant_delivery_order.FinishDate', 'ms_user.Name', 'tx_merchant_delivery_order.Distributor', 'tx_merchant_order.PaymentMethodID', 'tx_merchant_delivery_order.IsPaid', 'tx_merchant_delivery_order.Discount', 'tx_merchant_delivery_order.ServiceCharge', 'tx_merchant_delivery_order.DeliveryFee', 'tx_merchant_delivery_order.StatusDO', 'tx_merchant_delivery_order_log.ProcessTime as DeliveryDate', 'tx_merchant_delivery_order.PaymentDate')
            ->first();

        $detailDeliveryOrder = DB::table('tx_merchant_delivery_order_detail')
            ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_delivery_order_detail.ProductID')
            ->where('tx_merchant_delivery_order_detail.DeliveryOrderID', '=', $deliveryOrderId)
            ->where('tx_merchant_delivery_order_detail.StatusExpedition', '!=', 'S037')
            ->select('tx_merchant_delivery_order_detail.Qty', 'tx_merchant_delivery_order_detail.Price', 'ms_product.ProductName')
            ->get();

        $subTotal = 0;
        foreach ($detailDeliveryOrder as $key => $value) {
            $subTotal += $value->Price * $value->Qty;
        }

        $processTime = DB::table('tx_merchant_delivery_order_log')
            ->where('DeliveryOrderID', $deliveryOrderId)
            ->max('ProcessTime');

        // DENDA
        $dueDate = strtotime("$merchant->FinishDate +5 day");
        if ($merchant->IsPaid == 0) {
            $timeDiff = time() - $dueDate;
        } else {
            $timeDiff = strtotime($merchant->PaymentDate) - $dueDate;
        }
        $lateDays = round($timeDiff / (60 * 60 * 24));

        $grandTotal = $subTotal + $merchant->ServiceCharge + $merchant->DeliveryFee - $merchant->Discount;

        if ($lateDays > 0 && $merchant->PaymentMethodID == 14) {
            $sqlLateBillFee = DB::table('tx_merchant_delivery_order_bill')
                ->where('PaymentMethodID', $merchant->PaymentMethodID)
                ->whereRaw("$lateDays BETWEEN OverdueStartDay AND OverdueToDay")
                ->select('TypeFee', 'NominalFee')
                ->first();

            if ($sqlLateBillFee->TypeFee == "PERCENT") {
                $lateFee = $grandTotal * $sqlLateBillFee->NominalFee / 100;
                $grandTotal += $lateFee;
            }

            if ($sqlLateBillFee->TypeFee == 'NOMINAL') {
                $lateFee = $sqlLateBillFee->NominalFee;
                $grandTotal += $lateFee;
            }
        } else {
            $lateFee = 0;
        }

        return view('merchant.restock.invoice.invoice_do', [
            'merchant' => $merchant,
            'detailDeliveryOrder' => $detailDeliveryOrder,
            'subTotal' => $subTotal,
            'lateFee' => $lateFee,
            'grandTotal' => $grandTotal,
            'processTime' => $processTime
        ]);
    }

    public function invoiceDOselesai($stockOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tx_merchant_order.PaymentMethodID')
            ->where('tx_merchant_order.StockOrderID', $stockOrderId)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order.StatusOrderID')
            ->first();

        $deliveryOrder = DB::table('tx_merchant_delivery_order')
            ->where('tx_merchant_delivery_order.StockOrderID', $stockOrderId)
            ->selectRaw("SUM(Discount) AS Discount, SUM(ServiceCharge) AS ServiceCharge, SUM(DeliveryFee) AS DeliveryFee")
            ->where('tx_merchant_delivery_order.StatusDO', 'S025')
            ->first();

        $deliveryOrderSelesai = DB::table('tx_merchant_delivery_order')
            ->join('tx_merchant_delivery_order_detail', function ($join) {
                $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
                $join->where('tx_merchant_delivery_order_detail.StatusExpedition', 'S031');
            })
            ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
            ->where('tx_merchant_delivery_order.StockOrderID', $stockOrderId)
            ->where('tx_merchant_delivery_order.StatusDO', 'S025')
            ->selectRaw("ANY_VALUE(ms_product.ProductName) AS ProductName, SUM(tx_merchant_delivery_order_detail.Qty) AS Qty, ANY_VALUE(tx_merchant_delivery_order_detail.Price) AS Price")
            ->groupBy('tx_merchant_delivery_order_detail.ProductID')
            ->get();

        $subTotal = 0;
        foreach ($deliveryOrderSelesai as $key => $value) {
            $subTotal += $value->Price * $value->Qty;
        }

        return view('merchant.restock.invoice.invoice_do_selesai', [
            'stockOrderId' => $stockOrderId,
            'merchant' => $merchant,
            'deliveryOrder' => $deliveryOrder,
            'deliveryOrderSelesai' => $deliveryOrderSelesai,
            'subTotal' => $subTotal
        ]);
    }
}
