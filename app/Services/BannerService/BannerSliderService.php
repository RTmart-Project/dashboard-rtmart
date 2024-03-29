<?php

namespace App\Services\BannerService;

use Illuminate\Support\Facades\DB;

class BannerSliderService
{
  public function dataBannerSlider($startDate, $endDate, $filterStatus, $filterBy)
  {
    $sql = DB::table('ms_promo')
      ->selectRaw("ANY_VALUE(ID) AS ID, ANY_VALUE(PromoID) AS PromoID, ANY_VALUE(PromoTitle) AS PromoTitle, 
      ANY_VALUE(PromoImage) AS PromoImage, ANY_VALUE(PromoStartDate) AS PromoStartDate, 
      ANY_VALUE(PromoEndDate) AS PromoEndDate, ANY_VALUE(PromoStatus) AS PromoStatus, 
      CASE
        WHEN ANY_VALUE(PromoTarget) = 'MERCHANT' OR ANY_VALUE(PromoTarget) = 'CUSTOMER' THEN GROUP_CONCAT(TargetID SEPARATOR ', ')
        WHEN ANY_VALUE(PromoTarget) = 'MERCHANT_GROUP' THEN GROUP_CONCAT(ms_distributor.DistributorName SEPARATOR ', ')
      END AS TargetID,
      ANY_VALUE(PromoTarget) AS PromoTarget,
      ANY_VALUE(PromoExpiryDate) AS PromoExpiryDate, ANY_VALUE(ClassActivityPage) AS ClassActivityPage, 
      ANY_VALUE(ActivityButtonText) AS ActivityButtonText")
      ->leftJoin('ms_distributor', 'ms_promo.SourceID', 'ms_distributor.DistributorID')
      ->groupBy('PromoID');

    if ($filterBy === "tanggal-mulai") {
      $sql->whereDate('PromoStartDate', '>=', $startDate)->whereDate('PromoStartDate', '<=', $endDate);
    } else if ($filterBy === "tanggal-berakhir") {
      $sql->whereDate('PromoExpiryDate', '>=', $startDate)->whereDate('PromoExpiryDate', '<=', $endDate);
    }

    if ($filterStatus != null) {
      $sql->where('PromoStatus', $filterStatus);
    }

    $data = $sql;

    return $data;
  }

  public function targetBannerSlider()
  {
    $sql = DB::table('ms_promo_target')->select('PromoTarget')->get();

    return $sql;
  }

  public function listTargetIDBannerSlider($target)
  {
    if ($target === "MERCHANT") {
      $sql = DB::table('ms_merchant_account')->select('MerchantID', 'StoreName')->where('IsTesting', 0)->get();
    } elseif ($target === "MERCHANT_GROUP") {
      $sql = DB::table('ms_distributor')->select('DistributorID', 'DistributorName')->where('IsActive', 1)->get();
    } else {
      $sql = DB::table('ms_customer_account')->select('CustomerID', 'FullName')->where('IsTesting', 0)->get();
    }
    return $sql;
  }

  public function generatePromoID()
  {
    $max = DB::table('ms_promo')->max('PromoID');

    if ($max == null) {
      $newPromoID = "STR-000001";
    } else {
      $maxPromoID = substr($max, -6);
      $newNumberPromoID = $maxPromoID + 1;
      $newPromoID = "PR-" . str_pad($newNumberPromoID, 6, '0', STR_PAD_LEFT);
    }

    return $newPromoID;
  }
}
