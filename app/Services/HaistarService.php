<?php

namespace App\Services;

class HaistarService {

    public function haistarGetSignature()
    {
        $url = env('HAISTAR_URL') . 'Api/getSignature/?apikey=' . env('HAISTAR_API_KEY');

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
        $url = env('HAISTAR_URL') . 'GetStockInventory/?apikey=' . env('HAISTAR_API_KEY');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . env('HAISTAR_API_KEY'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        $payload = json_encode(
            array(
                "apikey" => env('HAISTAR_API_KEY'),
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
        $url = env('HAISTAR_URL') . 'Push_order/?apikey=' . env('HAISTAR_API_KEY');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . env('HAISTAR_API_KEY'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        $payload = json_encode(
            array(
                "apikey" => env('HAISTAR_API_KEY'),
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

    public function haistarGetLocation()
    {
        $url = env('HAISTAR_URL') . 'Location/getLocation/?apikey=' . env('HAISTAR_API_KEY');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . env('HAISTAR_API_KEY'),
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
        $url = env('HAISTAR_URL') . 'Courier/getCourier/?apikey=' . env('HAISTAR_API_KEY');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . env('HAISTAR_API_KEY'),
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
        $url = env('HAISTAR_URL') . 'Courier/getDeliveryType/?apikey=' . env('HAISTAR_API_KEY');
        $getSignature = json_decode($this->haistarGetSignature());

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type:application/json',
                'Apikey:' . env('HAISTAR_API_KEY'),
                'x-authorization:' . $getSignature->Data->Signature
            )
        );

        $payload = json_encode(
            array(
                "apikey" => env('HAISTAR_API_KEY'),
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

}