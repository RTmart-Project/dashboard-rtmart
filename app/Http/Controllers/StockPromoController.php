<?php

namespace App\Http\Controllers;

use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockPromoController extends Controller
{
    public function stockPromoInbound()
    {
        return view('stock-promo.list.index');
    }

    public function stockPromoInboundCreateByPurchase()
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

        return view('stock-promo.list.create-by-purchase', [
            'purchases' => $purchases
        ]);
    }

    public function stockPromoInboundStoreByPurchase(Request $request, StockService $stockService)
    {
        $request->validate([
            'purchase' => 'required',
            'inbound_date' => 'required',
            'purchase_price' => 'required',
            'purchase_price.*' => 'required|numeric',
            'selling_price' => 'required',
            'selling_price.*' => 'required|numeric',
            'qty_mutation' => 'required',
            'qty_mutation.*' => 'required|numeric'
        ]);

        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;
        $dateNow = date('Y-m-d H:i:s');
        $stockPromoInboundID = $stockService->generateStockPromoInboundID();
        $purchaseID = $request->input('purchase');

        $sqlPurchase = DB::table('ms_stock_purchase')
            ->where('PurchaseID', $purchaseID)
            ->select('DistributorID', 'InvestorID', 'SupplierID')->first();

        $dataStockPromo = [
            'StockPromoInboundID' => $stockPromoInboundID,
            'PurchaseID' => $purchaseID,
            'DistributorID' => $sqlPurchase->DistributorID,
            'InvestorID' => $sqlPurchase->InvestorID,
            'SupplierID' => $sqlPurchase->SupplierID,
            'InboundDate' => str_replace("T", " ", $request->input('inbound_date')),
            'CreatedBy' => $user,
            'StatusID' => 2,
            'StatusBy' => $user,
            'StatusDate' => $dateNow,
            'CreatedDate' => $dateNow,
            'Type' => 'INBOUND FROM PURCHASE'
        ];

        $productId = $request->input('product_id');
        $purchasePrice = $request->input('purchase_price');
        $sellingPrice = $request->input('selling_price');
        $qty = $request->input('qty_mutation');

        $dataStockPromoDetail = $stockService->stockPromoDetailByPurchase($purchaseID, $stockPromoInboundID, $productId, $qty, $purchasePrice, $sellingPrice);

        try {
            DB::transaction(function () use ($stockPromoInboundID, $dataStockPromo, $purchaseID, $dataStockPromoDetail, $sqlPurchase, $stockService) {
                DB::table('ms_stock_promo_inbound')->insert($dataStockPromo);
                DB::table('ms_stock_promo_inbound_detail')->insert($dataStockPromoDetail);
                $stockService->updateStockProduct($stockPromoInboundID, $purchaseID, $dataStockPromoDetail, $sqlPurchase);
            });
            return redirect()->route('stockPromo.inbound')->with('success', 'Data Stok Promo berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stockPromo.inbound')->with('failed', 'Terjadi kesalahan!');
        }
    }
}
