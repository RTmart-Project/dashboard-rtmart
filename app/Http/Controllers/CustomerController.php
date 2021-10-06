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
            ->select('ms_customer_account.*', 'ms_area.AreaName', 'ms_area.Subdistrict', 'ms_area.City', 'ms_area.Province');

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
                ->make(true);
        }
    }
}