<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantService
{

    public function merchantRestock()
    {
        $sqlMain = DB::table('tx_merchant_order')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', 'ms_distributor_merchant_grade.GradeID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->leftJoin('ms_merchant_assessment', function ($join) {
                $join->on('ms_merchant_assessment.MerchantID', 'tx_merchant_order.MerchantID');
                $join->whereRaw("ms_merchant_assessment.IsActive = 1");
            })
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'ms_merchant_account.ReferralCode')
            ->whereRaw('ms_merchant_account.IsTesting = 0')
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.MerchantID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'tx_merchant_order.NettPrice', 'tx_merchant_order.StatusOrderID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.PhoneNumber', 'ms_distributor.DistributorName', 'ms_status_order.StatusOrder', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.ReferralCode', 'ms_sales.SalesName', 'ms_payment_method.PaymentMethodName', 'ms_distributor_grade.Grade', 'tx_merchant_order.DistributorID', 'tx_merchant_order.PaymentMethodID', 'ms_distributor.Depo', 'ms_merchant_account.OwnerFullName', 'ms_merchant_assessment.NumberIDCard', 'ms_merchant_assessment.IsDownload', 'ms_merchant_assessment.TurnoverAverage')
            ->toSql();

        $sql = DB::table(DB::raw("($sqlMain) AS Restock"))
            ->selectRaw("
                Restock.*,
                (
                    SELECT IFNULL(SUM(tx_merchant_order_detail.Nett * (tx_merchant_order_detail.PromisedQuantity - IFNULL(DOkirim.Qty, 0))), 0)
                    FROM tx_merchant_order_detail
                    JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_order_detail.StockOrderID
                    LEFT JOIN ms_stock_product ON ms_stock_product.ProductID = tx_merchant_order_detail.ProductID
                        AND ms_stock_product.Qty > 0
                        AND ms_stock_product.ConditionStock = 'GOOD STOCK'
                        AND ms_stock_product.DistributorID = tx_merchant_order.DistributorID
                    LEFT JOIN (
                        SELECT SUM(tx_merchant_delivery_order_detail.Qty) AS Qty, tx_merchant_delivery_order_detail.ProductID, 
                        tx_merchant_delivery_order.StockOrderID
                        FROM tx_merchant_delivery_order_detail
                        JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                            AND (tx_merchant_delivery_order.StatusDO = 'S024' OR tx_merchant_delivery_order.StatusDO = 'S025')
                        WHERE (tx_merchant_delivery_order_detail.StatusExpedition = 'S030' OR tx_merchant_delivery_order_detail.StatusExpedition = 'S031')
                        GROUP BY tx_merchant_delivery_order.StockOrderID, tx_merchant_delivery_order_detail.ProductID
                    ) AS DOkirim ON DOkirim.ProductID = tx_merchant_order_detail.ProductID AND DOkirim.StockOrderID = tx_merchant_order_detail.StockOrderID
                    WHERE tx_merchant_order_detail.StockOrderID = Restock.StockOrderID
                    AND ms_stock_product.StockProductID IS NULL
                ) AS TotalPriceNotInStock,
                (
                    SELECT IFNULL(SUM((tx_merchant_order_detail.Nett - stock_log.PurchasePrice) * DOkirim.Qty), 0)
                    FROM tx_merchant_order_detail
                    LEFT JOIN (
                        SELECT SUM(tx_merchant_delivery_order_detail.Qty) AS Qty, tx_merchant_delivery_order_detail.ProductID, 
                        tx_merchant_delivery_order.StockOrderID, ANY_VALUE(tx_merchant_delivery_order_detail.DeliveryOrderDetailID) AS DeliveryOrderDetailID
                        FROM tx_merchant_delivery_order_detail
                        JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                            AND (tx_merchant_delivery_order.StatusDO = 'S024' OR tx_merchant_delivery_order.StatusDO = 'S025')
                        WHERE (tx_merchant_delivery_order_detail.StatusExpedition = 'S030' OR tx_merchant_delivery_order_detail.StatusExpedition = 'S031')
                        GROUP BY tx_merchant_delivery_order.StockOrderID, tx_merchant_delivery_order_detail.ProductID
                    ) AS DOkirim ON DOkirim.ProductID = tx_merchant_order_detail.ProductID AND DOkirim.StockOrderID = tx_merchant_order_detail.StockOrderID
                    JOIN (
                        SELECT tx_merchant_expedition_detail.DeliveryOrderDetailID, ANY_VALUE(ms_stock_product_log.PurchasePrice) AS PurchasePrice
                        FROM ms_stock_product_log
                        JOIN tx_merchant_expedition_detail ON tx_merchant_expedition_detail.MerchantExpeditionDetailID = ms_stock_product_log.MerchantExpeditionDetailID
                        GROUP BY ms_stock_product_log.MerchantExpeditionDetailID
                        HAVING MIN(ms_stock_product_log.CreatedDate)
                    ) AS stock_log ON stock_log.DeliveryOrderDetailID = DOkirim.DeliveryOrderDetailID
                    WHERE tx_merchant_order_detail.StockOrderID = Restock.StockOrderID
                ) AS MarginReal,
                (
                    SELECT IFNULL(SUM((tx_merchant_order_detail.Nett - IF(ms_stock_product.PurchasePrice = 0, tx_merchant_order_detail.Nett, ms_stock_product.PurchasePrice)) * (tx_merchant_order_detail.PromisedQuantity - IFNULL(DOkirim.Qty, 0))), 0)
                    FROM tx_merchant_order_detail
                    JOIN tx_merchant_order ON tx_merchant_order.StockOrderID = tx_merchant_order_detail.StockOrderID
                    LEFT JOIN ms_stock_product ON ms_stock_product.ProductID = tx_merchant_order_detail.ProductID
                        AND ms_stock_product.Qty > 0
                        AND ms_stock_product.ConditionStock = 'GOOD STOCK'
                        AND ms_stock_product.DistributorID = tx_merchant_order.DistributorID
                        AND ms_stock_product.CreatedDate = (
                                SELECT MAX(CreatedDate) 
                                FROM ms_stock_product 
                                WHERE ProductID = tx_merchant_order_detail.ProductID
                                AND ms_stock_product.Qty > 0
                                AND ms_stock_product.ConditionStock = 'GOOD STOCK'
                                AND ms_stock_product.DistributorID = tx_merchant_order.DistributorID
                            )
                    LEFT JOIN (
                        SELECT SUM(tx_merchant_delivery_order_detail.Qty) AS Qty, tx_merchant_delivery_order_detail.ProductID, 
                        tx_merchant_delivery_order.StockOrderID
                        FROM tx_merchant_delivery_order_detail
                        JOIN tx_merchant_delivery_order ON tx_merchant_delivery_order.DeliveryOrderID = tx_merchant_delivery_order_detail.DeliveryOrderID
                            AND (tx_merchant_delivery_order.StatusDO = 'S024' OR tx_merchant_delivery_order.StatusDO = 'S025')
                        WHERE (tx_merchant_delivery_order_detail.StatusExpedition = 'S030' OR tx_merchant_delivery_order_detail.StatusExpedition = 'S031')
                        GROUP BY tx_merchant_delivery_order.StockOrderID, tx_merchant_delivery_order_detail.ProductID
                    ) AS DOkirim ON DOkirim.ProductID = tx_merchant_order_detail.ProductID AND DOkirim.StockOrderID = tx_merchant_order_detail.StockOrderID
                    WHERE tx_merchant_order_detail.StockOrderID = Restock.StockOrderID
                ) AS MarginEstimation
            ");

        return $sql;
    }

    public function merchantRestockAllProduct()
    {
        $sql = DB::table('tx_merchant_order')
            ->leftJoin('tx_merchant_order_detail', 'tx_merchant_order_detail.StockOrderID', '=', 'tx_merchant_order.StockOrderID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', 'ms_distributor_merchant_grade.GradeID')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'ms_merchant_account.ReferralCode')
            ->leftJoin('ms_merchant_assessment', function ($join) {
                $join->on('ms_merchant_assessment.MerchantID', 'tx_merchant_order.MerchantID');
                $join->whereRaw("ms_merchant_assessment.IsActive = 1");
            })
            ->whereRaw('ms_merchant_account.IsTesting = 0')
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.DistributorID', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.MerchantID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.NettPrice', 'tx_merchant_order.DeliveryFee', 'tx_merchant_order.StatusOrderID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.PhoneNumber', 'ms_distributor.DistributorName', 'ms_status_order.StatusOrder', 'ms_merchant_account.ReferralCode', 'ms_sales.SalesName', 'tx_merchant_order.PaymentMethodID', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order_detail.ProductID', 'ms_product.ProductName', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Price', 'ms_merchant_account.StoreAddress', 'tx_merchant_order_detail.Discount', 'tx_merchant_order_detail.Nett', 'ms_distributor.Depo', 'ms_distributor_grade.Grade', 'ms_merchant_assessment.NumberIDCard', 'ms_merchant_assessment.TurnoverAverage', 'ms_merchant_account.OwnerFullName', 'ms_merchant_assessment.IsDownload');

        return $sql;
    }

    public function merchantAccountParamHaistar($stockOrderID)
    {
        $sql = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->leftJoin('ms_area', 'ms_area.AreaID', 'ms_merchant_account.AreaID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderID)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.Email', 'ms_merchant_account.MerchantFirebaseToken', 'ms_distributor.DistributorName', 'tx_merchant_order.OrderAddress', 'tx_merchant_order.PaymentMethodID', 'ms_area.PostalCode', 'ms_area.Province', 'ms_area.City', 'ms_area.Subdistrict', 'tx_merchant_order.DistributorNote', 'tx_merchant_order.MerchantNote')
            ->first();

        return $sql;
    }

    public function merchantAccount($merchantID)
    {
        $sql = DB::table('ms_merchant_account')
            ->select('MerchantID', 'StoreName', 'OwnerFullName', 'PhoneNumber', 'StoreImage')
            ->where('MerchantID', $merchantID)
            ->where('IsTesting', 0);

        return $sql;
    }

    public function merchantFairbanc()
    {
        $sql = DB::table('ms_merchant_account')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', '=', 'ms_merchant_account.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_merchant_grade.GradeID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->where('ms_merchant_account.Partner', 'FAIRBANC')
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_distributor.DistributorName', 'ms_distributor_grade.Grade');
        return $sql;
    }

    public function merchantNotFairbanc()
    {
        $sql = DB::table('ms_merchant_account')
            ->whereNull('Partner')
            ->where('IsTesting', 0)
            ->select('MerchantID', 'StoreName');
        return $sql;
    }

    public function deleteMerchantFairbanc($merchantID)
    {
        $sql = DB::table('ms_merchant_account')
            ->where('MerchantID', $merchantID)
            ->update([
                'Partner' => NULL
            ]);
        return $sql;
    }

    public function dataMerchantFairbanc($arrMerchantID)
    {
        $data = array_map(function () {
            return func_get_args();
        }, $arrMerchantID);

        foreach ($data as $key => $value) {
            $data[$key][] = "FAIRBANC";
        }

        return $data;
    }

    public function insertBulkMerchantFairbanc($arrMerchantID)
    {
        $insert = DB::transaction(function () use ($arrMerchantID) {
            foreach ($arrMerchantID as &$value) {
                $value = array_combine(['MerchantID', 'Partner'], $value);
                DB::table('ms_merchant_account')
                    ->where('MerchantID', '=', $value['MerchantID'])
                    ->update([
                        'Partner' => $value['Partner']
                    ]);
            }
        });

        return $insert;
    }

    public function merchantSpecialPrice($merchantID)
    {
        $sqlGetDepoAndGrade = DB::table('ms_distributor_merchant_grade')
            ->select('DistributorID', 'GradeID')
            ->where('MerchantID', $merchantID);

        $sqlGetDepoMerchant = DB::table('ms_merchant_account')
            ->join('ms_distributor_grade', 'ms_distributor_grade.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->select('ms_merchant_account.DistributorID', 'ms_distributor_grade.GradeID')
            ->where('ms_merchant_account.MerchantID', $merchantID)
            ->where('ms_distributor_grade.Grade', 'Retail');

        if ($sqlGetDepoAndGrade->count() > 0) {
            $distributorID = $sqlGetDepoAndGrade->get()[0]->DistributorID;
            $gradeID = $sqlGetDepoAndGrade->get()[0]->GradeID;
        } else {
            $distributorID = $sqlGetDepoMerchant->get()[0]->DistributorID;
            $gradeID = $sqlGetDepoMerchant->get()[0]->GradeID;
        }

        $sqlSpecialPrice = DB::table('ms_distributor_product_price')
            ->join('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_product_price.GradeID')
            ->join('ms_product', 'ms_product.ProductID', '=', 'ms_distributor_product_price.ProductID')
            ->leftJoin('ms_product_special_price', function ($join) use ($merchantID) {
                $join->on('ms_product_special_price.GradeID', '=', 'ms_distributor_product_price.GradeID')
                    ->on('ms_product_special_price.ProductID', '=', 'ms_distributor_product_price.ProductID')
                    ->where('ms_product_special_price.MerchantID', "$merchantID");
            })
            ->select('ms_distributor_product_price.ProductID', 'ms_product.ProductName', 'ms_distributor_grade.GradeID', 'ms_distributor_grade.Grade', 'ms_distributor_product_price.Price', 'ms_distributor_product_price.DistributorID', 'ms_product_special_price.SpecialPrice')
            ->where('ms_distributor_product_price.DistributorID', $distributorID)
            ->where('ms_distributor_product_price.GradeID', $gradeID);

        return $sqlSpecialPrice;
    }

    public function updateOrInsertSpecialPrice($merchantID, $productID, $gradeID, $specialPrice)
    {
        $getProduct = DB::table('ms_product')->where('ProductID', $productID)->select('ProductName')->first();
        $getData = DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->select('DistributorID')->first();

        $getOldPrice = DB::table('ms_product_special_price')
            ->where('ProductID', $productID)
            ->where('MerchantID', $merchantID)
            ->where('GradeID', $gradeID)->select('SpecialPrice')->first();

        if ($getOldPrice) {
            $logAction = "UPDATE";
            $oldPrice = $getOldPrice->SpecialPrice;
        } else {
            $logAction = "CREATE";
            $oldPrice = "0";
        }

        $data = [
            'LogType' => 'SPECIAL PRICE',
            'LogAction' => $logAction,
            'OldPrice' => $oldPrice,
            'NewPrice' => $specialPrice,
            'DistributorID' => $getData->DistributorID,
            'GradeID' => $gradeID,
            'MerchantID' => $merchantID,
            'ProductID' => $productID,
            'ProductName' => $getProduct->ProductName,
            'ActionByID' => Auth::user()->UserID,
            'ActionByName' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'CreatedDate' => date('Y-m-d H:i:s')
        ];

        $sql = DB::transaction(function () use ($merchantID, $productID, $gradeID, $specialPrice, $data) {
            DB::table('ms_product_special_price')
                ->updateOrInsert(
                    [
                        'MerchantID' => $merchantID,
                        'ProductID' => $productID,
                        'GradeID' => $gradeID
                    ],
                    [
                        'SpecialPrice' => $specialPrice
                    ]
                );
            DB::table('ms_product_price_log')->insert($data);
        });
        return $sql;
    }

    public function deleteSpecialPriceMerchant($merchantID, $productID, $gradeID)
    {
        $getProduct = DB::table('ms_product')->where('ProductID', $productID)->select('ProductName')->first();
        $getData = DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->select('DistributorID')->first();
        $getOldPrice = DB::table('ms_product_special_price')
            ->where('ProductID', $productID)
            ->where('MerchantID', $merchantID)
            ->where('GradeID', $gradeID)->select('SpecialPrice')->first();

        $data = [
            'LogType' => 'SPECIAL PRICE',
            'LogAction' => 'DELETE',
            'OldPrice' => $getOldPrice->SpecialPrice,
            'NewPrice' => 0,
            'DistributorID' => $getData->DistributorID,
            'GradeID' => $gradeID,
            'MerchantID' => $merchantID,
            'ProductID' => $productID,
            'ProductName' => $getProduct->ProductName,
            'ActionByID' => Auth::user()->UserID,
            'ActionByName' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'CreatedDate' => date('Y-m-d H:i:s')
        ];

        $sql = DB::transaction(function () use ($merchantID, $productID, $gradeID, $data) {
            DB::table('ms_product_special_price')
                ->where('MerchantID', $merchantID)
                ->where('ProductID', $productID)
                ->where('GradeID', $gradeID)
                ->delete();
            DB::table('ms_product_price_log')->insert($data);
        });

        return $sql;
    }

    public function resetSpecialPriceMerchant($merchantID, $gradeID)
    {
        $getData = DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->select('DistributorID')->first();
        $data = [
            'LogType' => 'SPECIAL PRICE',
            'LogAction' => 'RESET',
            'DistributorID' => $getData->DistributorID,
            'GradeID' => $gradeID,
            'MerchantID' => $merchantID,
            'ActionByID' => Auth::user()->UserID,
            'ActionByName' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'CreatedDate' => date('Y-m-d H:i:s')
        ];

        $sql = DB::transaction(function () use ($merchantID, $gradeID, $data) {
            DB::table('ms_product_special_price')
                ->where('MerchantID', $merchantID)
                ->where('GradeID', $gradeID)
                ->delete();
            DB::table('ms_product_price_log')->insert($data);
        });

        return $sql;
    }

    public function getDataAssessments()
    {
        $endDate = new DateTime();
        $endDateFormat = $endDate->format('Y-m-d');

        $sql = DB::table('ms_merchant_assessment')
            ->leftJoin('ms_merchant_account', function ($join) {
                $join->on('ms_merchant_account.MerchantID', 'ms_merchant_assessment.MerchantID');
                $join->where('ms_merchant_account.IsTesting', 0);
            })
            ->leftJoin('ms_sales as sales_merchant', 'sales_merchant.SalesCode', 'ms_merchant_account.ReferralCode')
            ->leftJoin('ms_store', function ($join) {
                $join->on('ms_store.StoreID', 'ms_merchant_assessment.StoreID');
                $join->where('ms_store.IsActive', 1);
            })
            ->leftJoin('ms_sales as sales_store', 'sales_store.SalesCode', 'ms_store.SalesCode')
            ->join('ms_merchant_assessment_transaction', 'ms_merchant_assessment_transaction.MerchantAssessmentID', 'ms_merchant_assessment.MerchantAssessmentID')
            ->where('ms_merchant_assessment.IsActive', 1)
            ->selectRaw("
                ms_merchant_assessment.MerchantAssessmentID,
                ms_merchant_assessment.PhotoMerchantFront,
                ms_merchant_assessment.PhotoMerchantSide,
                ms_merchant_assessment.StruckDistribution,
                ms_merchant_assessment.TurnoverAverage,
                ms_merchant_assessment.PhotoStockProduct,
                ms_merchant_assessment.PhotoIDCard,
                ms_merchant_assessment.NumberIDCard,
                ms_merchant_assessment.StoreID,
                ms_merchant_assessment.MerchantID,
                ms_merchant_assessment.CreatedAt,
                ms_merchant_assessment.IsDownload,
                GROUP_CONCAT(ANY_VALUE(ms_merchant_assessment_transaction.TransactionName) SEPARATOR ', ') AS Transaction,
                ms_store.StoreName,
                ms_store.PhoneNumber,
                ANY_VALUE(ms_merchant_account.StoreName) AS MerchantName,
                ANY_VALUE(ms_merchant_account.PhoneNumber) AS MerchantNumber,
                ANY_VALUE(ms_merchant_account.ReferralCode) AS ReferralCode,
                ANY_VALUE(sales_merchant.SalesName) AS SalesName,
                ANY_VALUE(ms_store.SalesCode) AS SalesCodeStore,
                ANY_VALUE(sales_store.SalesName) AS SalesNameStore,
                (
                    SELECT COUNT(tx_merchant_order.StockOrderID)
                    FROM tx_merchant_order
                    WHERE tx_merchant_order.MerchantID = ms_merchant_assessment.MerchantID
                    AND tx_merchant_order.PaymentMethodID = 14
                    AND tx_merchant_order.StatusOrderID != 'S011'
                    AND DATE(tx_merchant_order.CreatedDate) >= '2022-05-25'
                    AND DATE(tx_merchant_order.CreatedDate) <= '$endDateFormat'
                ) AS CountPO
            ")
            ->groupBy('ms_merchant_assessment.MerchantAssessmentID');

        return $sql;
    }

    public function getDataAssessmentByID($assessmentID)
    {
        $sql = DB::table('ms_merchant_assessment')
            ->where('ms_merchant_assessment.MerchantAssessmentID', $assessmentID)
            ->select(
                'ms_merchant_assessment.MerchantAssessmentID',
                'ms_merchant_assessment.StoreID',
                'ms_merchant_assessment.MerchantID',
                'ms_merchant_assessment.PhotoMerchantFront',
                'ms_merchant_assessment.PhotoMerchantSide',
                'ms_merchant_assessment.StruckDistribution',
                'ms_merchant_assessment.PhotoStockProduct',
                'ms_merchant_assessment.PhotoIDCard',
                'ms_merchant_assessment.NumberIDCard'
            );

        return $sql;
    }
}
