<?php

namespace App\Services;

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

  public function updateDetailDeliveryOrder($deliveryOrderDetailID, $qty, $statusExpedition)
  {
    $update = DB::table('tx_merchant_delivery_order_detail')
      ->where('DeliveryOrderDetailID', $deliveryOrderDetailID)
      ->update([
        'Qty' => $qty,
        'StatusExpedition' => $statusExpedition
      ]);

    return $update;
  }

  public function getDOfromDetailDO($deliveryOrderDetailID)
  {
    $deliveryOrderID = DB::table('tx_merchant_delivery_order_detail')
      ->select('DeliveryOrderID')
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
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO != 'S026', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDONotBatal
      ")
      ->groupBy('tx_merchant_delivery_order.StockOrderID', 'tx_merchant_delivery_order_detail.ProductID')->toSql();

    $sql = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_delivery_order_detail', function ($join) use ($arrayDeliveryOrderID) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
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
      ->selectRaw("
        ANY_VALUE(tx_merchant_delivery_order.StockOrderID) AS StockOrderID,
        ANY_VALUE(tx_merchant_delivery_order.DeliveryOrderID) AS DeliveryOrderID,
        ANY_VALUE(tx_merchant_delivery_order_detail.DeliveryOrderDetailID) AS DeliveryOrderDetailID,
        ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
        ANY_VALUE(ms_distributor.IsHaistar) AS IsHaistar,
        ANY_VALUE(tx_merchant_delivery_order_detail.ProductID) AS ProductID,
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

  public function getPreviewProduct()
  {
    $sql = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_delivery_order_detail', function ($join) {
        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID');
        $join->whereIn('tx_merchant_delivery_order_detail.DeliveryOrderDetailID', ['89', '102', '103']);
      })
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->selectRaw("
        ANY_VALUE(tx_merchant_delivery_order.StockOrderID) AS StockOrderID,
        ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
        ANY_VALUE(tx_merchant_delivery_order.DeliveryOrderID) AS DeliveryOrderID,
        ANY_VALUE(tx_merchant_delivery_order_detail.DeliveryOrderDetailID) AS DeliveryOrderDetailID,
        ANY_VALUE(tx_merchant_delivery_order_detail.ProductID) AS ProductID,
        ANY_VALUE(ms_product.ProductName) AS ProductName,
        ANY_VALUE(ms_product.ProductImage) AS ProductImage,
        ANY_VALUE(tx_merchant_delivery_order_detail.Price) AS Price
      ");

    return $sql;
  }

  public function getDeliveryRequest()
  {
    $sql = DB::table('tx_merchant_delivery_order')
      ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderID', 'tx_merchant_delivery_order.DeliveryOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
      ->join('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
      ->leftJoin('ms_area', 'ms_area.AreaID', 'ms_merchant_account.AreaID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
      ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'ms_merchant_account.MerchantID')
      ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', 'ms_distributor_merchant_grade.GradeID')
      ->whereIn('tx_merchant_delivery_order.StatusDO', ['S024', 'S028'])
      ->selectRaw("
        tx_merchant_delivery_order.StockOrderID, 
        tx_merchant_delivery_order.DeliveryOrderID, 
        tx_merchant_delivery_order.CreatedDate,
        DATEDIFF(CURDATE(), DATE(tx_merchant_delivery_order.CreatedDate)) AS DueDate,
        ANY_VALUE(ms_distributor.DistributorName) AS DistributorName, 
        ANY_VALUE(tx_merchant_order.MerchantID) AS MerchantID,
        ANY_VALUE(ms_merchant_account.StoreName) AS StoreName,
        GROUP_CONCAT(CONCAT(ms_product.ProductName, ' (', tx_merchant_delivery_order_detail.Qty, 'pcs)') SEPARATOR ', ') AS Products,
        ANY_VALUE(ms_merchant_account.PhoneNumber) AS PhoneNumber,
        ANY_VALUE(ms_merchant_account.StoreAddress) AS StoreAddress,
        ANY_VALUE(CONCAT(ms_sales.SalesCode, ' - ', ms_sales.SalesName)) AS Sales,
        ANY_VALUE(ms_merchant_account.Partner) AS Partner,
        ANY_VALUE(IFNULL(ms_distributor_grade.Grade, 'Retail')) AS Grade,
        ANY_VALUE(tx_merchant_order.OrderLatitude) AS OrderLatitude,
        ANY_VALUE(tx_merchant_order.OrderLongitude) AS OrderLongitude,
        ANY_VALUE(CONCAT(ms_area.AreaName, ', ', ms_area.Subdistrict)) AS Area
      ")
      ->groupBy('tx_merchant_delivery_order.DeliveryOrderID');

    return $sql;
  }

  public function validateRemainingQty($stockOrderID, $productID, $qty, $validateFor)
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
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO = 'S025', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDOSelesai,
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO = 'S024', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDODlmPengiriman,
        IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO != 'S026', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDONotBatal
      ")
      ->groupBy('tx_merchant_order_detail.ProductID')
      ->first();

    $maxCreateDO = $sql->PromisedQty - $sql->QtyDONotBatal;
    $maxEditDetailDO = $sql->PromisedQty - $sql->QtyDOSelesai;
    $maxRequestDO = $sql->PromisedQty - $sql->QtyDOSelesai - $sql->QtyDODlmPengiriman;

    if ($validateFor == "CreateDO") {
      if ($qty > 0 && $qty <= $maxCreateDO) {
        $status = true;
      } else {
        $status = false;
      }
    } elseif ($validateFor == "EditDetailDO") {
      if ($qty > 0 && $qty <= $maxEditDetailDO) {
        $status = true;
      } else {
        $status = false;
      }
    } elseif ($validateFor == "ConfirmRequestDO") {
      if ($qty > 0 && $qty <= $maxRequestDO) {
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
}