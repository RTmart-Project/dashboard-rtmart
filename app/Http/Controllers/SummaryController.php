<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function getSummary()
    {
        $filterStartDate = null;
        $filterEndDate = null;

        $day = date('d');
        $month = date('m');
        $year = date('Y');

        if ($filterStartDate != null && $filterEndDate != null) {
            $startDate = $filterStartDate;
            $endDate = $filterEndDate;
        } else {
            $startDate = $year . '-' . $month . '-01';
            $endDate = $year . '-' . $month . '-' . $day;
        }

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
                    AND tx_merchant_delivery_order.PaymentDate BETWEEN '$startDate' AND b.DateSummary
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
                    AND DATE(delivery_order_value.DueDate) BETWEEN '$startDate' AND b.DateSummary
                ) AS BillTarget,
                (
                    SELECT SUM(ms_stock_product_log.QtyAction * ms_stock_product_log.PurchasePrice)
                    FROM ms_stock_product_log
                    JOIN ms_stock_product ON ms_stock_product.StockProductID = ms_stock_product_log.StockProductID
                    WHERE ms_stock_product.DistributorID = a.DistributorID
                    AND DATE(ms_stock_product_log.CreatedDate) <= b.DateSummary
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
            ORDER BY b.DateSummary, a.DistributorName
        ");

        dd($sql);
    }
}
