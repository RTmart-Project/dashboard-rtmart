<?php

namespace App\Http\Controllers;

use App\Services\DeliveryOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DeliveryController extends Controller
{
    public function request()
    {
        return view('delivery.request.index');
    }

    public function getRequest(DeliveryOrderService $deliveryOrderService, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlDeliveryRequest = $deliveryOrderService->getDeliveryRequest();

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlDeliveryRequest->where('ms_distributor.Depo', '=', $depoUser);
        }
        if ($fromDate != '' && $toDate != '') {
            $sqlDeliveryRequest->whereDate('tx_merchant_delivery_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_merchant_delivery_order.CreatedDate', '<=', $toDate);
        }

        // Get data response
        $data = $sqlDeliveryRequest;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('Empty', function ($data) {
                    return "";
                })
                ->addColumn('Checkbox', function ($data) {
                    $checkbox = "<input type='checkbox' name='confirm[]' value='" . $data->DeliveryOrderID . "' />";
                    return $checkbox;
                })
                ->filterColumn('Area', function ($query, $keyword) {
                    $sql = "CONCAT(ms_area.Subdistrict, ', ', ms_area.City) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('DistributorName', function ($query, $keyword) {
                    $sql = "ms_distributor.DistributorName like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('MerchantID', function ($query, $keyword) {
                    $sql = "ms_merchant_account.MerchantID like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('StoreName', function ($query, $keyword) {
                    $sql = "ms_merchant_account.StoreName like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('PhoneNumber', function ($query, $keyword) {
                    $sql = "ms_merchant_account.PhoneNumber like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('Partner', function ($query, $keyword) {
                    $sql = "ms_merchant_account.Partner like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('Grade', function ($query, $keyword) {
                    $sql = "ms_distributor_grade.Grade like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('StoreAddress', function ($query, $keyword) {
                    $sql = "ms_merchant_account.StoreAddress like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_sales.SalesCode, ' - ', ms_sales.SalesName) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['Checkbox'])
                ->make(true);
        }
    }
}