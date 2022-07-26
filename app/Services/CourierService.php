<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CourierService
{
  public function getCouriers()
  {
    $sql = DB::table('ms_courier')
      ->select('CourierName', 'CourierCode', 'PhoneNumber', 'Email', 'CreatedDate', 'IsActive');

    return $sql;
  }

  public function nonActiveCourier($courierCode)
  {
    $sql = DB::table('ms_courier')
      ->where('CourierCode', $courierCode)
      ->update([
        'IsActive' => 0
      ]);

    return $sql;
  }

  public function orderByStatus($courierStatus)
  {
    $sql = DB::table('tx_product_order')
      ->join('ms_customer_account', 'ms_customer_account.CustomerID', 'tx_product_order.CustomerID')
      ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_product_order.MerchantID')
      ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
      ->join('ms_status_order', 'ms_status_order.StatusOrderID', 'tx_product_order.StatusOrderID')
      ->join('ms_courier', 'ms_courier.CourierCode', 'tx_product_order.CourierCode')
      ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tx_product_order.PaymentMethodID')
      ->whereNotNull('tx_product_order.CourierCode')
      ->where('tx_product_order.CourierStatus', $courierStatus)
      ->select('tx_product_order.OrderID', 'tx_product_order.CreatedDate', 'tx_product_order.CourierCode', 'ms_courier.CourierName', 'tx_product_order.CustomerID', 'ms_customer_account.FullName', 'tx_product_order.OrderAddress', 'ms_customer_account.PhoneNumber as CustomerPhoneNumber', 'tx_product_order.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.PhoneNumber as StorePhoneNumber', 'ms_payment_method.PaymentMethodName', 'ms_status_order.StatusOrder', DB::raw("(tx_product_order.NettPrice + tx_product_order.ServiceChargeNett + tx_product_order.DeliveryFee) AS GrandTotal"), 'tx_product_order.StatusOrderID');

    return $sql;
  }
}
