<?php

namespace App\Services;

use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SummaryService
{
  public function summaryGrandTotal($startDate, $endDate)
  {
    $sql = DB::select("
        SELECT DISTINCT
          'GrandTotal',
          b.DateSummary,
          (
            SELECT IFNULL(SUM(tx_merchant_order.TotalPrice), 0) 
            FROM tx_merchant_order
            JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
	            AND ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            WHERE tx_merchant_order.StatusOrderID != 'S011'
            AND DATE(tx_merchant_order.CreatedDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS PurchaseOrderExcludeBatal,
          (
            SELECT IFNULL(SUM(tx_merchant_order.TotalPrice), 0) 
            FROM tx_merchant_order
            JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
	            AND ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            WHERE tx_merchant_order.StatusOrderID = 'S023'
            AND DATE(tx_merchant_order.CreatedDate) BETWEEN '$startDate' AND b.DateSummary
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
            SELECT IFNULL(SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price), 0)
            FROM tx_merchant_delivery_order
            JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
            JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
              AND ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            JOIN tx_merchant_delivery_order_detail ON tx_merchant_delivery_order_detail.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
              AND tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
            WHERE tx_merchant_delivery_order.StatusDO = 'S025'
              AND DATE(tx_merchant_delivery_order.CreatedDate) BETWEEN '$startDate' AND b.DateSummary
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
            SELECT IFNULL(SUM(tx_merchant_order.TotalPrice), 0) 
            FROM tx_merchant_order
            JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
	            AND ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            WHERE tx_merchant_order.DistributorID = a.DistributorID AND tx_merchant_order.StatusOrderID != 'S011'
              AND DATE(tx_merchant_order.CreatedDate) BETWEEN '$startDate' AND b.DateSummary
          ) AS PurchaseOrderExcludeBatal,
          (
            SELECT IFNULL(SUM(tx_merchant_order.TotalPrice), 0) 
            FROM tx_merchant_order
            JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
	            AND ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            WHERE tx_merchant_order.DistributorID = a.DistributorID AND tx_merchant_order.StatusOrderID = 'S023'
              AND DATE(tx_merchant_order.CreatedDate) BETWEEN '$startDate' AND b.DateSummary
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
            SELECT IFNULL(SUM(tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price), 0)
            FROM tx_merchant_delivery_order
            JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
            JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
              AND ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            JOIN tx_merchant_delivery_order_detail ON tx_merchant_delivery_order_detail.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
              AND tx_merchant_delivery_order_detail.StatusExpedition = 'S031'
            WHERE tx_merchant_delivery_order.StatusDO = 'S025'
              AND tx_merchant_order.DistributorID = a.DistributorID
              AND DATE(tx_merchant_delivery_order.CreatedDate) BETWEEN '$startDate' AND b.DateSummary
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

  public function summaryReport($startDate, $endDate, $distributorID, $salesCode, $typePO, $partner)
  {
    // Summary Purchase Order
    $sqlMainPO = DB::table('tx_merchant_order as tmo')
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tmo.MerchantID');
        $join->whereRaw("ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)");
      })
      ->leftJoin('ms_merchant_partner', 'ms_merchant_partner.MerchantID', 'tmo.MerchantID')
      ->select('tmo.StockOrderID', 'tmo.MerchantID', 'tmo.TotalPrice', 'tmo.NettPrice', 'tmo.DiscountVoucher')
      ->distinct('tmo.StockOrderID')
      ->whereRaw("DATE(tmo.CreatedDate) >= '$startDate'")
      ->whereRaw("DATE(tmo.CreatedDate) <= '$endDate'")
      ->whereRaw("tmo.StatusOrderID IN ('S009', 'S010', 'S023')");

    $filterDistributor = "";
    if ($distributorID != null) {
      $distributorIn = "'" . implode("', '", $distributorID) . "'";
      $sqlMainPO->whereRaw("tmo.DistributorID IN ($distributorIn)");
      $filterDistributor = "AND tx_merchant_order.DistributorID IN ($distributorIn)";
    }

    $filterSales = "";
    if ($salesCode != null) {
      $salesCodeIn = "'" . implode("', '", $salesCode) . "'";
      $sqlMainPO->whereRaw("tmo.SalesCode IN ($salesCodeIn)");
      $filterSales = "AND tx_merchant_order.SalesCode IN ($salesCodeIn)";
    }

    $filterTypePO = "";
    if ($typePO != null) {
      $typePOin = "'" . implode("', '", $typePO) . "'";
      $sqlMainPO->whereRaw("tmo.Type IN ($typePOin)");
      $filterTypePO = "AND tx_merchant_order.Type IN ($typePOin)";
    }

    $filterPartner = "";
    if ($partner != null) {
      $partnerIn = "'" . implode("', '", $partner) . "'";
      $sqlMainPO->whereRaw("ms_merchant_partner.PartnerID IN ($partnerIn)");
      $filterPartner = "AND ms_merchant_partner.PartnerID IN ($partnerIn)";
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
              AND ConditionStock = 'GOOD STOCK'
              AND DATE(CreatedDate) >= DATE(NOW() - INTERVAL 7 DAY)
            ORDER BY LevelType, CreatedDate
            LIMIT 1
        ) AS PurchasePrice")
      )
      ->distinct('tmo.StockOrderID');

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
          SELECT IFNULL(FLOOR(SUM(DISTINCT tx_merchant_order.TotalPrice + CONV(SUBSTRING(MD5(CONCAT(tx_merchant_order.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
          FROM tx_merchant_order
          JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
          LEFT JOIN ms_merchant_partner ON ms_merchant_partner.MerchantID = tx_merchant_order.MerchantID
          WHERE ms_merchant_account.IsTesting = 0
            AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            AND DATE(tx_merchant_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_order.CreatedDate) <= '$endDate'
            $filterDistributor
            $filterSales
            $filterTypePO
            $filterPartner
        ) as TotalValuePOallStatus,
        (
          SELECT COUNT(DISTINCT tx_merchant_order.MerchantID)
          FROM tx_merchant_order
          JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
          LEFT JOIN ms_merchant_partner ON ms_merchant_partner.MerchantID = tx_merchant_order.MerchantID
          WHERE ms_merchant_account.IsTesting = 0
            AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            AND DATE(tx_merchant_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_order.CreatedDate) <= '$endDate'
            $filterDistributor
            $filterSales
            $filterTypePO
            $filterPartner
        ) as CountMerchantPOallStatus,
        ( 
          SELECT IFNULL(FLOOR(SUM(DISTINCT tx_merchant_order.TotalPrice + CONV(SUBSTRING(MD5(CONCAT(tx_merchant_order.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
          FROM tx_merchant_order
          JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
          LEFT JOIN ms_merchant_partner ON ms_merchant_partner.MerchantID = tx_merchant_order.MerchantID
          WHERE ms_merchant_account.IsTesting = 0
            AND tx_merchant_order.StatusOrderID = 'S011'
            AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            AND DATE(tx_merchant_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_order.CreatedDate) <= '$endDate'
            $filterDistributor
            $filterSales
            $filterTypePO
            $filterPartner
        ) as TotalValuePOcancelled,
        (
          SELECT COUNT(DISTINCT tx_merchant_order.MerchantID)
          FROM tx_merchant_order
          JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
          LEFT JOIN ms_merchant_partner ON ms_merchant_partner.MerchantID = tx_merchant_order.MerchantID
          WHERE ms_merchant_account.IsTesting = 0
            AND tx_merchant_order.StatusOrderID = 'S011'
            AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            AND DATE(tx_merchant_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_order.CreatedDate) <= '$endDate'
            $filterDistributor
            $filterSales
            $filterTypePO
            $filterPartner
        ) as CountMerchantPOcancelled,
        ( 
            SELECT IFNULL(FLOOR(SUM(DISTINCT SummaryPO.TotalPrice + CONV(SUBSTRING(MD5(CONCAT(SummaryPO.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
        ) as TotalValuePO,
        (
            SELECT COUNT(DISTINCT SummaryPO.StockOrderID)
        ) as CountTotalPO,
        (
            SELECT COUNT(DISTINCT SummaryPO.MerchantID)
        ) as CountMerchantPO,
        (
            SELECT $valueMarginPO
        ) as ValueMargin,
        ( 
            SELECT IFNULL(FLOOR(SUM(DISTINCT SummaryPO.DiscountVoucher + CONV(SUBSTRING(MD5(CONCAT(SummaryPO.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
        ) as VoucherPO,
        (
            SELECT $valueMarginPO - IFNULL(FLOOR(SUM(DISTINCT SummaryPO.DiscountVoucher + CONV(SUBSTRING(MD5(CONCAT(SummaryPO.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
        ) as ValueMarginEstimasi,
        (
            SELECT ROUND($valueMarginPO / IFNULL(FLOOR(SUM(DISTINCT SummaryPO.TotalPrice + CONV(SUBSTRING(MD5(CONCAT(SummaryPO.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0) * 100, 2)
        ) as PercentMarginEstimasiBeforeDisc,
        (
            SELECT 
              ROUND(($valueMarginPO - IFNULL(FLOOR(SUM(DISTINCT SummaryPO.DiscountVoucher + CONV(SUBSTRING(MD5(CONCAT(SummaryPO.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)) / IFNULL(FLOOR(SUM(DISTINCT SummaryPO.NettPrice + CONV(SUBSTRING(MD5(CONCAT(SummaryPO.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0) * 100, 2)
        ) as PercentMarginEstimasi
    ");

    $dataPO = $sqlPO->first();

    // Summary Delivery Order
    $sqlMainDO = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order as tmo', 'tmo.StockOrderID', 'tmdo.StockOrderID')
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tmo.MerchantID');
        $join->whereRaw("ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)");
      })
      ->leftJoin('ms_merchant_partner', 'ms_merchant_partner.MerchantID', 'tmo.MerchantID')
      ->select('tmdo.DeliveryOrderID', 'tmo.MerchantID', 'tmdo.Discount')
      ->whereRaw("DATE(tmdo.CreatedDate) >= '$startDate'")
      ->whereRaw("DATE(tmdo.CreatedDate) <= '$endDate'")
      ->whereRaw("tmdo.StatusDO IN ('S025')");

    if ($distributorID != null) {
      $distributorIn = "'" . implode("', '", $distributorID) . "'";
      $sqlMainDO->whereRaw("tmo.DistributorID IN ($distributorIn)");
    }

    if ($salesCode != null) {
      $salesCodeIn = "'" . implode("', '", $salesCode) . "'";
      $sqlMainDO->whereRaw("tmo.SalesCode IN ($salesCodeIn)");
    }

    if ($typePO != null) {
      $typePOin = "'" . implode("', '", $typePO) . "'";
      $sqlMainDO->whereRaw("tmo.Type IN ($typePOin)");
    }

    if ($partner != null) {
      $partnerIn = "'" . implode("', '", $partner) . "'";
      $sqlMainDO->whereRaw("ms_merchant_partner.PartnerID IN ($partnerIn)");
    }

    $sqlMargin = (clone $sqlMainDO)
      ->join('tx_merchant_delivery_order_detail as tmdod', function ($join) {
        $join->on('tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tmdod.StatusExpedition', 'S031');
      })
      ->leftJoin('ms_stock_product_log', 'ms_stock_product_log.DeliveryOrderDetailID', 'tmdod.DeliveryOrderDetailID')
      ->select(
        DB::raw("
          ABS(IFNULL(FLOOR(SUM(DISTINCT ((ms_stock_product_log.SellingPrice - ms_stock_product_log.PurchasePrice) * ms_stock_product_log.QtyAction) + CONV(SUBSTRING(MD5(CONCAT(ms_stock_product_log.DeliveryOrderDetailID)), 1, 8), 16, 10)/1000000000000000)), 0)) AS GrossMargin
        ")
      )
      ->first();

    $sqlTotalValuePObyDO = (clone $sqlMainDO)
      ->join('tx_merchant_delivery_order_detail as tmdod', function ($join) {
        $join->on('tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tmdod.StatusExpedition', 'S031');
      })
      ->select(
        DB::raw("IFNULL(FLOOR(SUM(DISTINCT tmo.TotalPrice + CONV(SUBSTRING(MD5(CONCAT(tmo.StockOrderID)), 1, 8), 16, 10)/1000000000000000)), 0) AS TotalValuePO"),
        DB::raw("IFNULL(FLOOR(SUM(DISTINCT (tmdod.Qty * tmdod.Price) + CONV(SUBSTRING(MD5(CONCAT(tmdod.DeliveryOrderDetailID)), 1, 8), 16, 10)/1000000000000000)), 0) AS TotalValueDO"),
        DB::raw("COUNT(DISTINCT tmo.MerchantID) AS CountMerchantDO")
      )
      ->first();

    $sqlMainDO = $sqlMainDO->toSql();

    $sqlDO = DB::table(DB::raw("($sqlMainDO) as SummaryDO"))
      ->selectRaw("
        (
          SELECT $sqlTotalValuePObyDO->TotalValuePO
        ) as TotalValuePObyDO,
        (
          SELECT $sqlTotalValuePObyDO->CountMerchantDO
        ) as cnt,
        (
          SELECT $sqlTotalValuePObyDO->TotalValueDO
        ) as TotalValueDO,
        (
          SELECT IFNULL(FLOOR(SUM(DISTINCT (tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price) + CONV(SUBSTRING(MD5(CONCAT(tx_merchant_delivery_order_detail.DeliveryOrderDetailID)), 1, 8), 16, 10)/1000000000000000)), 0)
          FROM tx_merchant_delivery_order
          JOIN tx_merchant_delivery_order_detail ON tx_merchant_delivery_order_detail.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
            AND tx_merchant_delivery_order_detail.StatusExpedition = 'S037'
          JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
          LEFT JOIN ms_merchant_partner ON ms_merchant_partner.MerchantID = tx_merchant_order.MerchantID
          JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
            AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            AND ms_merchant_account.IsTesting = 0
          WHERE DATE(tx_merchant_delivery_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_delivery_order.CreatedDate) <= '$endDate'
            $filterDistributor
            $filterSales
            $filterTypePO
            $filterPartner
        ) as TotalValueDOcancelled,
        (
          SELECT COUNT(DISTINCT tx_merchant_order.MerchantID)
          FROM tx_merchant_delivery_order
          JOIN tx_merchant_delivery_order_detail ON tx_merchant_delivery_order_detail.DeliveryOrderID = tx_merchant_delivery_order.DeliveryOrderID
            AND tx_merchant_delivery_order_detail.StatusExpedition = 'S037'
          JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
          LEFT JOIN ms_merchant_partner ON ms_merchant_partner.MerchantID = tx_merchant_order.MerchantID
          JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
            AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)
            AND ms_merchant_account.IsTesting = 0
          WHERE DATE(tx_merchant_delivery_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_delivery_order.CreatedDate) <= '$endDate'
            $filterDistributor
            $filterSales
            $filterTypePO
            $filterPartner
        ) as CountMerchantDOcancelled,
        (
            SELECT COUNT(SummaryDO.DeliveryOrderID)
        ) as CountTotalDO,
        (
            SELECT COUNT(DISTINCT SummaryDO.MerchantID)
        ) as CountMerchantDO,
        (
            SELECT $sqlMargin->GrossMargin
        ) as ValueMargin,
        (
            SELECT IFNULL(FLOOR(SUM(DISTINCT SummaryDO.Discount + CONV(SUBSTRING(MD5(CONCAT(SummaryDO.DeliveryOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
        ) as VoucherDO,
        (
            SELECT $sqlMargin->GrossMargin - IFNULL(FLOOR(SUM(DISTINCT SummaryDO.Discount + CONV(SUBSTRING(MD5(CONCAT(SummaryDO.DeliveryOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)
        ) as ValueMarginReal,
        (
            SELECT ROUND($sqlMargin->GrossMargin / $sqlTotalValuePObyDO->TotalValueDO * 100, 2)
        ) as PercentMarginRealBeforeDisc,
        (
            SELECT ROUND(($sqlMargin->GrossMargin - IFNULL(FLOOR(SUM(DISTINCT SummaryDO.Discount + CONV(SUBSTRING(MD5(CONCAT(SummaryDO.DeliveryOrderID)), 1, 8), 16, 10)/1000000000000000)), 0)) / ($sqlTotalValuePObyDO->TotalValueDO - SUM(SummaryDO.Discount)) * 100, 2)
        ) as PercentMarginReal
    ");

    $dataDO = $sqlDO->first();

    $data = new stdClass;
    $data->PO = $dataPO;
    $data->DO = $dataDO;

    return $data;
  }

  public function queryPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO)
  {
    $sql = DB::table('tx_merchant_order as tmo')
      ->join('ms_merchant_account as mma', function ($join) {
        $join->on('mma.MerchantID', 'tmo.MerchantID');
        $join->whereRaw("mma.IsTesting = 0 AND (mma.Partner != 'TRADING' OR mma.Partner IS NULL)");
      })
      ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmo.StatusOrderID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->whereDate('tmo.CreatedDate', '>=', $startDate)
      ->whereDate('tmo.CreatedDate', '<=', $endDate);

    if ($distributorID != null) {
      $sql->whereIn('tmo.DistributorID', explode(",", $distributorID));
    }
    if ($salesCode != null) {
      $sql->whereIn('tmo.SalesCode', explode(",", $salesCode));
    }
    if ($typePO != null) {
      $sql->whereIn('tmo.Type', explode(",", $typePO));
    }
    if ($type === "totalValuePO") {
      $sql->whereIn('tmo.StatusOrderID', ['S009', 'S010', 'S023']);
    }
    if ($type === "totalValuePOcancelled") {
      $sql->whereIn('tmo.StatusOrderID', ['S011']);
    }

    return $sql;
  }

  public function totalValuePO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO, $partner)
  {
    $sql = $this->queryPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO)
      ->join('tx_merchant_order_detail as tmod', 'tmo.StockOrderID', 'tmod.StockOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tmod.ProductID')
      ->leftJoin('ms_merchant_partner', 'ms_merchant_partner.MerchantID', 'tmo.MerchantID')
      ->leftJoin('ms_partner', 'ms_partner.PartnerID', 'ms_merchant_partner.PartnerID')
      ->selectRaw("
        tmo.StockOrderID,
        ANY_VALUE(tmo.CreatedDate) AS CreatedDate,
        ANY_VALUE(tmo.MerchantID) AS MerchantID,
        ANY_VALUE(mma.StoreName) AS StoreName,
        ANY_VALUE(mma.OwnerFullName) AS OwnerFullName,
        ANY_VALUE(mma.PhoneNumber) AS PhoneNumber,
        ANY_VALUE(mma.StoreAddress) AS StoreAddress,
        GROUP_CONCAT(ms_partner.Name SEPARATOR ', ') AS Partners,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(ms_payment_method.PaymentMethodName) AS PaymentMethodName,
        ANY_VALUE(tmo.StatusOrderID) AS StatusOrderID,
        ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder,
        ANY_VALUE(tmo.TotalPrice) AS TotalPrice,
        ANY_VALUE(tmo.NettPrice) AS NettPrice,
        ANY_VALUE(tmo.DiscountPrice) AS DiscountPrice,
        ANY_VALUE(tmo.DiscountVoucher) AS DiscountVoucher,
        ANY_VALUE(tmo.ServiceChargeNett) AS ServiceChargeNett,
        ANY_VALUE(tmo.DeliveryFee) AS DeliveryFee,
        (ANY_VALUE(tmo.NettPrice) + ANY_VALUE(tmo.ServiceChargeNett) + ANY_VALUE(tmo.DeliveryFee)) as GrandTotal,
        CONCAT(ANY_VALUE(tmo.SalesCode), ' - ', ANY_VALUE(ms_sales.SalesName)) as Sales,
        ANY_VALUE(tmod.ProductID) AS ProductID,
        ANY_VALUE(ms_product.ProductName) AS ProductName,
        ANY_VALUE(tmod.PromisedQuantity) AS PromisedQuantity,
        ANY_VALUE(tmod.Nett) AS Nett,
        (ANY_VALUE(tmod.PromisedQuantity) * ANY_VALUE(tmod.Nett)) as SubTotalProduct,
        (
            SELECT PurchasePrice
            FROM ms_stock_product
            WHERE DistributorID = ANY_VALUE(tmo.DistributorID)
              AND ProductID = ANY_VALUE(tmod.ProductID)
              AND Qty > 0
              AND ConditionStock = 'GOOD STOCK'
              AND DATE(CreatedDate) >= DATE(NOW() - INTERVAL 7 DAY)
            ORDER BY LevelType, CreatedDate
            LIMIT 1
        ) AS PurchasePrice,
        (
            SELECT Price
            FROM ms_product
            WHERE ProductID = ANY_VALUE(tmod.ProductID)
            LIMIT 1
        ) AS PurchasePriceProduct
      ");

    if ($partner != null) {
      $arrPartner = explode(",", $partner);
      $filterPartner = "(ms_merchant_partner.PartnerID = " . implode(" OR ms_merchant_partner.PartnerID = ", $arrPartner) . ")";
      $sql->whereRaw("$filterPartner");
    }

    $data = $sql->groupBy('tmo.StockOrderID', 'tmo.MerchantID', 'tmod.ProductID');

    return $data;
  }

  public function countPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO)
  {
    $sql = $this->queryPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO)
      ->whereIn('tmo.StatusOrderID', ['S009', 'S010', 'S023'])
      ->select('tmo.StockOrderID', 'tmo.CreatedDate', 'tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', 'ms_distributor.DistributorName', 'ms_payment_method.PaymentMethodName', 'tmo.StatusOrderID', 'ms_status_order.StatusOrder', 'tmo.TotalPrice', 'tmo.DiscountPrice', 'tmo.DiscountVoucher', 'tmo.ServiceChargeNett', 'tmo.DeliveryFee', DB::raw("(tmo.NettPrice + tmo.ServiceChargeNett + tmo.DeliveryFee) as GrandTotal"), DB::raw("CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) as Sales"));

    return $sql;
  }

  public function countMerchantPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO)
  {
    $sql = $this->queryPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO)
      ->whereIn('tmo.StatusOrderID', ['S009', 'S010', 'S023'])
      ->distinct('tmo.MerchantID')
      ->select('tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', 'ms_distributor.DistributorName', DB::raw("CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) as Sales"));

    return $sql;
  }

  public function queryDO($startDate, $endDate, $distributorID, $salesCode, $typePO)
  {
    $sql = DB::table('tx_merchant_delivery_order as tmdo')
      ->join('tx_merchant_order as tmo', 'tmo.StockOrderID', 'tmdo.StockOrderID')
      ->join('ms_merchant_account as mma', function ($join) {
        $join->on('mma.MerchantID', 'tmo.MerchantID');
        $join->where('mma.IsTesting', 0);
        $join->whereRaw("(mma.Partner != 'TRADING' OR mma.Partner IS NULL)");
      })
      ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmdo.StatusDO')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->whereRaw("DATE(tmdo.CreatedDate) >= '$startDate'")
      ->whereRaw("DATE(tmdo.CreatedDate) <= '$endDate'")
      ->whereRaw("tmdo.StatusDO IN ('S025')");

    if ($distributorID != null) {
      $sql->whereIn('tmo.DistributorID', explode(",", $distributorID));
    }
    if ($salesCode != null) {
      $sql->whereIn('tmo.SalesCode', explode(",", $salesCode));
    }
    if ($typePO != null) {
      $sql->whereIn('tmo.Type', explode(",", $typePO));
    }

    return $sql;
  }

  public function totalValueDO($startDate, $endDate, $distributorID, $salesCode, $typePO, $partner)
  {
    $sql = $this->queryDO($startDate, $endDate, $distributorID, $salesCode, $typePO)
      ->join('tx_merchant_delivery_order_detail as tmdod', function ($join) {
        $join->on('tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tmdod.StatusExpedition', 'S031');
      })
      ->join('ms_stock_product_log', function ($join) {
        $join->on('ms_stock_product_log.DeliveryOrderDetailID', 'tmdod.DeliveryOrderDetailID');
        $join->where('ms_stock_product_log.ActionType', 'OUTBOUND');
      })
      ->join('ms_stock_product', 'ms_stock_product.StockProductID', 'ms_stock_product_log.StockProductID')
      ->join('ms_investor', 'ms_investor.InvestorID', 'ms_stock_product.InvestorID')
      ->join('ms_product', 'ms_product.ProductID', 'tmdod.ProductID')
      ->leftJoin('tx_merchant_expedition_detail as tmed', function ($join) {
        $join->on('tmed.DeliveryOrderDetailID', 'tmdod.DeliveryOrderDetailID');
        $join->whereIn('tmed.StatusExpeditionDetail', ['S030', 'S031']);
      })
      ->leftJoin('ms_user', 'ms_user.UserID', 'tmdo.DriverID')
      ->leftJoin('ms_merchant_partner', 'ms_merchant_partner.MerchantID', 'tmo.MerchantID')
      ->leftJoin('ms_partner', 'ms_partner.PartnerID', 'ms_merchant_partner.PartnerID')
      ->selectRaw("
        tmdo.DeliveryOrderID,
        tmdo.StatusDO,
        tmdo.IsPaid,
        ANY_VALUE(tmo.PaymentMethodID) AS PaymentMethodID,
        ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder,
        tmdo.StockOrderID,
        ANY_VALUE(tmo.CreatedDate) AS DatePO,
        ANY_VALUE(tmed.MerchantExpeditionID) AS MerchantExpeditionID,
        ANY_VALUE(ms_user.Name) AS Driver,
        ANY_VALUE(tmdo.VehicleLicensePlate) AS Nopol,
        tmdo.CreatedDate,
        ANY_VALUE(mma.MerchantID) AS MerchantID,
        ANY_VALUE(mma.StoreName) AS StoreName,
        ANY_VALUE(mma.OwnerFullName) AS OwnerFullName,
        ANY_VALUE(mma.PhoneNumber) AS PhoneNumber,
        ANY_VALUE(mma.StoreAddress) AS StoreAddress,
        GROUP_CONCAT(ms_partner.Name SEPARATOR ', ') AS Partners,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(ms_payment_method.PaymentMethodName) AS PaymentMethodName,
        ANY_VALUE(tmdod.DeliveryOrderDetailID) AS DeliveryOrderDetailID,
        ANY_VALUE(tmdod.ProductID) AS ProductID,
        ANY_VALUE(ms_product.ProductName) AS ProductName,
        ANY_VALUE(tmdod.Qty) AS Qty,
        ANY_VALUE(tmdod.Price) AS Price,
        ANY_VALUE(tmdod.Qty) * ANY_VALUE(tmdod.Price) as ValueProduct,
        ANY_VALUE(tmo.Type) AS Type,
        tmdo.Discount,
        tmdo.ServiceCharge,
        tmdo.DeliveryFee,
        CONCAT(ANY_VALUE(tmo.SalesCode), ' - ', ANY_VALUE(ms_sales.SalesName)) as Sales,
        (
          SELECT SUM(Qty * Price)
          FROM tx_merchant_delivery_order_detail
          WHERE DeliveryOrderID = tmdo.DeliveryOrderID
            AND StatusExpedition = 'S031'
        ) AS SubTotal,
        (
          SELECT SUM(Qty * Price) - tmdo.Discount
          FROM tx_merchant_delivery_order_detail
          WHERE DeliveryOrderID = tmdo.DeliveryOrderID
            AND StatusExpedition = 'S031'
        ) AS SubTotalMinVoucher,
        (
          SELECT ms_stock_product_log.PurchasePrice
          FROM ms_stock_product_log
          WHERE ms_stock_product_log.DeliveryOrderDetailID = ANY_VALUE(tmdod.DeliveryOrderDetailID)
            AND ms_stock_product_log.MerchantExpeditionDetailID = ANY_VALUE(tmed.MerchantExpeditionDetailID)
          LIMIT 1
        ) AS PurchasePrice,
        ANY_VALUE(ms_investor.InvestorName) AS InvestorName,
        ANY_VALUE(ms_stock_product.ProductLabel) AS ProductLabel
      ");

    if ($partner != null) {
      $arrPartner = explode(",", $partner);
      $filterPartner = "(ms_merchant_partner.PartnerID = " . implode(" OR ms_merchant_partner.PartnerID = ", $arrPartner) . ")";
      $sql->whereRaw("$filterPartner");
    }

    $data = $sql->groupBy('tmdod.DeliveryOrderDetailID');

    return $data;
  }

  public function countDO($startDate, $endDate, $distributorID, $salesCode, $typePO)
  {
    $sql = $this->queryDO($startDate, $endDate, $distributorID, $salesCode, $typePO)
      ->join('tx_merchant_delivery_order_detail as tmdod', function ($join) {
        $join->on('tmdod.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->where('tmdod.StatusExpedition', 'S031');
      })
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

  public function countMerchantDO($startDate, $endDate, $distributorID, $salesCode, $typePO)
  {
    $sql = $this->queryDO($startDate, $endDate, $distributorID, $salesCode, $typePO)
      ->select('tmo.MerchantID', 'mma.StoreName', 'mma.OwnerFullName', 'mma.PhoneNumber', 'mma.StoreAddress', 'mma.Partner', DB::raw("ANY_VALUE(ms_distributor.DistributorName) AS DistributorName"), DB::raw("CONCAT(ANY_VALUE(tmo.SalesCode), ' - ', ANY_VALUE(ms_sales.SalesName)) as Sales"))
      ->groupBy('tmo.MerchantID');

    return $sql;
  }

  public function dataFilter($startDate, $endDate, $distributorID, $salesCode, $typePO, $partner)
  {
    $sqlFilterDepo = DB::table('ms_distributor')
      ->whereIn('DistributorID', explode(",", $distributorID))
      ->select('DistributorName')
      ->get()->toArray();

    $arrayDepo = array_map(function ($value) {
      return $value->DistributorName;
    }, $sqlFilterDepo);

    $filterDepo = implode(" | ", $arrayDepo);

    $sqlFilterSales = DB::table('ms_sales')
      ->whereIn('SalesCode', explode(",", $salesCode))
      ->select('SalesCode', 'SalesName')
      ->get()->toArray();

    $arraySales = array_map(function ($value) {
      return $value->SalesCode . ' - ' . $value->SalesName;
    }, $sqlFilterSales);

    $filterSales = implode(" | ", $arraySales);

    $sqlFilterPartner = DB::table('ms_partner')
      ->whereIn('PartnerID', explode(",", $partner))
      ->select('Name')
      ->get()->toArray();

    $arrayPartner = array_map(function ($value) {
      return $value->Name;
    }, $sqlFilterPartner);

    $filterPartner = implode(" | ", $arrayPartner);

    $typePO = explode(',', $typePO);
    $typePO = implode(", ", $typePO);

    $dataFilter = new stdClass;
    $dataFilter->startDate = $startDate;
    $dataFilter->endDate = $endDate;
    $dataFilter->distributor = $filterDepo;
    $dataFilter->sales = $filterSales;
    $dataFilter->typePO = $typePO;
    $dataFilter->partner = $filterPartner;

    return $dataFilter;
  }

  public function dataSummaryMargin($startDate, $endDate, $typePO)
  {
    $typePOin = "";
    $filterTypePO = "";
    $depoUser = Auth::user()->Depo;
    $regionalUser = Auth::user()->Regional;

    if ($typePO != null) {
      $typePOin = "'" . implode("', '", $typePO) . "'";
      $filterTypePO = "AND tx_merchant_order.Type IN ($typePOin)";
    }

    $grandTotal = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_order as tmo', 'tmo.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tmo.MerchantID');
        $join->whereRaw("ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)");
      })
      ->join('ms_distributor', function ($join) use ($depoUser, $regionalUser) {
        $join->on('ms_distributor.DistributorID', 'tmo.DistributorID');
        if ($depoUser != 'ALL') {
          $join->where('ms_distributor.depo', $depoUser);
        }
        if ($regionalUser != NULL && $depoUser == "ALL") {
          $join->where('ms_distributor.Regional', $regionalUser);
        }
      })
      ->join('tx_merchant_delivery_order_detail', function ($join) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_detail.StatusExpedition', 'S031');
      })
      ->join('ms_stock_product_log', 'ms_stock_product_log.DeliveryOrderDetailID', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID')
      ->where('tx_merchant_delivery_order.StatusDO', 'S025')
      ->whereDate('tx_merchant_delivery_order.CreatedDate', '>=', $startDate)
      ->whereDate('tx_merchant_delivery_order.CreatedDate', '<=', $endDate)
      ->selectRaw("
        'Total' AS DistributorID,
        '<b>Total</b>' AS DistributorName,
        ABS(IFNULL(SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.PurchasePrice), 0)) AS COGS,
        ABS(IFNULL(SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.SellingPrice), 0)) AS Sales,
        (
          SELECT IFNULL(SUM(tx_merchant_delivery_order.Discount), 0)
          FROM tx_merchant_delivery_order
          JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
          WHERE tx_merchant_delivery_order.StatusDO = 'S025' 
            AND DATE(tx_merchant_delivery_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_delivery_order.CreatedDate) <= '$endDate'
            $filterTypePO
        ) as Discount
      ");

    if ($typePO != null) {
      $grandTotal->whereRaw("tmo.Type IN ($typePOin)");
    }

    $sql = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_order as tmo', 'tmo.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tmo.MerchantID');
        $join->whereRaw("ms_merchant_account.IsTesting = 0 AND (ms_merchant_account.Partner != 'TRADING' OR ms_merchant_account.Partner IS NULL)");
      })
      ->join('ms_distributor', function ($join) use ($depoUser, $regionalUser) {
        $join->on('ms_distributor.DistributorID', 'tmo.DistributorID');
        if ($depoUser != 'ALL') {
          $join->where('ms_distributor.depo', $depoUser);
        }
        if ($regionalUser != NULL && $depoUser == "ALL") {
          $join->where('ms_distributor.Regional', $regionalUser);
        }
      })
      ->join('tx_merchant_delivery_order_detail', function ($join) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_detail.StatusExpedition', 'S031');
      })
      ->join('ms_stock_product_log', 'ms_stock_product_log.DeliveryOrderDetailID', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID')
      ->where('tx_merchant_delivery_order.StatusDO', 'S025')
      ->whereDate('tx_merchant_delivery_order.CreatedDate', '>=', $startDate)
      ->whereDate('tx_merchant_delivery_order.CreatedDate', '<=', $endDate)
      ->selectRaw("
        tmo.DistributorID,
        ms_distributor.DistributorName,
        ABS(IFNULL(SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.PurchasePrice), 0)) AS COGS,
        ABS(IFNULL(SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.SellingPrice), 0)) AS Sales,
        (
          SELECT IFNULL(SUM(tx_merchant_delivery_order.Discount), 0) 
          FROM tx_merchant_delivery_order
          JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_delivery_order.StockOrderID
          WHERE tx_merchant_delivery_order.StatusDO = 'S025' 
            AND DATE(tx_merchant_delivery_order.CreatedDate) >= '$startDate'
            AND DATE(tx_merchant_delivery_order.CreatedDate) <= '$endDate'
            AND tx_merchant_order.DistributorID = tmo.DistributorID
            $filterTypePO
        ) as Discount
      ")
      ->groupBy('tmo.DistributorID')
      ->unionAll($grandTotal);

    if ($typePO != null) {
      $sql->whereRaw("tmo.Type IN ($typePOin)");
    }

    return $sql;
  }

  public function dataSummaryMerchant($startDate, $endDate, $filterBy, $distributorID, $salesCode, $marginStatus)
  {
    $depoUser = Auth::user()->Depo;

    $subSql = DB::table('tx_merchant_order')
      ->selectRaw("
                tx_merchant_order.StockOrderID,
                ANY_VALUE(tx_merchant_order.CreatedDate) AS DatePO,
                ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
                ANY_VALUE(ms_sales.SalesCode) AS SalesCode,
                ANY_VALUE(ms_sales.SalesName) AS SalesName,
                tx_merchant_delivery_order.DeliveryOrderID,
                tx_merchant_delivery_order.CreatedDate AS DateDO,
                ANY_VALUE(ms_merchant_account.MerchantID) AS MerchantID,
                ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
                ANY_VALUE(tx_merchant_order.TotalPrice) AS TotalPrice,
                ABS(SUM(IFNULL(ms_stock_product_log.SellingPrice * ms_stock_product_log.QtyAction, 0))) AS TotalDO,
                IFNULL(tx_merchant_delivery_order.Discount, 0) AS DiscountDO,
                ABS(IFNULL(SUM((ms_stock_product_log.SellingPrice - ms_stock_product_log.PurchasePrice) * ms_stock_product_log.QtyAction), 0)) AS GrossMargin,
                ABS(IFNULL(SUM((ms_stock_product_log.SellingPrice - ms_stock_product_log.PurchasePrice) * ms_stock_product_log.QtyAction), 0)) - IFNULL(tx_merchant_delivery_order.Discount, 0) AS NettMargin")

      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('ms_merchant_account', function ($join) {
        $join->on('ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID');
        $join->whereRaw("ms_merchant_account.IsTesting = 0");
      })
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
      // ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tx_merchant_order.SalesCode')
      ->leftJoin('tx_merchant_delivery_order', function ($join) {
        $join->on('tx_merchant_delivery_order.StockOrderID', 'tx_merchant_order.StockOrderID');
        $join->whereRaw("tx_merchant_delivery_order.StatusDO = 'S025'");
      })
      ->leftJoin('tx_merchant_delivery_order_detail', function ($join) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
        $join->whereRaw("tx_merchant_delivery_order_detail.StatusExpedition = 'S031'");
      })
      ->leftJoin('ms_stock_product_log', 'ms_stock_product_log.DeliveryOrderDetailID', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID')
      ->whereRaw("tx_merchant_order.StatusOrderID != 'S011'");

    if ($filterBy == "DatePO" || $filterBy == "") {
      $subSql->whereRaw("DATE(tx_merchant_order.CreatedDate) >= '$startDate'")
        ->whereRaw("DATE(tx_merchant_order.CreatedDate) <= '$endDate'");
    } else {
      $subSql->whereRaw("DATE(tx_merchant_delivery_order.CreatedDate) >= '$startDate'")
        ->whereRaw("DATE(tx_merchant_delivery_order.CreatedDate) <= '$endDate'");
    }

    if ($distributorID) {
      $stringDistributorID = "'" . implode("', '", $distributorID) . "'";
      $subSql->whereRaw("tx_merchant_order.DistributorID IN ($stringDistributorID)");
    }

    if ($salesCode) {
      $stringSalesCode = "'" . implode("', '", $salesCode) . "'";
      $subSql->whereRaw("ms_sales.SalesCode IN ($stringSalesCode)");
    }

    if ($depoUser != "ALL" && !$distributorID) {
      $subSql->whereRaw("ms_distributor.Depo = '$depoUser'");
    }

    $sql = $subSql->groupBy(['tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.DeliveryOrderID'])->toSql();

    $sqlMain = DB::table(DB::raw("($sql) AS SummaryMerchant"))
      ->selectRaw("
        SummaryMerchant.MerchantID,
        SummaryMerchant.StoreName,
        SummaryMerchant.SalesCode,
        SummaryMerchant.SalesName,
        SummaryMerchant.DistributorName,
        FLOOR(SUM(DISTINCT SummaryMerchant.TotalPrice + CONV(SUBSTRING(MD5(CONCAT(SummaryMerchant.StockOrderID)), 1, 8), 16, 10) / 1000000000000000)) AS TotalPO,
        SUM(SummaryMerchant.TotalDO) AS TotalDO,
        SUM(SummaryMerchant.DiscountDO) AS DiscountDO,
        SUM(SummaryMerchant.GrossMargin) AS GrossMargin,
        IFNULL(FORMAT(SUM(SummaryMerchant.GrossMargin) / SUM(SummaryMerchant.TotalDO) * 100, 2), 0) AS PercentGrossMargin,
        SUM(SummaryMerchant.NettMargin) AS NettMargin,
        IFNULL(FORMAT(SUM(SummaryMerchant.NettMargin) / SUM(SummaryMerchant.TotalDO) * 100, 2), 0) AS PercentNettMargin
      ")->groupBy('SummaryMerchant.MerchantID')->toSql();

    $sqlFix = DB::table(DB::raw("($sqlMain) AS FinalSummaryMerchant"))
      ->selectRaw("
        FinalSummaryMerchant.*,
        CASE 
          WHEN FinalSummaryMerchant.PercentNettMargin > 8 THEN 'High'
          WHEN FinalSummaryMerchant.PercentNettMargin < 5 THEN 'Below'
          ELSE 'Standart'
        END AS NettMarginStatus
      ");

    if ($marginStatus === "high") {
      $sqlFix->whereRaw("FinalSummaryMerchant.PercentNettMargin > 8");
    }
    if ($marginStatus === "standart") {
      $sqlFix->whereRaw("FinalSummaryMerchant.PercentNettMargin BETWEEN 5 AND 8");
    }
    if ($marginStatus === "below") {
      $sqlFix->whereRaw("FinalSummaryMerchant.PercentNettMargin < 5");
    }

    $data = $sqlFix;

    return $data;
  }
}
