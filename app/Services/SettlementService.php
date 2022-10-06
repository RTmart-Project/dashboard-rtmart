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
        $join->whereIn('tx_merchant_order.PaymentMethodID', [1, 13]);
      })
      ->leftJoin('ms_status_settlement', 'ms_status_settlement.StatusSettlementID', 'tmdo.StatusSettlementID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID');
        $join->whereRaw("(ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)");
      })
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tx_merchant_order.SalesCode')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->leftJoin('tx_merchant_delivery_order_payment_log', function ($join) {
        $join->on('tx_merchant_delivery_order_payment_log.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_payment_log.StatusSettlementID', 2);
      })
      ->where('tmdo.StatusDO', 'S025')
      ->whereDate('tmdo.CreatedDate', '>=', '2022-09-01')
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
        IF(tmdo.StatusSettlementID IS NOT NULL, ms_status_settlement.StatusSettlementName, 'Belum Setoran') AS StatusSettlementName,
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
        ) AS TotalSettlement,
        tx_merchant_delivery_order_payment_log.CreatedDate AS SettlementDate,
        tx_merchant_delivery_order_payment_log.ActionBy
      ");
    return $sql;
  }

  public function summaryDataSettlemnet($startDateFormat, $endDateFormat, $distributor, $filterBy)
  {
    $settlement = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order', function ($join) {
        $join->on('tx_merchant_order.StockOrderID', 'tmdo.StockOrderID');
        $join->whereRaw("tx_merchant_order.PaymentMethodID IN (1,13)");
      })
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID');
        $join->whereRaw("(ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)");
      })
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->whereRaw("tmdo.StatusDO = 'S025'")
      ->whereRaw("DATE(tmdo.CreatedDate) >= '2022-09-01'")
      ->selectRaw("
        CASE 
          WHEN tmdo.StatusSettlementID = 3 THEN tmdo.PaymentNominal
          ELSE 0
        END AS TotalDoneSettlement,
        (
          SELECT SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price) - ANY_VALUE(tmdo.Discount) + ANY_VALUE(tmdo.DeliveryFee) + ANY_VALUE(tmdo.ServiceCharge) 
          FROM tx_merchant_delivery_order_detail
          WHERE tx_merchant_delivery_order_detail.DeliveryOrderID = tmdo.DeliveryOrderID
            AND tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
        ) AS TotalMustSettlement
      ");

    if ($filterBy == "CreatedDate") {
      $settlement->whereRaw("DATE(tmdo.CreatedDate) >= '$startDateFormat'")
        ->whereRaw("DATE(tmdo.CreatedDate) <= '$endDateFormat'");
    } elseif ($filterBy == "FinishDate") {
      $settlement->whereRaw("DATE(tmdo.FinishDate) >= '$startDateFormat'")
        ->whereRaw("DATE(tmdo.FinishDate) <= '$endDateFormat'");
    }

    if (!empty($distributor)) {
      $stringDistributorID = "'" . implode("', '", $distributor) . "'";
      $settlement->whereRaw("tx_merchant_order.DistributorID IN ($stringDistributorID)");
    }

    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $settlement->whereRaw("ms_distributor.Depo = '$depoUser'");
    }

    $data = $settlement->toSql();

    $sql = DB::table(DB::raw("($data) as Settlement"))
      ->selectRaw("SUM(Settlement.TotalDoneSettlement) as TotalDoneSettlement, SUM(Settlement.TotalMustSettlement) as TotalMustSettlement")->first();

    return $sql;
  }

  public function updateDataSettlement($deliveryOrderID, $data, $dataLog)
  {
    $sql = DB::transaction(function () use ($deliveryOrderID, $data, $dataLog) {
      DB::table('tx_merchant_delivery_order')
        ->where('DeliveryOrderID', $deliveryOrderID)
        ->update($data);
      DB::table('tx_merchant_delivery_order_payment_log')->insert($dataLog);
    });

    return $sql;
  }

  public function confirmDataSettlement($deliveryOrderID, $data, $dataLog)
  {
    $sql = DB::transaction(function () use ($deliveryOrderID, $data, $dataLog) {
      DB::table('tx_merchant_delivery_order')
        ->where('DeliveryOrderID', $deliveryOrderID)
        ->update($data);
      DB::table('tx_merchant_delivery_order_payment_log')->insert($dataLog);
    });

    return $sql;
  }
}
