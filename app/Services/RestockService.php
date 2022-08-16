<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestockService
{
  public function getRestokValidation()
  {
    $sql = DB::table('tx_merchant_order AS tmo')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tmo.MerchantID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
      // ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmo.StatusOrderID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->where('ms_merchant_account.IsTesting', 0)
      ->whereIn('tmo.StatusOrderID', ['S009', 'S010', 'S023'])
      ->selectRaw("
        tmo.StockOrderID,
        tmo.CreatedDate,
        tmo.StatusOrderID,
        ms_status_order.StatusOrder,
        -- ms_payment_method.PaymentMethodName,
        ms_distributor.DistributorName,
        tmo.MerchantID,
        ms_merchant_account.StoreName,
        ms_merchant_account.OwnerFullName,
        ms_merchant_account.PhoneNumber,
        -- ms_merchant_account.StoreAddress,
        CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) AS Sales,
        tmo.IsValid,
        tmo.ValidationNotes
      ");

    return $sql;
  }

  public function updateRestockValidation($stockOrderID, $isValid, $validationNotes)
  {
    $sql = DB::table('tx_merchant_order')
      ->where('StockOrderID', $stockOrderID)
      ->update([
        'IsValid' => $isValid,
        'ValidationNotes' => $validationNotes
      ]);
    return $sql;
  }

  public function dataDetailValidation($stockOrderID)
  {

    $sql =  DB::table('tx_merchant_order AS tmo')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tmo.MerchantID')
      ->where('tmo.StockOrderID', $stockOrderID)
      ->where('ms_merchant_account.IsTesting', 0)
      ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
      ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmo.StatusOrderID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->selectRaw("
        tmo.StockOrderID,
        tmo.CreatedDate,
        ms_payment_method.PaymentMethodName,
        ms_distributor.DistributorName,
        tmo.TotalPrice,
        tmo.NettPrice,
        tmo.DeliveryFee,
        tmo.DiscountVoucher + tmo.DiscountPrice AS Discount,
        tmo.ServiceChargeNett,
        CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) AS Sales,
        tmo.StatusOrderID,
        ms_status_order.StatusOrder,
        tmo.IsValid,
        CASE
            WHEN tmo.IsValid = 1 THEN 'Sudah Valid'
            WHEN tmo.IsValid = 0 THEN 'Tidak Valid'
            ELSE 'Belum Divalidasi'
        END AS Validation,
        tmo.ValidationNotes,
        tmo.MerchantID,
        ms_merchant_account.StoreName,
        ms_merchant_account.OwnerFullName,
        ms_merchant_account.PhoneNumber,
        ms_merchant_account.Latitude,
        ms_merchant_account.Longitude,
        ms_merchant_account.StoreAddress,
        ms_merchant_account.StoreImage,
        ms_merchant_account.StoreAddressNote,
        ms_merchant_account.CreatedDate AS RegisterDate,
        ms_merchant_account.LastPing,
        ms_merchant_account.IsBlocked,
        (
          SELECT CONCAT(IFNULL(COUNT(StockOrderID), 0), ' order - (Rp ', IFNULL(FORMAT(SUM(NettPrice), 0), 0), ')')
          FROM tx_merchant_order
          WHERE MerchantID = tmo.MerchantID
          AND StatusOrderID = 'S018'
        ) AS OrderSelesai,
        (
          SELECT CONCAT(IFNULL(COUNT(StockOrderID), 0), ' order - (Rp ', IFNULL(FORMAT(SUM(NettPrice), 0), 0), ')')
          FROM tx_merchant_order
          WHERE MerchantID = tmo.MerchantID
          AND StatusOrderID = 'S011'
        ) AS OrderBatal,
        (
          SELECT GROUP_CONCAT(DISTINCT ms_payment_method.PaymentMethodName SEPARATOR ', ')
          FROM tx_merchant_order
          JOIN ms_payment_method ON ms_payment_method.PaymentMethodID = tx_merchant_order.PaymentMethodID
          WHERE MerchantID = tmo.MerchantID
        ) AS OrderPaymentMethod
      ")
      ->first();

    $sql->OrderDetail = DB::table('tx_merchant_order_detail')
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_order_detail.ProductID')
      ->where('tx_merchant_order_detail.StockOrderID', $stockOrderID)
      ->select('ms_product.ProductName', 'ms_product.ProductImage', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Nett', DB::raw("tx_merchant_order_detail.PromisedQuantity * tx_merchant_order_detail.Nett AS TotalPriceProduct"))
      ->get()->toArray();

    $diff = abs(strtotime($sql->RegisterDate) - strtotime(date('Y-m-d H:i:s')));
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    if ($years == 0) {
      $joinedDuration = $months . ' bulan ' . $days . ' hari';
    } elseif ($months == 0) {
      $joinedDuration = $days . ' hari';
    } else {
      $joinedDuration = $years . ' tahun ' . $months . ' bulan ' . $days . ' hari';
    }

    $sql->JoinedDuration = $joinedDuration;

    $sqlOrder = DB::table('tx_merchant_order')
      ->where('tx_merchant_order.MerchantID', $sql->MerchantID)
      ->selectRaw("
        COUNT(StockOrderID) AS CountOrder,
        CONCAT('Rp ', IFNULL(FORMAT(SUM(NettPrice), 0, 'id_ID'), 0)) AS ValueOrder
      ");

    $sqlOrderDetail = DB::table('tx_merchant_order')
      ->join('tx_merchant_order_detail', 'tx_merchant_order_detail.StockOrderID', 'tx_merchant_order.StockOrderID')
      ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_order_detail.ProductID')
      ->where('tx_merchant_order.MerchantID', $sql->MerchantID)
      ->select('tx_merchant_order_detail.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage')
      ->groupBy('tx_merchant_order_detail.ProductID');

    $orderProcessed = (clone $sqlOrder)
      ->whereIn('tx_merchant_order.StatusOrderID', ['S012', 'S018'])
      ->first();

    $orderProcessed->DetailProduct = (clone $sqlOrderDetail)
      ->whereIn('tx_merchant_order.StatusOrderID', ['S012', 'S018'])
      ->get()->toArray();

    $sql->OrderProcessed = $orderProcessed;

    $orderNotProcessed = (clone $sqlOrder)
      ->whereNotIn('tx_merchant_order.StatusOrderID', ['S012', 'S018'])
      ->first();

    $orderNotProcessed->DetailProduct = (clone $sqlOrderDetail)
      ->whereNotIn('tx_merchant_order.StatusOrderID', ['S012', 'S018'])
      ->get()->toArray();

    $sql->OrderNotProcessed = $orderNotProcessed;

    $sql->LogBlocked = DB::table('ms_merchant_account_block_log')
      ->where('MerchantID', $sql->MerchantID)
      ->select('IsBlocked', 'BlockedMessage', 'CreatedDate', 'ActionBy')
      ->orderByDesc('CreatedDate')
      ->get()->toArray();

    return $sql;
  }
}
