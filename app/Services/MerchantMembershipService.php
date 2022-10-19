<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantMembershipService
{

  public function merchantMembershipData()
  {
    $sqlMembership = DB::table('ms_merchant_account')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_merchant_account.DistributorID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
      ->join('ms_status_couple_preneur', 'ms_status_couple_preneur.StatusCouplePreneurID', 'ms_merchant_account.ValidationStatusMembershipCouple')
      ->where('ms_merchant_account.IsTesting', 0)
      ->where('ms_merchant_account.ValidationStatusMembershipCouple', '!=', 0)
      ->select(
        'ms_merchant_account.MerchantID',
        'ms_merchant_account.StoreName',
        'ms_merchant_account.OwnerFullName',
        'ms_merchant_account.PhoneNumber',
        'ms_merchant_account.NumberIDCard',
        'ms_merchant_account.UsernameIDCard',
        'ms_merchant_account.NumberIDCardCouple',
        'ms_merchant_account.UsernameIDCardCouple',
        'ms_distributor.DistributorName',
        'ms_merchant_account.StoreAddress',
        'ms_merchant_account.ReferralCode',
        'ms_sales.SalesName',
        'ms_merchant_account.ValidationStatusMembershipCouple',
        'ms_status_couple_preneur.StatusName',
        'ms_merchant_account.MembershipCoupleSubmitDate',
        'ms_merchant_account.MembershipCoupleConfirmDate',
        'ms_merchant_account.MembershipCoupleConfirmBy',
        'ms_merchant_account.ValidationNoteMembershipCouple'
      );

    return $sqlMembership;
  }

  public function merchantMembershipPhoto($merchantID)
  {
    $sql = DB::table('ms_merchant_account')
      ->where('MerchantID', $merchantID)
      ->select('PhotoIDCard', 'NumberIDCard', 'UsernameIDCard', 'AsIDCard', 'PhotoIDCardCouple', 'NumberIDCardCouple', 'UsernameIDCardCouple', 'AsIDCardCouple', 'StorePhotoMembership', 'ValidationStatusMembershipCouple')
      ->first();

    return $sql;
  }

  public function merchantMembershipConfirm($merchantID, $status, $dataMerchantAccount, $dataMerchantCouplePreneurLog)
  {
    $sql = DB::transaction(function () use ($merchantID, $status, $dataMerchantAccount, $dataMerchantCouplePreneurLog) {
      DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->update($dataMerchantAccount);
      DB::table('ms_merchant_couple_preneur_log')->insert($dataMerchantCouplePreneurLog);
      if ($status === "approve") {
        DB::table('ms_merchant_partner')->updateOrInsert(['MerchantID' => $merchantID], ['PartnerID' => 1]);
      }
    });

    return $sql;
  }
}
