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
        $investors = DB::table('ms_investor')->where('IsActive', 1)->get();
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

    public function purchasePlan(Request $request, PurchaseService $purchaseService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $sqlPurchasePlan = $purchaseService->getPurchasePlan();

        if ($fromDate != '' && $toDate != '') {
            $sqlPurchasePlan->whereDate('ms_purchase_plan.PlanDate', '>=', $fromDate)
                ->whereDate('ms_purchase_plan.PlanDate', '<=', $toDate);
        }
        $data = $sqlPurchasePlan;

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('PlanDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->PlanDate));
                })
                ->addColumn('FlagPurchasePlan', function ($data) {
                    if ($data->PurchaseID === null && $data->StatusID === 9) {
                        $flag = '<span class="badge badge-secondary">Belum di Purchase</span>';
                    } else if ($data->PurchaseID !== null && $data->StatusID === 9) {
                        $flag = '<span class="badge badge-primary">Sudah di Purchase</span>';
                    } else {
                        $flag = '-';
                    }
                    return $flag;
                })
                ->editColumn('ConfirmDate', function ($data) {
                    if ($data->ConfirmDate !== null) {
                        $confirmDate = date('d M Y H:i', strtotime($data->ConfirmDate));
                    } else {
                        $confirmDate = '';
                    }
                    return $confirmDate;
                })
                ->editColumn('StatusName', function ($data) {
                    if ($data->StatusID === 8) {
                        $badge = 'badge-warning';
                    } elseif ($data->StatusID === 9) {
                        $badge = 'badge-success';
                    } elseif ($data->StatusID === 10) {
                        $badge = 'badge-danger';
                    } else {
                        $badge = 'badge-info';
                    }

                    return '<span class="badge ' . $badge . '">' . $data->StatusName . '</span>';
                })
                ->addColumn('Detail', function ($data) {
                    $detail = '<a class="btn btn-xs btn-secondary" href="/stock/plan-purchase/detail/' . $data->PurchasePlanID . '">Lihat</a>';
                    return $detail;
                })
                ->addColumn('Action', function ($data) {
                    if ($data->StatusID === 8) {
                        if (Auth::user()->RoleID == "CEO" || Auth::user()->RoleID == "IT") {
                            $confirm = '<a class="btn btn-xs btn-info mb-1" href="/stock/plan-purchase/detail/' . $data->PurchasePlanID . '">Konfirmasi</a>';
                        } else {
                            $confirm = '';
                        }
                        if (Auth::user()->RoleID != "CEO") {
                            $edit = '<a class="btn btn-xs btn-warning mr-1 mb-1" href="/stock/plan-purchase/edit/' . $data->PurchasePlanID . '">Edit</a>';
                        } else {
                            $edit = '';
                        }
                    } else {
                        $confirm = '';
                        $edit = '';
                    }
                    return $edit . $confirm;
                })
                ->rawColumns(['StatusName', 'FlagPurchasePlan', 'Detail', 'Action'])
                ->make();
        }

        return view('stock.purchase-plan.index');
    }

    public function purchasePlanDetail($purchasePlanID, PurchaseService $purchaseService, Request $request)
    {
        $dataPurchasePlan = $purchaseService->getPurchasePlan()->where('ms_purchase_plan.PurchasePlanID', $purchasePlanID)->first();

        $data = $purchaseService->getPurchasePlanDetail($purchasePlanID);

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('PlanDate', function ($data) {
                    return date('d M Y', strtotime($data->PlanDate));
                })
                ->addColumn('PercentagePO', function ($data) {
                    return round($data->QtyPO / $data->Qty * 100, 2) . '%';
                })
                ->editColumn('PercentInterest', function ($data) {
                    return $data->PercentInterest . '%';
                })
                ->editColumn('PercentVoucher', function ($data) {
                    return $data->PercentVoucher . '%';
                })
                ->addColumn('NettMargin', function ($data) {
                    return $data->GrossMargin - $data->InterestValue - $data->VoucherValue;
                })
                ->addColumn('PercentageMargin', function ($data) {
                    $nettMargin = $data->GrossMargin - $data->InterestValue - $data->VoucherValue;
                    return round($nettMargin / $data->SellingValue * 100, 2) . '%';
                })
                ->make();
        }

        return view('stock.purchase-plan.detail', [
            'data' => $dataPurchasePlan
        ]);
    }

    public function createPurchasePlan(PurchaseService $purchaseService)
    {
        $suppliers = DB::table('ms_suppliers')->get();
        $investors = DB::table('ms_investor')->where('IsActive', 1)->get();
        $percentVoucher = DB::table('ms_mobileconfig')->where('ID', 'VOUCHER_PURCHASE_PLAN')->select('Value')->first();
        $products = DB::table('ms_product')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
            ->where('ms_product.IsActive', 1)
            ->orderBy('ms_product.ProductID')
            ->get();
        $distributors = $purchaseService->getDistributors()->get();
        return view('stock.purchase-plan.create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors,
            'investors' => $investors,
            'percentVoucher' => $percentVoucher
        ]);
    }

    public function storePurchasePlan(Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'investor' => 'required',
            'purchase_plan_date' => 'required',
            'distributor' => 'required',
            'distributor.*' => 'required',
            'supplier' => 'required',
            'supplier.*' => 'required',
            'product' => 'required',
            'product.*' => 'required',
            'labeling' => 'required',
            'labeling.*' => 'required',
            'quantity' => 'required',
            'quantity.*' => 'required',
            'quantity_po' => 'required',
            'quantity_po.*' => 'required',
            'purchase_price' => 'required',
            'purchase_price.*' => 'required',
            'selling_price' => 'required',
            'selling_price.*' => 'required',
            'percent_voucher' => 'required',
            'percent_voucher.*' => 'required',
            'stock' => 'required',
            'stock.*' => 'required',
        ]);

        $purchasePlanID = $purchaseService->generatePurchasePlanID();
        $purchasePlanDate = str_replace("T", " ", $request->input('purchase_plan_date'));
        $investorInterest = $request->input('investor_interest');
        $investor = $request->input('investor');
        if ($investor == "Lainnya") {
            $request->validate([
                'other_investor' => 'unique:ms_investor,InvestorName'
            ]);
            $newInvestorID = DB::table('ms_investor')
                ->insertGetId(['InvestorName' => $request->input('other_investor')]);
            $investorID = $newInvestorID;
        } else {
            $investorID = $investor;
        }

        $dataPurchasePlan = [
            'PurchasePlanID' => $purchasePlanID,
            'InvestorID' => $investorID,
            'PlanDate' => $purchasePlanDate,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'CreatedBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'StatusID' => 8
        ];

        $distributor = $request->input('distributor');
        $supplier = $request->input('supplier');
        $note = $request->input('note');
        $productID = $request->input('product');
        $labeling = $request->input('labeling');
        $qty = $request->input('quantity');
        $qtyPO = $request->input('quantity_po');
        $purchasePrice = $request->input('purchase_price');
        $sellingPrice = $request->input('selling_price');
        $stock = $request->input('stock');
        $percentVoucher = $request->input('percent_voucher');

        $dataPurchasePlanDetail = [];
        $purchasePlanDetail = array_map(function () {
            return func_get_args();
        }, $distributor, $supplier, $note, $productID, $labeling, $qty, $qtyPO, $purchasePrice, $sellingPrice, $stock, $percentVoucher);

        foreach ($purchasePlanDetail as $key => $value) {
            $value = array_combine(['DistributorID', 'SupplierID', 'Note', 'ProductID', 'ProductLabel', 'Qty', 'QtyPO', 'PurchasePrice', 'SellingPrice', 'LastStock', 'PercentVoucher'], $value);
            $value += ['PurchasePlanID' => $purchasePlanID];
            $value += ['PercentInterest' => $investorInterest];
            array_push($dataPurchasePlanDetail, $value);
        }

        try {
            DB::transaction(function () use ($dataPurchasePlan, $dataPurchasePlanDetail) {
                DB::table('ms_purchase_plan')->insert($dataPurchasePlan);
                DB::table('ms_purchase_plan_detail')->insert($dataPurchasePlanDetail);
            });
            return redirect()->route('stock.purchasePlan')->with('success', 'Data Purchase Plan berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stock.purchasePlan')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function editPurchasePlan($purchasePlanID, PurchaseService $purchaseService)
    {
        $data = $purchaseService->getPurchasePlan()->where('ms_purchase_plan.PurchasePlanID', $purchasePlanID)->first();
        $dataDetail = $purchaseService->getPurchasePlanDetail($purchasePlanID)->get();

        $suppliers = DB::table('ms_suppliers')->get();
        $investors = DB::table('ms_investor')->where('IsActive', 1)->get();
        $products = DB::table('ms_product')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
            ->where('ms_product.IsActive', 1)
            ->orderBy('ms_product.ProductID')
            ->get();
        $distributors = $purchaseService->getDistributors()->get();
        return view('stock.purchase-plan.edit', [
            'data' => $data,
            'dataDetail' => $dataDetail,
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors,
            'investors' => $investors
        ]);
    }

    public function updatePurchasePlan($purchasePlanID, Request $request)
    {
        $request->validate([
            'investor' => 'required',
            'purchase_plan_date' => 'required',
            'distributor' => 'required',
            'distributor.*' => 'required',
            'supplier' => 'required',
            'supplier.*' => 'required',
            'product' => 'required',
            'product.*' => 'required',
            'labeling' => 'required',
            'labeling.*' => 'required',
            'quantity' => 'required',
            'quantity.*' => 'required',
            'quantity_po' => 'required',
            'quantity_po.*' => 'required',
            'purchase_price' => 'required',
            'purchase_price.*' => 'required',
            'selling_price' => 'required',
            'selling_price.*' => 'required',
            'percent_voucher' => 'required',
            'percent_voucher.*' => 'required',
            'stock' => 'required',
            'stock.*' => 'required',
        ]);

        $purchasePlanDate = str_replace("T", " ", $request->input('purchase_plan_date'));
        $investorID = $request->input('investor');
        $investor = DB::table('ms_investor')->where('InvestorID', $investorID)->select('Interest')->first();
        // if ($investor == "Lainnya") {
        //     $request->validate([
        //         'other_investor' => 'unique:ms_investor,InvestorName'
        //     ]);
        //     $newInvestorID = DB::table('ms_investor')
        //         ->insertGetId(['InvestorName' => $request->input('other_investor')]);
        //     $investorID = $newInvestorID;
        // } else {
        //     $investorID = $investor;
        // }

        $dataPurchasePlan = [
            'InvestorID' => $investorID,
            'PlanDate' => $purchasePlanDate
        ];

        $distributor = $request->input('distributor');
        $supplier = $request->input('supplier');
        $note = $request->input('note');
        $productID = $request->input('product');
        $labeling = $request->input('labeling');
        $qty = $request->input('quantity');
        $qtyPO = $request->input('quantity_po');
        $purchasePrice = $request->input('purchase_price');
        $sellingPrice = $request->input('selling_price');
        $stock = $request->input('stock');
        $percentVoucher = $request->input('percent_voucher');

        $dataPurchasePlanDetail = [];
        $purchasePlanDetail = array_map(function () {
            return func_get_args();
        }, $distributor, $supplier, $note, $productID, $labeling, $qty, $qtyPO, $purchasePrice, $sellingPrice, $stock, $percentVoucher);

        foreach ($purchasePlanDetail as $key => $value) {
            $value = array_combine(['DistributorID', 'SupplierID', 'Note', 'ProductID', 'ProductLabel', 'Qty', 'QtyPO', 'PurchasePrice', 'SellingPrice', 'LastStock', 'PercentVoucher'], $value);
            $value += ['PurchasePlanID' => $purchasePlanID];
            $value += ['PercentInterest' => $investor->Interest];
            array_push($dataPurchasePlanDetail, $value);
        }

        try {
            DB::transaction(function () use ($purchasePlanID, $dataPurchasePlan, $dataPurchasePlanDetail) {
                DB::table('ms_purchase_plan')->where('PurchasePlanID', $purchasePlanID)->update($dataPurchasePlan);
                DB::table('ms_purchase_plan_detail')->where('PurchasePlanID', $purchasePlanID)->delete();
                DB::table('ms_purchase_plan_detail')->insert($dataPurchasePlanDetail);
            });
            return redirect()->route('stock.purchasePlan')->with('success', 'Data Purchase Plan berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stock.purchasePlan')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function confirmPurchasePlan($purchasePlanID, $status)
    {
        if ($status === "approve") {
            $statusID = 9;
        } else {
            $statusID = 10;
        }
        $confirm = DB::table('ms_purchase_plan')
            ->where('PurchasePlanID', $purchasePlanID)
            ->update([
                'StatusID' => $statusID,
                'ConfirmBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
                'ConfirmDate' => date('Y-m-d H:i:s')
            ]);
        if ($confirm) {
            return redirect()->route('stock.purchasePlan')->with('success', 'Data Purchase Plan berhasil dikonfirmasi');
        } else {
            return redirect()->route('stock.purchasePlan')->with('failed', 'Terjadi kesalahan!');
        }
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
                ->editColumn('PurchasePlanID', function ($data) {
                    return '<a target="_blank" href="/stock/plan-purchase/detail/' . $data->PurchasePlanID . '">' . $data->PurchasePlanID . '</a>';
                })
                ->editColumn('PurchaseDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->PurchaseDate));
                })
                ->editColumn('DistributorName', function ($data) {
                    if ($data->DistributorName === null) {
                        $distributor = $data->DistributorCombined;
                    } else {
                        $distributor = $data->DistributorName;
                    }
                    return $distributor;
                })
                ->editColumn('SupplierName', function ($data) {
                    if ($data->SupplierName === null) {
                        $supplier = $data->SupplierCombined;
                    } else {
                        $supplier = $data->SupplierName;
                    }
                    return $supplier;
                })
                ->editColumn('StatusName', function ($data) {
                    if ($data->StatusID == 1) {
                        $color = 'warning';
                    } elseif ($data->StatusID == 2) {
                        $color = 'success';
                    } elseif ($data->StatusID == 4) {
                        $color = 'info';
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
                ->rawColumns(['PurchasePlanID', 'InvoiceNumber', 'InvoiceFile', 'StatusName', 'Action', 'Confirmation'])
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
        $investors = DB::table('ms_investor')->where('IsActive', 1)->get();
        $products = DB::table('ms_product')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', 'ms_product.ProductUOMID')
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_uom.ProductUOMName')
            ->where('ms_product.IsActive', 1)
            ->orderBy('ms_product.ProductID')
            ->get();
        $distributors = $purchaseService->getDistributors()->get();
        $purchasePlan = DB::table('ms_purchase_plan')
            ->leftJoin('ms_stock_purchase', 'ms_stock_purchase.PurchasePlanID', 'ms_purchase_plan.PurchasePlanID')
            ->join('ms_investor', 'ms_investor.InvestorID', 'ms_purchase_plan.InvestorID')
            ->where('ms_purchase_plan.StatusID', 9)
            ->whereNull('ms_stock_purchase.PurchaseID')
            ->select('ms_purchase_plan.PurchasePlanID', 'ms_investor.InvestorName', 'ms_purchase_plan.PlanDate')
            ->get();

        // return view('stock.purchase.create', [
        //     'suppliers' => $suppliers,
        //     'products' => $products,
        //     'distributors' => $distributors,
        //     'investors' => $investors,
        //     'purchasePlan' => $purchasePlan
        // ]);

        return view('stock.purchase.createNew', [
            'suppliers' => $suppliers,
            'products' => $products,
            'distributors' => $distributors,
            'investors' => $investors,
            'purchasePlan' => $purchasePlan
        ]);
    }

    public function storePurchase(Request $request, PurchaseService $purchaseService)
    {
        $request->validate([
            'purchase_plan' => 'required',
            'investor_id' => 'required',
            'purchase_date' => 'required',
            'estimation_arrive' => 'required',
        ]);

        $purchaseID = $purchaseService->generatePurchaseID();
        $purchasePlanID = $request->input('purchase_plan');
        $investorID = $request->input('investor_id');
        $purchaseDate = str_replace("T", " ", $request->input('purchase_date'));
        $estimationArrive = str_replace("T", " ", $request->input('estimation_arrive'));
        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;

        $dataPurchase = [
            'PurchaseID' => $purchaseID,
            'PurchasePlanID' => $purchasePlanID,
            'InvestorID' => $investorID,
            'PurchaseDate' => $purchaseDate,
            'EstimationArrive' => $estimationArrive,
            'CreatedBy' => $user,
            'StatusID' => 4,
            'CreatedDate' => date('Y-m-d H:i:s'),
        ];

        $dataPurchaseDetail = DB::table('ms_purchase_plan_detail')
            ->where('PurchasePlanID', $purchasePlanID)
            ->select('DistributorID', 'SupplierID', 'ProductID', 'ProductLabel', 'Qty', 'PurchasePrice')
            ->get();

        try {
            DB::transaction(function () use ($dataPurchase, $dataPurchaseDetail, $purchaseID) {
                DB::table('ms_stock_purchase')->insert($dataPurchase);
                foreach ($dataPurchaseDetail as $key => $value) {
                    DB::table('ms_stock_purchase_detail')->insert([
                        'PurchaseID' => $purchaseID,
                        'DistributorID' => $value->DistributorID,
                        'SupplierID' => $value->SupplierID,
                        'ProductID' => $value->ProductID,
                        'ProductLabel' => $value->ProductLabel,
                        'ConditionStock' => 'GOOD STOCK',
                        'Qty' => $value->Qty,
                        'PurchasePrice' => $value->PurchasePrice,
                        'Type' => 'INBOUND',
                        'StatusStockID' => 5
                    ]);
                }
            });
            return redirect()->route('stock.purchase')->with('success', 'Data Purchase Stock berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('stock.purchase')->with('failed', 'Terjadi kesalahan!');
        }

        // dd($dataPurchase, $dataPurchaseDetail);

        // if ($request->hasFile('invoice_image')) {
        //     $invoiceFile = str_replace(' ', '', $purchaseID) . '_' . time() . '.' . $request->file('invoice_image')->extension();
        //     $request->file('invoice_image')->move($this->saveImageUrl . 'stock_invoice/', $invoiceFile);
        // } else {
        //     $invoiceFile = NULL;
        // }

        // $supplier = $request->input('supplier');
        // if ($supplier == "Lainnya") {
        //     $request->validate([
        //         'other_supplier' => 'unique:ms_suppliers,SupplierName'
        //     ]);
        //     DB::table('ms_suppliers')->insert(['SupplierName' => $request->input('other_supplier')]);
        //     $getSupplier = DB::table('ms_suppliers')->where('SupplierName', $request->input('other_supplier'))->select('SupplierID')->first();
        //     $supplierID = $getSupplier->SupplierID;
        // } else {
        //     $supplierID = $supplier;
        // }

        // if ($investor == "Lainnya") {
        //     $request->validate([
        //         'other_investor' => 'unique:ms_investor,InvestorName'
        //     ]);
        //     DB::table('ms_investor')->insert(['InvestorName' => $request->input('other_investor')]);
        //     $getInvestor = DB::table('ms_investor')->where('InvestorName', $request->input('other_investor'))->select('InvestorID')->first();
        //     $investorID = $getInvestor->InvestorID;
        // } else {
        //     $investorID = $investor;
        // }

        // $dataPurchase = [
        //     'PurchaseID' => $purchaseID,
        //     'DistributorID' => $request->input('distributor'),
        //     'InvestorID' => $investorID,
        //     'SupplierID' => $supplierID,
        //     'PurchaseDate' => $purchaseDate,
        //     'CreatedBy' => $user,
        //     'StatusID' => 1,
        //     'CreatedDate' => date('Y-m-d H:i:s'),
        //     'InvoiceNumber' => $request->input('invoice_number'),
        //     'InvoiceFile' => $invoiceFile
        // ];

        // $productID = $request->input('product');
        // $labeling = $request->input('labeling');
        // $qty = $request->input('quantity');
        // $purchasePrice = $request->input('purchase_price');

        // $dataPurchaseDetail = $purchaseService->dataPurchaseDetail($productID, $labeling, $qty, $purchasePrice, $purchaseID);


    }

    public function getPurchaseByPurchasePlan($purchasePlanID, PurchaseService $purchaseService)
    {
        $data = $purchaseService->getPurchasePlan()->where('ms_purchase_plan.PurchasePlanID', $purchasePlanID)->first();
        $data->Detail = $purchaseService->getPurchasePlanDetail($purchasePlanID)->get();

        return $data;
    }

    public function editPurchase(PurchaseService $purchaseService, $purchaseID)
    {
        $suppliers = DB::table('ms_suppliers')->get();
        $investors = DB::table('ms_investor')->where('IsActive', 1)->get();
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

    public function detailPurchase(PurchaseService $purchaseService, $purchaseID)
    {
        $purchaseByID = $purchaseService->getStockPurchaseByID($purchaseID);
        $suppliers = DB::table('ms_suppliers')->select('*')->orderBy('SupplierID')->get();

        return view('stock.purchase.detail', [
            'purchaseByID' => $purchaseByID,
            'suppliers' => $suppliers
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

    public function confirmProductPurchase(Request $request, $status, $purchaseDetailID)
    {
        $purchase = DB::table('ms_stock_purchase_detail')
            ->join('ms_stock_purchase', 'ms_stock_purchase.PurchaseID', 'ms_stock_purchase_detail.PurchaseID')
            ->where('ms_stock_purchase_detail.PurchaseDetailID', $purchaseDetailID)
            ->select('ms_stock_purchase_detail.PurchaseID', 'ms_stock_purchase.InvestorID', 'ms_stock_purchase.EstimationArrive', 'ms_stock_purchase_detail.ProductID', 'ms_stock_purchase_detail.ProductLabel', 'ms_stock_purchase_detail.ConditionStock', 'ms_stock_purchase_detail.DistributorID')
            ->first();


        $supplier = $request->input('supplier');
        $confirmDate = str_replace("T", " ", $request->input('confirm_date'));
        $qty = $request->input('qty');
        $purchasePrice = str_replace(".", "", $request->input('purchase_price'));
        $note = $request->input('note');

        if ($confirmDate > $purchase->EstimationArrive) {
            $isGIT = 1;
        } else {
            $isGIT = 0;
        }

        if ($status === "approve") {
            $qtyBefore = DB::table('ms_stock_product')
                ->where('DistributorID', $purchase->DistributorID)->where('InvestorID', $purchase->InvestorID)
                ->where('ProductID', $purchase->ProductID)->where('ProductLabel', $purchase->ProductLabel)
                ->where('ConditionStock', $purchase->ConditionStock)->where('Qty', '>', 0)
                ->sum('Qty');

            $statusStockID = 6;
            $dataPurchaseDetail = [
                'SupplierID' => $supplier,
                'Qty' => $qty,
                'PurchasePrice' => $purchasePrice,
                'StatusStockID' => $statusStockID,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'ConfirmDate' => $confirmDate,
                'ConfirmBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
                'IsGIT' => $isGIT,
                'Note' => $note
            ];
        } else {
            $qtyBefore = 0;
            $statusStockID = 7;
            $dataPurchaseDetail = [
                'StatusStockID' => $statusStockID,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'ConfirmBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
                'Note' => $note
            ];
        }

        $dataStockProduct = [
            'PurchaseID' => $purchase->PurchaseID,
            'InvestorID' => $purchase->InvestorID,
            'ProductID' => $purchase->ProductID,
            'ProductLabel' => $purchase->ProductLabel,
            'ConditionStock' => $purchase->ConditionStock,
            'Qty' => $qty,
            'PurchasePrice' => $purchasePrice,
            'DistributorID' => $purchase->DistributorID,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'Type' => 'INBOUND',
            'LevelType' => 3
        ];

        try {
            DB::transaction(function () use ($status, $purchaseDetailID, $dataPurchaseDetail, $dataStockProduct, $purchase, $qtyBefore, $qty, $purchasePrice) {
                DB::table('ms_stock_purchase_detail')->where('PurchaseDetailID', $purchaseDetailID)->update($dataPurchaseDetail);

                if ($status === "approve") {
                    $stockProductID = DB::table('ms_stock_product')->insertGetId($dataStockProduct);
                    DB::table('ms_stock_product_log')->insert([
                        'StockProductID' => $stockProductID,
                        'ProductID' => $purchase->ProductID,
                        'QtyBefore' => $qtyBefore,
                        'QtyAction' => $qty,
                        'QtyAfter' => $qtyBefore + $qty,
                        'PurchasePrice' => $purchasePrice,
                        'SellingPrice' => 0,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
                        'ActionType' => 'INBOUND'
                    ]);
                }
            });
            return redirect()->back()->with('success', 'Produk Purchase Berhasil dikonfirmasi!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function editInvoice($purchaseID)
    {
        $sql = DB::table('ms_stock_purchase')
            ->leftJoin('ms_distributor', 'ms_distributor.DistributorID', 'ms_stock_purchase.DistributorID')
            ->leftJoin('ms_investor', 'ms_investor.InvestorID', 'ms_stock_purchase.InvestorID')
            ->leftJoin('ms_suppliers', 'ms_suppliers.SupplierID', 'ms_stock_purchase.SupplierID')
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

    public function getMutationStockAllProduct(Request $request, PurchaseService $purchaseService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlGetMutationAllProduct = $purchaseService->getMutationAllProduct();

        if ($fromDate != '' && $toDate != '') {
            $sqlGetMutationAllProduct->whereDate('ms_stock_mutation.MutationDate', '>=', $fromDate)
                ->whereDate('ms_stock_mutation.MutationDate', '<=', $toDate);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetMutationAllProduct->whereRaw("from_distributor.Depo = '$depoUser' OR to_distributor.Depo = '$depoUser'");
        }

        $data = $sqlGetMutationAllProduct;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('MutationDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->MutationDate));
                })
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
            ->join('ms_investor', function ($join) {
                $join->on('ms_investor.InvestorID', 'purchase.InvestorID');
                $join->where('ms_investor.IsActive', 1);
            })
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
