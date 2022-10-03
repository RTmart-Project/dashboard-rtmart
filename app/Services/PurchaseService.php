<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
  public function getStockPurchase($fromDate, $toDate, $filterTipe)
  {
    $mainSql = DB::table('ms_stock_purchase')
      ->join('ms_stock_purchase_detail', 'ms_stock_purchase_detail.PurchaseID', 'ms_stock_purchase.PurchaseID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
      ->leftJoin('ms_distributor as single_distributor', 'single_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->leftJoin('ms_distributor as combine_distributor', 'combine_distributor.DistributorID', 'ms_stock_purchase_detail.DistributorID')
      ->leftJoin('ms_suppliers as single_supplier', 'single_supplier.SupplierID', 'ms_stock_purchase.SupplierID')
      ->leftJoin('ms_suppliers as combine_supplier', 'combine_supplier.SupplierID', 'ms_stock_purchase_detail.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->selectRaw("
        ms_stock_purchase.PurchaseID,
        ms_stock_purchase.PurchasePlanID,
        ANY_VALUE(single_distributor.DistributorName) AS DistributorName,
        ms_stock_purchase.PurchaseDate,
        ms_stock_purchase.CreatedBy,
        ANY_VALUE(single_supplier.SupplierName) AS SupplierName,
        ms_stock_purchase.StatusID,
        ms_status_stock.StatusName,
        ms_stock_purchase.StatusBy,
        ms_stock_purchase.InvoiceNumber,
        ms_stock_purchase.InvoiceFile,
        ms_investor.InvestorName,
        ms_stock_purchase.Type,
        GROUP_CONCAT(DISTINCT combine_distributor.DistributorName SEPARATOR ', ') AS DistributorCombined,
        GROUP_CONCAT(DISTINCT combine_supplier.SupplierName SEPARATOR ', ') AS SupplierCombined
      ")
      ->groupBy('ms_stock_purchase.PurchaseID');

    if ($fromDate != '' && $toDate != '') {
      $mainSql->whereDate('ms_stock_purchase.PurchaseDate', '>=', $fromDate)
        ->whereDate('ms_stock_purchase.PurchaseDate', '<=', $toDate);
    }
    if ($filterTipe == "inbound") {
      $mainSql->where('ms_stock_purchase.Type', 'INBOUND');
    } elseif ($filterTipe == "retur") {
      $mainSql->where('ms_stock_purchase.Type', 'RETUR');
    }
    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $mainSql->where('single_distributor.Depo', '=', $depoUser);
    }
    if (Auth::user()->InvestorID != null) {
      $investorUser = Auth::user()->InvestorID;
      $mainSql->where('ms_stock_purchase.InvestorID', $investorUser);
    }

    $sql = $mainSql->get();

    foreach ($sql as $key => $value) {
      $grandTotal = 0;
      $detailPurchase = DB::table('ms_stock_purchase_detail')
        ->where('PurchaseID', $value->PurchaseID)
        ->select('Qty', 'PurchasePrice')
        ->get();

      foreach ($detailPurchase as $key => $detail) {
        $grandTotal += $detail->Qty * $detail->PurchasePrice;
      }
      $value->GrandTotal = $grandTotal;
    }

    return $sql;
  }

  public function getStockPurchaseAllProduct($fromDate, $toDate, $filterTipe)
  {
    $mainSql = DB::table('ms_stock_purchase')
      ->join('ms_stock_purchase_detail', 'ms_stock_purchase_detail.PurchaseID', 'ms_stock_purchase.PurchaseID')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_purchase_detail.ProductID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
      ->leftJoin('ms_distributor as single_distributor', 'single_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->leftJoin('ms_distributor as product_distributor', 'product_distributor.DistributorID', 'ms_stock_purchase_detail.DistributorID')
      ->leftJoin('ms_suppliers as single_supplier', 'single_supplier.SupplierID', 'ms_stock_purchase.SupplierID')
      ->leftJoin('ms_suppliers as product_supplier', 'product_supplier.SupplierID', 'ms_stock_purchase_detail.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->leftJoin('ms_status_stock as status_product', 'status_product.StatusID', 'ms_stock_purchase_detail.StatusStockID')
      ->selectRaw("
        ms_stock_purchase.PurchaseID,
        single_distributor.DistributorName,
        ms_stock_purchase.PurchaseDate,
        ms_stock_purchase.CreatedBy,
        single_supplier.SupplierName,
        ms_stock_purchase.StatusID,
        ms_status_stock.StatusName,
        status_product.StatusName AS StatusProduct,
        ms_stock_purchase.StatusBy,
        ms_stock_purchase.InvoiceNumber,
        ms_stock_purchase.InvoiceFile,
        ms_investor.InvestorName,
        ms_stock_purchase.Type,
        product_distributor.DistributorName AS DistributorProduct,
        product_supplier.SupplierName AS SupplierProduct,
        ms_stock_purchase_detail.ProductID,
        ms_product.ProductName,
        ms_stock_purchase_detail.ProductLabel,
        ms_stock_purchase_detail.Qty,
        ms_stock_purchase_detail.PurchasePrice,
        ms_stock_purchase_detail.Qty * ms_stock_purchase_detail.PurchasePrice AS SubTotalPrice
      ");

    if ($fromDate != '' && $toDate != '') {
      $mainSql->whereDate('ms_stock_purchase.PurchaseDate', '>=', $fromDate)
        ->whereDate('ms_stock_purchase.PurchaseDate', '<=', $toDate);
    }
    if ($filterTipe == "inbound") {
      $mainSql->where('ms_stock_purchase.Type', 'INBOUND');
    } elseif ($filterTipe == "retur") {
      $mainSql->where('ms_stock_purchase.Type', 'RETUR');
    }
    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $mainSql->where('single_distributor.Depo', '=', $depoUser);
    }
    if (Auth::user()->InvestorID != null) {
      $investorUser = Auth::user()->InvestorID;
      $mainSql->where('ms_stock_purchase.InvestorID', $investorUser);
    }

    $sql = $mainSql->get();

    foreach ($sql as $key => $value) {
      $grandTotal = 0;
      $detailPurchase = DB::table('ms_stock_purchase_detail')
        ->where('PurchaseID', $value->PurchaseID)
        ->select('Qty', 'PurchasePrice')
        ->get();

      foreach ($detailPurchase as $key => $detail) {
        $grandTotal += $detail->Qty * $detail->PurchasePrice;
      }
      $value->GrandTotal = $grandTotal;
    }

    return $sql;
  }

  public function getStockPurchaseByID($purchaseID)
  {
    $sql = DB::table('ms_stock_purchase')
      ->join('ms_stock_purchase_detail', 'ms_stock_purchase_detail.PurchaseID', 'ms_stock_purchase.PurchaseID')
      ->leftJoin('ms_distributor as single_distributor', 'single_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->leftJoin('ms_distributor as combine_distributor', 'combine_distributor.DistributorID', 'ms_stock_purchase_detail.DistributorID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
      ->leftJoin('ms_suppliers as single_supplier', 'single_supplier.SupplierID', 'ms_stock_purchase.SupplierID')
      ->leftJoin('ms_suppliers as combine_supplier', 'combine_supplier.SupplierID', 'ms_stock_purchase_detail.SupplierID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase.StatusID')
      ->where('ms_stock_purchase.PurchaseID', $purchaseID)
      ->selectRaw("
        ms_stock_purchase.PurchaseID,
        ms_stock_purchase.DistributorID,
        ms_stock_purchase.SupplierID,
        ANY_VALUE(single_distributor.DistributorName) AS DistributorName,
        ms_stock_purchase.PurchaseDate,
        ms_stock_purchase.EstimationArrive,
        ms_stock_purchase.CreatedBy,
        ms_stock_purchase.CreatedDate,
        ms_stock_purchase.StatusID,
        ANY_VALUE(single_supplier.SupplierName) AS SupplierName,
        ms_status_stock.StatusName,
        ms_stock_purchase.StatusBy,
        ms_stock_purchase.StatusDate,
        ms_stock_purchase.InvoiceNumber,
        ms_stock_purchase.InvoiceFile,
        ms_stock_purchase.InvestorID,
        ms_investor.InvestorName,
        GROUP_CONCAT(DISTINCT combine_distributor.DistributorName SEPARATOR ', ') AS DistributorCombined,
        GROUP_CONCAT(DISTINCT combine_supplier.SupplierName SEPARATOR ', ') AS SupplierCombined,
        (
          SELECT SUM(IF(ms_stock_purchase_detail.StatusStockID = 6, 1, 0))
          FROM ms_stock_purchase_detail
          WHERE PurchaseID = ms_stock_purchase.PurchaseID
        ) AS CountStatusProductApprove,
        (
          SELECT SUM(IF(ms_stock_purchase_detail.StatusStockID = 5, 1, 0))
          FROM ms_stock_purchase_detail
          WHERE PurchaseID = ms_stock_purchase.PurchaseID
        ) AS CountStatusProductNotConfirmed
      ")
      ->groupBy('ms_stock_purchase.PurchaseID')
      ->first();

    $sqlDetail = DB::table('ms_stock_purchase_detail')
      ->join('ms_stock_purchase', 'ms_stock_purchase.PurchaseID', 'ms_stock_purchase_detail.PurchaseID')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_purchase_detail.ProductID')
      ->leftJoin('ms_distributor as single_distributor', 'single_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
      ->leftJoin('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_purchase_detail.DistributorID')
      ->leftJoin('ms_suppliers as single_supplier', 'single_supplier.SupplierID', 'ms_stock_purchase.SupplierID')
      ->leftJoin('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase_detail.SupplierID')
      ->leftJoin('ms_status_stock', 'ms_status_stock.StatusID', 'ms_stock_purchase_detail.StatusStockID')
      ->where('ms_stock_purchase_detail.PurchaseID', $purchaseID)
      ->select('ms_stock_purchase_detail.PurchaseDetailID', 'ms_stock_purchase_detail.ProductID', 'ms_product.ProductName', 'ms_stock_purchase_detail.ProductLabel', 'ms_stock_purchase_detail.Qty', 'ms_stock_purchase_detail.PurchasePrice', 'ms_distributor.DistributorName', 'single_distributor.DistributorName as Distributor', 'ms_suppliers.SupplierName', 'ms_stock_purchase_detail.SupplierID', 'ms_stock_purchase.SupplierID as SingleSupplierID', 'single_supplier.SupplierName as Supplier', 'ms_stock_purchase_detail.StatusStockID', 'ms_status_stock.StatusName', 'ms_stock_purchase_detail.IsGIT', 'ms_stock_purchase_detail.Note', 'ms_stock_purchase_detail.ConfirmDate', 'ms_stock_purchase_detail.CreatedDate', 'ms_stock_purchase_detail.ConfirmBy')
      ->get()->toArray();

    $grandTotal = 0;

    foreach ($sqlDetail as $key => $value) {
      $grandTotal += $value->Qty * $value->PurchasePrice;
    }
    $sql->Detail = $sqlDetail;

    $sql->GrandTotal = $grandTotal;

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
      $maxPurchaseNumber = substr($max->PurchaseID, -6);
      $newPurchaseNumber = $maxPurchaseNumber + 1;
      $newPurchaseID = "PRCH-" . date('YmdHis') . "-" . str_pad($newPurchaseNumber, 6, '0', STR_PAD_LEFT);
    }

    return $newPurchaseID;
  }

  public function generatePurchasePlanID()
  {
    $max = DB::table('ms_purchase_plan')
      ->where('PurchasePlanID', 'like', '%PLAN%')
      ->selectRaw('MAX(PurchasePlanID) AS PurchasePlanID, MAX(CreatedDate) AS CreatedDate')
      ->first();

    $maxMonth = date('m', strtotime($max->CreatedDate));
    $now = date('m');

    if ($max->PurchasePlanID == null || (strcmp($maxMonth, $now) != 0)) {
      $newPurchasePlanID = "PLAN-" . date('YmdHis') . '-000001';
    } else {
      $maxPurchaseNumber = substr($max->PurchasePlanID, -6);
      $newPurchaseNumber = $maxPurchaseNumber + 1;
      $newPurchasePlanID = "PLAN-" . date('YmdHis') . "-" . str_pad($newPurchaseNumber, 6, '0', STR_PAD_LEFT);
    }

    return $newPurchasePlanID;
  }

  public function getPurchasePlan()
  {
    $sql = DB::table('ms_purchase_plan')
      ->join('ms_investor', 'ms_investor.InvestorID', 'ms_purchase_plan.InvestorID')
      ->join('ms_status_stock', 'ms_status_stock.StatusID', 'ms_purchase_plan.StatusID')
      ->leftJoin('ms_stock_purchase', 'ms_stock_purchase.PurchasePlanID', 'ms_purchase_plan.PurchasePlanID')
      ->select('ms_purchase_plan.PurchasePlanID', 'ms_stock_purchase.PurchaseID', 'ms_purchase_plan.InvestorID', 'ms_investor.Interest', 'ms_investor.InvestorName', 'ms_purchase_plan.PlanDate', 'ms_purchase_plan.CreatedBy', 'ms_purchase_plan.CreatedDate', 'ms_purchase_plan.ConfirmBy', 'ms_purchase_plan.ConfirmDate', 'ms_purchase_plan.StatusID', 'ms_status_stock.StatusName');

    return $sql;
  }

  public function getPurchasePlanDetail($purchasePlanID)
  {
    $investor = DB::table('ms_purchase_plan')
      ->join('ms_investor', 'ms_investor.InvestorID', 'ms_purchase_plan.InvestorID')
      ->where('PurchasePlanID', $purchasePlanID)
      ->select('Interest', 'PlanDate')
      ->first();

    $sql = DB::table('ms_purchase_plan_detail')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_purchase_plan_detail.DistributorID')
      ->join('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_purchase_plan_detail.SupplierID')
      ->join('ms_product', 'ms_product.ProductID', 'ms_purchase_plan_detail.ProductID')
      ->where('ms_purchase_plan_detail.PurchasePlanID', $purchasePlanID)
      ->selectRaw("
        '$investor->PlanDate' AS PlanDate,
        ms_purchase_plan_detail.DistributorID,
        ms_distributor.DistributorName,
        ms_purchase_plan_detail.SupplierID,
        ms_suppliers.SupplierName,
        ms_purchase_plan_detail.Note,
        ms_purchase_plan_detail.ProductID,
        ms_product.ProductName,
        ms_purchase_plan_detail.ProductLabel,
        ms_purchase_plan_detail.Qty,
        ms_purchase_plan_detail.QtyPO,
        ms_purchase_plan_detail.PurchasePrice,
        ms_purchase_plan_detail.PurchasePrice * ms_purchase_plan_detail.Qty AS PurchaseValue,
        ms_purchase_plan_detail.PercentInterest,
        ROUND(ms_purchase_plan_detail.PercentInterest * ms_purchase_plan_detail.PurchasePrice * ms_purchase_plan_detail.Qty / 100, 0) AS InterestValue,
        ms_purchase_plan_detail.SellingPrice,
        ms_purchase_plan_detail.SellingPrice * ms_purchase_plan_detail.Qty AS SellingValue,
        ms_purchase_plan_detail.PercentVoucher,
        ROUND(ms_purchase_plan_detail.PercentVoucher * ms_purchase_plan_detail.SellingPrice * ms_purchase_plan_detail.Qty / 100, 0) AS VoucherValue,
        (ms_purchase_plan_detail.SellingPrice * ms_purchase_plan_detail.Qty) - (ms_purchase_plan_detail.PurchasePrice * ms_purchase_plan_detail.Qty) AS GrossMargin,
        ms_purchase_plan_detail.SellingPrice - ms_purchase_plan_detail.PurchasePrice AS MarginCtn,
        ms_purchase_plan_detail.LastStock
      ")
      ->orderByRaw("ms_distributor.DistributorName, ms_purchase_plan_detail.ProductID");

    return $sql;
  }

  public function dataPurchaseDetail($productID, $labeling, $qty, $purchasePrice, $purchaseID)
  {
    $dataPurchaseDetail = [];
    $purchaseDetail = array_map(function () {
      return func_get_args();
    }, $productID, $labeling, $qty, $purchasePrice);

    foreach ($purchaseDetail as $key => $value) {
      $value = array_combine(['ProductID', 'ProductLabel', 'Qty', 'PurchasePrice'], $value);
      $value += ['PurchaseID' => $purchaseID];
      $value += ['Type' => 'INBOUND'];
      array_push($dataPurchaseDetail, $value);
    }

    return $dataPurchaseDetail;
  }

  public function confirmationPurchase($status, $purchaseID)
  {
    // $detail = DB::table('ms_stock_purchase_detail')
    //   ->join('ms_stock_purchase', 'ms_stock_purchase.PurchaseID', 'ms_stock_purchase_detail.PurchaseID')
    //   ->where('ms_stock_purchase_detail.PurchaseID', $purchaseID)
    //   ->select('ms_stock_purchase_detail.PurchaseID', 'ms_stock_purchase_detail.ProductID', 'ms_stock_purchase_detail.ProductLabel', 'ms_stock_purchase_detail.Qty', 'ms_stock_purchase_detail.PurchasePrice', 'ms_stock_purchase.DistributorID', 'ms_stock_purchase.InvestorID')->get()
    //   ->map(function ($item, $key) {
    //     $item->CreatedDate  = date('Y-m-d H:i:s');
    //     $item->Type = 'INBOUND';
    //     $item->LevelType = 3;
    //     return (array) $item;
    //   })
    //   ->all();

    // if ($status == "approved") {
    //   $confirm = DB::transaction(function () use ($purchaseID, $detail) {
    //     DB::table('ms_stock_purchase')
    //       ->where('PurchaseID', $purchaseID)
    //       ->update([
    //         'StatusID' => 2,
    //         'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
    //         'StatusDate' => date('Y-m-d H:i:s')
    //       ]);
    //     // $stockProductID = DB::table('ms_stock_product')->insertGetId($detail, 'StockProductID');
    //     foreach ($detail as $key => $value) {
    //       $stockProductID = DB::table('ms_stock_product')->insertGetId($value, 'StockProductID');
    //       $qtyBefore = DB::table('ms_stock_product')
    //         ->where('ms_stock_product.StockProductID', '!=', $stockProductID)
    //         ->where('ms_stock_product.ProductID', $value['ProductID'])
    //         ->where('ms_stock_product.ProductLabel', $value['ProductLabel'])
    //         ->where('ms_stock_product.DistributorID', $value['DistributorID'])
    //         ->where('ms_stock_product.InvestorID', $value['InvestorID'])
    //         ->where('ms_stock_product.ConditionStock', 'GOOD STOCK')
    //         ->selectRaw("IFNULL(SUM(ms_stock_product.Qty), 0) AS QtyBefore")
    //         ->first();

    //       DB::table('ms_stock_product_log')
    //         ->insert([
    //           'StockProductID' => $stockProductID,
    //           'ProductID' => $value['ProductID'],
    //           'QtyBefore' => $qtyBefore->QtyBefore,
    //           'QtyAction' => $value['Qty'],
    //           'QtyAfter' => $qtyBefore->QtyBefore + $value['Qty'],
    //           'PurchasePrice' => $value['PurchasePrice'],
    //           'SellingPrice' => 0,
    //           'DeliveryOrderDetailID' => 0,
    //           'CreatedDate' => date('Y-m-d H:i:s'),
    //           'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
    //           'ActionType' => 'INBOUND'
    //         ]);
    //     }
    //   });
    // } else {
    //   $confirm = DB::table('ms_stock_purchase')
    //     ->where('PurchaseID', $purchaseID)
    //     ->update([
    //       'StatusID' => 3,
    //       'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
    //       'StatusDate' => date('Y-m-d H:i:s')
    //     ]);
    // }

    if ($status === "approved") {
      $confirm = DB::table('ms_stock_purchase')
        ->where('PurchaseID', $purchaseID)
        ->update([
          'StatusID' => 2,
          'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
          'StatusDate' => date('Y-m-d H:i:s')
        ]);
    } else {
      $confirm = DB::transaction(function () use ($purchaseID) {
        DB::table('ms_stock_purchase')
          ->where('PurchaseID', $purchaseID)
          ->update([
            'StatusID' => 3,
            'StatusBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'StatusDate' => date('Y-m-d H:i:s')
          ]);
        DB::table('ms_stock_purchase_detail')
          ->where('PurchaseID', $purchaseID)
          ->update([
            'StatusStockID' => 7,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'ConfirmBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo
          ]);
      });
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

  public function getProducts($distributorID)
  {
    $products = DB::table('ms_product')
      ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
      ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
      ->where('ms_product.IsActive', 1)
      ->whereRaw("ProductID NOT IN (SELECT DISTINCT ProductID FROM ms_stock_product WHERE Qty > 0 AND DistributorID = '$distributorID')")
      ->orderBy('ms_product.ProductID');

    return $products;
  }

  public function getUsers()
  {
    $users = DB::table('ms_user')
      ->where('IsTesting', 0)
      ->whereNotIn('RoleID', ['SL', 'DRV', 'HLP'])
      ->select('Name', 'UserID')
      ->orderBy('Name');

    return $users;
  }

  public function getStocks()
  {
    $sql = DB::table('ms_stock_product')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_product.DistributorID')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_product.ProductID')
      ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_product.InvestorID')
      ->selectRaw("
        ANY_VALUE(ms_distributor.DistributorID) AS DistributorID,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(ms_product.ProductName) AS ProductName,
        ANY_VALUE(ms_product.ProductImage) AS ProductImage,
        ms_stock_product.InvestorID,
        ms_investor.InvestorName,
        ms_stock_product.ProductID,
        ms_stock_product.ProductLabel,
        SUM(CASE WHEN ms_stock_product.ConditionStock = 'GOOD STOCK' THEN ms_stock_product.Qty ELSE 0 END) AS GoodStock,
        SUM(CASE WHEN ms_stock_product.ConditionStock = 'BAD STOCK' THEN ms_stock_product.Qty ELSE 0 END) AS BadStock,
        SUM(CASE WHEN ms_stock_product.ConditionStock = 'GOOD STOCK' THEN ms_stock_product.Qty * ms_stock_product.PurchasePrice ELSE 0 END) AS NominalGoodStock,
        SUM(CASE WHEN ms_stock_product.ConditionStock = 'BAD STOCK' THEN ms_stock_product.Qty * ms_stock_product.PurchasePrice ELSE 0 END) AS NominalBadStock
      ")
      ->groupBy('ms_stock_product.DistributorID', 'ms_stock_product.InvestorID', 'ms_stock_product.ProductID', 'ms_stock_product.ProductLabel');

    return $sql;
  }

  public function getDetailStock($distributorID, $productID, $label)
  {
    $sql = DB::table('ms_stock_product_log')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_product_log.ProductID')
      ->join('ms_stock_product AS stock_product', 'stock_product.StockProductID', 'ms_stock_product_log.StockProductID')
      ->leftJoin('ms_stock_product AS reference_stock_product', 'reference_stock_product.StockProductID', 'ms_stock_product_log.ReferenceStockProductID')
      ->leftJoin('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID', 'ms_stock_product_log.DeliveryOrderDetailID')
      ->leftJoin('ms_stock_promo', 'ms_stock_promo.StockPromoID', 'ms_stock_product_log.StockPromoID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'stock_product.DistributorID')
      ->where('stock_product.DistributorID', $distributorID)
      ->where('ms_stock_product_log.ProductID', $productID)
      ->where('stock_product.ProductLabel', $label)
      ->select('stock_product.PurchaseID', 'stock_product.ConditionStock', 'ms_stock_product_log.PurchasePrice', 'ms_stock_product_log.ActionType', 'ms_stock_product_log.ActionBy', 'ms_stock_product_log.QtyBefore', 'ms_stock_product_log.QtyAction', 'ms_stock_product_log.QtyAfter', 'ms_stock_product_log.CreatedDate', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_distributor.DistributorName', 'reference_stock_product.PurchaseID AS RefPurchaseID', 'tx_merchant_delivery_order_detail.DeliveryOrderID', 'stock_product.ProductLabel', 'ms_stock_promo.StockPromoInboundID', 'ms_stock_product_log.StockPromoID')
      ->orderByDesc('ms_stock_product_log.CreatedDate')
      ->orderByDesc('ms_stock_product_log.StockProductLogID');

    return $sql;
  }

  public function getMutations()
  {
    $sql = DB::table('ms_stock_mutation')
      ->join('ms_distributor AS from_distributor', 'from_distributor.DistributorID', 'ms_stock_mutation.FromDistributor')
      ->join('ms_distributor AS to_distributor', 'to_distributor.DistributorID', 'ms_stock_mutation.ToDistributor')
      ->select('ms_stock_mutation.StockMutationID', 'ms_stock_mutation.MutationDate', 'ms_stock_mutation.CreatedDate', 'ms_stock_mutation.CreatedBy', 'ms_stock_mutation.PurchaseID', 'ms_stock_mutation.Notes', 'from_distributor.DistributorName AS FromDistributorName', 'to_distributor.DistributorName AS ToDistributorName');

    return $sql;
  }

  public function getMutationAllProduct()
  {
    $sql = DB::table('ms_stock_mutation')
      ->join('ms_stock_mutation_detail', 'ms_stock_mutation_detail.StockMutationID', 'ms_stock_mutation.StockMutationID')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_mutation_detail.ProductID')
      ->join('ms_distributor AS from_distributor', 'from_distributor.DistributorID', 'ms_stock_mutation.FromDistributor')
      ->join('ms_distributor AS to_distributor', 'to_distributor.DistributorID', 'ms_stock_mutation.ToDistributor')
      ->select('ms_stock_mutation.StockMutationID', 'ms_stock_mutation.MutationDate', 'ms_stock_mutation.CreatedDate', 'ms_stock_mutation.CreatedBy', 'ms_stock_mutation.PurchaseID', 'ms_stock_mutation.Notes', 'from_distributor.DistributorName AS FromDistributorName', 'to_distributor.DistributorName AS ToDistributorName', 'ms_stock_mutation_detail.ProductID', 'ms_product.ProductName', 'ms_stock_mutation_detail.ProductLabel', 'ms_stock_mutation_detail.Qty', 'ms_stock_mutation_detail.PurchasePrice', DB::raw("ms_stock_mutation_detail.Qty * ms_stock_mutation_detail.PurchasePrice as ValueProduct"));

    return $sql;
  }

  public function getMutationByID($mutationID)
  {
    $sql = $this->getMutations()->where('ms_stock_mutation.StockMutationID', $mutationID)->first();

    $sqlDetail = DB::table('ms_stock_mutation_detail')
      ->join('ms_product', 'ms_product.ProductID', 'ms_stock_mutation_detail.ProductID')
      ->where('ms_stock_mutation_detail.StockMutationID', $mutationID)
      ->select('ms_stock_mutation_detail.ProductID', 'ms_stock_mutation_detail.ProductLabel', 'ms_stock_mutation_detail.PurchasePrice', 'ms_stock_mutation_detail.Qty', 'ms_stock_mutation_detail.ConditionStock', 'ms_product.ProductName')->get()->toArray();

    $sql->Detail = $sqlDetail;

    return $sql;
  }
}
