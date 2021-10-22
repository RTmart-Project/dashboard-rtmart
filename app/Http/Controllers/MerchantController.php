<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{
    protected $baseImageUrl;

    public function __construct()
    {
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function account()
    {
        function countMerchantAccount($distributorId = "all", $thisYear = null, $thisMonth = null, $thisDay = null)
        {
            $merchantAccount = DB::table('ms_merchant_account')
                ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
                ->whereNotIn('ms_merchant_account.DistributorID', ['D-2004-000003', 'D-2004-000007', 'D-2004-000008', 'D-2004-000009', 'D-2004-000010', 'D-2101-000001', 'D-0000-000000', 'D-2104-000001', 'D-2104-000002'])
                ->where('ms_merchant_account.IsTesting', 0)
                ->where('ms_distributor.Ownership', '=', 'RTMart')
                ->where('ms_distributor.Email', '!=', null)
                ->select('ms_merchant_account.MerchantID');

            if ($thisMonth != null && $thisYear != null) {
                $merchantAccount->whereYear('ms_merchant_account.CreatedDate', '=', $thisYear)
                    ->whereMonth('ms_merchant_account.CreatedDate', '=', $thisMonth);
            }

            if ($thisDay != null && $thisMonth != null && $thisYear != null) {
                $merchantAccount->whereYear('ms_merchant_account.CreatedDate', '=', $thisYear)
                    ->whereMonth('ms_merchant_account.CreatedDate', '=', $thisMonth)
                    ->whereDay('ms_merchant_account.CreatedDate', '=', $thisDay);
            }
            if ($distributorId != "all") {
                $merchantAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
            }

            return $merchantAccount->count();
        }

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        return view('merchant.account.index', [
            'countTotalMerchant' => countMerchantAccount(),
            'countNewMerchantThisMonth' => countMerchantAccount("all", $thisYear, $thisMonth),
            'countNewMerchantThisDay' => countMerchantAccount("all", $thisMonth, $thisYear, $thisDay),
            'countTotalMerchantBali' => countMerchantAccount("D-2004-000004"),
            'countNewMerchantBaliThisMonth' => countMerchantAccount("D-2004-000004", $thisYear, $thisMonth),
            'countNewMerchantBaliThisDay' => countMerchantAccount("D-2004-000004", $thisYear, $thisMonth, $thisDay),
            'countTotalMerchantBandung' => countMerchantAccount("D-2004-000005"),
            'countNewMerchantBandungThisMonth' => countMerchantAccount("D-2004-000005", $thisYear, $thisMonth),
            'countNewMerchantBandungThisDay' => countMerchantAccount("D-2004-000005", $thisYear, $thisMonth, $thisDay),
            'countTotalMerchantCakung' => countMerchantAccount("D-2004-000001"),
            'countNewMerchantCakungThisMonth' => countMerchantAccount("D-2004-000001", $thisYear, $thisMonth),
            'countNewMerchantCakungThisDay' => countMerchantAccount("D-2004-000001", $thisMonth, $thisYear, $thisDay),
            'countTotalMerchantCiracas' => countMerchantAccount("D-2004-000006"),
            'countNewMerchantCiracasThisMonth' => countMerchantAccount("D-2004-000006", $thisYear, $thisMonth),
            'countNewMerchantCiracasThisDay' => countMerchantAccount("D-2004-000006", $thisMonth, $thisYear, $thisDay),
            'countTotalMerchantSemarang' => countMerchantAccount("D-2004-000002"),
            'countNewMerchantSemarangThisMonth' => countMerchantAccount("D-2004-000002", $thisYear, $thisMonth),
            'countNewMerchantSemarangThisDay' => countMerchantAccount("D-2004-000002", $thisMonth, $thisYear, $thisDay)
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
            ->where('ms_merchant_account.IsTesting', 0)
            ->where('ms_distributor.Ownership', '=', 'RTMart')
            ->where('ms_distributor.Email', '!=', null)
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
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/merchant/account/product/' . $data->MerchantID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->rawColumns(['CreatedDate', 'Action'])
                ->make(true);
        }
    }

    public function product($merchantId)
    {
        $merchant = DB::table('ms_merchant_account')
            ->where('MerchantID', '=', $merchantId)
            ->select('StoreName', 'OwnerFullName', 'StoreAddress', 'StoreImage')
            ->first();

        return view('merchant.product.index', [
            'merchantId' => $merchantId,
            'merchant' => $merchant
        ]);
    }

    public function getProducts(Request $request, $merchantId)
    {
        $merchantProducts = DB::table('ms_product_merchant')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_product_merchant.ProductID')
            ->join('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->join('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->where('ms_product_merchant.MerchantID', '=', $merchantId)
            ->select('ms_product_merchant.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_product_uom.ProductUOMName', 'ms_product.ProductUOMDesc', 'ms_product_merchant.Price');

        $data = $merchantProducts->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="90">';
                })
                ->rawColumns(['ProductImage'])
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
                ->editColumn('IsVerified', function ($data) {
                    if ($data->IsVerified == "0") {
                        $isVerified = '<span class="badge badge-danger">Belum Terverifikasi</span>';
                    } elseif ($data->IsVerified == "1") {
                        $isVerified = '<span class="badge badge-success">Terverifikasi</span>';
                    }

                    return $isVerified;
                })
                ->rawColumns(['IsVerified'])
                ->make(true);
        }
    }

    public function restock()
    {

        function countMerchantRestock($distributorId = "all", $thisYear = null, $thisMonth = null, $thisDay = null)
        {
            $merchantRestock = DB::table('tx_merchant_order')
                ->whereNotIn('tx_merchant_order.DistributorID', ['D-2004-000003', 'D-2004-000007', 'D-2004-000008', 'D-2004-000009', 'D-2004-000010', 'D-2101-000001', 'D-0000-000000', 'D-2104-000001', 'D-2104-000002'])
                ->select('tx_merchant_order.StockOrderID');

            if ($thisMonth != null && $thisYear != null) {
                $merchantRestock->whereYear('tx_merchant_order.CreatedDate', '=', $thisYear)
                    ->whereMonth('tx_merchant_order.CreatedDate', '=', $thisMonth);
            }

            if ($thisDay != null && $thisMonth != null && $thisYear != null) {
                $merchantRestock->whereYear('tx_merchant_order.CreatedDate', '=', $thisYear)
                    ->whereMonth('tx_merchant_order.CreatedDate', '=', $thisMonth)
                    ->whereDay('tx_merchant_order.CreatedDate', '=', $thisDay);
            }
            if ($distributorId != "all") {
                $merchantRestock->where('tx_merchant_order.DistributorID', '=', $distributorId);
            }

            return $merchantRestock->count();
        }

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        return view('merchant.restock.index', [
            'countTotalRestock' => countMerchantRestock(),
            'countRestockThisMonth' => countMerchantRestock("all", $thisYear, $thisMonth),
            'countRestockThisDay' => countMerchantRestock("all", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockBali' => countMerchantRestock("D-2004-000004"),
            'countRestockBaliThisMonth' => countMerchantRestock("D-2004-000004", $thisYear, $thisMonth),
            'countRestockBaliThisDay' => countMerchantRestock("D-2004-000004", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockBandung' => countMerchantRestock("D-2004-000005"),
            'countRestockBandungThisMonth' => countMerchantRestock("D-2004-000005", $thisYear, $thisMonth),
            'countRestockBandungThisDay' => countMerchantRestock("D-2004-000005", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockCakung' => countMerchantRestock("D-2004-000001"),
            'countRestockCakungThisMonth' => countMerchantRestock("D-2004-000001", $thisYear, $thisMonth),
            'countRestockCakungThisDay' => countMerchantRestock("D-2004-000001", $thisMonth, $thisYear, $thisDay),
            'countTotalRestockCiracas' => countMerchantRestock("D-2004-000006"),
            'countRestockCiracasThisMonth' => countMerchantRestock("D-2004-000006", $thisYear, $thisMonth),
            'countRestockCiracasThisDay' => countMerchantRestock("D-2004-000006", $thisMonth, $thisYear, $thisDay),
            'countTotalRestockSemarang' => countMerchantRestock("D-2004-000002"),
            'countRestockSemarangThisMonth' => countMerchantRestock("D-2004-000002", $thisYear, $thisMonth),
            'countRestockSemarangThisDay' => countMerchantRestock("D-2004-000002", $thisMonth, $thisYear, $thisDay)
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
            ->select('tx_merchant_order.*', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber', 'ms_distributor.DistributorName', 'ms_status_order.StatusOrder', 'ms_merchant_account.ReferralCode', 'ms_payment_method.PaymentMethodName');

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
