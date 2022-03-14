<?php

namespace App\Services;

use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class HaistarService
{

    private $txLogService;
    private $merchantService;

    public function __construct(TxLogService $txLogService, MerchantService $merchantService)
    {
        $this->txLogService = $txLogService;
        $this->merchantService = $merchantService;
    }

    public function distributorHaistar()
    {
        $sql = DB::table('ms_distributor')
            ->where('IsHaistar', 1)
            ->where('Ownership', '=', 'RTMart')
            ->where('Email', '!=', NULL)
            ->select('DistributorID', 'DistributorName', 'Email', 'Address', 'CreatedDate', 'IsHaistar');
        return $sql;
    }

    public function distributorNotHaistar()
    {
        $sql = DB::table('ms_distributor')
            ->where('IsHaistar', 0)
            ->where('Ownership', '=', 'RTMart')
            ->where('Email', '!=', NULL)
            ->select('DistributorID', 'DistributorName');
        return $sql;
    }

    public function deleteDistributorHaistar($distributorID)
    {
        $sql = DB::table('ms_distributor')
            ->where('DistributorID', $distributorID)
            ->update([
                'IsHaistar' => 0
            ]);
        return $sql;
    }

    public function dataDistributorHaistar($arrDistributorID)
    {
        $data = array_map(function () {
            return func_get_args();
        }, $arrDistributorID);

        foreach ($data as $key => $value) {
            $data[$key][] = 1;
        }

        return $data;
    }

    public function insertBulkDistributorHaistar($arrDistributorID)
    {
        $insert = DB::transaction(function () use ($arrDistributorID) {
            foreach ($arrDistributorID as &$value) {
                $value = array_combine(['DistributorID', 'IsHaistar'], $value);
                DB::table('ms_distributor')
                    ->where('DistributorID', '=', $value['DistributorID'])
                    ->update([
                        'IsHaistar' => $value['IsHaistar']
                    ]);
            }
        });

        return $insert;
    }

    public function haistarGetSignature()
    {
        $url = config('app.haistar_url') . 'Api/getSignature/?apikey=' . config('app.haistar_api_key');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->txLogService->insertTxLog("GET SIGNATURE", "HAISTAR API", "HAISTAR", "", json_encode($result), "HITTED");

        return $result;
    }

    public function haistarGetStock($productID)
    {
        $url = config('app.haistar_url') . 'GetStockInventory/?apikey=' . config('app.haistar_api_key');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        $payload = json_encode(
            array(
                "apikey" => config('app.haistar_api_key'),
                "code" => $productID
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->txLogService->insertTxLog("GET STOCK", "HAISTAR API", "HAISTAR", $payload, json_encode($result), "HITTED");

        return json_decode($result);
    }

    public function haistarPushOrder($stockOrderID, $objectParams)
    {
        $arrGetLocation = $this->haistarGetLocation();
        $location = Helper::arrayFilterFirst($arrGetLocation->data, "location_name", config('app.haistar_location'));
        $arrGetCourier = $this->haistarGetCourier();
        $courier = Helper::arrayFilterFirst($arrGetCourier->data, "name", "Haistar");
        $arrGetCourierDeliveryType = $this->haistarGetCourierDeliveryType("Haistar");
        $courierDeliveryType = Helper::arrayFilterFirst($arrGetCourierDeliveryType->data, "delivery_type", "Reguler");

        $dataMerchant = $this->merchantService->merchantAccountParamHaistar($stockOrderID);
        if ($dataMerchant->PaymentMethodID == 1) {
            $paymetType = "COD";
        } else {
            $paymetType = "NON COD";
        }

        $url = config('app.haistar_url') . 'Push_order/?apikey=' . config('app.haistar_api_key');
        $getSignature = json_decode($this->haistarGetSignature());

        // $ch = curl_init();
        // curl_setopt(
        //     $ch,
        //     CURLOPT_HTTPHEADER,
        //     array(
        //         'Content-Type:application/json',
        //         'Apikey:' . config('app.haistar_api_key'),
        //         'x-authorization:' . $getSignature->Data->Signature
        //     )
        // );

        $payload = json_encode(
            array(
                "apikey" => config('app.haistar_api_key'),
                "location" => $location->location_code,
                "code" => $objectParams->code,
                "channel_id" => "MULTI CHANNEL",
                "shop_name" => $dataMerchant->StoreName,
                "courier_name" => $courier->name,
                "delivery_type_name" => $courierDeliveryType->delivery_type_id,
                "stock_type_name" => "MULTI CHANNEL",
                "payment_type" => $paymetType,
                "cod_price" => $objectParams->cod_price,
                "total_price" => $objectParams->total_price,
                "total_product_price" => $objectParams->total_product_price,
                "recipient_name" => $dataMerchant->OwnerFullName,
                "recipient_postal_code" => $dataMerchant->PostalCode,
                "recipient_phone" => $dataMerchant->PhoneNumber,
                "recipient_email" => $dataMerchant->Email,
                "recipient_address" => $dataMerchant->OrderAddress,
                "recipient_country" => "Indonesia",
                "recipient_province" => $dataMerchant->Province,
                "recipient_city" => $dataMerchant->City,
                "recipient_district" => $dataMerchant->Subdistrict,
                "stock_source" => "GOOD STOCK",
                "payment_notes" => $dataMerchant->DistributorNote,
                "remark" => $dataMerchant->MerchantNote,
                "order_type" => "Sales Order",
                "dfod_price" => 0,
                "shipping_price" => 0,
                "is_insurance" => "no",
                "items" => $objectParams->items,
            )
        );

        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $result = curl_exec($ch);
        // curl_close($ch);

        // Insert ke Tx Transaction Log
        // $resultDecode = json_decode($result);
        // $this->txLogService->insertTxLog($stockOrderID, "PUSH ORDER HAISTAR", "HAISTAR", $payload, $result, $resultDecode->status);

        return json_decode($payload);
    }

    public function haistarCancelOrder($deliveryOrderID, $cancelReason)
    {
        $url = config('app.haistar_url') . 'requestCancel/?apikey=' . config('app.haistar_api_key');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        $payload = json_encode(
            array(
                "apikey" => config('app.haistar_api_key'),
                "code" => $deliveryOrderID,
                "remarks" => $cancelReason
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->txLogService->insertTxLog($deliveryOrderID, "REQUEST CANCEL ORDER HAISTAR", "HAISTAR", $payload, json_encode($result), "HITTED");

        return json_decode($result);
    }

    public function haistarGetLocation()
    {
        $url = config('app.haistar_url') . 'Location/getLocation/?apikey=' . config('app.haistar_api_key');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->txLogService->insertTxLog("GET LOCATION", "HAISTAR API", "HAISTAR", "", json_encode($result), "HITTED");

        return json_decode($result);
    }

    public function haistarGetCourier()
    {
        $url = config('app.haistar_url') . 'Courier/getCourier/?apikey=' . config('app.haistar_api_key');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->txLogService->insertTxLog("GET COURIER", "HAISTAR API", "HAISTAR", "", json_encode($result), "HITTED");

        return json_decode($result);
    }

    public function haistarGetCourierDeliveryType($courierName)
    {
        $url = config('app.haistar_url') . 'Courier/getDeliveryType/?apikey=' . config('app.haistar_api_key');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        $payload = json_encode(
            array(
                "apikey" => config('app.haistar_api_key'),
                "courier_name" => $courierName
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->txLogService->insertTxLog("GET COURIER DELIVERY TYPE", "HAISTAR API", "HAISTAR", $payload, json_encode($result), "HITTED");

        return json_decode($result);
    }

    // public function haistarSubscribeNewOrder()
    // {
    //     $url = config('app.haistar_url') . 'Api/Subscribe_Webhook_Order_New/?apikey=' . config('app.haistar_api_key');

    //     $ch = curl_init();
    //     curl_setopt(
    //         $ch,
    //         CURLOPT_HTTPHEADER,
    //         array(
    //             'Content-Type:application/json',
    //             'Apikey:' . config('app.haistar_api_key')
    //         )
    //     );

    //     $payload = json_encode(
    //         array(
    //             "apikey" => config('app.haistar_api_key'),
    //             "platform" => "WEB",	
    //             "url" => "https://yoururl", 
    //             "hash_key" => config('app.haistar_hash_key')
    //         )
    //     );

    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     $result = curl_exec($ch);
    //     curl_close($ch);

    //     return json_decode($result);
    // }

    // public function haistarSubscribeStatusOrder()
    // {
    //     $url = config('app.haistar_url') . 'Api/Subscribe_Webhook_Order_Status/?apikey=' . config('app.haistar_api_key');

    //     $ch = curl_init();
    //     curl_setopt(
    //         $ch,
    //         CURLOPT_HTTPHEADER,
    //         array(
    //             'Content-Type:application/json',
    //             'Apikey:' . config('app.haistar_api_key')
    //         )
    //     );

    //     $payload = json_encode(
    //         array(
    //             "apikey" => config('app.haistar_api_key'),
    //             "platform" => "WEB",	
    //             "url" => "https://mobile.rt-mart.id/merchant/api/transaction/hasitarOrderStatusUpdateCallback.php", 
    //             "hash_key" => config('app.haistar_hash_key')
    //         )
    //     );

    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     $result = curl_exec($ch);
    //     curl_close($ch);

    //     return json_decode($result);
    // }

}