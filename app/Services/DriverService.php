<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverService
{
  public function getDrivers()
  {
    $drivers = DB::table('ms_user')
      ->where('RoleID', 'DRV')
      ->where('IsTesting', 0)
      ->select('UserID', 'Name')
      ->orderBy('Name');

    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $drivers->where('Depo', $depoUser);
    }

    return $drivers;
  }

  public function getHelpers()
  {
    $helpers = DB::table('ms_user')
      ->where('RoleID', 'HLP')
      ->where('IsTesting', 0)
      ->select('UserID', 'Name')
      ->orderBy('Name');

    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $helpers->where('Depo', $depoUser);
    }

    return $helpers;
  }
}