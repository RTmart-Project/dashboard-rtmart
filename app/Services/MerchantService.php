<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class MerchantService {

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
        $insert = DB::transaction(function() use ($arrMerchantID) {
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
            ->leftJoin('ms_product_special_price', function($join) use ($merchantID) {
                $join->on('ms_product_special_price.GradeID', '=', 'ms_distributor_product_price.GradeID')
                    ->on('ms_product_special_price.ProductID', '=', 'ms_distributor_product_price.ProductID')
                    ->where('ms_product_special_price.MerchantID', "$merchantID");
            })
            ->select('ms_distributor_product_price.ProductID', 'ms_product.ProductName', 'ms_distributor_grade.GradeID', 'ms_distributor_grade.Grade', 'ms_distributor_product_price.Price', 'ms_distributor_product_price.DistributorID', 'ms_product_special_price.SpecialPrice')
            ->where('ms_distributor_product_price.DistributorID', $distributorID)
            ->where('ms_distributor_product_price.GradeID', $gradeID)
            ;

        return $sqlSpecialPrice;
    }

    public function updateOrInsertSpecialPrice($merchantID, $productID, $gradeID, $specialPrice)
    {
        $sql = DB::table('ms_product_special_price')
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

        return $sql;
    }

    public function deleteSpecialPriceMerchant($merchantID, $productID, $gradeID)
    {
        $sql = DB::table('ms_product_special_price')
            ->where('MerchantID', $merchantID)
            ->where('ProductID', $productID)
            ->where('GradeID', $gradeID)
            ->delete();

        return $sql;
    }

    public function resetSpecialPriceMerchant($merchantID, $gradeID)
    {
        $sql = DB::table('ms_product_special_price')
            ->where('MerchantID', $merchantID)
            ->where('GradeID', $gradeID)
            ->delete();

        return $sql;
    }

}