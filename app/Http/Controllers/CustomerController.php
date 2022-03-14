<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function account()
    {
        function countCustomerAccount($distributorId = "all", $thisYear = null, $thisMonth = null, $thisDay = null)
        {
            $customerAccount = DB::table('ms_customer_account')
                ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'ms_customer_account.MerchantID')
                ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
                ->where('ms_customer_account.IsTesting', 0)
                ->select('ms_customer_account.CustomerID');

            if ($thisMonth != null && $thisYear != null) {
                $customerAccount->whereYear('ms_customer_account.CreatedDate', '=', $thisYear)
                    ->whereMonth('ms_customer_account.CreatedDate', '=', $thisMonth);
            }

            if ($thisDay != null && $thisMonth != null && $thisYear != null) {
                $customerAccount->whereYear('ms_customer_account.CreatedDate', '=', $thisYear)
                    ->whereMonth('ms_customer_account.CreatedDate', '=', $thisMonth)
                    ->whereDay('ms_customer_account.CreatedDate', '=', $thisDay);
            }
            if ($distributorId != "all") {
                $customerAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
            }
            if (Auth::user()->Depo != "ALL") {
                $depoUser = Auth::user()->Depo;
                $customerAccount->where('ms_distributor.Depo', '=', $depoUser);
            }

            return $customerAccount->count();
        }

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        return view('customer.account.index', [
            'countTotalCustomer' => countCustomerAccount(),
            'countNewCustomerThisMonth' => countCustomerAccount("all", $thisYear, $thisMonth),
            'countNewCustomerThisDay' => countCustomerAccount("all", $thisYear, $thisMonth, $thisDay),
            'countTotalCustomerBandung' => countCustomerAccount("D-2004-000005"),
            'countNewCustomerBandungThisMonth' => countCustomerAccount("D-2004-000005", $thisYear, $thisMonth),
            'countNewCustomerBandungThisDay' => countCustomerAccount("D-2004-000005", $thisYear, $thisMonth, $thisDay),
            'countTotalCustomerCakung' => countCustomerAccount("D-2004-000001"),
            'countNewCustomerCakungThisMonth' => countCustomerAccount("D-2004-000001", $thisYear, $thisMonth),
            'countNewCustomerCakungThisDay' => countCustomerAccount("D-2004-000001", $thisYear, $thisMonth, $thisDay),
            'countTotalCustomerCiracas' => countCustomerAccount("D-2004-000006"),
            'countNewCustomerCiracasThisMonth' => countCustomerAccount("D-2004-000006", $thisYear, $thisMonth),
            'countNewCustomerCiracasThisDay' => countCustomerAccount("D-2004-000006", $thisYear, $thisMonth, $thisDay),
            'countTotalCustomerSemarang' => countCustomerAccount("D-2004-000002"),
            'countNewCustomerSemarangThisMonth' => countCustomerAccount("D-2004-000002", $thisYear, $thisMonth),
            'countNewCustomerSemarangThisDay' => countCustomerAccount("D-2004-000002", $thisYear, $thisMonth, $thisDay)
        ]);
    }

    public function getAccounts(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data account, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_customer_account')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'ms_customer_account.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->select(['ms_customer_account.CustomerID', 'ms_customer_account.FullName', 'ms_customer_account.PhoneNumber', 'ms_customer_account.CreatedDate', 'ms_customer_account.Address', 'ms_customer_account.MerchantID', 'ms_customer_account.ReferralCode', 'ms_merchant_account.StoreName', 'ms_distributor.DistributorName']);

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_customer_account.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_customer_account.CreatedDate', '<=', $toDate);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->filterColumn('ms_customer_account.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_customer_account.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
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
            ->select(['ms_verification.PhoneNumber', 'ms_verification.OTP', 'ms_verification.IsVerified', 'ms_verification_log.SendOn', 'ms_verification_log.ReceiveOn']);

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_verification_log.SendOn', '>=', $fromDate)
                ->whereDate('ms_verification_log.SendOn', '<=', $toDate);
        }

        // Get data response
        $data = $sqlAllAccount;

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
                ->editColumn('SendOn', function ($data) {
                    return date('d-M-Y H:i:s', strtotime($data->SendOn));
                })
                ->editColumn('ReceiveOn', function ($data) {
                    return date('d-M-Y H:i:s', strtotime($data->ReceiveOn));
                })
                ->filterColumn('ms_verification_log.SendOn', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_verification_log.SendOn,'%d-%b-%Y %H:%i:%s') like ?", ["%$keyword%"]);
                })
                ->filterColumn('ms_verification_log.ReceiveOn', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_verification_log.ReceiveOn,'%d-%b-%Y %H:%i:%s') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['IsVerified'])
                ->make(true);
        }
    }

    public function transaction()
    {
        function countCustomerTransaction($distributorId = "all", $thisYear = null, $thisMonth = null, $thisDay = null)
        {
            $customerTransaction = DB::table('tx_product_order')
                ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_product_order.MerchantID')
                ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
                ->where('ms_merchant_account.IsTesting', 0)
                ->select('tx_product_order.OrderID');

            if ($thisMonth != null && $thisYear != null) {
                $customerTransaction->whereYear('tx_product_order.CreatedDate', '=', $thisYear)
                    ->whereMonth('tx_product_order.CreatedDate', '=', $thisMonth);
            }

            if ($thisDay != null && $thisMonth != null && $thisYear != null) {
                $customerTransaction->whereYear('tx_product_order.CreatedDate', '=', $thisYear)
                    ->whereMonth('tx_product_order.CreatedDate', '=', $thisMonth)
                    ->whereDay('tx_product_order.CreatedDate', '=', $thisDay);
            }
            if ($distributorId != "all") {
                $customerTransaction->where('ms_merchant_account.DistributorID', '=', $distributorId);
            }
            if (Auth::user()->Depo != "ALL") {
                $depoUser = Auth::user()->Depo;
                $customerTransaction->where('ms_distributor.Depo', '=', $depoUser);
            }

            return $customerTransaction->count();
        }

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        return view('customer.transaction.index', [
            'countTotalTransaction' => countCustomerTransaction(),
            'countTransactionThisMonth' => countCustomerTransaction("all", $thisYear, $thisMonth),
            'countTransactionThisDay' => countCustomerTransaction("all", $thisYear, $thisMonth, $thisDay),
            'countTotalTransactionBandung' => countCustomerTransaction("D-2004-000005"),
            'countTransactionBandungThisMonth' => countCustomerTransaction("D-2004-000005", $thisYear, $thisMonth),
            'countTransactionBandungThisDay' => countCustomerTransaction("D-2004-000005", $thisYear, $thisMonth, $thisDay),
            'countTotalTransactionCakung' => countCustomerTransaction("D-2004-000001"),
            'countTransactionCakungThisMonth' => countCustomerTransaction("D-2004-000001", $thisYear, $thisMonth),
            'countTransactionCakungThisDay' => countCustomerTransaction("D-2004-000001", $thisYear, $thisMonth, $thisDay),
            'countTotalTransactionCiracas' => countCustomerTransaction("D-2004-000006"),
            'countTransactionCiracasThisMonth' => countCustomerTransaction("D-2004-000006", $thisYear, $thisMonth),
            'countTransactionCiracasThisDay' => countCustomerTransaction("D-2004-000006", $thisYear, $thisMonth, $thisDay),
            'countTotalTransactionSemarang' => countCustomerTransaction("D-2004-000002"),
            'countTransactionSemarangThisMonth' => countCustomerTransaction("D-2004-000002", $thisYear, $thisMonth),
            'countTransactionSemarangThisDay' => countCustomerTransaction("D-2004-000002", $thisYear, $thisMonth, $thisDay)
        ]);
    }

    public function getTransactions(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $paymentMethodId = $request->input('paymentMethodId');

        $sqlAllAccount = DB::table('tx_product_order')
            ->leftJoin('ms_customer_account', 'ms_customer_account.CustomerID', '=', 'tx_product_order.CustomerID')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_product_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_product_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_product_order.PaymentMethodID')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'ms_merchant_account.ReferralCode')
            ->where('ms_merchant_account.IsTesting', 0)
            ->select(['tx_product_order.OrderID', 'tx_product_order.MerchantID', 'tx_product_order.TotalPrice', 'ms_customer_account.FullName', 'tx_product_order.CreatedDate', 'ms_customer_account.PhoneNumber', 'ms_merchant_account.StoreName', 'ms_merchant_account.StoreAddress', 'tx_product_order.StatusOrderID', 'ms_status_order.StatusOrder', 'ms_distributor.DistributorName', 'ms_sales.SalesName', 'ms_payment_method.PaymentMethodName', 'ms_customer_account.Address']);

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('tx_product_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_product_order.CreatedDate', '<=', $toDate);
        }

        if ($paymentMethodId != null) {
            $sqlAllAccount->where('tx_product_order.PaymentMethodID', '=', $paymentMethodId);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('StatusOrder', function ($data) {
                    $pesananBaru = "S013";
                    $dikonfirmasi = "S014";
                    $dalamProses = "S019";
                    $dikirim = "S015";
                    $selesai = "S016";
                    $dibatalkan = "S017";

                    if ($data->StatusOrderID == $pesananBaru) {
                        $statusOrder = '<span class="badge badge-secondary">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dikonfirmasi) {
                        $statusOrder = '<span class="badge badge-primary">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dalamProses) {
                        $statusOrder = '<span class="badge badge-warning">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dikirim) {
                        $statusOrder = '<span class="badge badge-info">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $selesai) {
                        $statusOrder = '<span class="badge badge-success">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dibatalkan) {
                        $statusOrder = '<span class="badge badge-danger">' . $data->StatusOrder . '</span>';
                    } else {
                        $statusOrder = 'Status tidak ditemukan';
                    }

                    return $statusOrder;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/customer/transaction/detail/' . $data->OrderID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->filterColumn('tx_product_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_product_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['StatusOrder', 'Action'])
                ->make(true);
        }
    }

    public function getTransactionProduct(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $paymentMethodId = $request->input('paymentMethodId');

        $sqlAllAccount = DB::table('tx_product_order')
            ->leftJoin('tx_product_order_detail', 'tx_product_order_detail.OrderID', '=', 'tx_product_order.OrderID')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_product_order_detail.productID')
            ->leftJoin('ms_customer_account', 'ms_customer_account.CustomerID', '=', 'tx_product_order.CustomerID')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_product_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_product_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_product_order.PaymentMethodID')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'ms_merchant_account.ReferralCode')
            ->where('ms_merchant_account.IsTesting', 0)
            ->select(['tx_product_order.OrderID', 'tx_product_order.MerchantID', 'tx_product_order.TotalPrice', 'ms_customer_account.FullName', 'tx_product_order.CreatedDate', 'ms_customer_account.PhoneNumber', 'ms_merchant_account.StoreName', 'ms_merchant_account.StoreAddress', 'tx_product_order.StatusOrderID', 'ms_status_order.StatusOrder', 'ms_distributor.DistributorName', 'ms_sales.SalesName', 'ms_payment_method.PaymentMethodName', 'ms_customer_account.Address', 'tx_product_order_detail.productID', 'ms_product.ProductName', 'tx_product_order_detail.Quantity', 'tx_product_order_detail.Price', 'tx_product_order_detail.Discount', 'tx_product_order_detail.Nett', 'tx_product_order_detail.SubTotalPrice']);

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('tx_product_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_product_order.CreatedDate', '<=', $toDate);
        }

        if ($paymentMethodId != null) {
            $sqlAllAccount->where('tx_product_order.PaymentMethodID', '=', $paymentMethodId);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('StatusOrder', function ($data) {
                    $pesananBaru = "S013";
                    $dikonfirmasi = "S014";
                    $dalamProses = "S019";
                    $dikirim = "S015";
                    $selesai = "S016";
                    $dibatalkan = "S017";

                    if ($data->StatusOrderID == $pesananBaru) {
                        $statusOrder = '<span class="badge badge-secondary">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dikonfirmasi) {
                        $statusOrder = '<span class="badge badge-primary">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dalamProses) {
                        $statusOrder = '<span class="badge badge-warning">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dikirim) {
                        $statusOrder = '<span class="badge badge-info">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $selesai) {
                        $statusOrder = '<span class="badge badge-success">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dibatalkan) {
                        $statusOrder = '<span class="badge badge-danger">' . $data->StatusOrder . '</span>';
                    } else {
                        $statusOrder = 'Status tidak ditemukan';
                    }

                    return $statusOrder;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/customer/transaction/detail/' . $data->OrderID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->filterColumn('tx_product_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_product_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['StatusOrder', 'Action'])
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

        $customerOrderHistory = DB::table('tx_product_order_log')
            ->leftJoin('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_product_order_log.StatusOrderId')
            ->where('tx_product_order_log.OrderID', '=', $orderId)
            ->select('tx_product_order_log.ProcessTime', 'tx_product_order_log.StatusOrderId', 'ms_status_order.StatusOrder')
            ->orderByDesc('tx_product_order_log.ProcessTime')
            ->get();

        return view('customer.transaction.details', [
            'orderId' => $orderId,
            'customer' => $customer,
            'customerOrderHistory' => $customerOrderHistory
        ]);
    }

    public function getTransactionDetails(Request $request, $orderId)
    {
        $orderById = DB::table('tx_product_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_product_order_detail.productID')
            ->where('tx_product_order_detail.OrderID', '=', $orderId)
            ->select('tx_product_order_detail.*', 'ms_product.ProductName')->get();

        if ($request->ajax()) {
            return DataTables::of($orderById)
                ->make(true);
        }
    }
}