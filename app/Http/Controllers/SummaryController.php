<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function getSummary()
    {
        // DB::table('tx_merchant_order')
        //     ->whereDate('CreatedDate', '')
        //     ->select('CreatedDate')
        //     ->get();

        dd("ok");
    }
}
