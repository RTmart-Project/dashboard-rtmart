<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    public function getPaymentMethods(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('ms_payment_method')->select('PaymentMethodID', 'PaymentMethodName')->get();

            return response($data);
        }
    }
}
