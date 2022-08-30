<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettlementService
{

  public function dataSettlement()
  {
    $sql = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order', function ($join) {
        $join->on('tx_merchant_order.StockOrderID', 'tmdo.StockOrderID');
        $join->where('tx_merchant_order.PaymentMethodID', 1);
      })
      ->leftJoin('ms_status_settlement', 'ms_status_settlement.StatusSettlementID', 'tmdo.StatusSettlementID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tx_merchant_order.SalesCode')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->where('tmdo.StatusDO', 'S025')
      ->where('tmdo.Distributor', 'RT MART')
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
        tmdo.StatusSettlementID,
        IF(ms_status_settlement.StatusSettlementName != NULL, ms_status_settlement.StatusSettlementName, 'Belum Setoran') AS StatusSettlementName,
        tmdo.PaymentDate,
        tmdo.PaymentSlip,
        tmdo.PaymentNominal,
        ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        CONCAT(ANY_VALUE(tx_merchant_order.SalesCode), ' ', ANY_VALUE(ms_sales.SalesName)) AS Sales,
        (
          SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
          WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
          AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID
        ) AS UrutanDO,
        (
          SELECT SUM(Qty * Price) - tmdo.Discount + tmdo.DeliveryFee + tmdo.ServiceCharge 
          FROM tx_merchant_delivery_order_detail
          WHERE tx_merchant_delivery_order_detail.DeliveryOrderID = tmdo.DeliveryOrderID
          AND tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
        ) AS TotalSettlement
      ");
    return $sql;
  }
}
