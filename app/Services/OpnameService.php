<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnameService
{
  public function generateOpnameID()
  {
    $max = DB::table('ms_stock_opname')
      ->selectRaw('MAX(StockOpnameID) AS StockOpnameID, MAX(CreatedDate) AS CreatedDate')
      ->first();

    $maxMonth = date('m', strtotime($max->CreatedDate));
    $now = date('m');

    if ($max->StockOpnameID == null || (strcmp($maxMonth, $now) != 0)) {
      $newStockOpnameID = "OPNM-" . date('YmdHis') . '-000001';
    } else {
      $maxOpnameNumber = substr($max->StockOpnameID, -6);
      $newOpnameNumber = $maxOpnameNumber + 1;
      $newStockOpnameID = "OPNM-" . date('YmdHis') . "-" . str_pad($newOpnameNumber, 6, '0', STR_PAD_LEFT);
    }

    return $newStockOpnameID;
  }

  public function getStockOpname()
  {
    $sql = DB::table('ms_stock_opname')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_opname.DistributorID')
      ->join('ms_stock_opname_officer', 'ms_stock_opname_officer.StockOpnameID', 'ms_stock_opname.StockOpnameID')
      ->join('ms_user', 'ms_user.UserID', 'ms_stock_opname_officer.UserID')
      ->selectRaw("
        ms_stock_opname.StockOpnameID,
        ms_stock_opname.OpnameDate,
        ms_stock_opname.Notes,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        GROUP_CONCAT(ms_user.Name SEPARATOR ', ') AS OfficerOpname
      ")
      ->groupBy('ms_stock_opname.StockOpnameID');

    return $sql;
  }
}
