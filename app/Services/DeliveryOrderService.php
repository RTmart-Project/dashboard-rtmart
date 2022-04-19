<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryOrderService
{
  public function generateExpeditionID()
  {
    $max = DB::table('tx_merchant_expedition_log')
      ->selectRaw('MAX(MerchantExpeditionID) AS MerchantExpeditionID, MAX(ProcessTime) AS ProcessTime')
      ->first();

    $maxMonth = date('m', strtotime($max->ProcessTime));
    $now = date('m');

    if ($max->MerchantExpeditionID == null || (strcmp($maxMonth, $now) != 0)) {
      $newMerchantExpeditionID = "EXPD-" . date('YmdHis') . '-000001';
    } else {
      $maxExpeditionID = substr($max->MerchantExpeditionID, -6);
      $newExpeditionID = $maxExpeditionID + 1;
      $newMerchantExpeditionID = "EXPD-" . date('YmdHis') . "-" . str_pad($newExpeditionID, 6, '0', STR_PAD_LEFT);
    }

    return $newMerchantExpeditionID;
  }

  public function insertTable($tableName, $data)
  {
    $sql = DB::table($tableName)
      ->insert($data);

    return $sql;
  }

  public function updateDeliveryOrder($deliveryOrderID, $statusDO, $driverID, $helperID, $vehicleID, $vehicleLicensePlate)
  {
    $sql = DB::table('tx_merchant_delivery_order')
      ->where('DeliveryOrderID', $deliveryOrderID)
      ->update([
        'StatusDO' => $statusDO,
        'DriverID' => $driverID,
        'HelperID' => $helperID,
        'VehicleID' => $vehicleID,
        'VehicleLicensePlate' => $vehicleLicensePlate
      ]);

    return $sql;
  }

  public function updateDetailDeliveryOrder($deliveryOrderID, $productID, $qty, $statusExpedition, $distributor)
  {
    $sql = DB::table('tx_merchant_delivery_order_detail')
      ->where('DeliveryOrderID', $deliveryOrderID)
      ->where('ProductID', $productID)
      ->update([
        'Qty' => $qty,
        'StatusExpedition' => $statusExpedition,
        'Distributor' => $distributor
      ]);

    return $sql;
  }

  public function insertDeliveryOrderLog($deliveryOrderID, $statusDO, $driverID, $helperID, $vehicleID, $vehicleLicensePlate, $actionBy)
  {
    $getSO = DB::table('tx_merchant_delivery_order')
      ->where('DeliveryOrderID', $deliveryOrderID)
      ->select('StockOrderID')
      ->first();

    $sql = DB::table('tx_merchant_delivery_order_log')
      ->insert([
        'StockOrderID' => $getSO->StockOrderID,
        'DeliveryOrderID' => $deliveryOrderID,
        'StatusDO' => $statusDO,
        'DriverID' => $driverID,
        'HelperID' => $helperID,
        'VehicleID' => $vehicleID,
        'VehicleLicensePlate' => $vehicleLicensePlate,
        'ActionBy' => $actionBy
      ]);

    return $sql;
  }

  public function insertExpeditionDetail($merchantExpeditionID, $deliveryOrderID, $productID, $statusExpeditionDetail)
  {
    $doDetailID = DB::table('tx_merchant_delivery_order_detail')
      ->where('DeliveryOrderID', $deliveryOrderID)
      ->where('ProductID', $productID)
      ->select('DeliveryOrderDetailID')
      ->first();

    $sql = DB::table('tx_merchant_expedition_detail')
      ->insert([
        'MerchantExpeditionID' => $merchantExpeditionID,
        'DeliveryOrderDetailID' => $doDetailID->DeliveryOrderDetailID,
        'StatusExpeditionDetail' => $statusExpeditionDetail
      ]);

    return $sql;
  }

  public function getDOfromDetailDO($deliveryOrderDetailID)
  {
    $deliveryOrderID = DB::table('tx_merchant_delivery_order_detail')
      ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->select('tx_merchant_delivery_order.StockOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_order.PaymentMethodID', 'tx_merchant_delivery_order_detail.ProductID', 'tx_merchant_delivery_order_detail.Price', 'tx_merchant_order.DistributorID')
      ->where('DeliveryOrderDetailID', $deliveryOrderDetailID)
      ->first();

    return $deliveryOrderID;
  }

  public function getArea()
  {
    $sql = DB::table('ms_area')
      ->distinct('City')
      ->whereNotNull('Province')
      ->whereIn('City', ['Bandung', 'Bandung (KAB)', 'Bandung Barat (KAB)', 'Kepulauan Seribu (KAB)', 'Jakarta Pusat', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Utara', 'Jakarta Timur', 'Bogor', 'Depok', 'Tangerang', 'Tangerang (KAB)', 'Tangerang Selatan', 'Bekasi', 'Bekasi (KAB)'])
      ->select(DB::raw("ANY_VALUE(Subdistrict) AS Subdistrict"), 'City')
      ->orderBy('City')
      ->get();

    $collection = collect($sql);

    return $collection;
  }

  public function getMultipleDeliveryOrder($stringDeliveryOrderID, $arrayDeliveryOrderID)
  {
    $sql1 = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->whereRaw("tx_merchant_delivery_order.StockOrderID IN (
          SELECT StockOrderID 
          FROM tx_merchant_delivery_order 
          WHERE DeliveryOrderID IN ($stringDeliveryOrderID)
      )")
      ->selectRaw("
        ANY_VALUE(tx_merchant_order.StockOrderID) AS StockOrderID,
        ANY_VALUE(tx_merchant_order.TotalPrice) AS TotalPrice,
        IFNULL(SUM(IF(tx_merchant_delivery_order_detail.StatusExpedition != 'S029' AND tx_merchant_delivery_order_detail.StatusExpedition != 'S037', tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price, 0)), 0) AS SumPriceCreatedDO,
        COUNT(DISTINCT CASE WHEN tx_merchant_delivery_order.StatusDO IN ('S024', 'S026', 'S028') THEN tx_merchant_delivery_order.DeliveryOrderID END) AS CountCreatedDO
      ")
      ->groupBy('tx_merchant_order.StockOrderID')->toSql();

    $sql2 = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
      ->whereRaw("tx_merchant_delivery_order.StockOrderID IN (
          SELECT StockOrderID 
          FROM tx_merchant_delivery_order 
          WHERE DeliveryOrderID IN ($stringDeliveryOrderID)
      )")
      ->selectRaw("
        ANY_VALUE(tx_merchant_delivery_order.StockOrderID) AS StockOrderID,
        ANY_VALUE(tx_merchant_delivery_order.DeliveryOrderID) AS DeliveryOrderID,
        ANY_VALUE(tx_merchant_delivery_order_detail.ProductID) AS ProductID,
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO != 'S026' AND tx_merchant_delivery_order.StatusDO != 'S028' AND tx_merchant_delivery_order_detail.StatusExpedition != 'S029', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDONotBatal
      ")
      ->groupBy('tx_merchant_delivery_order.StockOrderID', 'tx_merchant_delivery_order_detail.ProductID')->toSql();

    $sql = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_delivery_order_detail', function ($join) use ($arrayDeliveryOrderID) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_detail.StatusExpedition', 'S029');
        $join->whereIn('tx_merchant_delivery_order_detail.DeliveryOrderID', $arrayDeliveryOrderID);
      })
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->join('tx_merchant_order_detail as tmod', function ($join) {
        $join->on('tmod.StockOrderID', 'tx_merchant_delivery_order.StockOrderID');
        $join->on('tmod.ProductID', 'tx_merchant_delivery_order_detail.ProductID');
      })
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->join(DB::raw("($sql2) as sql2"), function ($join) {
        $join->on('tx_merchant_delivery_order.StockOrderID', '=', 'sql2.StockOrderID');
        $join->on('tx_merchant_delivery_order_detail.ProductID', '=', 'sql2.ProductID');
      })
      ->join(DB::raw("($sql1) as sql1"), function ($join) {
        $join->on('tx_merchant_delivery_order.StockOrderID', '=', 'sql1.StockOrderID');
      })
      ->leftJoin('ms_stock_product', function ($join) {
        $join->on('ms_stock_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID');
        $join->on('ms_stock_product.DistributorID', 'tx_merchant_order.DistributorID');
      })
      ->selectRaw("
        ANY_VALUE(tx_merchant_delivery_order.StockOrderID) AS StockOrderID,
        ANY_VALUE(tx_merchant_delivery_order.DeliveryOrderID) AS DeliveryOrderID,
        ANY_VALUE(tx_merchant_delivery_order_detail.DeliveryOrderDetailID) AS DeliveryOrderDetailID,
        ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
        ANY_VALUE(ms_merchant_account.MerchantID) AS MerchantID,
        ANY_VALUE(tx_merchant_order.DistributorID) AS DistributorID,
        ANY_VALUE(ms_merchant_account.PhoneNumber) AS PhoneNumber,
        ANY_VALUE(sql1.TotalPrice) AS TotalPrice,
        ANY_VALUE(sql1.SumPriceCreatedDO) AS SumPriceCreatedDO,
        ANY_VALUE(sql1.CountCreatedDO) AS CountCreatedDO,
        ANY_VALUE(ms_distributor.IsHaistar) AS IsHaistar,
        ANY_VALUE(tx_merchant_delivery_order_detail.ProductID) AS ProductID,
        IFNULL(SUM(ms_stock_product.Qty), 0) AS QtyStock,
        ANY_VALUE(tx_merchant_delivery_order_detail.Qty) AS QtyDO,
        ANY_VALUE(tx_merchant_delivery_order_detail.Price) AS PriceDO,
        ANY_VALUE(ms_product.ProductName) AS ProductName,
        ANY_VALUE(ms_product.ProductImage) AS ProductImage,
        ANY_VALUE(tmod.PromisedQuantity) AS PromisedQty,
        ANY_VALUE(tmod.Nett) AS Nett,
        ANY_VALUE(sql2.QtyDONotBatal) AS QtyDONotBatal
      ")
      ->groupBy('tx_merchant_delivery_order.StockOrderID', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.ProductID');

    return $sql;
  }

  public function getDeliveryRequest()
  {
    $sql = DB::table('tx_merchant_delivery_order AS tmdo')
      ->join('tx_merchant_delivery_order_detail', function ($join) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tmdo.DeliveryOrderID');
        $join->whereIn('tx_merchant_delivery_order_detail.StatusExpedition', ['S029', 'S037']);
      })
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tmdo.StockOrderID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->leftJoin('ms_area', 'ms_area.AreaID', 'ms_merchant_account.AreaID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
      ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'ms_merchant_account.MerchantID')
      ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', 'ms_distributor_merchant_grade.GradeID')
      ->whereIn('tmdo.StatusDO', ['S024', 'S026', 'S028'])
      ->selectRaw("
        (
          SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
          WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
          AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID
        ) AS UrutanDO,
        tmdo.StockOrderID,
        tmdo.DeliveryOrderID,
        tmdo.CreatedDate,
        DATEDIFF(CURDATE(), DATE(tmdo.CreatedDate)) AS DueDate,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName, 
        ANY_VALUE(tx_merchant_order.MerchantID) AS MerchantID,
        ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
        GROUP_CONCAT(CONCAT(ms_product.ProductName, ' (', tx_merchant_delivery_order_detail.Qty, 'pcs)') SEPARATOR ',<br> ') AS Products,
        ANY_VALUE(ms_merchant_account.PhoneNumber) AS PhoneNumber,
        ANY_VALUE(ms_merchant_account.StoreAddress) AS StoreAddress,
        ANY_VALUE(CONCAT(ms_sales.SalesCode, ' - ', ms_sales.SalesName)) AS Sales,
        ANY_VALUE(ms_merchant_account.Partner) AS Partner,
        ANY_VALUE(IFNULL(ms_distributor_grade.Grade, 'Retail')) AS Grade,
        ANY_VALUE(tx_merchant_order.OrderLatitude) AS OrderLatitude,
        ANY_VALUE(tx_merchant_order.OrderLongitude) AS OrderLongitude,
        ANY_VALUE(CONCAT(ms_area.AreaName, ', ', ms_area.Subdistrict)) AS Area
      ")
      ->groupBy('tmdo.DeliveryOrderID');

    return $sql;
  }

  public function reduceStock($productID, $distributorID, $qtyReduce, $deliveryOrderDetailID)
  {
    $sql = DB::table('ms_stock_product')
      ->where('ProductID', $productID)
      ->where('DistributorID', $distributorID)
      ->where('Qty', '!=', 0)
      ->orderBy('CreatedDate')
      ->select('StockProductID', 'Qty', 'PurchasePrice')->first();

    $sellingPrice = DB::table('tx_merchant_delivery_order_detail')
      ->where('DeliveryOrderDetailID', $deliveryOrderDetailID)
      ->select('Price')->first();

    $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;

    $qtyAfter = $sql->Qty - $qtyReduce;

    if ($qtyAfter < 0) {
      DB::table('ms_stock_product')
        ->where('StockProductID', $sql->StockProductID)
        ->update([
          'Qty' => 0
        ]);
      DB::table('ms_stock_product_log')
        ->insert([
          'StockProductID' => $sql->StockProductID,
          'ProductID' => $productID,
          'QtyBefore' => $sql->Qty,
          'QtyAction' => $sql->Qty,
          'QtyAfter' => 0,
          'PurchasePrice' => $sql->PurchasePrice,
          'SellingPrice' => $sellingPrice->Price,
          'DeliveryOrderDetailID' => $deliveryOrderDetailID,
          'CreatedDate' => date('Y-m-d H:i:s'),
          'ActionBy' => $user,
          'ActionType' => 'REDUCE'
        ]);
      $this->reduceStock($productID, $distributorID, $qtyAfter * (-1), $deliveryOrderDetailID);
    } else {
      DB::table('ms_stock_product')
        ->where('StockProductID', $sql->StockProductID)
        ->update([
          'Qty' => $qtyAfter
        ]);
      DB::table('ms_stock_product_log')
        ->insert([
          'StockProductID' => $sql->StockProductID,
          'ProductID' => $productID,
          'QtyBefore' => $sql->Qty,
          'QtyAction' => $qtyReduce,
          'QtyAfter' => $qtyAfter,
          'PurchasePrice' => $sql->PurchasePrice,
          'SellingPrice' => $sellingPrice->Price,
          'DeliveryOrderDetailID' => $deliveryOrderDetailID,
          'CreatedDate' => date('Y-m-d H:i:s'),
          'ActionBy' => $user,
          'ActionType' => 'REDUCE'
        ]);
    }

    return;
  }

  public function validateRemainingQty($stockOrderID, $deliveryOrderID, $productID, $qty, $validateFor)
  {
    $sql = DB::table('tx_merchant_order_detail')
      ->leftJoin('tx_merchant_delivery_order', 'tx_merchant_order_detail.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->leftJoin('tx_merchant_delivery_order_detail', function ($join) use ($productID) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
        $join->where('tx_merchant_delivery_order_detail.ProductID', $productID);
      })
      ->where('tx_merchant_order_detail.StockOrderID', $stockOrderID)
      ->where('tx_merchant_order_detail.ProductID', $productID)
      ->selectRaw("
        tx_merchant_order_detail.ProductID,
        ANY_VALUE(tx_merchant_order_detail.PromisedQuantity) AS PromisedQty,
        ANY_VALUE(tx_merchant_order_detail.Nett) AS Nett,
        (SELECT Qty FROM tx_merchant_delivery_order_detail WHERE DeliveryOrderID = '$deliveryOrderID' AND ProductID = '$productID') AS QtyDO,
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO = 'S025', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDOSelesai,
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO = 'S024', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDODlmPengiriman,
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO != 'S026', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDONotBatal
      ")
      ->groupBy('tx_merchant_order_detail.ProductID')
      ->first();

    $maxCreateDO = $sql->PromisedQty - $sql->QtyDONotBatal;
    $maxEditDetailDO = $sql->PromisedQty - $sql->QtyDOSelesai - $sql->QtyDODlmPengiriman + $sql->QtyDO;
    $maxRequestDO = $sql->PromisedQty - $sql->QtyDOSelesai - $sql->QtyDODlmPengiriman;

    if ($validateFor == "CreateDO") {
      if ($qty <= $maxCreateDO) {
        $status = true;
      } else {
        $status = false;
      }
    } elseif ($validateFor == "EditDetailDO") {
      if ($qty <= $maxEditDetailDO) {
        $status = true;
      } else {
        $status = false;
      }
    } elseif ($validateFor == "ConfirmRequestDO") {
      if ($qty <= $maxRequestDO) {
        $status = true;
      } else {
        $status = false;
      }
    } else {
      $status = "error";
    }

    $output = [
      'status' => $status,
      'price' => $sql->Nett
    ];

    return $output;
  }

  public function rejectRequestDeliveryOrder($deliveryOrderID, $cancelReason, $stockOrderId)
  {
    $sql = DB::transaction(function () use ($deliveryOrderID, $cancelReason, $stockOrderId) {
      DB::table('tx_merchant_delivery_order')
        ->where('tx_merchant_delivery_order.DeliveryOrderID', $deliveryOrderID)
        ->update([
          'StatusDO' => 'S026',
          'CancelReason' => $cancelReason
        ]);
      DB::table('tx_merchant_delivery_order_log')
        ->insert([
          'StockOrderID' => $stockOrderId,
          'DeliveryOrderID' => $deliveryOrderID,
          'StatusDO' => 'S026',
          'ActionBy' => 'DISTRIBUTOR'
        ]);
    });

    return $sql;
  }

  public function dataDetailConfirmDO($deliveryOrderId, $arrProduct, $arrQty)
  {
    $dataDetailDO = array_map(function () {
      return func_get_args();
    }, $arrProduct, $arrQty);
    foreach ($dataDetailDO as $key => $value) {
      $dataDetailDO[$key][] = $deliveryOrderId;
    }

    return $dataDetailDO;
  }

  public function updateDataDetailConfirmDO($deliveryOrderID, $dataDetailDO)
  {
    $update = DB::transaction(function () use ($deliveryOrderID, $dataDetailDO) {
      foreach ($dataDetailDO as $value) {
        DB::table('tx_merchant_delivery_order_detail')
          ->where('DeliveryOrderID', '=', $value['DeliveryOrderID'])
          ->where('ProductID', '=', $value['ProductID'])
          ->update([
            'DeliveryOrderID' => $deliveryOrderID,
            'Qty' => $value['Qty']
          ]);
      }
    });

    return $update;
  }

  public function expeditions()
  {
    $sql = DB::table('tx_merchant_expedition AS expd')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'expd.StatusExpedition')
      ->leftJoin('ms_user AS driver', 'driver.UserID', 'expd.DriverID')
      ->leftJoin('ms_user AS helper', 'helper.UserID', 'expd.HelperID')
      ->leftJoin('ms_vehicle', 'ms_vehicle.VehicleID', 'expd.VehicleID')
      ->join('tx_merchant_expedition_detail', 'tx_merchant_expedition_detail.MerchantExpeditionID', 'expd.MerchantExpeditionID')
      ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID', 'tx_merchant_expedition_detail.DeliveryOrderDetailID')
      ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->distinct()
      ->selectRaw("
        expd.MerchantExpeditionID,
        expd.StatusExpedition,
        expd.CreatedDate,
        ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName,
        ANY_VALUE(driver.Name) AS DriverName,
        ANY_VALUE(helper.Name) AS HelperName,
        expd.VehicleLicensePlate,
        ANY_VALUE(ms_vehicle.VehicleName) AS VehicleName,
        COUNT(DISTINCT tx_merchant_delivery_order_detail.DeliveryOrderID) AS CountDO
      ")
      ->groupBy('expd.MerchantExpeditionID');

    return $sql;
  }

  public function expedition($expeditionID)
  {
    $sql = DB::table('tx_merchant_expedition AS expd')
      ->join('ms_status_order AS StatusExpd', 'StatusExpd.StatusOrderID', 'expd.StatusExpedition')
      ->leftJoin('ms_user AS driver', 'driver.UserID', 'expd.DriverID')
      ->leftJoin('ms_user AS helper', 'helper.UserID', 'expd.HelperID')
      ->leftJoin('ms_vehicle', 'ms_vehicle.VehicleID', 'expd.VehicleID')
      ->join('tx_merchant_expedition_detail', 'tx_merchant_expedition_detail.MerchantExpeditionID', 'expd.MerchantExpeditionID')
      ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID', 'tx_merchant_expedition_detail.DeliveryOrderDetailID')
      ->join('ms_status_order AS StatusExpdProduct', 'StatusExpdProduct.StatusOrderID', 'tx_merchant_expedition_detail.StatusExpeditionDetail')
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
      ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->where('tx_merchant_expedition_detail.MerchantExpeditionID', $expeditionID)
      ->select('tx_merchant_expedition_detail.MerchantExpeditionID', 'tx_merchant_expedition_detail.StatusExpeditionDetail', 'tx_merchant_expedition_detail.MerchantExpeditionDetailID', 'tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.StockOrderID', 'tx_merchant_expedition_detail.DeliveryOrderDetailID', 'tx_merchant_order.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber', 'tx_merchant_delivery_order_detail.ProductID', 'StatusExpdProduct.StatusOrder AS StatusProduct', 'ms_product.ProductName', 'ms_product.ProductImage', 'tx_merchant_delivery_order_detail.Qty', 'tx_merchant_delivery_order_detail.Price', 'tx_merchant_delivery_order_detail.StatusExpedition', 'tx_merchant_delivery_order_detail.Distributor', 'expd.CreatedDate', 'expd.StatusExpedition AS StatusExpd', 'StatusExpd.StatusOrder', 'driver.Name AS DriverName', 'helper.Name AS HelperName', 'expd.VehicleLicensePlate', 'ms_vehicle.VehicleName')
      ->orderBy('tx_merchant_delivery_order_detail.Distributor');

    return $sql;
  }

  public function countStatusDeliveryDetail($merchantExpeditionID)
  {
    $sql = DB::table('tx_merchant_expedition_detail')
      ->where('MerchantExpeditionID', $merchantExpeditionID)
      ->selectRaw("
        COUNT(DISTINCT CASE 
              WHEN tx_merchant_expedition_detail.StatusExpeditionDetail = 'S030' OR tx_merchant_expedition_detail.StatusExpeditionDetail = 'S029' 
              THEN tx_merchant_expedition_detail.DeliveryOrderDetailID 
              END)
        AS DlmPengiriman,
        COUNT(DISTINCT CASE 
              WHEN tx_merchant_expedition_detail.StatusExpeditionDetail = 'S031'
              THEN tx_merchant_expedition_detail.DeliveryOrderDetailID 
              END)
        AS Selesai
      ");

    return $sql;
  }

  public function generateReturID()
  {
    $max = DB::table('ms_stock_purchase')
      ->where('PurchaseID', 'like', '%RETUR%')
      ->selectRaw('MAX(PurchaseID) AS PurchaseID, MAX(CreatedDate) AS CreatedDate')
      ->first();

    $maxMonth = date('m', strtotime($max->CreatedDate));
    $now = date('m');

    if ($max->PurchaseID == null || (strcmp($maxMonth, $now) != 0)) {
      $newReturID = "RETUR-" . date('YmdHis') . '-000001';
    } else {
      $maxExpeditionID = substr($max->PurchaseID, -6);
      $newExpeditionID = $maxExpeditionID + 1;
      $newReturID = "RETUR-" . date('YmdHis') . "-" . str_pad($newExpeditionID, 6, '0', STR_PAD_LEFT);
    }

    return $newReturID;
  }

  public function cancelProductExpedition($expeditionDetailID)
  {
    $sql = DB::table('tx_merchant_expedition_detail')
      ->join('ms_stock_product_log', 'ms_stock_product_log.DeliveryOrderDetailID', 'tx_merchant_expedition_detail.DeliveryOrderDetailID')
      ->join('ms_stock_product', 'ms_stock_product.StockProductID', 'ms_stock_product_log.StockProductID')
      ->join('ms_stock_purchase', 'ms_stock_purchase.PurchaseID', 'ms_stock_product.PurchaseID')
      ->where('tx_merchant_expedition_detail.MerchantExpeditionDetailID', $expeditionDetailID)
      ->select('ms_stock_product.DistributorID', 'ms_stock_purchase.SupplierID', 'ms_stock_product.ProductID', 'ms_stock_product.PurchasePrice', 'ms_stock_product_log.QtyAction', 'ms_stock_product.Qty', 'ms_stock_product_log.SellingPrice', 'ms_stock_product_log.DeliveryOrderDetailID')
      ->first();

    $returID = $this->generateReturID();
    $dateTime = date('Y-m-d H:i:s');
    $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;

    $dataStockPurchase = [
      'PurchaseID' => $returID,
      'DistributorID' => $sql->DistributorID,
      'SupplierID' => $sql->SupplierID,
      'PurchaseDate' => $dateTime,
      'CreatedBy' => $user,
      'StatusID' => 2,
      'StatusBy' => $user,
      'StatusDate' => $dateTime,
      'CreatedDate' => $dateTime,
      'Type' => 'RETUR'
    ];

    $dataStockPurchaseDetail = [
      'PurchaseID' => $returID,
      'ProductID' => $sql->ProductID,
      'Qty' => $sql->QtyAction,
      'PurchasePrice' => $sql->PurchasePrice
    ];

    $dataStockProduct = [
      'PurchaseID' => $returID,
      'ProductID' => $sql->ProductID,
      'Qty' => $sql->QtyAction,
      'PurchasePrice' => $sql->PurchasePrice,
      'DistributorID' => $sql->DistributorID,
      'CreatedDate' => $dateTime
    ];

    $dbTransaction = DB::transaction(function () use ($dataStockPurchase, $dataStockPurchaseDetail, $dataStockProduct, $returID, $sql, $dateTime, $user) {
      DB::table('ms_stock_purchase')->insert($dataStockPurchase);
      DB::table('ms_stock_purchase_detail')->insert($dataStockPurchaseDetail);
      DB::table('ms_stock_product')->insert($dataStockProduct);
      $getStockProductID = DB::table('ms_stock_product')->where('PurchaseID', $returID)->select('StockProductID')->first();
      $stockProductID = $getStockProductID->StockProductID;
      DB::table('ms_stock_product_log')->insert([
        'StockProductID' => $stockProductID,
        'ProductID' => $sql->ProductID,
        'QtyBefore' => $sql->Qty,
        'QtyAction' => $sql->QtyAction,
        'QtyAfter' => $sql->Qty + $sql->QtyAction,
        'PurchasePrice' => $sql->PurchasePrice,
        'SellingPrice' => $sql->SellingPrice,
        'DeliveryOrderDetailID' => $sql->DeliveryOrderDetailID,
        'CreatedDate' => $dateTime,
        'ActionBy' => $user,
        'ActionType' => 'RETUR'
      ]);
    });

    return $dbTransaction;
  }
}
