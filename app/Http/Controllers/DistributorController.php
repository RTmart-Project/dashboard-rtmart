<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function account()
    {
        return view('distributor.account.index');
    }

    public function getAccounts(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlAllAccount = DB::table('ms_distributor')
            ->where('Ownership', '=', 'RTMart')
            ->where('Email', '!=', NULL)
            ->select('*');

        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_distributor.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_distributor.CreatedDate', '<=', $toDate);
        }

        $data = $sqlAllAccount->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('Y-m-d', strtotime($data->CreatedDate));
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/distributor/account/product/' . $data->DistributorID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->rawColumns(['CreatedDate', 'Action'])
                ->make(true);
        }
    }

    public function productDetails($distributorId)
    {
        $distributor = DB::table('ms_distributor')
            ->where('ms_distributor.DistributorID', '=', $distributorId)
            ->select('DistributorName')
            ->first();

        return view('distributor.product.index', [
            'distributorId' => $distributorId,
            'distributor' => $distributor
        ]);
    }

    public function getProductDetails(Request $request, $distributorId)
    {
        $distributorProducts = DB::table('ms_distributor_product_price')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_distributor_product_price.ProductID')
            ->join('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_product_price.GradeID')
            ->where('ms_distributor_product_price.DistributorID', '=', $distributorId)
            ->select('ms_distributor_product_price.ProductID', 'ms_product.ProductName', 'ms_distributor_grade.Grade', 'ms_distributor_product_price.Price');

        $data = $distributorProducts->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->make(true);
        }
    }
}
