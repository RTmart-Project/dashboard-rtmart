<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class HaistarService {

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
        $insert = DB::transaction(function() use ($arrDistributorID) {
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

        return json_decode($result);
    }

    public function haistarPushOrder($objectParams)
    {
        $url = config('app.haistar_url') . 'Push_order/?apikey=' . config('app.haistar_api_key');
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
                "location" => $objectParams->location,
                "code" => $objectParams->code,
                "channel_id" => "MULTI CHANNEL",
                "shop_name" => $objectParams->shop_name,
                "courier_name" => $objectParams->courier_name,
                "delivery_type_name" => $objectParams->delivery_type_name,
                "stock_type_name" => "MULTI CHANNEL",
                "payment_type" => $objectParams->payment_type,
                "cod_price" => $objectParams->cod_price,
                "total_price" => $objectParams->total_price,
                "total_product_price" => $objectParams->total_product_price,
                "recipient_name" => $objectParams->recipient_name,
                "recipient_postal_code" => $objectParams->recipient_postal_code,
                "recipient_phone" => $objectParams->recipient_phone,
                "recipient_email" => $objectParams->recipient_email,
                "recipient_address" => $objectParams->recipient_address,
                "recipient_country" => "Indonesia",
                "recipient_province" => $objectParams->recipient_province,
                "recipient_city" => $objectParams->recipient_city,
                "recipient_district" => $objectParams->recipient_district,
                "stock_source" => "GOOD STOCK",
                "payment_notes" => $objectParams->payment_notes,
                "remark" => $objectParams->remark,
                "order_type" => "Sales Order",
                "dfod_price" => 0,
                "shipping_price" => 0,
                "is_insurance" => "no",
                "items" => $objectParams->items,
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
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

        return json_decode($result);
    }

    public function haistarSubscribeNewOrder()
    {
        $url = config('app.haistar_url') . 'Api/Subscribe_Webhook_Order_New/?apikey=' . config('app.haistar_api_key');

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key')
            )
        );

        $payload = json_encode(
            array(
                "apikey" => config('app.haistar_api_key'),
                "platform" => "WEB",	
                "url" => "https://yoururl", 
                "hash_key" => config('app.haistar_hash_key')
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function haistarSubscribeStatusOrder()
    {
        $url = config('app.haistar_url') . 'Api/Subscribe_Webhook_Order_Status/?apikey=' . config('app.haistar_api_key');

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . config('app.haistar_api_key')
            )
        );

        $payload = json_encode(
            array(
                "apikey" => config('app.haistar_api_key'),
                "platform" => "WEB",	
                "url" => "https://yoururl", 
                "hash_key" => config('app.haistar_hash_key')
            )
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

}