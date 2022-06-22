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
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->where('tmdo.StatusDO', 'S025')
      ->select(
        'tmdo.DeliveryOrderID',
        'tmdo.StockOrderID',
        'tmdo.StatusDO',
        'tmdo.Discount',
        'tmdo.ServiceCharge',
        'tmdo.DeliveryFee',
        'ms_merchant_account.StoreName',
        'ms_merchant_account.PhoneNumber',
        'tmdo.CreatedDate',
        'tmdo.FinishDate',
        'tmdo.IsPaid',
        'tmdo.PaymentDate',
        'tmdo.PaymentSlip',
        'tmdo.PaymentNominal',
        'ms_status_order.StatusOrder',
        'ms_distributor.DistributorName',
        DB::raw("
          CONCAT(ms_merchant_account.ReferralCode, ' ', ms_sales.SalesName) AS Sales
        "),
        DB::raw("
          (
            SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
            WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
            AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID
          ) AS UrutanDO
        "),
        DB::raw("
          (
            SELECT SUM(Qty * Price) FROM tx_merchant_delivery_order_detail
            WHERE tx_merchant_delivery_order_detail.DeliveryOrderID = tmdo.DeliveryOrderID
            AND tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
          ) AS SubTotal
        ")
      );

    return $sql;
  }
}
