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
      // ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tmo.StatusOrderID')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
      ->where('ms_merchant_account.IsTesting', 0)
      ->where('tmo.StatusOrderID', 'S009')
      ->selectRaw("
        tmo.StockOrderID,
        tmo.CreatedDate,
        -- ms_status_order.StatusOrder,
        -- ms_payment_method.PaymentMethodName,
        ms_distributor.DistributorName,
        tmo.MerchantID,
        ms_merchant_account.StoreName,
        ms_merchant_account.OwnerFullName,
        ms_merchant_account.PhoneNumber,
        -- ms_merchant_account.StoreAddress,
        CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) AS Sales,
        tmo.IsValid,
        CASE
          WHEN tmo.IsValid = 1 THEN 'Sudah Valid'
          WHEN tmo.IsValid = 0 THEN 'Tidak Valid'
          ELSE 'Belum Divalidasi'
        END AS Validation,
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
}
