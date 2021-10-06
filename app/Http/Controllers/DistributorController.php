<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function getAccounts(Request $request)
    {
        if ($request->ajax()) {
            $sqlAllAccount = DB::table('ms_distributor')
                ->where('Ownership', '=', 'RTMart')
                ->where('Email', '!=', NULL)
                ->select('DistributorID', 'DistributorName')->get();

            return response($sqlAllAccount);
        }
    }
}