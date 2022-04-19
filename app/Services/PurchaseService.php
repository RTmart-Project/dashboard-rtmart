<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
  public function getStockPurchase()
  {
    $sql = DB::table('ms_stock_purchase')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->join('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->select('ms_stock_purchase.PurchaseID', 'ms_distributor.DistributorName', 'ms_stock_purchase.PurchaseDate', 'ms_stock_purchase.CreatedBy', 'ms_suppliers.SupplierName', 'ms_stock_purchase.StatusID', 'ms_status_stock.StatusName', 'ms_stock_purchase.StatusBy', 'ms_stock_purchase.InvoiceFile');

    return $sql;
  }

  public function getStockPurchaseByID($purchaseID)
  {
    $sql = DB::table('ms_stock_purchase')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->join('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->where('ms_stock_purchase.PurchaseID', $purchaseID)
      ->select('ms_stock_purchase.PurchaseID', 'ms_stock_purchase.DistributorID', 'ms_stock_purchase.SupplierID', 'ms_distributor.DistributorName', 'ms_stock_purchase.PurchaseDate', 'ms_stock_purchase.CreatedBy', 'ms_stock_purchase.CreatedDate', 'ms_stock_purchase.StatusID', 'ms_suppliers.SupplierName', 'ms_status_stock.StatusName', 'ms_stock_purchase.StatusBy', 'ms_stock_purchase.StatusDate', 'ms_stock_purchase.InvoiceFile')->first();

    $sqlDetail = DB::table('ms_stock_purchase_detail')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_purchase_detail.ProductID')
      ->where('ms_stock_purchase_detail.PurchaseID', $purchaseID)
      ->select('ms_stock_purchase_detail.ProductID', 'ms_product.ProductName', 'ms_stock_purchase_detail.Qty', 'ms_stock_purchase_detail.PurchasePrice')->get()->toArray();

    $sql->Detail = $sqlDetail;

    return $sql;
  }

  public function generatePurchaseID()
  {
    $max = DB::table('ms_stock_purchase')
      ->where('PurchaseID', 'like', '%PRCH%')
      ->selectRaw('MAX(PurchaseID) AS PurchaseID, MAX(CreatedDate) AS CreatedDate')
      ->first();

    $maxMonth = date('m', strtotime($max->CreatedDate));
    $now = date('m');

    if ($max->PurchaseID == null || (strcmp($maxMonth, $now) != 0)) {
      $newPurchaseID = "PRCH-" . date('YmdHis') . '-000001';
    } else {
      $maxExpeditionID = substr($max->PurchaseID, -6);
      $newExpeditionID = $maxExpeditionID + 1;
      $newPurchaseID = "PRCH-" . date('YmdHis') . "-" . str_pad($newExpeditionID, 6, '0', STR_PAD_LEFT);
    }

    return $newPurchaseID;
  }

  public function dataPurchaseDetail($productID, $qty, $purchasePrice, $purchaseID)
  {
    $dataPurchaseDetail = [];
    $purchaseDetail = array_map(function () {
      return func_get_args();
    }, $productID, $qty, $purchasePrice);

    foreach ($purchaseDetail as $key => $value) {
      $value = array_combine(['ProductID', 'Qty', 'PurchasePrice'], $value);
      $value += ['PurchaseID' => $purchaseID];
      array_push($dataPurchaseDetail, $value);
    }

    return $dataPurchaseDetail;
  }

  public function confirmationPurchase($status, $purchaseID)
  {
    $detail = DB::table('ms_stock_purchase_detail')
      ->join('ms_stock_purchase', 'ms_stock_purchase.PurchaseID', 'ms_stock_purchase_detail.PurchaseID')
      ->where('ms_stock_purchase_detail.PurchaseID', $purchaseID)
      ->select('ms_stock_purchase_detail.PurchaseID', 'ms_stock_purchase_detail.ProductID', 'ms_stock_purchase_detail.Qty', 'ms_stock_purchase_detail.PurchasePrice', 'ms_stock_purchase.DistributorID')->get()
      ->map(function ($item, $key) {
        $item->CreatedDate  = date('Y-m-d H:i:s');
        return (array) $item;
      })
      ->all();;

    if ($status == "approved") {
      $confirm = DB::transaction(function () use ($purchaseID, $detail) {
        DB::table('ms_stock_purchase')
          ->where('PurchaseID', $purchaseID)
          ->update([
            'StatusID' => 2,
            'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'StatusDate' => date('Y-m-d H:i:s')
          ]);
        DB::table('ms_stock_product')->insert($detail);
      });
    } else {
      $confirm = DB::table('ms_stock_purchase')
        ->where('PurchaseID', $purchaseID)
        ->update([
          'StatusID' => 3,
          'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
          'StatusDate' => date('Y-m-d H:i:s')
        ]);
    }

    return $confirm;
  }

  public function getDistributors()
  {
    $sqlDistributors = DB::table('ms_distributor')
      ->where('ms_distributor.IsActive', 1)
      ->whereNotIn('ms_distributor.DistributorID', ['D-0000-000000']);

    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $sqlDistributors->where('ms_distributor.Depo', '=', $depoUser);
    }

    $distributors = $sqlDistributors;

    return $distributors;
  }

  public function getProducts()
  {
    $products = DB::table('ms_product')
      ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
      ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
      ->where('ms_product.IsActive', 1)
      ->orderBy('ms_product.ProductID');

    return $products;
  }
}
