<?php

namespace App\Http\Controllers;

use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    protected $saveImageUrl;
    protected $baseImageUrl;

    public function __construct()
    {
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function purchase()
    {
        return view('stock.purchase.index');
    }

    public function getPurchase(Request $request, PurchaseService $purchaseService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlGetPurchase = $purchaseService->getStockPurchase();

        if ($fromDate != '' && $toDate != '') {
            $sqlGetPurchase->whereDate('ms_stock_purchase.PurchaseDate', '>=', $fromDate)
                ->whereDate('ms_stock_purchase.PurchaseDate', '<=', $toDate);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetPurchase->where('ms_distributor.Depo', '=', $depoUser);
        }

        $data = $sqlGetPurchase;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('PurchaseDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->PurchaseDate));
                })
                ->editColumn('StatusName', function ($data) {
                    if ($data->StatusID == 1) {
                        $color = 'warning';
                    } elseif ($data->StatusID == 2) {
                        $color = 'success';
                    } else {
                        $color = 'danger';
                    }
                    return '<span class="badge badge-' . $color . '">' . $data->StatusName . '</span>';
                })
                ->editColumn('InvoiceFile', function ($data) {
                    $baseImageUrl = config('app.base_image_url');
                    $invoice = '<a href="' . $baseImageUrl . 'stock_invoice/' . $data->InvoiceFile . '" target="_blank">' . $data->InvoiceFile . '</a>';
                    return $invoice;
                })
                ->addColumn('Action', function ($data) {
                    if ($data->StatusBy == null) {
                        $ubah = '<a class="btn btn-xs btn-warning" href="/stock/purchase/edit/' . $data->PurchaseID . '">Ubah</a>';
                    } else {
                        $ubah = '';
                    }
                    $action = '<div class="d-flex flex-wrap" style="gap:5px">' . $ubah . '
                                <a href="/stock/purchase/detail/' . $data->PurchaseID . '" class="btn btn-xs btn-info">Detail</a>
                               </div>';

                    return $action;
                })
                ->addColumn('Confirmation', function ($data) {
                    if ($data->StatusBy == null) {
                        $btn = '<div class="d-flex flex-wrap" style="gap:5px">
                                    <a class="btn btn-xs btn-success btn-approved" data-purchase-id="' . $data->PurchaseID . '">Setujui</a>
                                    <a class="btn btn-xs btn-danger btn-reject" data-purchase-id="' . $data->PurchaseID . '">Tolak</a>
                                </div>';
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->rawColumns(['InvoiceFile', 'StatusName', 'Action', 'Confirmation'])
                ->make(true);
        }
    }

    public function createPurchase(PurchaseService $purchaseService)
    {
        $suppliers = DB::table('ms_suppliers')->get();
        $products = $purchaseService->getProducts()->get();
        $distributors = $purchaseService->getDistributors()->get();

        return view('stock.purchase.create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors
        ]);
    }

    public function storePurchase(Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'distributor' => 'required',
            'purchase_date' => 'required',
            'supplier' => 'required',
            'invoice_number' => 'required',
            'product' => 'required',
            'product.*' => 'required',
            'quantity' => 'required',
            'quantity.*' => 'required|numeric|gte:1',
            'purchase_price' => 'required',
            'purchase_price.*' => 'required|numeric|gte:1'
        ]);

        $purchaseID = $purchaseService->generatePurchaseID();
        $purchaseDate = str_replace("T", " ", $request->input('purchase_date'));
        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;

        if ($request->hasFile('invoice_image')) {
            $invoiceFile = str_replace(' ', '', $purchaseID) . '_' . time() . '.' . $request->file('invoice_image')->extension();
            $request->file('invoice_image')->move($this->saveImageUrl . 'stock_invoice/', $invoiceFile);
        } else {
            $invoiceFile = NULL;
        }

        $supplier = $request->input('supplier');

        if ($supplier == "Lainnya") {
            $request->validate([
                'other_supplier' => 'unique:ms_suppliers,SupplierName'
            ]);
            DB::table('ms_suppliers')->insert(['SupplierName' => $request->input('other_supplier')]);
            $getSupplier = DB::table('ms_suppliers')->where('SupplierName', $request->input('other_supplier'))->select('SupplierID')->first();
            $supplierID = $getSupplier->SupplierID;
        } else {
            $supplierID = $supplier;
        }

        $dataPurchase = [
            'PurchaseID' => $purchaseID,
            'DistributorID' => $request->input('distributor'),
            'SupplierID' => $supplierID,
            'PurchaseDate' => $purchaseDate,
            'CreatedBy' => $user,
            'StatusID' => 1,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'InvoiceNumber' => $request->input('invoice_number'),
            'InvoiceFile' => $invoiceFile
        ];

        $productID = $request->input('product');
        $qty = $request->input('quantity');
        $purchasePrice = $request->input('purchase_price');

        $dataPurchaseDetail = $purchaseService->dataPurchaseDetail($productID, $qty, $purchasePrice, $purchaseID);

        try {
            DB::transaction(function () use ($dataPurchase, $dataPurchaseDetail) {
                DB::table('ms_stock_purchase')->insert($dataPurchase);
                DB::table('ms_stock_purchase_detail')->insert($dataPurchaseDetail);
            });
            return redirect()->route('stock.purchase')->with('success', 'Data Purchase Stock berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stock.purchase')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function editPurchase(PurchaseService $purchaseService, $purchaseID)
    {
        $suppliers = DB::table('ms_suppliers')->get();
        $products = $purchaseService->getProducts()->get();
        $distributors = $purchaseService->getDistributors()->get();
        $purchaseByID = $purchaseService->getStockPurchaseByID($purchaseID);

        return view('stock.purchase.edit', [
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors,
            'purchaseByID' => $purchaseByID
        ]);
    }

    public function updatePurchase($purchaseID, Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'distributor' => 'required',
            'purchase_date' => 'required',
            'supplier' => 'required',
            'invoice_number' => 'required',
            'product' => 'required',
            'product.*' => 'required',
            'quantity' => 'required',
            'quantity.*' => 'required|numeric|gte:1',
            'purchase_price' => 'required',
            'purchase_price.*' => 'required|numeric|gte:1'
        ]);

        $purchaseDate = str_replace("T", " ", $request->input('purchase_date'));
        $invoiceOld = DB::table('ms_stock_purchase')->where('PurchaseID', $purchaseID)->select('InvoiceFile')->first();

        if ($request->hasFile('invoice_image')) {
            $invoiceFile = str_replace(' ', '', $purchaseID) . '_' . time() . '.' . $request->file('invoice_image')->extension();
            unlink($this->saveImageUrl . 'stock_invoice/' . $invoiceOld->InvoiceFile);
            $request->file('invoice_image')->move($this->saveImageUrl . 'stock_invoice/', $invoiceFile);
        } else {
            $invoiceFile = $invoiceOld->InvoiceFile;
        }

        $supplier = $request->input('supplier');

        if ($supplier == "Lainnya") {
            $request->validate([
                'other_supplier' => 'unique:ms_suppliers,SupplierName'
            ]);
            DB::table('ms_suppliers')->insert(['SupplierName' => $request->input('other_supplier')]);
            $getSupplier = DB::table('ms_suppliers')->where('SupplierName', $request->input('other_supplier'))->select('SupplierID')->first();
            $supplierID = $getSupplier->SupplierID;
        } else {
            $supplierID = $supplier;
        }

        $dataPurchase = [
            'DistributorID' => $request->input('distributor'),
            'SupplierID' => $supplierID,
            'PurchaseDate' => $purchaseDate,
            'InvoiceNumber' => $request->input('invoice_number'),
            'InvoiceFile' => $invoiceFile
        ];

        $productID = $request->input('product');
        $qty = $request->input('quantity');
        $purchasePrice = $request->input('purchase_price');

        $dataPurchaseDetail = $purchaseService->dataPurchaseDetail($productID, $qty, $purchasePrice, $purchaseID);

        try {
            DB::transaction(function () use ($purchaseID, $dataPurchase, $dataPurchaseDetail) {
                DB::table('ms_stock_purchase')->where('PurchaseID', $purchaseID)->update($dataPurchase);
                DB::table('ms_stock_purchase_detail')->where('PurchaseID', $purchaseID)->delete();
                DB::table('ms_stock_purchase_detail')->insert($dataPurchaseDetail);
            });
            return redirect()->route('stock.purchase')->with('success', 'Data Purchase Stock berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('stock.purchase')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function detailPurchase(PurchaseService $purchaseService, $purchaseID)
    {
        $purchaseByID = $purchaseService->getStockPurchaseByID($purchaseID);
        return view('stock.purchase.detail', [
            'purchaseByID' => $purchaseByID
        ]);
    }

    public function confirmPurchase($status, $purchaseID, PurchaseService $purchaseService)
    {
        try {
            $purchaseService->confirmationPurchase($status, $purchaseID);
            return redirect()->route('stock.purchase')->with('success', 'Data Purchase Stock berhasil dikonfirmasi');
        } catch (\Throwable $th) {
            return redirect()->route('stock.purchase')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function readyStock()
    {
        return view('stock.ready.index');
    }

    public function getReadyStock(Request $request, PurchaseService $purchaseService)
    {
        $distributorId = $request->input('distributorId');

        $sqlGetReadyStocks = $purchaseService->getStocks();

        if ($distributorId != null) {
            $sqlGetReadyStocks->where('ms_distributor.DistributorID', '=', $distributorId);
        }
        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetReadyStocks->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlGetReadyStocks;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="80">';
                })
                ->rawColumns(['ProductImage'])
                ->make(true);
        }
    }
}
