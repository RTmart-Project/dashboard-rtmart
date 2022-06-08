<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayLaterService
{
  public function billPayLaterGet()
  {
    $sql = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order', function ($join) {
        $join->on('tx_merchant_order.StockOrderID', 'tmdo.StockOrderID');
        $join->where('tx_merchant_order.PaymentMethodID', 14);
      })
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->select(
        'tmdo.DeliveryOrderID',
        'tmdo.StockOrderID',
        'ms_merchant_account.StoreName',
        'ms_merchant_account.PhoneNumber',
        'tmdo.CreatedDate',
        'tmdo.FinishDate',
        'tmdo.IsPaid',
        'tmdo.PaymentDate',
        'tmdo.PaymentSlip',
        'tmdo.PaymentNominal',
        'ms_status_order.StatusOrder',
        DB::raw("
          (
            SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
            WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
            AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID
          ) AS UrutanDO
        ")
      );

    return $sql;
  }
}
