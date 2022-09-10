<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayLaterService
{
  public function billPayLaterGet()
  {
    $sql = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_delivery_order_detail', function ($join) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_detail.StatusExpedition', 'S031');
      })
      ->join('tx_merchant_order', function ($join) {
        $join->on('tx_merchant_order.StockOrderID', 'tmdo.StockOrderID');
        $join->where('tx_merchant_order.PaymentMethodID', 14);
      })
      ->leftJoin('tx_merchant_delivery_order_log', function ($join) {
        $join->on('tx_merchant_delivery_order_log.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_log.StatusDO', '=', 'S024');
      })
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tx_merchant_order.SalesCode')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->where('tmdo.StatusDO', 'S025')
      ->selectRaw("
        tmdo.DeliveryOrderID,
        tmdo.StockOrderID,
        tmdo.StatusDO,
        tmdo.Discount,
        tmdo.ServiceCharge,
        tmdo.DeliveryFee,
        ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
        ANY_VALUE(ms_merchant_account.PhoneNumber) AS PhoneNumber,
        tmdo.CreatedDate,
        tmdo.FinishDate,
        tmdo.IsPaid,
        tmdo.PaymentDate,
        tmdo.PaymentSlip,
        tmdo.PaymentNominal,
        ANY_VALUE(tx_merchant_order.PaymentMethodID) AS PaymentMethodID,
        ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        MAX(tx_merchant_delivery_order_log.ProcessTime) AS DeliveryDate,
        CONCAT(ANY_VALUE(tx_merchant_order.SalesCode), ' ', ANY_VALUE(ms_sales.SalesName)) AS Sales,
        (
          SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
          WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
          AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID
        ) AS UrutanDO,
        (
          SELECT SUM(Qty * Price) FROM tx_merchant_delivery_order_detail
          WHERE tx_merchant_delivery_order_detail.DeliveryOrderID = tmdo.DeliveryOrderID
          AND tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
        ) AS SubTotal
      ")
      ->groupBy('tmdo.DeliveryOrderID');

    return $sql;
  }
}
