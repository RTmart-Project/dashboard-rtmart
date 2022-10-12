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

    $data = $sql->groupBy('PromoID');

    return $data;
  }
}
