<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class VehicleService
{
  public function getVehicles()
  {
    $vehicles = DB::table('ms_vehicle')
      ->whereNotIn('VehicleID', [1, 2, 3])
      ->orderBy('VehicleName');

    return $vehicles;
  }
}
