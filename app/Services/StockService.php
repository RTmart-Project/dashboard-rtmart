<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
  public function generateStockPromoInboundID()
  {
    $max = DB::table('ms_stock_promo_inbound')
      ->selectRaw('MAX(StockPromoInboundID) AS StockPromoInboundID, MAX(CreatedDate) AS CreatedDate')
      ->first();

    $maxMonth = date('m', strtotime($max->CreatedDate));
    $now = date('m');

    if ($max->StockPromoInboundID == null || (strcmp($maxMonth, $now) != 0)) {
      $newStockPromoInboundID = "PRMO-" . date('YmdHis') . '-000001';
    } else {
      $maxStockPromoNumber = substr($max->StockPromoInboundID, -6);
      $newStockPromoNumber = $maxStockPromoNumber + 1;
      $newStockPromoInboundID = "PRMO-" . date('YmdHis') . "-" . str_pad($newStockPromoNumber, 6, '0', STR_PAD_LEFT);
    }

    return $newStockPromoInboundID;
  }

  public function stockPromoDetailByPurchase($purchaseID, $stockPromoInboundID, $productId, $qty, $purchasePrice, $sellingPrice)
  {
    $detail = [];
    foreach ($productId as $key => $value) {
      $dataDetail = DB::table('ms_stock_purchase_detail')
        ->join('ms_product', 'ms_product.ProductID', 'ms_stock_purchase_detail.ProductID')
        ->where('ms_stock_purchase_detail.PurchaseID', $purchaseID)
        ->where('ms_stock_purchase_detail.ProductID', $value)
        ->select('ms_stock_purchase_detail.ProductID', 'ms_stock_purchase_detail.ProductLabel', 'ms_stock_purchase_detail.ConditionStock', 'ms_product.ProductUOMDesc')
        ->get()->toArray();
      array_push($detail, $dataDetail);
    }

    $dataStockPromoDetail = array_map(function () {
      return func_get_args();
    }, $productId, $qty, $purchasePrice, $sellingPrice);

    $arrayInverted = array();
    foreach ($detail as $key => $value) {
      $arrayInverted[$value[0]->ProductID] = $value;
    }

    foreach ($dataStockPromoDetail as $key => &$element) {
      $element =  array_combine(['ProductID', 'Qty', 'PurchasePrice', 'SellingPrice'], $element);
      $arrayElement = $arrayInverted[$element['ProductID']];
      $element['StockPromoInboundID'] = $stockPromoInboundID;
      $element['ProductLabel'] = $arrayElement[0]->ProductLabel;
      $element['ConditionStock'] = $arrayElement[0]->ConditionStock;
      $element['Qty'] *= $arrayElement[0]->ProductUOMDesc;
      $element['Type'] = 'INBOUND FROM PURCHASE';
      if ($element['Qty'] == 0) {
        unset($dataStockPromoDetail[$key]);
      }
    }

    return $dataStockPromoDetail;
  }

  public function updateStockProduct($stockPromoInboundID, $purchaseID, $dataStockPromoDetail, $sqlPurchase)
  {
    $updateStockProduct = DB::transaction(function () use ($stockPromoInboundID, $purchaseID, $dataStockPromoDetail, $sqlPurchase) {
      foreach ($dataStockPromoDetail as $key => $value) {

        $uomDesc = DB::table('ms_product')->where('ProductID', $value['ProductID'])->select('ProductUOMDesc')->first();

        $sqlStockProduct = DB::table('ms_stock_product')
          ->where('DistributorID', $sqlPurchase->DistributorID)
          ->where('InvestorID', $sqlPurchase->InvestorID)
          ->where('ProductID', $value['ProductID'])
          ->where('ProductLabel', $value['ProductLabel'])
          ->where('ConditionStock', $value['ConditionStock']);

        $sqlStockPromo = DB::table('ms_stock_promo')
          ->where('DistributorID', $sqlPurchase->DistributorID)
          ->where('InvestorID', $sqlPurchase->InvestorID)
          ->where('ProductID', $value['ProductID'])
          ->where('ProductLabel', $value['ProductLabel'])
          ->where('ConditionStock', $value['ConditionStock']);

        $stockProduct = (clone $sqlStockProduct)
          ->where('PurchaseID', $purchaseID)
          ->select('Qty', 'StockProductID', 'PurchasePrice')->first();

        $sumStockProduct = (clone $sqlStockProduct)->where('Qty', '>', 0)->sum('Qty');

        $sumStockPromo = (clone $sqlStockPromo)->where('Qty', '>', 0)->sum('Qty');

        $qtyUpdated = $stockProduct->Qty - ($value['Qty'] / $uomDesc->ProductUOMDesc);

        DB::table('ms_stock_product')
          ->where('PurchaseID', $purchaseID)
          ->where('DistributorID', $sqlPurchase->DistributorID)
          ->where('InvestorID', $sqlPurchase->InvestorID)
          ->where('ProductID', $value['ProductID'])
          ->where('ProductLabel', $value['ProductLabel'])
          ->where('ConditionStock', $value['ConditionStock'])
          ->update(['Qty' => $qtyUpdated]);

        $stockPromoID = DB::table('ms_stock_promo')->insertGetId([
          'StockPromoInboundID' => $stockPromoInboundID,
          'InvestorID' => $sqlPurchase->InvestorID,
          'ProductID' => $value['ProductID'],
          'ProductLabel' => $value['ProductLabel'],
          'ConditionStock' => $value['ConditionStock'],
          'Qty' => $value['Qty'],
          'PurchasePrice' => $value['PurchasePrice'],
          'SellingPrice' => $value['SellingPrice'],
          'DistributorID' => $sqlPurchase->DistributorID,
          'CreatedDate' => date('Y-m-d H:i:s'),
          'Type' => 'INBOUND FROM PURCHASE',
          'LevelType' => 2
        ]);

        DB::table('ms_stock_promo_log')
          ->insert([
            'StockPromoID' => $stockPromoID,
            'ProductID' => $value['ProductID'],
            'QtyBefore' => $sumStockPromo,
            'QtyAction' => $value['Qty'],
            'QtyAfter' => $sumStockPromo + $value['Qty'],
            'PurchasePrice' => $value['PurchasePrice'],
            'SellingPrice' => $value['SellingPrice'],
            'StockProductID' => $stockProduct->StockProductID,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'ActionType' => 'INBOUND FROM PURCHASE'
          ]);

        DB::table('ms_stock_product_log')
          ->insert([
            'StockProductID' => $stockProduct->StockProductID,
            'ProductID' => $value['ProductID'],
            'QtyBefore' => $sumStockProduct,
            'QtyAction' => $value['Qty'] / $uomDesc->ProductUOMDesc * -1,
            'QtyAfter' => $sumStockProduct - ($value['Qty'] / $uomDesc->ProductUOMDesc),
            'PurchasePrice' => $stockProduct->PurchasePrice,
            'SellingPrice' => 0,
            'StockPromoID' => $stockPromoID,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'ActionType' => 'MOVING TO STOCK PROMO'
          ]);
      }
    });

    return $updateStockProduct;
  }
}
