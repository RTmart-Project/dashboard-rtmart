<?php

namespace App\Services;

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
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'ms_merchant_account.ReferralCode')
            ->whereRaw('ms_merchant_account.IsTesting = 0')
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.MerchantID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'tx_merchant_order.NettPrice', 'tx_merchant_order.StatusOrderID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.PhoneNumber', 'ms_distributor.DistributorName', 'ms_status_order.StatusOrder', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.ReferralCode', 'ms_sales.SalesName', 'ms_payment_method.PaymentMethodName', 'ms_distributor_grade.Grade', 'tx_merchant_order.DistributorID')
            ->whereRaw("tx_merchant_order.StockOrderID = 'SO-20220324120725-007163'")
            // ->limit(10)
            // ->get()
            ->toSql();

        $sql = DB::table(DB::raw("($sqlMain) AS Restock"))
            ->selectRaw("
                Restock.*,
                (
                    SELECT SUM(tx_merchant_order_detail.Nett) FROM tx_merchant_order_detail
                    LEFT JOIN ms_stock_product ON ms_stock_product.ProductID = tx_merchant_order_detail.ProductID
                        AND ms_stock_product.Qty > 0 
                        AND ms_stock_product.ConditionStock = 'GOOD STOCK'
                        AND ms_stock_product.DistributorID = Restock.DistributorID
                    WHERE tx_merchant_order_detail.StockOrderID = Restock.StockOrderID
                    AND ms_stock_product.StockProductID IS NULL
                ) AS sum 
            ");

        // foreach ($sql as $key => $value) {
        //     $orderDetails = DB::table('tx_merchant_order_detail')
        //         ->where('tx_merchant_order_detail.StockOrderID', $value->StockOrderID)
        //         ->select('ProductID', 'PromisedQuantity', 'Nett')
        //         ->get()->toArray();

        //     foreach ($orderDetails as $key => $orderDetail) {
        //         $deliveryOrderDetails = DB::table('tx_merchant_delivery_order_detail')
        //             ->whereRaw("
        //                 DeliveryOrderID IN (
        //                     SELECT DeliveryOrderID FROM tx_merchant_delivery_order 
        //                     WHERE StockOrderID IN ('$value->StockOrderID')
        //                 )
        //                 AND ProductID = '$orderDetail->ProductID'
        //                 AND (StatusExpedition = 'S030' OR StatusExpedition = 'S031')
        //                 ")
        //             ->sum('Qty');

        //         $sqlPurchasePriceReal = DB::table(DB::raw("(SELECT ms_stock_product_log.PurchasePrice FROM ms_stock_product_log
        //             LEFT JOIN tx_merchant_expedition_detail ON tx_merchant_expedition_detail.MerchantExpeditionDetailID = ms_stock_product_log.MerchantExpeditionDetailID
        //             LEFT JOIN tx_merchant_delivery_order_detail ON tx_merchant_delivery_order_detail.DeliveryOrderDetailID = tx_merchant_expedition_detail.DeliveryOrderDetailID
        //             WHERE tx_merchant_delivery_order_detail.DeliveryOrderID IN (
        //                 SELECT DeliveryOrderID FROM tx_merchant_delivery_order WHERE StockOrderID = '$value->StockOrderID'
        //                 AND (StatusDO = 'S024' OR StatusDO = 'S025')
        //             ) AND tx_merchant_delivery_order_detail.ProductID = '$orderDetail->ProductID'
        //             AND (tx_merchant_delivery_order_detail.StatusExpedition = 'S030' OR tx_merchant_delivery_order_detail.StatusExpedition = 'S031')
        //             ORDER BY ms_stock_product_log.CreatedDate LIMIT 1) AS PurchasePriceReal"))->select('PurchasePriceReal.*')->first();

        //         $sqlPurchasePriceEstimation = DB::table(DB::raw("(SELECT PurchasePrice FROM ms_stock_product 
        //             WHERE ProductID = '$orderDetail->ProductID' AND DistributorID = '$value->DistributorID'
        //             AND ConditionStock = 'GOOD STOCK' AND Qty > 0
        //             ORDER BY LevelType, CreatedDate LIMIT 1) AS PurchasePriceEstimation"))->select('PurchasePriceEstimation.*')->first();

        //         if ($sqlPurchasePriceReal == null) {
        //             $purchasePriceReal = 0;
        //         } else {
        //             $purchasePriceReal = $sqlPurchasePriceReal->PurchasePrice;
        //         }
        //         if ($sqlPurchasePriceEstimation == null) {
        //             $purchasePriceEstimation = 0;
        //         } else {
        //             $purchasePriceEstimation = $sqlPurchasePriceEstimation->PurchasePrice;
        //         }

        //         $orderDetail->QtyDOkirim = $deliveryOrderDetails;
        //         $orderDetail->PurchasePriceReal = $purchasePriceReal;
        //         $orderDetail->PurchasePriceEstimation = $purchasePriceEstimation;
        //     }

        //     $value->OrderDetail = $orderDetails;
        // }

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
            ->whereRaw('ms_merchant_account.IsTesting = 0')
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.DistributorID', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.MerchantID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.NettPrice', 'tx_merchant_order.DeliveryFee', 'tx_merchant_order.StatusOrderID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.PhoneNumber', 'ms_distributor.DistributorName', 'ms_status_order.StatusOrder', 'ms_merchant_account.ReferralCode', 'ms_sales.SalesName', 'tx_merchant_order.PaymentMethodID', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order_detail.ProductID', 'ms_product.ProductName', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Price', 'ms_merchant_account.StoreAddress', 'tx_merchant_order_detail.Discount', 'tx_merchant_order_detail.Nett', 'ms_distributor.Depo', 'ms_distributor_grade.Grade');

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
}
