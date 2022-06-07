<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayLaterService
{
  public function billPayLaterGet()
  {
    $sql = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_order', function ($join) {
        $join->on('tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID');
        $join->where('tx_merchant_order.PaymentMethodID', 14);
      })
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tx_merchant_delivery_order.StatusDO')
      ->select('tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order.StockOrderID', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber', 'tx_merchant_delivery_order.CreatedDate', 'tx_merchant_delivery_order.FinishDate', 'tx_merchant_delivery_order.IsPaid', 'tx_merchant_delivery_order.PaymentDate', 'tx_merchant_delivery_order.PaymentSlip', 'tx_merchant_delivery_order.PaymentNominal', 'ms_status_order.StatusOrder');

    return $sql;
  }
}
