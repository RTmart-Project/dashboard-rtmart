<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{

    public function account()
    {
        $merchantAccount = DB::table('ms_merchant_account')
            ->select('MerchantID', 'CreatedDate');

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        $countTotalMerchant = $merchantAccount->count();

        $countNewMerchantThisMonth = $merchantAccount
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->count();

        $countNewMerchantThisDay = $merchantAccount
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->whereDay('CreatedDate', '=', $thisDay)
            ->count();

        return view('merchant.account.index', [
            'countTotalMerchant' => $countTotalMerchant,
            'countNewMerchantThisMonth' => $countNewMerchantThisMonth,
            'countNewMerchantThisDay' => $countNewMerchantThisDay
        ]);
    }

    public function getAccounts(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributorId = $request->input('distributorId');

        // Get data account, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_merchant_account')
            ->leftJoin('ms_area', 'ms_area.AreaID', '=', 'ms_merchant_account.AreaID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->select('ms_merchant_account.*', 'ms_area.AreaName', 'ms_area.Subdistrict', 'ms_area.City', 'ms_area.Province', 'ms_distributor.DistributorName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_merchant_account.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_merchant_account.CreatedDate', '<=', $toDate);
        }

        if ($distributorId != null) {
            $sqlAllAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
        }

        // Get data response
        $data = $sqlAllAccount->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('Y-m-d', strtotime($data->CreatedDate));
                })
                ->rawColumns(['CreatedDate'])
                ->make(true);
        }
    }

    public function otp()
    {
        return view('merchant.otp.index');
    }

    public function getOtps(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data otp, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_verification')
            ->join('ms_verification_log', 'ms_verification_log.PhoneNumber', '=', 'ms_verification.PhoneNumber')
            ->where('ms_verification.Type', '=', 'MERCHANT')
            ->select('ms_verification.*', 'ms_verification_log.SendOn', 'ms_verification_log.ReceiveOn');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_verification_log.SendOn', '>=', $fromDate)
                ->whereDate('ms_verification_log.SendOn', '<=', $toDate);
        }

        // Get data response
        $data = $sqlAllAccount->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->make(true);
        }
    }

    public function restock()
    {
        $merchantRestock = DB::table('tx_merchant_order')
            ->select('StockOrderID', 'CreatedDate');

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        $countTotalRestock = $merchantRestock->count();

        $countRestockThisMonth = $merchantRestock
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->count();

        $countRestockThisDay = $merchantRestock
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->whereDay('CreatedDate', '=', $thisDay)
            ->count();

        return view('merchant.restock.index', [
            'countTotalRestock' => $countTotalRestock,
            'countRestockThisMonth' => $countRestockThisMonth,
            'countRestockThisDay' => $countRestockThisDay
        ]);
    }

    public function getRestocks(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $paymentMethodId = $request->input('paymentMethodId');

        $sqlAllAccount = DB::table('tx_merchant_order')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->select('tx_merchant_order.*', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber', 'ms_distributor.DistributorName', 'ms_status_order.StatusOrder', 'ms_merchant_account.ReferralCode');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('tx_merchant_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_merchant_order.CreatedDate', '<=', $toDate);
        }

        if ($paymentMethodId != null) {
            $sqlAllAccount->where('tx_merchant_order.PaymentMethodID', '=', $paymentMethodId);
        }

        // Get data response
        $data = $sqlAllAccount->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('Y-m-d', strtotime($data->CreatedDate));
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/merchant/restock/detail/' . $data->StockOrderID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->rawColumns(['CreatedDate', 'Action'])
                ->make(true);
        }
    }

    public function restockDetails($stockOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderId)
            ->select('*')
            ->first();

        return view('merchant.restock.details', [
            'stockOrderId' => $stockOrderId,
            'merchant' => $merchant
        ]);
    }

    public function getRestockDetails(Request $request, $stockOrderId)
    {
        $stockOrderById = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('StockOrderID', '=', $stockOrderId)
            ->select('tx_merchant_order_detail.*', 'ms_product.ProductName');

        $data = $stockOrderById->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('SubTotalPrice', function ($data) {
                    $subTotalPrice = $data->Nett * $data->PromisedQuantity;
                    return "$subTotalPrice";
                })
                ->editColumn('Price', function ($data) {
                    return "$data->Price";
                })
                ->make(true);
        }
    }
}