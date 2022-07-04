<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MutationService
{
  public function generateMutationID()
  {
    $max = DB::table('ms_stock_mutation')
      ->selectRaw('MAX(StockMutationID) AS StockMutationID, MAX(CreatedDate) AS CreatedDate')
      ->first();

    $maxMonth = date('m', strtotime($max->CreatedDate));
    $now = date('m');

    if ($max->StockMutationID == null || (strcmp($maxMonth, $now) != 0)) {
      $newStockMutationID = "MTSI-" . date('YmdHis') . '-000001';
    } else {
      $maxStockMutationNumber = substr($max->StockMutationID, -6);
      $newStockMutationNumber = $maxStockMutationNumber + 1;
      $newStockMutationID = "MTSI-" . date('YmdHis') . "-" . str_pad($newStockMutationNumber, 6, '0', STR_PAD_LEFT);
    }

    return $newStockMutationID;
  }

  public function getProductByPurchaseID($purchaseID)
  {
    $sql = DB::table('ms_stock_product as stock_product')
      ->join('ms_product', 'ms_product.ProductID', 'stock_product.ProductID')
      ->where('stock_product.PurchaseID', $purchaseID)
      ->selectRaw("
              stock_product.StockProductID, 
              stock_product.PurchaseID, 
              stock_product.ProductID, 
              ms_product.ProductName,
              stock_product.PurchasePrice,
              (
                SELECT SUM(
                  IF(ActionType = 'INBOUND', QtyAction, 0) - IF(ActionType = 'OUTBOUND', QtyAction, 0) + 
                  IF(ActionType = 'RETUR', QtyAction, 0) + IF(ActionType = 'MUTASI', QtyAction, 0)
                )
                FROM ms_stock_product_log
                JOIN ms_stock_product ON ms_stock_product.StockProductID = ms_stock_product_log.StockProductID
                WHERE (
                  ms_stock_product_log.StockProductID IN (
                    SELECT StockProductID FROM ms_stock_product_log 
                    WHERE ReferenceStockProductID = stock_product.StockProductID OR StockProductID = stock_product.StockProductID
                  ) OR 
                  ms_stock_product_log.ReferenceStockProductID IN (
                    SELECT StockProductID FROM ms_stock_product_log 
                    WHERE ReferenceStockProductID = stock_product.StockProductID OR StockProductID = stock_product.StockProductID
                  )
                ) AND ms_stock_product.DistributorID = (SELECT DISTINCT DistributorID FROM ms_stock_product WHERE PurchaseID = '$purchaseID')
              ) AS QtyReady
            ")
      ->get();

    return $sql;
  }

  public function dataMutationDetail($purchaseID, $productId, $qty, $mutationID)
  {
    $detail = [];
    foreach ($productId as $key => $value) {
      $dataDetail = DB::table('ms_stock_purchase_detail')
        ->where('PurchaseID', $purchaseID)
        ->where('ProductID', $value)
        ->select('ProductID', 'ProductLabel', 'PurchasePrice', 'ConditionStock')
        ->get()->toArray();
      array_push($detail, $dataDetail);
    }

    $dataMutationDetail = array_map(function () {
      return func_get_args();
    }, $productId, $qty);

    $arrayInverted = array();
    foreach ($detail as $key => $value) {
      $arrayInverted[$value[0]->ProductID] = $value;
    }

    foreach ($dataMutationDetail as &$element) {
      $element =  array_combine(['ProductID', 'Qty'], $element);
      $arrayElement = $arrayInverted[$element['ProductID']];
      $element['StockMutationID'] = $mutationID;
      $element['ProductLabel'] = $arrayElement[0]->ProductLabel;
      $element['PurchasePrice'] = $arrayElement[0]->PurchasePrice;
      $element['ConditionStock'] = $arrayElement[0]->ConditionStock;
    }

    return $dataMutationDetail;
  }

  public function dataStockProduct($dataMutationDetail, $purchase, $toDistributor, $dateNow)
  {
    $dataStockProduct = [];
    foreach ($dataMutationDetail as $key => $value) {
      $data = [
        [
          'PurchaseID' => $value['StockMutationID'],
          'InvestorID' => $purchase->InvestorID,
          'ProductID' => $value['ProductID'],
          'ProductLabel' => $value['ProductLabel'],
          'ConditionStock' => $value['ConditionStock'],
          'Qty' => $value['Qty'],
          'PurchasePrice' => $value['PurchasePrice'],
          'DistributorID' => $toDistributor,
          'CreatedDate' => $dateNow,
          'Type' => 'MUTASI',
          'LevelType' => 4
        ],
        [
          'PurchaseID' => $value['StockMutationID'],
          'InvestorID' => $purchase->InvestorID,
          'ProductID' => $value['ProductID'],
          'ProductLabel' => $value['ProductLabel'],
          'ConditionStock' => $value['ConditionStock'],
          'Qty' => 0,
          'PurchasePrice' => $value['PurchasePrice'],
          'DistributorID' => $purchase->DistributorID,
          'CreatedDate' => $dateNow,
          'Type' => 'MUTASI',
          'LevelType' => 4
        ]
      ];
      array_push($dataStockProduct, $data);
    }

    return $dataStockProduct;
  }
}
