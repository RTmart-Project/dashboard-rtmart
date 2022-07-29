<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnameService
{
  private $purchaseService;
  public function __construct(PurchaseService $purchaseService)
  {
    $this->purchaseService = $purchaseService;
  }

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

  public function getInbound()
  {
    $sql = DB::table('ms_stock_product')
      ->where('Qty', '>', 0)
      ->selectRaw("PurchaseID, ANY_VALUE(CreatedDate) AS CreatedDate")
      ->groupBy('PurchaseID')
      ->orderBy('CreatedDate');

    return $sql;
  }

  public function getDetailInbound($inbound)
  {
    if ($inbound != "Lainnya") {
      $sql = DB::table('ms_stock_product')
        ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_product.DistributorID')
        ->join('ms_investor', 'ms_investor.InvestorID', 'ms_stock_product.InvestorID')
        ->where('ms_stock_product.PurchaseID', $inbound)
        ->select('ms_stock_product.PurchaseID', 'ms_distributor.DistributorName', 'ms_investor.InvestorName')
        ->first();

      $sql->DetailProduk = DB::table('ms_stock_product')
        ->join('ms_product', 'ms_product.ProductID', 'ms_stock_product.ProductID')
        ->where('ms_stock_product.PurchaseID', $inbound)
        ->where('ms_stock_product.Qty', '>', 0)
        ->select('ms_stock_product.ProductID', 'ms_product.ProductName', 'ms_stock_product.PurchasePrice', 'ms_stock_product.ProductLabel', 'ms_stock_product.ConditionStock', 'ms_stock_product.Qty')
        ->get()->toArray();
    } else {
      $distributors = $this->purchaseService->getDistributors()->get()->toArray();
      $investors = DB::table('ms_investor')->get()->toArray();

      $sql = [
        'distributors' => $distributors,
        'investors' => $investors
      ];
    }

    return $sql;
  }

  public function getStockOpname()
  {
    $sql = DB::table('ms_stock_opname')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_opname.DistributorID')
      ->join('ms_stock_opname_officer', 'ms_stock_opname_officer.StockOpnameID', 'ms_stock_opname.StockOpnameID')
      ->join('ms_user', 'ms_user.UserID', 'ms_stock_opname_officer.UserID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_opname.InvestorID')
      ->selectRaw("
        ms_stock_opname.StockOpnameID,
        ms_stock_opname.OpnameDate,
        ms_stock_opname.Notes,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(ms_investor.InvestorName) AS InvestorName,
        GROUP_CONCAT(ms_user.Name SEPARATOR ', ') AS OfficerOpname
      ")
      ->groupBy('ms_stock_opname.StockOpnameID');

    return $sql;
  }

  public function getStockOpnameByID($stockOpnameID)
  {
    $sql = DB::table('ms_stock_opname')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_opname.DistributorID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_opname.InvestorID')
      ->where('ms_stock_opname.StockOpnameID', $stockOpnameID)
      ->select('ms_stock_opname.StockOpnameID', 'ms_stock_opname.OpnameDate', 'ms_stock_opname.Notes', 'ms_distributor.DistributorName', 'ms_investor.InvestorName')
      ->first();

    $sql->Officer = DB::table('ms_stock_opname_officer')
      ->join('ms_user', 'ms_user.UserID', 'ms_stock_opname_officer.UserID')
      ->where('ms_stock_opname_officer.StockOpnameID', $stockOpnameID)
      ->select('ms_user.Name', 'ms_user.RoleID')
      ->get()->toArray();

    $sql->Detail = DB::table('ms_stock_opname_detail')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_opname_detail.ProductID')
      ->where('ms_stock_opname_detail.StockOpnameID', $stockOpnameID)
      ->select('ms_stock_opname_detail.ProductID', 'ms_product.ProductName', 'ms_stock_opname_detail.ProductLabel', 'ms_product.ProductImage', 'ms_stock_opname_detail.PurchasePrice', 'ms_stock_opname_detail.OldQty', 'ms_stock_opname_detail.NewQty', 'ms_stock_opname_detail.ConditionStock')
      ->get()->toArray();

    return $sql;
  }

  public function dataOfficer($officer, $opnameID)
  {
    $dataOpnameOfficer = array_map(function () {
      return func_get_args();
    }, $officer);
    foreach ($dataOpnameOfficer as $key => &$value) {
      $value = array_combine(['UserID'], $value);
      $dataOpnameOfficer[$key]['StockOpnameID'] = $opnameID;
    }

    return $dataOpnameOfficer;
  }

  public function dataStockOpnameDetail($productID, $label, $oldGoodStock, $newGoodStock, $oldBadStock, $newBadStock, $purchasePriceGoodStock, $purchasePriceBadStock, $opnameID)
  {
    $dataDetailGoodStock = array_map(function () {
      return func_get_args();
    }, $productID, $label, $oldGoodStock, $newGoodStock, $purchasePriceGoodStock);
    foreach ($dataDetailGoodStock as $key => &$value) {
      $value = array_combine(['ProductID', 'ProductLabel', 'OldQty', 'NewQty', 'PurchasePrice'], $value);
      $dataDetailGoodStock[$key]['StockOpnameID'] = $opnameID;
      $dataDetailGoodStock[$key]['ConditionStock'] = 'GOOD STOCK';
    }

    $dataDetailBadStock = array_map(function () {
      return func_get_args();
    }, $productID, $label, $oldBadStock, $newBadStock, $purchasePriceBadStock);
    foreach ($dataDetailBadStock as $key => &$value) {
      $value = array_combine(['ProductID', 'ProductLabel', 'OldQty', 'NewQty', 'PurchasePrice'], $value);
      $dataDetailBadStock[$key]['StockOpnameID'] = $opnameID;
      $dataDetailBadStock[$key]['ConditionStock'] = 'BAD STOCK';
    }
    $dataStockOpnameDetail = array_merge($dataDetailGoodStock, $dataDetailBadStock);

    $sortDataStockOpnameDetail = array_values(Arr::sort($dataStockOpnameDetail, function ($value) {
      return $value['ProductID'];
    }));

    return $sortDataStockOpnameDetail;
  }

  public function dataStockProduct($dataStockOpnameDetail, $distributorID)
  {
    $dataStockProduct = array_map(function ($data) use ($distributorID) {
      return array(
        'PurchaseID' => $data['StockOpnameID'],
        'ProductID' => $data['ProductID'],
        'ConditionStock' => $data['ConditionStock'],
        'Qty' => $data['NewQty'] - $data['OldQty'],
        'PurchasePrice' => $data['PurchasePrice'],
        'DistributorID' => $distributorID,
        'CreatedDate' => date('Y-m-d H:i:s'),
        'Type' => 'OPNAME',
        'LevelType' => 2
      );
    }, $dataStockOpnameDetail);

    return $dataStockProduct;
  }
}
