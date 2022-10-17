<?php

namespace App\Services\BannerService;

use Illuminate\Support\Facades\DB;

class BannerSliderService
{
  public function dataBannerSlider($startDate, $endDate, $filterStatus, $filterBy)
  {
    $sql = DB::table('ms_promo')
      ->select('PromoID', 'PromoTitle', 'PromoImage', 'PromoStartDate', 'PromoEndDate', 'PromoStatus', 'PromoTarget', 'PromoExpiryDate', 'ClassActivityPage', 'ActivityButtonText');

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
    $sql = DB::table('ms_promo')->select('PromoTarget')->distinct()->get();
    return $sql;
  }

  public function listTargetIDBannerSlider($target)
  {
    if ($target === "MERCHANT") {
      $sql = DB::table('ms_merchant_account')->where('IsTesting', 0)->select('MerchantID', 'StoreName')->get();
    } else {
      $sql = DB::table('ms_customer_account')->where('IsTesting', 0)->select('CustomerID', 'FullName')->get();
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
