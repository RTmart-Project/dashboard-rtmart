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

  public function queryPO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = DB::table('tx_merchant_order as tmo')
      ->join('ms_merchant_account as mma', 'mma.MerchantID', 'tmo.MerchantID')
      ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmo.StatusOrderID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->whereIn('tmo.StatusOrderID', ['S009', 'S010', 'S023'])
      ->whereDate('tmo.CreatedDate', '>=', $startDate)
      ->whereDate('tmo.CreatedDate', '<=', $endDate);

    if ($distributorID != null) {
      $sql->whereIn('tmo.DistributorID', explode(",", $distributorID));
    }
    if ($salesCode != null) {
      $sql->whereIn('tmo.SalesCode', explode(",", $salesCode));
    }

    return $sql;
  }

  public function totalValuePO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = $this->queryPO($startDate, $endDate, $distributorID, $salesCode)
      ->join('tx_merchant_order_detail as tmod', 'tmo.StockOrderID', 'tmod.StockOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tmod.ProductID')
      ->select('tmo.StockOrderID', 'tmo.CreatedDate', 'tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', 'ms_distributor.DistributorName', 'ms_payment_method.PaymentMethodName', 'tmo.StatusOrderID', 'ms_status_order.StatusOrder', 'tmo.TotalPrice', 'tmo.DiscountPrice', 'tmo.DiscountVoucher', 'tmo.ServiceChargeNett', 'tmo.DeliveryFee', DB::raw("(tmo.NettPrice + tmo.ServiceChargeNett + tmo.DeliveryFee) as GrandTotal"), DB::raw("CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) as Sales"), 'tmod.ProductID', 'ms_product.ProductName', 'tmod.PromisedQuantity', 'tmod.Nett', DB::raw("(tmod.PromisedQuantity * tmod.Nett) as SubTotalProduct"));

    return $sql;
  }

  public function countPO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = $this->queryPO($startDate, $endDate, $distributorID, $salesCode)
      ->select('tmo.StockOrderID', 'tmo.CreatedDate', 'tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', 'ms_distributor.DistributorName', 'ms_payment_method.PaymentMethodName', 'tmo.StatusOrderID', 'ms_status_order.StatusOrder', 'tmo.TotalPrice', 'tmo.DiscountPrice', 'tmo.DiscountVoucher', 'tmo.ServiceChargeNett', 'tmo.DeliveryFee', DB::raw("(tmo.NettPrice + tmo.ServiceChargeNett + tmo.DeliveryFee) as GrandTotal"), DB::raw("CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) as Sales"));

    return $sql;
  }

  public function countMerchantPO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = $this->queryPO($startDate, $endDate, $distributorID, $salesCode)
      ->distinct('tmo.MerchantID')
      ->select('tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', 'ms_distributor.DistributorName', DB::raw("CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) as Sales"));

    return $sql;
  }

  public function queryDO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order as tmo', 'tmo.StockOrderID', 'tmdo.StockOrderID')
      ->join('ms_merchant_account as mma', 'mma.MerchantID', 'tmo.MerchantID')
      ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->whereRaw("DATE(tmdo.CreatedDate) >= '$startDate'")
      ->whereRaw("DATE(tmdo.CreatedDate) <= '$endDate'")
      ->whereRaw("tmdo.StatusDO IN ('S024', 'S025')");

    if ($distributorID != null) {
      $sql->whereIn('tmo.DistributorID', explode(",", $distributorID));
    }
    if ($salesCode != null) {
      $sql->whereIn('tmo.SalesCode', explode(",", $salesCode));
    }

    return $sql;
  }

  public function totalValueDO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = $this->queryDO($startDate, $endDate, $distributorID, $salesCode)
      ->join('tx_merchant_delivery_order_detail as tmdod', 'tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tmdod.ProductID')
      ->leftJoin('tx_merchant_expedition_detail as tmed', function ($join) {
        $join->on('tmed.DeliveryOrderDetailID', 'tmdod.DeliveryOrderDetailID');
        $join->whereIn('tmed.StatusExpeditionDetail', ['S030', 'S031']);
      })
      ->select('tmdo.DeliveryOrderID', 'tmdo.StatusDO', 'ms_status_order.StatusOrder', 'tmdo.StockOrderID', 'tmed.MerchantExpeditionID', 'tmdo.CreatedDate', 'mma.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', 'ms_distributor.DistributorName', 'ms_payment_method.PaymentMethodName', 'tmdod.ProductID', 'ms_product.ProductName', 'tmdod.Qty', 'tmdod.Price', DB::raw("(tmdod.Qty * tmdod.Price) as ValueProduct"), 'tmdo.Discount', 'tmdo.ServiceCharge', 'tmdo.DeliveryFee', DB::raw("CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) as Sales"))->get();

    foreach ($sql as $key => $value) {
      $subTotal = 0;
      $detailDO = DB::table('tx_merchant_delivery_order_detail')
        ->where('DeliveryOrderID', $value->DeliveryOrderID)
        ->select('Qty', 'Price')
        ->get();

      foreach ($detailDO as $key => $detail) {
        $subTotal += $detail->Qty * $detail->Price;
      }
      $value->SubTotal = $subTotal;
    }

    return $sql;
  }

  public function countDO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = $this->queryDO($startDate, $endDate, $distributorID, $salesCode)
      ->join('tx_merchant_delivery_order_detail as tmdod', 'tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tmdod.ProductID')
      ->leftJoin('tx_merchant_expedition_detail as tmed', function ($join) {
        $join->on('tmed.DeliveryOrderDetailID', 'tmdod.DeliveryOrderDetailID');
        $join->whereIn('tmed.StatusExpeditionDetail', ['S030', 'S031']);
      })
      ->selectRaw("
        tmdo.DeliveryOrderID,
        tmdo.StatusDO, 
        ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder,
        tmdo.StockOrderID,
        ANY_VALUE(tmed.MerchantExpeditionID) AS MerchantExpeditionID, 
        tmdo.CreatedDate,
        ANY_VALUE(mma.MerchantID) AS MerchantID,
        ANY_VALUE(mma.StoreName) AS StoreName,
        ANY_VALUE(mma.OwnerFullName) AS OwnerFullName,
        ANY_VALUE(mma.PhoneNumber) AS PhoneNumber,
        ANY_VALUE(mma.StoreAddress) AS StoreAddress,
        ANY_VALUE(mma.Partner) AS Partner,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(ms_payment_method.PaymentMethodName) AS PaymentMethodName,
        tmdo.Discount,
        tmdo.ServiceCharge,
        tmdo.DeliveryFee, 
        CONCAT(ANY_VALUE(tmo.SalesCode), ' - ', ANY_VALUE(ms_sales.SalesName)) as Sales
      ")
      ->groupBy('tmdo.DeliveryOrderID')
      ->get();

    foreach ($sql as $key => $value) {
      $subTotal = 0;
      $detailDO = DB::table('tx_merchant_delivery_order_detail')
        ->where('DeliveryOrderID', $value->DeliveryOrderID)
        ->select('Qty', 'Price')
        ->get();

      foreach ($detailDO as $key => $detail) {
        $subTotal += $detail->Qty * $detail->Price;
      }
      $value->SubTotal = $subTotal;
    }

    return $sql;
  }

  public function countMerchantDO($startDate, $endDate, $distributorID, $salesCode)
  {
    $sql = $this->queryDO($startDate, $endDate, $distributorID, $salesCode)
      ->select('tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', DB::raw("ANY_VALUE(ms_distributor.DistributorName) AS DistributorName"), DB::raw("CONCAT(ANY_VALUE(tmo.SalesCode), ' - ', ANY_VALUE(ms_sales.SalesName)) as Sales"))
      ->groupBy('tmo.MerchantID');

    return $sql;
  }
}
