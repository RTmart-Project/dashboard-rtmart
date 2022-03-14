<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DeliveryOrderService
{
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
        ANY_VALUE(CONCAT(ms_area.Subdistrict, ', ', ms_area.City)) AS Area
      ")
      ->groupBy('tx_merchant_delivery_order.DeliveryOrderID');

    return $sql;
  }

  public function validateRemainingQty($stockOrderID, $productID, $qty, $validateFor)
  {
    $sql = DB::table('tx_merchant_order_detail')
      ->leftJoin('tx_merchant_delivery_order', 'tx_merchant_order_detail.StockOrderID', 'tx_merchant_delivery_order.StockOrderID')
      ->join('tx_merchant_delivery_order_detail', function ($join) use ($productID) {
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