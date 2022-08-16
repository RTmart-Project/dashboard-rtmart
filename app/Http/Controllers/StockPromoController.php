<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockPromoController extends Controller
{
    public function stockPromoInbound()
    {
        return view('stock-promo.list.index');
    }

    public function stockPromoInboundCreate()
    {
        $purchases = DB::table(DB::raw("
            (
                SELECT PurchaseID, Qty, DistributorID, InvestorID
                FROM ms_stock_product
                WHERE (PurchaseID IN (
                    SELECT PurchaseID FROM ms_stock_purchase WHERE Type LIKE 'INBOUND' AND StatusID = 2
                ) OR PurchaseID IN (
                    SELECT StockMutationID FROM ms_stock_mutation
                )) AND Qty > 0
            ) AS purchase"))
            ->join('ms_distributor', 'ms_distributor.DistributorID', 'purchase.DistributorID')
            ->join('ms_investor', 'ms_investor.InvestorID', 'purchase.InvestorID')
            ->distinct()
            ->select('purchase.PurchaseID', 'purchase.DistributorID', 'ms_distributor.DistributorName', 'ms_investor.InvestorName')
            ->get();

        return view('stock-promo.list.create', [
            'purchases' => $purchases
        ]);
    }
}
