<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use stdClass;

class SummaryService
{
  public function summaryGrandTotal($startDate, $endDate)
  {
    $sql = DB::select("
        SELECT DISTINCT
          'GrandTotal',
          b.DateSummary,
          (
            SELECT IFNULL(SUM(TotalPrice), 0) FROM tx_merchant_order
            WHERE StatusOrderID = 'S023'
            AND DATE(CreatedDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS PurchaseOrder,
          (
            SELECT IFNULL(SUM(ms_stock_purchase_detail.Qty * ms_stock_purchase_detail.PurchasePrice), 0)
            FROM ms_stock_purchase
            JOIN ms_stock_purchase_detail ON ms_stock_purchase_detail.PurchaseID = ms_stock_purchase.PurchaseID
            WHERE ms_stock_purchase.StatusID = 2 AND ms_stock_purchase.Type = 'INBOUND'
            AND DATE(ms_stock_purchase.PurchaseDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS Purchasing,
          (
            SELECT IFNULL(SUM(DiscountPrice + DiscountVoucher), 0) FROM tx_merchant_order
            WHERE StatusOrderID = 'S023'
            AND DATE(CreatedDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS Voucher,
          (
            SELECT IFNULL(
                SUM(delivery_order_value.SubTotal - delivery_order_value.Discount + delivery_order_value.ServiceCharge + delivery_order_value.DeliveryFee)
              , 0)
            FROM (
              SELECT 
                tx_merchant_delivery_order.DeliveryOrderID,
                MAX(tx_merchant_delivery_order_log.ProcessTime) AS DeliveryDate,
                SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price) AS SubTotal,
                IFNULL(tx_merchant_delivery_order.Discount, 0) AS Discount,
                IFNULL(tx_merchant_delivery_order.ServiceCharge, 0) AS ServiceCharge,
                IFNULL(tx_merchant_delivery_order.DeliveryFee, 0) AS DeliveryFee
              FROM tx_merchant_delivery_order_detail
              JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                AND tx_merchant_delivery_order.StatusDO = 'S025'
              JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
              LEFT JOIN tx_merchant_delivery_order_log ON tx_merchant_delivery_order_log.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
                AND tx_merchant_delivery_order_log.StatusDO = 'S024'
              WHERE tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
              GROUP BY tx_merchant_delivery_order.DeliveryOrderID
            ) AS delivery_order_value
            WHERE DATE(delivery_order_value.DeliveryDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS DeliveryOrder,
          (
            SELECT IFNULL(SUM(PaymentNominal), 0)
            FROM tx_merchant_delivery_order
            WHERE StockOrderID IN (
              SELECT StockOrderID FROM tx_merchant_order WHERE PaymentMethodID = 14
            ) AND StatusDO = 'S025'
            AND tx_merchant_delivery_order.PaymentDate <= b.DateSummary
          ) AS BillReal,
          (
            SELECT IFNULL(
                SUM(delivery_order_value.SubTotal - delivery_order_value.Discount + delivery_order_value.ServiceCharge + delivery_order_value.DeliveryFee)
              , 0)
            FROM (
              SELECT 
                tx_merchant_delivery_order.DeliveryOrderID,
                DATE_ADD(tx_merchant_delivery_order.FinishDate, INTERVAL 5 DAY) AS DueDate,
                SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price) AS SubTotal,
                IFNULL(tx_merchant_delivery_order.Discount, 0) AS Discount,
                IFNULL(tx_merchant_delivery_order.ServiceCharge, 0) AS ServiceCharge,
                IFNULL(tx_merchant_delivery_order.DeliveryFee, 0) AS DeliveryFee
              FROM tx_merchant_delivery_order_detail
              JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                AND tx_merchant_delivery_order.StatusDO = 'S025'
              JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
                AND tx_merchant_order.PaymentMethodID = 14
              LEFT JOIN tx_merchant_delivery_order_log ON tx_merchant_delivery_order_log.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
                AND tx_merchant_delivery_order_log.StatusDO = 'S024'
              WHERE tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
              GROUP BY tx_merchant_delivery_order.DeliveryOrderID
            ) AS delivery_order_value
            WHERE DATE(delivery_order_value.DueDate) <= b.DateSummary
          ) AS BillTarget,
          (
            SELECT IFNULL(SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.PurchasePrice), 0)
            FROM ms_stock_product_log
            JOIN ms_stock_product ON ms_stock_product.StockProductID = ms_stock_product_log.StockProductID
            WHERE DATE(ms_stock_product_log.CreatedDate) <= b.DateSummary
          ) AS EndingInventory
        FROM 
          (
            SELECT DISTINCT ms_distributor.DistributorName, ms_distributor.DistributorID FROM tx_merchant_order
            JOIN ms_distributor ON ms_distributor.DistributorID = tx_merchant_order.DistributorID
            WHERE tx_merchant_order.DistributorID IN ('D-2004-000001', 'D-2004-000005', 'D-2004-000006')
          ) a
        CROSS JOIN 
          (
            select DateSummary from (
              select @maxDate - interval (a.a+(10*b.a)+(100*c.a)+(1000*d.a)) day DateSummary from
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) a, /*10 day range*/
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) b, /*100 day range*/
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) c, /*1000 day range*/
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) d, /*10000 day range*/
              (select @minDate := '$startDate', @maxDate := '$endDate') e
            ) f
            where DateSummary between @minDate and @maxDate
          ) b
        ORDER BY b.DateSummary
      ");

    return $sql;
  }

  public function getSummary($startDate, $endDate, $distributorID)
  {
    $sql = DB::select("
        SELECT 
          a.DistributorName,
          b.DateSummary,
          (
            SELECT IFNULL(SUM(TotalPrice), 0) FROM tx_merchant_order
            WHERE DistributorID = a.DistributorID AND StatusOrderID = 'S023'
            AND DATE(CreatedDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS PurchaseOrder,
          (
            SELECT IFNULL(SUM(ms_stock_purchase_detail.Qty * ms_stock_purchase_detail.PurchasePrice), 0)
            FROM ms_stock_purchase
            JOIN ms_stock_purchase_detail ON ms_stock_purchase_detail.PurchaseID = ms_stock_purchase.PurchaseID
            WHERE ms_stock_purchase.StatusID = 2 AND ms_stock_purchase.Type = 'INBOUND'
            AND ms_stock_purchase.DistributorID = a.DistributorID
            AND DATE(ms_stock_purchase.PurchaseDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS Purchasing,
          (
            SELECT IFNULL(SUM(DiscountPrice + DiscountVoucher), 0) FROM tx_merchant_order
            WHERE DistributorID = a.DistributorID AND StatusOrderID = 'S023'
            AND DATE(CreatedDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS Voucher,
          (
            SELECT IFNULL(
                SUM(delivery_order_value.SubTotal - delivery_order_value.Discount + delivery_order_value.ServiceCharge + delivery_order_value.DeliveryFee)
              , 0)
            FROM (
              SELECT 
                tx_merchant_delivery_order.DeliveryOrderID,
                MAX(tx_merchant_delivery_order_log.ProcessTime) AS DeliveryDate,
                ANY_VALUE(tx_merchant_order.DistributorID) AS DistributorID,
                SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price) AS SubTotal,
                IFNULL(tx_merchant_delivery_order.Discount, 0) AS Discount,
                IFNULL(tx_merchant_delivery_order.ServiceCharge, 0) AS ServiceCharge,
                IFNULL(tx_merchant_delivery_order.DeliveryFee, 0) AS DeliveryFee
              FROM tx_merchant_delivery_order_detail
              JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                AND tx_merchant_delivery_order.StatusDO = 'S025'
              JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
              LEFT JOIN tx_merchant_delivery_order_log ON tx_merchant_delivery_order_log.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
                AND tx_merchant_delivery_order_log.StatusDO = 'S024'
              WHERE tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
              GROUP BY tx_merchant_delivery_order.DeliveryOrderID
            ) AS delivery_order_value
            WHERE delivery_order_value.DistributorID = a.DistributorID
            AND DATE(delivery_order_value.DeliveryDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS DeliveryOrder,
          (
            SELECT IFNULL(SUM(PaymentNominal), 0)
            FROM tx_merchant_delivery_order
            WHERE StockOrderID IN (
              SELECT StockOrderID FROM tx_merchant_order WHERE PaymentMethodID = 14 AND DistributorID = a.DistributorID
            ) AND StatusDO = 'S025'
            AND tx_merchant_delivery_order.PaymentDate <= b.DateSummary
          ) AS BillReal,
          (
            SELECT IFNULL(
                SUM(delivery_order_value.SubTotal - delivery_order_value.Discount + delivery_order_value.ServiceCharge + delivery_order_value.DeliveryFee)
              , 0)
            FROM (
              SELECT 
                tx_merchant_delivery_order.DeliveryOrderID,
                DATE_ADD(tx_merchant_delivery_order.FinishDate, INTERVAL 5 DAY) AS DueDate,
                ANY_VALUE(tx_merchant_order.DistributorID) AS DistributorID,
                SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price) AS SubTotal,
                IFNULL(tx_merchant_delivery_order.Discount, 0) AS Discount,
                IFNULL(tx_merchant_delivery_order.ServiceCharge, 0) AS ServiceCharge,
                IFNULL(tx_merchant_delivery_order.DeliveryFee, 0) AS DeliveryFee
              FROM tx_merchant_delivery_order_detail
              JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                AND tx_merchant_delivery_order.StatusDO = 'S025'
              JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
                AND tx_merchant_order.PaymentMethodID = 14
              LEFT JOIN tx_merchant_delivery_order_log ON tx_merchant_delivery_order_log.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
                AND tx_merchant_delivery_order_log.StatusDO = 'S024'
              WHERE tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
              GROUP BY tx_merchant_delivery_order.DeliveryOrderID
            ) AS delivery_order_value
            WHERE delivery_order_value.DistributorID = a.DistributorID
            AND DATE(delivery_order_value.DueDate) <= b.DateSummary
          ) AS BillTarget,
          (
            SELECT IFNULL(SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.PurchasePrice), 0)
            FROM ms_stock_product_log
            JOIN ms_stock_product ON ms_stock_product.StockProductID = ms_stock_product_log.StockProductID
            WHERE ms_stock_product.DistributorID = a.DistributorID
            AND DATE(ms_stock_product_log.CreatedDate) <= b.DateSummary
          ) AS EndingInventory
        FROM 
          (
            SELECT DISTINCT ms_distributor.DistributorName, ms_distributor.DistributorID FROM tx_merchant_order
            JOIN ms_distributor ON ms_distributor.DistributorID = tx_merchant_order.DistributorID
            WHERE tx_merchant_order.DistributorID = '$distributorID'
          ) a
        CROSS JOIN 
          (
            select DateSummary from (
              select @maxDate - interval (a.a+(10*b.a)+(100*c.a)+(1000*d.a)) day DateSummary from
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) a, /*10 day range*/
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) b, /*100 day range*/
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) c, /*1000 day range*/
              (select 0 as a union all select 1 union all select 2 union all select 3
              union all select 4 union all select 5 union all select 6 union all
              select 7 union all select 8 union all select 9) d, /*10000 day range*/
              (select @minDate := '$startDate', @maxDate := '$endDate') e
            ) f
            where DateSummary between @minDate and @maxDate
          ) b
        ORDER BY b.DateSummary, a.DistributorName DESC
    ");

    return $sql;
  }

  public function summaryReport($startDate, $endDate, $distributorID, $salesCode)
  {
    // Summary Purchase Order
    $sqlMainPO = DB::table('tx_merchant_order as tmo')
      ->select('tmo.StockOrderID', 'tmo.CreatedDate', 'tmo.MerchantID', 'tmo.NettPrice')
      ->whereRaw("DATE(tmo.CreatedDate) >= '$startDate'")
      ->whereRaw("DATE(tmo.CreatedDate) <= '$endDate'")
      ->whereRaw("tmo.StatusOrderID IN ('S009', 'S010', 'S023')");

    if ($distributorID != null) {
      $distributorIn = "'" . implode("', '", $distributorID) . "'";
      $sqlMainPO->whereRaw("tmo.DistributorID IN ($distributorIn)");
    }

    if ($salesCode != null) {
      $salesCodeIn = "'" . implode("', '", $salesCode) . "'";
      $sqlMainPO->whereRaw("tmo.SalesCode IN ($salesCodeIn)");
    }

    $sqlProductPO = (clone $sqlMainPO)
      ->join('tx_merchant_order_detail as tmod', 'tmod.StockOrderID', 'tmo.StockOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tmod.ProductID')
      ->select(
        'tmo.StockOrderID',
        'tmo.DistributorID',
        'tmod.ProductID',
        'tmod.PromisedQuantity',
        'tmod.Nett',
        'ms_product.Price',
        DB::raw("(
            SELECT PurchasePrice
            FROM ms_stock_product
            WHERE DistributorID = tmo.DistributorID
            AND ProductID = tmod.ProductID
            AND Qty > 0
            ORDER BY LevelType, CreatedDate
            LIMIT 1
        ) AS PurchasePrice")
      );

    $marginPO = $sqlProductPO->get()->toArray();

    $valueMarginPO = 0;
    foreach ($marginPO as $key => $value) {
      if ($value->PurchasePrice != null) {
        $valueMarginPO += ($value->Nett - $value->PurchasePrice) * $value->PromisedQuantity;
      } else {
        $valueMarginPO += ($value->Nett - $value->Price) * $value->PromisedQuantity;
      }
    }

    $sqlMainPO = $sqlMainPO->toSql();

    $sqlPO = DB::table(DB::raw("($sqlMainPO) as SummaryPO"))
      ->selectRaw("
        ( 
            SELECT SUM(SummaryPO.NettPrice)
        ) as TotalValuePO,
        (
            SELECT COUNT(SummaryPO.StockOrderID)
        ) as CountTotalPO,
        (
            SELECT COUNT(DISTINCT SummaryPO.MerchantID)
        ) as CountMerchantPO,
        (
            SELECT $valueMarginPO
        ) as ValueMarginEstimasi,
        (
            SELECT ROUND($valueMarginPO / SUM(SummaryPO.NettPrice) * 100, 2)
        ) as PercentMarginEstimasi
    ");

    $dataPO = $sqlPO->first();

    // Summary Delivery Order
    $sqlMainDO = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order as tmo', 'tmo.StockOrderID', 'tmdo.StockOrderID')
      ->select('tmdo.DeliveryOrderID', 'tmo.MerchantID', 'tmdo.Discount')
      ->whereRaw("DATE(tmdo.CreatedDate) >= '$startDate'")
      ->whereRaw("DATE(tmdo.CreatedDate) <= '$endDate'")
      ->whereRaw("tmdo.StatusDO IN ('S024', 'S025')");

    if ($distributorID != null) {
      $distributorIn = "'" . implode("', '", $distributorID) . "'";
      $sqlMainDO->whereRaw("tmo.DistributorID IN ($distributorIn)");
    }

    if ($salesCode != null) {
      $salesCodeIn = "'" . implode("', '", $salesCode) . "'";
      $sqlMainDO->whereRaw("tmo.SalesCode IN ($salesCodeIn)");
    }

    $sqlProductDO = (clone $sqlMainDO)
      ->join('tx_merchant_delivery_order_detail as tmdod', 'tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID')
      ->select(
        'tmdo.DeliveryOrderID',
        'tmdod.ProductID',
        'tmdod.Qty',
        'tmdod.Price',
        DB::raw("(
            SELECT PurchasePrice
            FROM ms_stock_product_log
            WHERE DeliveryOrderDetailID = tmdod.DeliveryOrderDetailID
            LIMIT 1
        ) as PurchasePrice")
      );

    $productDO = $sqlProductDO->get()->toArray();

    $valueDO = 0;
    $valueMarginDO = 0;
    foreach ($productDO as $key => $value) {
      $valueDO += $value->Price * $value->Qty;
      $valueMarginDO += ($value->Price - $value->PurchasePrice) * $value->Qty;
    }

    $sqlMainDO = $sqlMainDO->toSql();

    $sqlDO = DB::table(DB::raw("($sqlMainDO) as SummaryDO"))
      ->selectRaw("
        (
            SELECT $valueDO - SUM(SummaryDO.Discount)
        ) as TotalValueDO,
        (
            SELECT COUNT(SummaryDO.DeliveryOrderID)
        ) as CountTotalDO,
        (
            SELECT COUNT(DISTINCT SummaryDO.MerchantID)
        ) as CountMerchantDO,
        (
            SELECT $valueMarginDO
        ) as ValueMarginReal,
        (
            SELECT ROUND($valueMarginDO / ($valueDO - SUM(SummaryDO.Discount)) * 100, 2)
        ) as PercentMarginReal
    ");

    $dataDO = $sqlDO->first();

    $data = new stdClass;
    $data->PO = $dataPO;
    $data->DO = $dataDO;

    return $data;
  }
}
