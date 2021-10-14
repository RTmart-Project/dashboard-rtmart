<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function account()
    {
        $customerAccount = DB::table('ms_customer_account')
            ->select('CustomerID', 'CreatedDate');

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        $countTotalCustomer = $customerAccount->count();

        $countNewCustomerThisMonth = $customerAccount
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->count();

        $countNewCustomerThisDay = $customerAccount
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->whereDay('CreatedDate', '=', $thisDay)
            ->count();

        return view('customer.account.index', [
            'countTotalCustomer' => $countTotalCustomer,
            'countNewCustomerThisMonth' => $countNewCustomerThisMonth,
            'countNewCustomerThisDay' => $countNewCustomerThisDay
        ]);
    }

    public function getAccounts(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data account, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_customer_account')
            ->leftJoin('ms_area', 'ms_area.AreaID', '=', 'ms_customer_account.AreaID')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'ms_customer_account.MerchantID')
            ->select('ms_customer_account.*', 'ms_merchant_account.StoreName', 'ms_area.AreaName', 'ms_area.Subdistrict', 'ms_area.City', 'ms_area.Province');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_customer_account.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_customer_account.CreatedDate', '<=', $toDate);
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
        return view('customer.otp.index');
    }

    public function getOtps(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data otp, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_verification')
            ->join('ms_verification_log', 'ms_verification_log.PhoneNumber', '=', 'ms_verification.PhoneNumber')
            ->where('ms_verification.Type', '=', 'CUSTOMER')
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

    public function transaction()
    {
        $customerTransaction = DB::table('tx_product_order')
            ->select('OrderID', 'CreatedDate');

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        $countTotalTransaction = $customerTransaction->count();

        $countTransactionThisMonth = $customerTransaction
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->count();

        $countTransactionThisDay = $customerTransaction
            ->whereYear('CreatedDate', '=', $thisYear)
            ->whereMonth('CreatedDate', '=', $thisMonth)
            ->whereDay('CreatedDate', '=', $thisDay)
            ->count();

        return view('customer.transaction.index', [
            'countTotalTransaction' => $countTotalTransaction,
            'countTransactionThisMonth' => $countTransactionThisMonth,
            'countTransactionThisDay' => $countTransactionThisDay
        ]);
    }

    public function getTransactions(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $paymentMethodId = $request->input('paymentMethodId');

        $sqlAllAccount = DB::table('tx_product_order')
            ->leftJoin('ms_customer_account', 'ms_customer_account.CustomerID', '=', 'tx_product_order.CustomerID')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_product_order.MerchantID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_product_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_product_order.PaymentMethodID')
            ->select('tx_product_order.OrderID', 'tx_product_order.CustomerID', 'tx_product_order.MerchantID', 'tx_product_order.TotalPrice', 'ms_customer_account.FullName', 'tx_product_order.CreatedDate', 'ms_customer_account.PhoneNumber', 'ms_merchant_account.StoreName', 'ms_status_order.StatusOrder');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('tx_product_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_product_order.CreatedDate', '<=', $toDate);
        }

        if ($paymentMethodId != null) {
            $sqlAllAccount->where('tx_product_order.PaymentMethodID', '=', $paymentMethodId);
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
                    $actionBtn = '<a href="/customer/transaction/detail/' . $data->OrderID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->rawColumns(['CreatedDate', 'Action'])
                ->make(true);
        }
    }

    public function transactionDetails($orderId)
    {
        $customer = DB::table('tx_product_order')
            ->join('ms_customer_account', 'ms_customer_account.CustomerID', '=', 'tx_product_order.CustomerID')
            ->where('tx_product_order.OrderID', '=', $orderId)
            ->select('*')
            ->first();

        return view('customer.transaction.details', [
            'orderId' => $orderId,
            'customer' => $customer
        ]);
    }

    public function getTransactionDetails(Request $request, $orderId)
    {
        $orderById = DB::table('tx_product_order_detail')
            ->where('OrderID', '=', $orderId)
            ->select('*')->get();

        if ($request->ajax()) {
            return DataTables::of($orderById)
                ->make(true);
        }
    }
}
