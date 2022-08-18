<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Services\MutationService;
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

    public function createOpname(PurchaseService $purchaseService, OpnameService $opnameService)
    {
        $inbounds = $opnameService->getInbound()->get();
        $distributors = $purchaseService->getDistributors()->get();
        $investors = DB::table('ms_investor')->get();
        $users = $purchaseService->getUsers()->get();
        return view('stock.opname.create', [
            'inbounds' => $inbounds,
            'distributors' => $distributors,
            'users' => $users,
            'investors' => $investors
        ]);
    }

    public function getProductExcluded($distributorID, PurchaseService $purchaseService)
    {
        $products = $purchaseService->getProducts($distributorID)->get()->toArray();
        return $products;
    }

    public function getDetailFromInbound($inboundID, OpnameService $opnameService)
    {
        $data = $opnameService->getDetailInbound($inboundID);

        return $data;
    }

    public function storeOpname(Request $request, OpnameService $opnameService)
    {
        $inbound = $request->input('inbound');
        $opnameID = $opnameService->generateOpnameID();
        $purchaseDate = str_replace("T", " ", $request->input('opname_date'));
        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;
        $notes = $request->input('notes');
        $dateNow = date('Y-m-d H:i:s');

        $opnameOfficer = $request->input('opname_officer');

        if ($inbound == "Lainnya") {
            $distributor = $request->input('distributor');
            $investor = $request->input('investor');

            $productID = $request->input('product');
            $label = $request->input('labeling');
            $oldGoodStock = $request->input('old_good_stock');
            $newGoodStock = $request->input('new_good_stock');
            $oldBadStock = $request->input('old_bad_stock');
            $newBadStock = $request->input('new_bad_stock');
            $purchasePriceGoodStock = $request->input('purchase_price_good_stock');
            $purchasePriceBadStock = $request->input('purchase_price_bad_stock');

            // data ms_stock_opname_detail
            $dataStockOpnameDetail = $opnameService->dataStockOpnameDetail($productID, $label, $oldGoodStock, $newGoodStock, $oldBadStock, $newBadStock, $purchasePriceGoodStock, $purchasePriceBadStock, $opnameID);
        } else {
            $stockProduct = DB::table('ms_stock_product')
                ->where('PurchaseID', $inbound)
                ->where('Qty', '>', 0);
            $inboundInfo = (clone $stockProduct)->select('InvestorID', 'DistributorID')->first();

            $distributor = $inboundInfo->DistributorID;
            $investor = $inboundInfo->InvestorID;
            $product = $request->input('product_id');
            $newQty = $request->input('new_qty');

            $detailProduct = (clone $stockProduct)->select('ProductID', 'ProductLabel', 'ConditionStock', 'Qty', 'PurchasePrice')->get()->toArray();

            // data ms_stock_opname_detail
            $dataStockOpnameDetail = [];
            foreach ($detailProduct as $key => $value) {
                $data = [
                    'StockOpnameID' => $opnameID,
                    'ProductID' => $value->ProductID,
                    'ProductLabel' => $value->ProductLabel,
                    'ConditionStock' => $value->ConditionStock,
                    'PurchasePrice' => $value->PurchasePrice,
                    'OldQty' => $value->Qty,
                    'NewQty' => $newQty[$key]
                ];
                array_push($dataStockOpnameDetail, $data);
            }
        }

        // data ms_stock_opname
        $dataStockOpname = [
            'StockOpnameID' => $opnameID,
            'OpnameDate' => $purchaseDate,
            'CreatedBy' => $user,
            'CreatedDate' => $dateNow,
            'DistributorID' => $distributor,
            'InvestorID' => $investor,
            'Notes' => $notes
        ];

        // data ms_stock_opname_officer
        $dataOpnameOfficer = $opnameService->dataOfficer($opnameOfficer, $opnameID);

        try {
            DB::transaction(function () use ($dataStockOpname, $dataStockOpnameDetail, $dataOpnameOfficer, $distributor, $investor, $user, $inbound) {
                DB::table('ms_stock_opname')->insert($dataStockOpname);
                DB::table('ms_stock_opname_detail')->insert($dataStockOpnameDetail);
                DB::table('ms_stock_opname_officer')->insert($dataOpnameOfficer);
                foreach ($dataStockOpnameDetail as $key => $value) {
                    $differentQty = $value['NewQty'] - $value['OldQty'];

                    if ($differentQty < 0) {
                        $differentQty = 0;
                    }

                    $stockProduct = DB::table('ms_stock_product')
                        ->where('ProductID', $value['ProductID'])
                        ->where('InvestorID', $investor)->where('ProductLabel', $value['ProductLabel'])
                        ->where('ConditionStock', $value['ConditionStock'])->where('DistributorID', $distributor);

                    $qtyBefore = (clone $stockProduct)
                        ->sum('Qty');

                    if ($inbound != "Lainnya") {
                        $stockProductID = (clone $stockProduct)->select('StockProductID')->first();
                        $referenceStockProductID = $stockProductID->StockProductID;
                    } else {
                        $referenceStockProductID = NULL;
                    }

                    $stockProductID = DB::table('ms_stock_product')->insertGetId([
                        'PurchaseID' => $value['StockOpnameID'],
                        'ProductID' => $value['ProductID'],
                        'ProductLabel' => $value['ProductLabel'],
                        'ConditionStock' => $value['ConditionStock'],
                        'Qty' => $differentQty,
                        'PurchasePrice' => $value['PurchasePrice'],
                        'DistributorID' => $distributor,
                        'InvestorID' => $investor,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'Type' => 'OPNAME',
                        'LevelType' => 2
                    ], 'StockProductID');

                    DB::table('ms_stock_product_log')->insert([
                        'StockProductID' => $stockProductID,
                        'ReferenceStockProductID' => $referenceStockProductID,
                        'ProductID' => $value['ProductID'],
                        'QtyBefore' => $qtyBefore,
                        'QtyAction' => $value['NewQty'] - $value['OldQty'],
                        'QtyAfter' => $qtyBefore + ($value['NewQty'] - $value['OldQty']),
                        'PurchasePrice' => $value['PurchasePrice'],
                        'SellingPrice' => 0,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'ActionBy' => $user,
                        'ActionType' => 'OPNAME'
                    ]);

                    if ($inbound != "Lainnya") {
                        if ($differentQty <= 0) {
                            $valQty = $value['NewQty'];
                        } else {
                            $valQty = $value['OldQty'];
                        }

                        (clone $stockProduct)
                            ->where('PurchaseID', $inbound)
                            ->update([
                                'Qty' => $valQty * 1
                            ]);
                    }
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
        $filterTipe = $request->input('filterTipe');

        $sqlGetPurchase = $purchaseService->getStockPurchase($fromDate, $toDate, $filterTipe);

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
                    if (($data->InvoiceNumber == null || $data->InvoiceFile == null) && ((Auth::user()->RoleID == "IT") || (Auth::user()->RoleID == "FI")) && str_contains($data->PurchaseID, "PRCH")) {
                        $editInvoice = '<a href="/stock/purchase/edit/invoice/' . $data->PurchaseID . '" class="btn btn-xs btn-primary text-nowrap">Edit Invoice</a>';
                    } else {
                        $editInvoice = '';
                    }

                    if ($data->StatusBy == null && ((Auth::user()->RoleID == "IT") || (Auth::user()->RoleID == "FI"))) {
                        $ubah = '<a class="btn btn-xs btn-warning" href="/stock/purchase/edit/' . $data->PurchaseID . '">Ubah</a>';
                    } else {
                        $ubah = '';
                    }
                    $action = '<div class="d-flex flex-wrap" style="gap:5px">
                                ' . $ubah . '
                                <a href="/stock/purchase/detail/' . $data->PurchaseID . '" class="btn btn-xs btn-info">Detail</a>
                                ' . $editInvoice . '
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
                ->rawColumns(['InvoiceNumber', 'InvoiceFile', 'StatusName', 'Action', 'Confirmation'])
                ->make(true);
        }
    }

    public function getPurchaseAllProduct(Request $request, PurchaseService $purchaseService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $filterTipe = $request->input('filterTipe');

        $sqlGetPurchaseAllProduct = $purchaseService->getStockPurchaseAllProduct($fromDate, $toDate, $filterTipe);

        $data = $sqlGetPurchaseAllProduct;

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
                ->rawColumns(['InvoiceNumber', 'InvoiceFile', 'StatusName'])
                ->make(true);
        }
    }

    public function createPurchase(PurchaseService $purchaseService)
    {
        $suppliers = DB::table('ms_suppliers')->get();
        $investors = DB::table('ms_investor')->get();
        $products = DB::table('ms_product')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
            ->where('ms_product.IsActive', 1)
            ->orderBy('ms_product.ProductID')
            ->get();
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
        $products = DB::table('ms_product')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
            ->where('ms_product.IsActive', 1)
            ->orderBy('ms_product.ProductID')
            ->get();
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

    public function detailPurchase(PurchaseService $purchaseService, $purchaseID, Request $request)
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

    public function editInvoice($purchaseID)
    {
        $sql = DB::table('ms_stock_purchase')
            ->join('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
            ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
            ->join('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase.SupplierID')
            ->select('ms_stock_purchase.PurchaseID', 'ms_stock_purchase.InvoiceNumber', 'ms_stock_purchase.InvoiceFile', 'ms_distributor.DistributorName', 'ms_investor.InvestorName', 'ms_suppliers.SupplierName')
            ->where('ms_stock_purchase.PurchaseID', $purchaseID)
            ->first();

        return view('stock.purchase.editInvoice', [
            'purchase' => $sql
        ]);
    }

    public function updateInvoice($purchaseID, Request $request)
    {
        $sql = DB::table('ms_stock_purchase')
            ->where('PurchaseID', $purchaseID)
            ->select('ms_stock_purchase.InvoiceFile')->first();

        if ($sql->InvoiceFile == null) {
            $img = "required";
        } else {
            $img = "";
        }

        $request->validate([
            'invoice_number' => 'required',
            'invoice_image' => $img
        ]);

        if ($request->hasFile('invoice_image')) {
            $invoiceFile = str_replace(' ', '', $purchaseID) . '_' . time() . '.' . $request->file('invoice_image')->extension();
            $request->file('invoice_image')->move($this->saveImageUrl . 'stock_invoice/', $invoiceFile);
        } else {
            $invoiceFile = $sql->InvoiceFile;
        }

        $updateInvoice = DB::table('ms_stock_purchase')
            ->where('PurchaseID', $purchaseID)
            ->update([
                'InvoiceNumber' => $request->input('invoice_number'),
                'InvoiceFile' => $invoiceFile
            ]);

        if ($updateInvoice) {
            return redirect()->route('stock.purchase')->with('success', 'Data Invoice Purchase Stock berhasil diubah');
        } else {
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
                    } elseif ($data->StockPromoID != null) {
                        $purchaseID = $data->StockPromoInboundID . '<br> dari ' . $data->PurchaseID;
                    } else {
                        $purchaseID = $data->PurchaseID;
                    }

                    return $purchaseID;
                })
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('PurchasePrice', function ($data) {
                    if (Auth::user()->RoleID == "AD") {
                        $purchasePrice = "";
                    } else {
                        $purchasePrice = Helper::formatCurrency($data->PurchasePrice, "Rp ");
                    }
                    return $purchasePrice;
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

    public function mutationStock()
    {
        return view('stock.mutation.index');
    }

    public function getMutationStock(Request $request, PurchaseService $purchaseService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlGetMutation = $purchaseService->getMutations();

        if ($fromDate != '' && $toDate != '') {
            $sqlGetMutation->whereDate('ms_stock_mutation.MutationDate', '>=', $fromDate)
                ->whereDate('ms_stock_mutation.MutationDate', '<=', $toDate);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetMutation->whereRaw("from_distributor.Depo = '$depoUser' OR to_distributor.Depo = '$depoUser'");
        }

        $data = $sqlGetMutation;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('MutationDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->MutationDate));
                })
                ->addColumn('Detail', function ($data) {
                    $detail = '<a class="btn btn-xs btn-info" href="/stock/mutation/detail/' . $data->StockMutationID . '">Detail</a>';
                    return $detail;
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }
    }

    public function detailMutation($mutationID, PurchaseService $purchaseService)
    {
        $data = $purchaseService->getMutationByID($mutationID);

        return view('stock.mutation.detail', [
            'mutation' => $data
        ]);
    }

    public function createMutation(PurchaseService $purchaseService)
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

        $distributors = $purchaseService->getDistributors()->get();

        return view('stock.mutation.create', [
            'distributors' => $distributors,
            'purchases' => $purchases
        ]);
    }

    public function getExcludeDistributorID($distributorID)
    {
        $sql = DB::table('ms_distributor')
            ->where('ms_distributor.IsActive', 1)
            ->whereNotIn('ms_distributor.DistributorID', ['D-0000-000000', $distributorID])
            ->select('ms_distributor.DistributorID', 'ms_distributor.DistributorName')
            ->get();

        return $sql;
    }

    public function getProductByPurchaseID($purchaseID, MutationService $mutationService)
    {
        $data = $mutationService->getProductByPurchaseID($purchaseID);

        return $data;
    }

    public function storeMutation(Request $request, MutationService $mutationService)
    {
        $request->validate([
            'purchase' => 'required',
            'distributor' => 'required',
            'mutation_date' => 'required',
            'qty_mutation' => 'required',
            'qty_mutation.*' => 'required|numeric'
        ]);

        $mutationID = $mutationService->generateMutationID();
        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;
        $dateNow = date('Y-m-d H:i:s');
        $purchaseID = $request->input('purchase');
        $purchase = DB::table('ms_stock_product')->where('PurchaseID', $purchaseID)
            ->where('Qty', '>', 0)
            ->select('DistributorID', 'InvestorID')->first();
        $toDistributor = $request->input('distributor');

        $dataMutation = [
            'StockMutationID' => $mutationID,
            'MutationDate' => str_replace("T", " ", $request->input('mutation_date')),
            'CreatedBy' => $user,
            'CreatedDate' => $dateNow,
            'PurchaseID' => $purchaseID,
            'FromDistributor' => $purchase->DistributorID,
            'ToDistributor' => $toDistributor,
            'Notes' => $request->input('notes')
        ];

        $productId = $request->input('product_id');
        $qty = $request->input('qty_mutation');

        $dataMutationDetail = $mutationService->dataMutationDetail($purchaseID, $productId, $qty, $mutationID);
        $dataStockProduct = $mutationService->dataStockProduct($dataMutationDetail, $purchase, $toDistributor, $dateNow);

        try {
            DB::transaction(function () use ($dataMutation, $dataMutationDetail, $mutationService, $dataStockProduct, $purchaseID, $purchase, $dateNow, $user) {
                DB::table('ms_stock_mutation')->insert($dataMutation);
                DB::table('ms_stock_mutation_detail')->insert($dataMutationDetail);
                $mutationService->insertIntoStockProductAndLog($dataStockProduct, $purchaseID, $purchase, $dateNow, $user);
                $mutationService->updateQtyStockProduct($dataMutationDetail, $purchaseID, $purchase);
            });
            return redirect()->route('stock.mutation')->with('success', 'Data Mutasi Stok berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stock.mutation')->with('failed', 'Terjadi kesalahan!');
        }
    }
}
