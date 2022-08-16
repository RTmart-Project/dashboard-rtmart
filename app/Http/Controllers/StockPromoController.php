<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockPromoController extends Controller
{
    public function stockPromoInbound()
    {
        return view('stock-promo.list.index');
    }

    public function stockPromoInboundCreate()
    {
        return view('stock-promo.list.create');
    }
}
