<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class MerchantService {

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

}