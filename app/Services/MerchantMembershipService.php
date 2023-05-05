<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantMembershipService
{
  public function merchantMembershipData()
  {
    $sqlMembership = DB::table('ms_merchant_account')
      ->join('ms_status_couple_preneur as StatusMembership', 'StatusMembership.StatusCouplePreneurID', 'ms_merchant_account.ValidationStatusMembershipCouple')
      ->leftJoin('ms_status_couple_preneur as StatusCrowdo', 'StatusCrowdo.StatusCouplePreneurID', 'ms_merchant_account.StatusCrowdo')
      ->leftJoin('ms_history_membership', function ($join) {
        $join->on('ms_merchant_account.MerchantID', '=', 'ms_history_membership.merchant_id')
          ->where('ms_history_membership.disclaimer_id', '=', DB::raw('(SELECT MAX(disclaimer_id) FROM ms_history_membership WHERE merchant_id = ms_merchant_account.MerchantID)'));
      })
      ->leftJoin('ms_membership_status_rejection', 'ms_history_membership.rejected_id', 'ms_membership_status_rejection.id')
      ->where('ms_merchant_account.IsTesting', 0)
      ->where('ms_merchant_account.ValidationStatusMembershipCouple', '!=', 0)
      ->selectRaw("ANY_VALUE(ms_merchant_account.MerchantID) AS MerchantID, 
        ms_merchant_account.StoreName, ms_merchant_account.OwnerFullName, 
        ms_merchant_account.PhoneNumber,
        ms_merchant_account.NumberIDCard, ms_merchant_account.UsernameIDCard,
        ms_merchant_account.BirthDate, ms_merchant_account.StoreAddress, ms_merchant_account.ReferralCode, 
        ms_merchant_account.ValidationStatusMembershipCouple, StatusMembership.StatusName, 
        ms_merchant_account.StatusCrowdo, ms_merchant_account.CrowdoApprovedDate, StatusCrowdo.StatusName AS StatusNameCrowdo,
        ms_merchant_account.MembershipCoupleSubmitDate, 
        ANY_VALUE(ms_history_membership.action_date) AS action_date, 
        ANY_VALUE(ms_history_membership.action_by) AS action_by, 
        ANY_VALUE(ms_history_membership.status_payment_id) AS status_payment_id,
        ANY_VALUE(ms_history_membership.rejected_id) AS rejected_id,
        IFNULL(ANY_VALUE(ms_history_membership.rejected_reason), ANY_VALUE(ms_membership_status_rejection.status_name)) AS rejected_reason, 
        ms_merchant_account.ValidationNoteMembershipCouple")
      ->groupBy('ms_merchant_account.MerchantID')
      ->orderBy('ms_merchant_account.MembershipCoupleSubmitDate', 'DESC');
    // ->select(
    //   'ms_merchant_account.MerchantID',
    //   'ms_merchant_account.StoreName',
    //   'ms_merchant_account.OwnerFullName',
    //   'ms_merchant_account.PhoneNumber',
    //   'ms_marital_status.MaritalStatusName',
    //   'ms_merchant_account.NumberIDCard',
    //   'ms_merchant_account.UsernameIDCard',
    //   'ms_merchant_account.BirthDate',
    //   // 'ms_merchant_account.NumberIDCardCouple',
    //   // 'ms_merchant_account.UsernameIDCardCouple',
    //   // 'ms_merchant_account.BirthDateCouple',
    //   // 'ms_distributor.DistributorName',
    //   // 'ms_merchant_account.StoreLength',
    //   // 'ms_merchant_account.StoreWidth',
    //   // DB::raw("IF(ms_merchant_account.StoreOmzet = 0 OR ISNULL(ms_merchant_account.StoreOmzet), '', ms_merchant_account.StoreOmzet) AS StoreOmzet"),
    //   // DB::raw("IF(ms_merchant_account.StoreEmployees = 0 OR ISNULL(ms_merchant_account.StoreEmployees), '', ms_merchant_account.StoreEmployees) AS StoreEmployees"),
    //   // DB::raw("IF(ms_merchant_account.MotherName = 'none' OR ISNULL(ms_merchant_account.MotherName), '', ms_merchant_account.MotherName) AS MotherName"),
    //   'ms_merchant_account.StoreAddress',
    //   // 'ms_area.AreaName',
    //   // 'ms_area.Subdistrict',
    //   // 'ms_area.City',
    //   // 'ms_area.Province',
    //   // 'ms_area.PostalCode',
    //   'ms_merchant_account.ReferralCode',
    //   // DB::raw("ANY_VALUE(ms_sales.SalesName) AS SalesName"),
    //   'ms_merchant_account.ValidationStatusMembershipCouple',
    //   'StatusMembership.StatusName',
    //   'ms_merchant_account.StatusCrowdo',
    //   // 'ms_merchant_account.CrowdoLoanID',
    //   // 'ms_merchant_account.CrowdoAmount',
    //   // 'ms_merchant_account.CrowdoBatch',
    //   'ms_merchant_account.CrowdoApprovedDate',
    //   'StatusCrowdo.StatusName AS StatusNameCrowdo',
    //   'ms_merchant_account.MembershipCoupleSubmitDate',
    //   'ms_merchant_account.MembershipCoupleConfirmDate',
    //   'ms_merchant_account.MembershipCoupleConfirmBy',
    //   'ms_merchant_account.ValidationNoteMembershipCouple',
    //   DB::raw("IF(ms_distributor.Regional = 'REGIONAL1', TRUE, FALSE) AS Disclaimer"),
    //   // DB::raw("
    //   //   (
    //   //     SELECT COUNT(StockOrderID)
    //   //     FROM tx_merchant_order
    //   //     WHERE MerchantID = ms_merchant_account.MerchantID
    //   //       AND StatusOrderID IN ('S023', 'S012','S018')
    //   //   ) AS CountTrx
    //   // "),
    //   // DB::raw("
    //   //   (
    //   //     SELECT IFNULL(SUM(NettPrice), 0)
    //   //     FROM tx_merchant_order
    //   //     WHERE MerchantID = ms_merchant_account.MerchantID
    //   //       AND StatusOrderID IN ('S023', 'S012','S018')
    //   //   ) AS SumTrx
    //   // ")
    // );

    return $sqlMembership;
  }

  public function merchantMembershipDataPartner()
  {
    $sqlMembership = DB::table('ms_merchant_account')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_merchant_account.DistributorID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
      ->join('ms_status_couple_preneur as StatusMembership', 'StatusMembership.StatusCouplePreneurID', 'ms_merchant_account.ValidationStatusMembershipCouple')
      ->join('ms_history_membership', 'ms_merchant_account.MerchantID', 'ms_history_membership.merchant_id')
      ->leftJoin('ms_membership_status_payment', 'ms_history_membership.status_payment_id', 'ms_membership_status_payment.id')
      ->leftJoin('ms_status_couple_preneur as StatusCrowdo', 'StatusCrowdo.StatusCouplePreneurID', 'ms_merchant_account.StatusCrowdo')
      ->leftJoin('ms_area', 'ms_area.AreaID', 'ms_merchant_account.AreaID')
      ->leftJoin('ms_marital_status', 'ms_marital_status.MaritalStatusID', 'ms_merchant_account.MaritalStatusID')
      ->where('ms_history_membership.partner_id', 5)
      ->where('ms_merchant_account.IsTesting', 0)
      ->where('ms_merchant_account.ValidationStatusMembershipCouple', '!=', 0)
      ->whereIn('ms_history_membership.disclaimer_id', function ($query) {
        $query->selectRaw('MAX(ms_history_membership.disclaimer_id)')
          ->from('ms_history_membership')
          ->groupBy('ms_history_membership.merchant_id');
      })
      ->select(
        DB::raw("ms_merchant_account.MerchantID"),
        'ms_merchant_account.StoreName',
        'ms_merchant_account.OwnerFullName',
        'ms_merchant_account.PhoneNumber',
        'ms_marital_status.MaritalStatusName',
        'ms_merchant_account.NumberIDCard',
        'ms_merchant_account.UsernameIDCard',
        'ms_merchant_account.BirthDate',
        'ms_merchant_account.StoreAddress',
        'ms_merchant_account.ValidationStatusMembershipCouple',
        'StatusMembership.StatusName',
        'ms_merchant_account.StatusCrowdo',
        'ms_merchant_account.CrowdoLoanID',
        'ms_merchant_account.CrowdoAmount',
        'ms_merchant_account.CrowdoBatch',
        'ms_merchant_account.CrowdoApprovedDate',
        'StatusCrowdo.StatusName AS StatusNameCrowdo',
        'ms_merchant_account.MembershipCoupleSubmitDate',
        'ms_merchant_account.MembershipCoupleConfirmDate',
        'ms_merchant_account.MembershipCoupleConfirmBy',
        'ms_merchant_account.ValidationNoteMembershipCouple',
        DB::raw("ANY_VALUE(StatusMembership.StatusCouplePreneurID) AS StatusCouplePreneurID"),
        DB::raw("ANY_VALUE(ms_history_membership.disclaimer_id) AS disclaimer_id"),
        DB::raw("ANY_VALUE(ms_history_membership.status_payment_id) AS status_payment_id"),
        DB::raw("ANY_VALUE(ms_membership_status_payment.status_name) AS StatusPaymentName"),
        DB::raw("ANY_VALUE(ms_history_membership.batch_number) AS batch_number"),
        DB::raw("IF(ms_distributor.Regional = 'REGIONAL1', TRUE, FALSE) AS Disclaimer"),
      )
      ->groupBy('ms_history_membership.merchant_id');

    return $sqlMembership;
  }

  public function merchantMembershipPhoto($merchantID)
  {
    $sql = DB::table('ms_merchant_account')
      ->where('MerchantID', $merchantID)
      ->select('PhotoIDCard', 'NumberIDCard', 'UsernameIDCard', 'AsIDCard', 'PhotoIDCardCouple', 'NumberIDCardCouple', 'UsernameIDCardCouple', 'AsIDCardCouple', 'StorePhotoMembership', 'ValidationStatusMembershipCouple', 'ValidationNoteMembershipCouple')
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

  public function updateStatusCrowdo($merchantID, $dataMembership, $status, $dataCrowdo, $dataCouplePreneurCrowdoLog)
  {
    $membership = DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->select('ValidationStatusMembershipCouple')->first();
    $statusMembership = $membership->ValidationStatusMembershipCouple;

    $data = $this->merchantMembershipPhoto($merchantID);

    if ($status == 6) {
      $statusMembership = 3;
    } else if ($status == 7) {
      $statusMembership = 2;
    } else if ($status == 5) {
      $statusMembership = 1;
    }

    $sql = DB::transaction(function () use ($merchantID, $dataMembership, $status, $dataCouplePreneurCrowdoLog, $statusMembership, $data, $dataCrowdo) {
      DB::table('ms_merchant_account')
        ->where('MerchantID', $merchantID)
        ->update([
          'StatusCrowdo' => $status,
          'ValidationStatusMembershipCouple' => $statusMembership,
          'ValidationNoteMembershipCouple' => null
        ]);

      DB::table('ms_history_membership')->insert($dataMembership);

      DB::table('ms_merchant_couple_preneur_crowdo_log')->insert($dataCouplePreneurCrowdoLog);

      if ($status == 6) {
        DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->update($dataCrowdo);
        DB::table('ms_history_membership')->where('merchant_id', $merchantID)->update(['status_payment_id' => 1]);
      }

      if ($status == 7) {
        DB::table('ms_merchant_account')->where('MerchantID', $merchantID)->update(['ValidationNoteMembershipCouple' => $dataMembership['rejected_reason']]);
      }

      if ($status == 6 || $status == 7) {
        DB::table('ms_merchant_couple_preneur_log')->insert([
          'MerchantID' => $merchantID,
          'PhotoIDCard' => $data->PhotoIDCard,
          'NumberIDCard' => $data->NumberIDCard,
          'UsernameIDCard' => $data->UsernameIDCard,
          'AsIDCard' => $data->AsIDCard,
          'PhotoIDCardCouple' => $data->PhotoIDCardCouple,
          'NumberIDCardCouple' => $data->NumberIDCardCouple,
          'UsernameIDCardCouple' => $data->UsernameIDCardCouple,
          'AsIDCardCouple' => $data->AsIDCardCouple,
          'StorePhotoMembership' => $data->StorePhotoMembership,
          'StatusMembershipCouple' => $statusMembership,
          'NoteMembershipCouple' => $data->ValidationNoteMembershipCouple,
          'CreatedDate' => date('Y-m-d H:i:s'),
          'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo
        ]);
      }
    });

    return $sql;
  }
}
