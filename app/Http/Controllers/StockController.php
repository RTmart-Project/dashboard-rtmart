<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Services\OpnameService;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;
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

    public function opname()
    {
        return view('stock.opname.index');
    }

    public function getOpname(Request $request, OpnameService $opnameService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlGetOpname = $opnameService->getStockOpname();

        if ($fromDate != '' && $toDate != '') {
            $sqlGetOpname->whereDate('ms_stock_opname.OpnameDate', '>=', $fromDate)
                ->whereDate('ms_stock_opname.OpnameDate', '<=', $toDate);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetOpname->where('ms_distributor.Depo', '=', $depoUser);
        }

        if (Auth::user()->InvestorID != null) {
            $investorUser = Auth::user()->InvestorID;
            $sqlGetOpname->where('ms_stock_opname.InvestorID', $investorUser);
        }

        $data = $sqlGetOpname;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('OpnameDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->OpnameDate));
                })
                ->addColumn('Detail', function ($data) {
                    return '<a class="btn btn-sm btn-warning" href="/stock/opname/detail/' . $data->StockOpnameID . '">Detail</a>';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }
    }

    public function sumOldProduct($distributorID, $investorID, $productID, $label)
    {
        $sql = DB::table('ms_stock_product')
            ->where('DistributorID', $distributorID)
            ->where('ProductID', $productID)
            ->where('ProductLabel', $label);

        if ($investorID != "null") {
            $sql->where('InvestorID', $investorID);
        } else {
            $sql->whereNull('InvestorID');
        }

        $sumOldGoodStock = (clone $sql)->where('ConditionStock', 'GOOD STOCK')->sum('Qty');
        $sumOldBadStock = (clone $sql)->where('ConditionStock', 'BAD STOCK')->sum('Qty');

        $sumOld = new stdClass();
        $sumOld->goodStock = $sumOldGoodStock;
        $sumOld->badStock = $sumOldBadStock;

        $response = json_encode($sumOld);

        return $response;
    }

    public function createOpname(PurchaseService $purchaseService)
    {
        $distributors = $purchaseService->getDistributors()->get();
        $users = $purchaseService->getUsers()->get();
        $products = $purchaseService->getProducts()->get();
        $investors = DB::table('ms_investor')->get();
        return view('stock.opname.create', [
            'distributors' => $distributors,
            'products' => $products,
            'users' => $users,
            'investors' => $investors
        ]);
    }

    public function storeOpname(Request $request, OpnameService $opnameService)
    {
        $request->validate([
            'distributor' => 'required|exists:ms_distributor,DistributorID',
            'opname_date' => 'required',
            'investor' => 'required',
            'opname_officer' => 'required',
            'opname_officer.*' => 'required|exists:ms_user,UserID',
            'product' => 'required',
            'product.*' => 'required',
            'labeling' => 'required',
            'labeling.*' => 'required',
            'new_good_stock' => 'required',
            'new_good_stock.*' => 'required|numeric|gte:0',
            'new_bad_stock' => 'required',
            'new_bad_stock.*' => 'required|numeric|gte:0'
        ]);

        $opnameID = $opnameService->generateOpnameID();
        $purchaseDate = str_replace("T", " ", $request->input('opname_date'));
        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;
        $distributor = $request->input('distributor');
        $investor = $request->input('investor');

        // insert data ms_stock_opname
        $dataStockOpname = [
            'StockOpnameID' => $opnameID,
            'OpnameDate' => $purchaseDate,
            'CreatedBy' => $user,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'DistributorID' => $distributor,
            'InvestorID' => $investor,
            'Notes' => $request->input('notes')
        ];

        $opnameOfficer = $request->input('opname_officer');
        // insert data ms_stock_opname_officer
        $dataOpnameOfficer = $opnameService->dataOfficer($opnameOfficer, $opnameID);

        $productID = $request->input('product');
        $label = $request->input('labeling');
        $oldGoodStock = $request->input('old_good_stock');
        $newGoodStock = $request->input('new_good_stock');
        $oldBadStock = $request->input('old_bad_stock');
        $newBadStock = $request->input('new_bad_stock');
        // insert data ms_stock_opname_detail
        $dataStockOpnameDetail = $opnameService->dataStockOpnameDetail($distributor, $productID, $label, $oldGoodStock, $newGoodStock, $oldBadStock, $newBadStock, $opnameID);

        try {
            DB::transaction(function () use ($dataStockOpname, $dataStockOpnameDetail, $dataOpnameOfficer, $distributor, $investor, $user) {
                DB::table('ms_stock_opname')->insert($dataStockOpname);
                DB::table('ms_stock_opname_detail')->insert($dataStockOpnameDetail);
                DB::table('ms_stock_opname_officer')->insert($dataOpnameOfficer);
                foreach ($dataStockOpnameDetail as $key => $value) {
                    $stockProductID = DB::table('ms_stock_product')->insertGetId([
                        'PurchaseID' => $value['StockOpnameID'],
                        'ProductID' => $value['ProductID'],
                        'ProductLabel' => $value['ProductLabel'],
                        'ConditionStock' => $value['ConditionStock'],
                        'Qty' => $value['NewQty'] - $value['OldQty'],
                        'PurchasePrice' => $value['PurchasePrice'],
                        'DistributorID' => $distributor,
                        'InvestorID' => $investor,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'Type' => 'OPNAME',
                        'LevelType' => 2
                    ], 'StockProductID');

                    DB::table('ms_stock_product_log')->insert([
                        'StockProductID' => $stockProductID,
                        'ProductID' => $value['ProductID'],
                        'QtyBefore' => $value['OldQty'],
                        'QtyAction' => $value['NewQty'] - $value['OldQty'],
                        'QtyAfter' => $value['NewQty'],
                        'PurchasePrice' => $value['PurchasePrice'],
                        'SellingPrice' => 0,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'ActionBy' => $user,
                        'ActionType' => 'OPNAME'
                    ]);
                }
            });
            return redirect()->route('stock.opname')->with('success', 'Data Stock Opname berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stock.opname')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function detailOpname($stockOpnameID, OpnameService $opnameService)
    {
        $opnameByID = $opnameService->getStockOpnameByID($stockOpnameID);
        return view('stock.opname.detail', [
            'opnameByID' => $opnameByID
        ]);
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
        if (Auth::user()->InvestorID != null) {
            $investorUser = Auth::user()->InvestorID;
            $sqlGetPurchase->where('ms_stock_purchase.InvestorID', $investorUser);
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
                    if ($data->StatusBy == null && ((Auth::user()->RoleID == "IT") || (Auth::user()->RoleID == "FI"))) {
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
                    if ($data->StatusBy == null && ((Auth::user()->RoleID == "IT") || (Auth::user()->RoleID == "FI"))) {
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
        $investors = DB::table('ms_investor')->get();
        $products = $purchaseService->getProducts()->get();
        $distributors = $purchaseService->getDistributors()->get();

        return view('stock.purchase.create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors,
            'investors' => $investors
        ]);
    }

    public function storePurchase(Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'distributor' => 'required',
            'investor' => 'required',
            'purchase_date' => 'required',
            'supplier' => 'required',
            'invoice_number' => 'required',
            'product' => 'required',
            'product.*' => 'required',
            'labeling' => 'required',
            'labeling.*' => 'required',
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

        $investor = $request->input('investor');
        if ($investor == "Lainnya") {
            $request->validate([
                'other_investor' => 'unique:ms_investor,InvestorName'
            ]);
            DB::table('ms_investor')->insert(['InvestorName' => $request->input('other_investor')]);
            $getInvestor = DB::table('ms_investor')->where('InvestorName', $request->input('other_investor'))->select('InvestorID')->first();
            $investorID = $getInvestor->InvestorID;
        } else {
            $investorID = $investor;
        }

        $dataPurchase = [
            'PurchaseID' => $purchaseID,
            'DistributorID' => $request->input('distributor'),
            'InvestorID' => $investorID,
            'SupplierID' => $supplierID,
            'PurchaseDate' => $purchaseDate,
            'CreatedBy' => $user,
            'StatusID' => 1,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'InvoiceNumber' => $request->input('invoice_number'),
            'InvoiceFile' => $invoiceFile
        ];

        $productID = $request->input('product');
        $labeling = $request->input('labeling');
        $qty = $request->input('quantity');
        $purchasePrice = $request->input('purchase_price');

        $dataPurchaseDetail = $purchaseService->dataPurchaseDetail($productID, $labeling, $qty, $purchasePrice, $purchaseID);

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
        $investors = DB::table('ms_investor')->get();
        $products = $purchaseService->getProducts()->get();
        $distributors = $purchaseService->getDistributors()->get();
        $purchaseByID = $purchaseService->getStockPurchaseByID($purchaseID);

        return view('stock.purchase.edit', [
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors,
            'purchaseByID' => $purchaseByID,
            'investors' => $investors
        ]);
    }

    public function updatePurchase($purchaseID, Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'distributor' => 'required',
            'investor' => 'required',
            'purchase_date' => 'required',
            'supplier' => 'required',
            'invoice_number' => 'required',
            'product' => 'required',
            'product.*' => 'required',
            'labeling' => 'required',
            'labeling.*' => 'required',
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

        $investor = $request->input('investor');
        if ($investor == "Lainnya") {
            $request->validate([
                'other_investor' => 'unique:ms_investor,InvestorName'
            ]);
            DB::table('ms_investor')->insert(['InvestorName' => $request->input('other_investor')]);
            $getInvestor = DB::table('ms_investor')->where('InvestorName', $request->input('other_investor'))->select('InvestorID')->first();
            $investorID = $getInvestor->InvestorID;
        } else {
            $investorID = $investor;
        }

        $dataPurchase = [
            'DistributorID' => $request->input('distributor'),
            'InvestorID' => $investorID,
            'SupplierID' => $supplierID,
            'PurchaseDate' => $purchaseDate,
            'InvoiceNumber' => $request->input('invoice_number'),
            'InvoiceFile' => $invoiceFile
        ];

        $productID = $request->input('product');
        $labeling = $request->input('labeling');
        $qty = $request->input('quantity');
        $purchasePrice = $request->input('purchase_price');

        $dataPurchaseDetail = $purchaseService->dataPurchaseDetail($productID, $labeling, $qty, $purchasePrice, $purchaseID);

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

    public function listStock()
    {
        return view('stock.list.index');
    }

    public function getListStock(Request $request, PurchaseService $purchaseService)
    {
        $distributorId = $request->input('distributorId');

        $sqlGetListStocks = $purchaseService->getStocks();

        if ($distributorId != null) {
            $sqlGetListStocks->where('ms_distributor.DistributorID', '=', $distributorId);
        }
        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetListStocks->where('ms_distributor.Depo', '=', $depoUser);
        }
        if (Auth::user()->InvestorID != null) {
            $investorUser = Auth::user()->InvestorID;
            $sqlGetListStocks->where('ms_stock_product.InvestorID', $investorUser);
        }

        // Get data response
        $data = $sqlGetListStocks;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="80">';
                })
                ->addColumn('Detail', function ($data) {
                    if ($data->InvestorID == null) {
                        $data->InvestorID = "no-investor";
                    }
                    return '<a class="btn btn-sm btn-warning" href="/stock/list/detail/' . $data->DistributorID . '/' . $data->InvestorID . '/' . $data->ProductID . '/' . $data->ProductLabel . '">Detail</a>';
                })
                ->rawColumns(['ProductImage', 'Detail'])
                ->make(true);
        }
    }

    public function detailStock($distributorID, $investorID, $productID, $label, PurchaseService $purchaseService, Request $request)
    {
        $sql = $purchaseService->getDetailStock($distributorID, $productID, $label);

        if ($investorID != "no-investor") {
            $sql->where('stock_product.InvestorID', $investorID);
            $getInvestor = DB::table('ms_investor')->where('InvestorID', $investorID)->select('InvestorName')->first();
            $investor = $getInvestor->InvestorName;
        } else {
            $sql->whereNull('stock_product.InvestorID');
            $investor = "-";
        }

        $data = $sql->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('PurchaseID', function ($data) {
                    if ($data->RefPurchaseID != null) {
                        $purchaseID = $data->PurchaseID . '<br> dari ' . $data->RefPurchaseID;
                    } elseif ($data->ActionType == "OUTBOUND") {
                        $purchaseID = $data->DeliveryOrderID . '<br> dari ' . $data->PurchaseID;
                    } else {
                        $purchaseID = $data->PurchaseID;
                    }

                    return $purchaseID;
                })
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('PurchasePrice', function ($data) {
                    return Helper::formatCurrency($data->PurchasePrice, "Rp ");
                })
                ->rawColumns(['PurchaseID'])
                ->make(true);
        }

        return view('stock.list.detail', [
            'distributor' => DB::table('ms_distributor')->where('DistributorID', $distributorID)->select('DistributorName')->first(),
            'investor' => $investor,
            'product' => DB::table('ms_stock_product')
                ->join('ms_product', 'ms_product.ProductID', 'ms_stock_product.ProductID')
                ->where('ms_stock_product.ProductID', $productID)
                ->where('ms_stock_product.ProductLabel', $label)
                ->select('ms_stock_product.ProductLabel', 'ms_product.ProductImage', 'ms_product.ProductName')
                ->first()
        ]);
    }
}
