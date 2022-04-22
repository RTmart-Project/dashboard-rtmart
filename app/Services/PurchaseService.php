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
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
      ->join('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->select('ms_stock_purchase.PurchaseID', 'ms_distributor.DistributorName', 'ms_stock_purchase.PurchaseDate', 'ms_stock_purchase.CreatedBy', 'ms_suppliers.SupplierName', 'ms_stock_purchase.StatusID', 'ms_status_stock.StatusName', 'ms_stock_purchase.StatusBy', 'ms_stock_purchase.InvoiceNumber', 'ms_stock_purchase.InvoiceFile', 'ms_investor.InvestorName');

    return $sql;
  }

  public function getStockPurchaseByID($purchaseID)
  {
    $sql = DB::table('ms_stock_purchase')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
      ->join('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->where('ms_stock_purchase.PurchaseID', $purchaseID)
      ->select('ms_stock_purchase.PurchaseID', 'ms_stock_purchase.DistributorID', 'ms_stock_purchase.SupplierID', 'ms_distributor.DistributorName', 'ms_stock_purchase.PurchaseDate', 'ms_stock_purchase.CreatedBy', 'ms_stock_purchase.CreatedDate', 'ms_stock_purchase.StatusID', 'ms_suppliers.SupplierName', 'ms_status_stock.StatusName', 'ms_stock_purchase.StatusBy', 'ms_stock_purchase.StatusDate', 'ms_stock_purchase.InvoiceNumber', 'ms_stock_purchase.InvoiceFile', 'ms_stock_purchase.InvestorID', 'ms_investor.InvestorName')->first();

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
      $value += ['Type' => 'INBOUND'];
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
        $item->Type = 'INBOUND';
        $item->LevelType = 3;
        return (array) $item;
      })
      ->all();

    if ($status == "approved") {
      $confirm = DB::transaction(function () use ($purchaseID, $detail) {
        DB::table('ms_stock_purchase')
          ->where('PurchaseID', $purchaseID)
          ->update([
            'StatusID' => 2,
            'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'StatusDate' => date('Y-m-d H:i:s')
          ]);
        // $stockProductID = DB::table('ms_stock_product')->insertGetId($detail, 'StockProductID');
        foreach ($detail as $key => $value) {
          $stockProductID = DB::table('ms_stock_product')->insertGetId($value, 'StockProductID');
          $qtyBefore = DB::table('ms_stock_product')
            ->where('ms_stock_product.StockProductID', '!=', $stockProductID)
            ->where('ms_stock_product.ProductID', $value['ProductID'])
            ->where('ms_stock_product.DistributorID', $value['DistributorID'])
            ->selectRaw("IFNULL(SUM(ms_stock_product.Qty), 0) AS QtyBefore")
            ->first();

          DB::table('ms_stock_product_log')
            ->insert([
              'StockProductID' => $stockProductID,
              'ProductID' => $value['ProductID'],
              'QtyBefore' => $qtyBefore->QtyBefore,
              'QtyAction' => $value['Qty'],
              'QtyAfter' => $qtyBefore->QtyBefore + $value['Qty'],
              'PurchasePrice' => $value['PurchasePrice'],
              'SellingPrice' => 0,
              'DeliveryOrderDetailID' => 0,
              'CreatedDate' => date('Y-m-d H:i:s'),
              'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
              'ActionType' => 'INBOUND'
            ]);
        }
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

  public function getStocks()
  {
    $sql = DB::table('ms_stock_product')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_product.DistributorID')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_product.ProductID')
      ->selectRaw("
        ANY_VALUE(ms_distributor.DistributorID) AS DistributorID,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(ms_product.ProductName) AS ProductName,
        ANY_VALUE(ms_product.ProductImage) AS ProductImage, 
        ms_stock_product.ProductID, 
        SUM(CASE WHEN ms_stock_product.ConditionStock = 'GOOD STOCK' THEN ms_stock_product.Qty ELSE 0 END) AS GoodStock,
        SUM(CASE WHEN ms_stock_product.ConditionStock = 'BAD STOCK' THEN ms_stock_product.Qty ELSE 0 END) AS BadStock
      ")
      ->groupBy('ms_stock_product.DistributorID', 'ms_stock_product.ProductID');

    return $sql;
  }

  public function getDetailStock($distributorID, $productID)
  {
    $sql = DB::table('ms_stock_product_log')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_product_log.ProductID')
      ->join('ms_stock_product AS stock_product', 'stock_product.StockProductID', 'ms_stock_product_log.StockProductID')
      ->leftJoin('ms_stock_product AS reference_stock_product', 'reference_stock_product.StockProductID', 'ms_stock_product_log.ReferenceStockProductID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'stock_product.DistributorID')
      ->where('stock_product.DistributorID', $distributorID)
      ->where('ms_stock_product_log.ProductID', $productID)
      ->select('stock_product.PurchaseID', 'stock_product.ConditionStock', 'ms_stock_product_log.PurchasePrice', 'ms_stock_product_log.ActionType', 'ms_stock_product_log.ActionBy', 'ms_stock_product_log.QtyBefore', 'ms_stock_product_log.QtyAction', 'ms_stock_product_log.QtyAfter', 'ms_stock_product_log.CreatedDate', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_distributor.DistributorName', 'reference_stock_product.PurchaseID AS RefPurchaseID');

    return $sql;
  }
}
