<?php

namespace App\Services\BannerService;

use Illuminate\Support\Facades\DB;

class BannerSliderService
{
  public function dataBannerSlider()
  {
    $sql = DB::table('ms_promo')
      ->select('PromoID', 'PromoTitle', 'PromoImage', 'PromoStartDate', 'PromoEndDate', 'PromoStatus', 'PromoTarget', 'PromoExpiryDate', 'ClassActivityPage', 'ActivityButtonText')
      ->groupBy('PromoID');

    return $sql;
  }
}
