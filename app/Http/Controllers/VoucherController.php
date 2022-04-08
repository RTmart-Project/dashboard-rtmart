<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    protected $saveImageUrl;
    protected $baseImageUrl;

    public function __construct()
    {
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function list()
    {
        return view('voucher.list.index');
    }

    public function getList(Request $request)
    {
        $sqlGetVoucher = DB::table('ms_voucher')
            ->join('ms_voucher_type', 'ms_voucher_type.VoucherTypeID', '=', 'ms_voucher.VoucherTypeID')
            ->select('ms_voucher.VoucherCode', 'ms_voucher.VoucherName', 'ms_voucher_type.VoucherTypeName', 'ms_voucher.PercentageValue', 'ms_voucher.MaxNominalValue', 'ms_voucher.StartDate', 'ms_voucher.EndDate', 'ms_voucher.IsActive', 'ms_voucher.IsFor');

        $data = $sqlGetVoucher->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('PercentageValue', function ($data) {
                    return $data->PercentageValue . "%";
                })
                ->addColumn('ValidityPeriod', function ($data) {
                    $valPeriod = date('d M Y', strtotime($data->StartDate)) . " - " . date('d M Y', strtotime($data->EndDate));
                    return $valPeriod;
                })
                ->editColumn('IsActive', function ($data) {
                    if ($data->IsActive == 1) {
                        $isActive = "Ya";
                    } else {
                        $isActive = "Tidak";
                    }
                    return $isActive;
                })
                ->editColumn('IsFor', function ($data) {
                    if ($data->IsFor == "All") {
                        $isFor = "Customer dan Merchant";
                    } else {
                        $isFor = $data->IsFor;
                    }
                    return $isFor;
                })
                ->addColumn('Detail', function ($data) {
                    $detailBtn = '<a href="/voucher/list/detail/' . $data->VoucherCode . '" class="btn btn-xs btn-info">Lihat</a>';
                    return $detailBtn;
                })
                ->addColumn('Action', function ($data) {
                    if (Auth::user()->RoleID == 'IT') {
                        $detailBtn = '<a href="/voucher/list/edit/' . $data->VoucherCode . '" class="btn btn-xs btn-warning">Edit</a>';
                    } else {
                        $detailBtn = '';
                    }
                    return $detailBtn;
                })
                ->rawColumns(['ValidityPeriod', 'IsActive', 'IsFor', 'Detail', 'Action'])
                ->make(true);
        }
    }

    public function detail($voucherCode)
    {
        $voucher = DB::table('ms_voucher')
            ->join('ms_voucher_type', 'ms_voucher_type.VoucherTypeID', '=', 'ms_voucher.VoucherTypeID')
            ->where('ms_voucher.VoucherCode', '=', $voucherCode)
            ->select('ms_voucher.*', 'ms_voucher_type.VoucherTypeName')
            ->first();

        $termPaymentMethod = DB::table('ms_voucher_term_payment_method')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'ms_voucher_term_payment_method.PaymentMethodID')
            ->where('ms_voucher_term_payment_method.VoucherCode', '=', $voucherCode)
            ->select('ms_payment_method.PaymentMethodName')->get();

        $termDistributorLocation = DB::table('ms_voucher_term_distributor_location')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_voucher_term_distributor_location.DistributorID')
            ->where('ms_voucher_term_distributor_location.VoucherCode', '=', $voucherCode)
            ->select('ms_distributor.DistributorName')->get();

        $termBrand = DB::table('ms_voucher_term_brand')
            ->join('ms_brand_type', 'ms_brand_type.BrandID', '=', 'ms_voucher_term_brand.BrandID')
            ->where('ms_voucher_term_brand.VoucherCode', '=', $voucherCode)
            ->select('ms_brand_type.Brand')->get();

        $termCategory = DB::table('ms_voucher_term_category')
            ->join('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_voucher_term_category.ProductCategoryID')
            ->where('ms_voucher_term_category.VoucherCode', '=', $voucherCode)
            ->select('ms_product_category.ProductCategoryName')->get();

        $termProduct = DB::table('ms_voucher_term_product')
            ->join('ms_product', 'ms_product.ProductID', '=', 'ms_voucher_term_product.ProductID')
            ->where('ms_voucher_term_product.VoucherCode', '=', $voucherCode)
            ->select('ms_voucher_term_product.ProductID', 'ms_product.ProductName', 'ms_voucher_term_product.MinimumTrx', 'ms_voucher_term_product.MinimumQty', 'ms_voucher_term_product.MinimumTrxAccumulative', 'ms_voucher_term_product.MinimumQtyAccumulative', 'ms_voucher_term_product.MinimumTrxAccumulativeRestock', 'ms_voucher_term_product.MinimumQtyAccumulativeRestock')
            ->get();

        return view('voucher.list.details', [
            'voucher' => $voucher,
            'termPaymentMethod' => $termPaymentMethod,
            'termDistributorLocation' => $termDistributorLocation,
            'termBrand' => $termBrand,
            'termCategory' => $termCategory,
            'termProduct' => $termProduct
        ]);
    }

    public function addList()
    {
        $voucherType = DB::table('ms_voucher_type')->get();

        $paymentMethod = DB::table('ms_payment_method')
            ->select('PaymentMethodID', 'PaymentMethodName')->get();

        $distributorLocation = DB::table('ms_distributor')
            ->where('DistributorID', '!=', 'D-0000-000000')
            ->select('DistributorID', 'DistributorName')->get();

        $termBrand = DB::table('ms_brand_type')
            ->select('BrandID', 'Brand')->get();

        $termCategory = DB::table('ms_product_category')
            ->select('ProductCategoryID', 'ProductCategoryName')->get();

        $termProduct = DB::table('ms_product')
            ->where('ProductID', '!=', 'P-000000')
            ->select('ProductID', 'ProductName')->get();

        return view('voucher.list.new', [
            'voucherType' => $voucherType,
            'paymentMethod' => $paymentMethod,
            'distributorLocation' => $distributorLocation,
            'termBrand' => $termBrand,
            'termCategory' => $termCategory,
            'termProduct' => $termProduct
        ]);
    }

    public function insertList(Request $request)
    {
        $request->validate(
            [
                'voucher_code' => 'required|string|unique:ms_voucher,VoucherCode',
                'voucher_name' => 'required|string',
                'voucher_type' => 'required|integer|exists:ms_voucher_type,VoucherTypeID',
                'percentage' => 'required|integer|max:100',
                'max_nominal' => 'required',
                'is_for' => 'required|in:Customer,Merchant,All',
                'check_power_merchant' => 'required|in:1,0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'quota_per_user' => 'required|integer',
                'max_quota' => 'required|integer|gt:quota_per_user',
                'banner' => 'image',
                'minimum_transaction' => 'required',
                'minimum_quantity' => 'required|integer',
                'minimum_tx_history' => 'required',
                'minimum_qty_history' => 'required|integer',
                'start_date_new_user' => 'date',
                'end_date_new_user' => 'date|after:start_date_new_user',
                'start_date_merchant_restock' => 'date',
                'end_date_merchant_restock' => 'date|after:start_date_merchant_restock',
                'start_date_customer_tx' => 'date',
                'end_date_customer_tx' => 'date|after:start_date_customer_tx',
                'details' => 'required',
                'payment_method' => 'exists:ms_payment_method,PaymentMethodID',
                'distributor_location' => 'exists:ms_distributor,DistributorID',
                'term_brand' => 'exists:ms_brand_type,BrandID',
                'term_category' => 'exists:ms_product_category,ProductCategoryID',
                'term_product' => 'nullable',
                'term_product.*' => 'nullable|exists:ms_product,ProductID',
                'minimum_tx_product' => 'nullable',
                'minimum_tx_product.*' => 'nullable|integer',
                'minimum_qty_product' => 'nullable',
                'minimum_qty_product.*' => 'nullable|integer',
                'minimum_tx_product_history' => 'nullable',
                'minimum_tx_product_history.*' => 'nullable|integer',
                'minimum_qty_product_history' => 'nullable',
                'minimum_qty_product_history.*' => 'nullable|integer',
                'minimum_qty_product_restock' => 'nullable',
                'minimum_qty_product_restock.*' => 'nullable|integer'
            ],
            [
                'max_quota.gt' => 'The max quota must be greater than quota per user'
            ]
        );

        $voucherCode = $request->input('voucher_code');

        $bannerName = $voucherCode . '.' . $request->file('banner')->extension();
        $request->file('banner')->move($this->saveImageUrl . 'voucher/banner/', $bannerName);

        $startDateVoucher = str_replace("T", " ", $request->input('start_date'));
        $endDateVoucher = str_replace("T", " ", $request->input('end_date'));

        if (($request->input('start_date_new_user') !== null) && ($request->input('end_date_new_user') !== null)) {
            $startDateNewUser = str_replace("T", " ", $request->input('start_date_new_user'));
            $endDateNewUser = str_replace("T", " ", $request->input('end_date_new_user'));
        } else {
            $startDateNewUser = null;
            $endDateNewUser = null;
        }

        if (($request->input('start_date_merchant_restock') !== null) && ($request->input('end_date_merchant_restock') !== null)) {
            $startDateMerchantRestock = str_replace("T", " ", $request->input('start_date_merchant_restock'));
            $endDateMerchantRestock = str_replace("T", " ", $request->input('end_date_merchant_restock'));
        } else {
            $startDateMerchantRestock = null;
            $endDateMerchantRestock = null;
        }

        if (($request->input('start_date_customer_tx') !== null) && ($request->input('end_date_customer_tx') !== null)) {
            $startDateCustomerTx = str_replace("T", " ", $request->input('start_date_customer_tx'));
            $endDateCustomerTx = str_replace("T", " ", $request->input('end_date_customer_tx'));
        } else {
            $startDateCustomerTx = null;
            $endDateCustomerTx = null;
        }

        $data = [
            'VoucherCode' => $request->input('voucher_code'),
            'VoucherName' => $request->input('voucher_name'),
            'VoucherTypeID' => $request->input('voucher_type'),
            'PercentageValue' => $request->input('percentage'),
            'MaxNominalValue' => $request->input('max_nominal'),
            'IsFor' => $request->input('is_for'),
            'IsCheckPowerMerchant' => $request->input('check_power_merchant'),
            'StartDate' => $startDateVoucher,
            'EndDate' => $endDateVoucher,
            'QuotaPerUser' => $request->input('quota_per_user'),
            'MaxQuota' => $request->input('max_quota'),
            'Banner' => $bannerName,
            'MinimumTrx' => $request->input('minimum_transaction'),
            'MinimumQty' => $request->input('minimum_quantity'),
            'MinimumTrxAccumulative' => $request->input('minimum_tx_history'),
            'MinimumQtyAccumulative' => $request->input('minimum_qty_history'),
            'StartDateNewUser' => $startDateNewUser,
            'EndDateNewUser' => $endDateNewUser,
            'MerchantRestockStartDate' => $startDateMerchantRestock,
            'MerchantRestockEndDate' => $endDateMerchantRestock,
            'StartDateCustomerTrx' => $startDateCustomerTx,
            'EndDateCustomerTrx' => $endDateCustomerTx,
            'Details' => $request->input('details')
        ];

        // Term Payment Method
        $payment = $request->input('payment_method');
        if ($payment !== null) {
            $termPayment = array_map(function () {
                return func_get_args();
            }, $payment);

            foreach ($termPayment as $key => $value) {
                $termPayment[$key][] = $voucherCode;
            }
        } else {
            $termPayment = null;
        }

        // Term Distributor Location
        $distributor = $request->input('distributor_location');
        if ($distributor !== null) {
            $termDistributor = array_map(function () {
                return func_get_args();
            }, $distributor);

            foreach ($termDistributor as $key => $value) {
                $termDistributor[$key][] = $voucherCode;
            }
        } else {
            $termDistributor = null;
        }

        // Term Brand
        $brand = $request->input('term_brand');
        if ($brand !== null) {
            $termBrand = array_map(function () {
                return func_get_args();
            }, $brand);

            foreach ($termBrand as $key => $value) {
                $termBrand[$key][] = $voucherCode;
            }
        } else {
            $termBrand = null;
        }

        // Term Category
        $category = $request->input('term_category');
        if ($category !== null) {
            $termCategory = array_map(function () {
                return func_get_args();
            }, $category);

            foreach ($termCategory as $key => $value) {
                $termCategory[$key][] = $voucherCode;
            }
        } else {
            $termCategory = null;
        }

        // Term Product
        $switch_term_product = $request->input('switch_term_product');
        $product = $request->input('term_product');
        $minimum_tx_product = $request->input('minimum_tx_product');
        $minimum_qty_product = $request->input('minimum_qty_product');
        $minimum_tx_product_history = $request->input('minimum_tx_product_history');
        $minimum_qty_product_history = $request->input('minimum_qty_product_history');
        $minimum_tx_product_restock = $request->input('minimum_tx_product_restock');
        $minimum_qty_product_restock = $request->input('minimum_qty_product_restock');

        if ($switch_term_product == "on") {
            $termProduct = array_map(function () {
                return func_get_args();
            }, $product, $minimum_tx_product, $minimum_qty_product, $minimum_tx_product_history, $minimum_qty_product_history, $minimum_tx_product_restock, $minimum_qty_product_restock);

            foreach ($termProduct as $key => $value) {
                $termProduct[$key][] = $voucherCode;
            }
        } else {
            $termProduct = null;
        }

        function insertTermVoucher($term, $column, $table)
        {
            foreach ($term as &$value) {
                $value = array_combine([$column, 'VoucherCode'], $value);
                DB::table($table)
                    ->insert([
                        'VoucherCode' => $value['VoucherCode'],
                        $column => $value[$column]
                    ]);
            }
        }

        try {
            DB::transaction(function () use ($data, $termPayment, $termDistributor, $termBrand, $termCategory, $termProduct) {
                DB::table('ms_voucher')->insert($data);
                if ($termPayment != null) {
                    insertTermVoucher($termPayment, "PaymentMethodID", "ms_voucher_term_payment_method");
                }
                if ($termDistributor != null) {
                    insertTermVoucher($termDistributor, "DistributorID", "ms_voucher_term_distributor_location");
                }
                if ($termBrand != null) {
                    insertTermVoucher($termBrand, "BrandID", "ms_voucher_term_brand");
                }
                if ($termCategory != null) {
                    insertTermVoucher($termCategory, "ProductCategoryID", "ms_voucher_term_category");
                }
                if ($termProduct != null) {
                    foreach ($termProduct as &$value) {
                        $value = array_combine(['ProductID', 'MinimumTrx', 'MinimumQty', 'MinimumTrxAccumulative', 'MinimumQtyAccumulative', 'MinimumTrxAccumulativeRestock', 'MinimumQtyAccumulativeRestock', 'VoucherCode'], $value);
                        DB::table('ms_voucher_term_product')
                            ->insert([
                                'VoucherCode' => $value['VoucherCode'],
                                'ProductID' => $value['ProductID'],
                                'MinimumTrx' => $value['MinimumTrx'],
                                'MinimumQty' => $value['MinimumQty'],
                                'MinimumTrxAccumulative' => $value['MinimumTrxAccumulative'],
                                'MinimumQtyAccumulative' => $value['MinimumQtyAccumulative'],
                                'MinimumTrxAccumulativeRestock' => $value['MinimumTrxAccumulativeRestock'],
                                'MinimumQtyAccumulativeRestock' => $value['MinimumQtyAccumulativeRestock']
                            ]);
                    }
                }
            });

            return redirect()->route('voucher.list')->with('success', 'Data Voucher berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('voucher.list')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editList($voucherCode)
    {
        $voucherType = DB::table('ms_voucher_type')->get();

        $paymentMethod = DB::table('ms_payment_method')
            ->select('PaymentMethodID', 'PaymentMethodName')->get();

        $distributorLocation = DB::table('ms_distributor')
            ->where('DistributorID', '!=', 'D-0000-000000')
            ->select('DistributorID', 'DistributorName')->get();

        $termBrand = DB::table('ms_brand_type')
            ->select('BrandID', 'Brand')->get();

        $termCategory = DB::table('ms_product_category')
            ->select('ProductCategoryID', 'ProductCategoryName')->get();

        $termProduct = DB::table('ms_product')
            ->where('ProductID', '!=', 'P-000000')
            ->select('ProductID', 'ProductName')->get();

        $voucher = DB::table('ms_voucher')
            ->where('VoucherCode', '=', $voucherCode)
            ->select('*')
            ->first();

        $voucherPaymentMethod = DB::table('ms_voucher_term_payment_method')
            ->where('VoucherCode', '=', $voucherCode)
            ->select('*')->get();

        $voucherDistributorLocation = DB::table('ms_voucher_term_distributor_location')
            ->where('VoucherCode', '=', $voucherCode)
            ->select('*')->get();

        $voucherBrand = DB::table('ms_voucher_term_brand')
            ->where('VoucherCode', '=', $voucherCode)
            ->select('*')->get();

        $voucherCategory = DB::table('ms_voucher_term_category')
            ->where('VoucherCode', '=', $voucherCode)
            ->select('*')->get();

        $voucherProduct = DB::table('ms_voucher_term_product')
            ->where('VoucherCode', '=', $voucherCode)
            ->select('*')->get();

        return view('voucher.list.edit', [
            'voucherType' => $voucherType,
            'paymentMethod' => $paymentMethod,
            'distributorLocation' => $distributorLocation,
            'termBrand' => $termBrand,
            'termCategory' => $termCategory,
            'termProduct' => $termProduct,
            'voucher' => $voucher,
            'voucherPaymentMethod' => $voucherPaymentMethod,
            'voucherDistributorLocation' => $voucherDistributorLocation,
            'voucherBrand' => $voucherBrand,
            'voucherCategory' => $voucherCategory,
            'voucherProduct' => $voucherProduct
        ]);
    }

    public function updateList(Request $request, $voucherCodeDB)
    {
        $request->validate(
            [
                'active' => 'required|in:1,0',
                'voucher_code' => [
                    'required', 'string', Rule::unique('ms_voucher', 'VoucherCode')->ignore($voucherCodeDB, 'VoucherCode')
                ],
                'voucher_name' => 'required|string',
                'voucher_type' => 'required|integer|exists:ms_voucher_type,VoucherTypeID',
                'percentage' => 'required|integer|max:100',
                'max_nominal' => 'required',
                'is_for' => 'required|in:Customer,Merchant,All',
                'check_power_merchant' => 'required|in:1,0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'quota_per_user' => 'required|integer',
                'max_quota' => 'required|integer|gt:quota_per_user',
                'banner' => 'image',
                'minimum_transaction' => 'required',
                'minimum_quantity' => 'required|integer',
                'minimum_tx_history' => 'required',
                'minimum_qty_history' => 'required|integer',
                'start_date_new_user' => 'date',
                'end_date_new_user' => 'date|after:start_date_new_user',
                'start_date_merchant_restock' => 'date',
                'end_date_merchant_restock' => 'date|after:start_date_merchant_restock',
                'start_date_customer_tx' => 'date',
                'end_date_customer_tx' => 'date|after:start_date_customer_tx',
                'details' => 'required',
                'payment_method' => 'exists:ms_payment_method,PaymentMethodID',
                'distributor_location' => 'exists:ms_distributor,DistributorID',
                'term_brand' => 'exists:ms_brand_type,BrandID',
                'term_category' => 'exists:ms_product_category,ProductCategoryID',
                'term_product' => 'nullable',
                'term_product.*' => 'nullable|exists:ms_product,ProductID',
                'minimum_tx_product' => 'nullable',
                'minimum_tx_product.*' => 'nullable|integer',
                'minimum_qty_product' => 'nullable',
                'minimum_qty_product.*' => 'nullable|integer',
                'minimum_tx_product_history' => 'nullable',
                'minimum_tx_product_history.*' => 'nullable|integer',
                'minimum_qty_product_history' => 'nullable',
                'minimum_qty_product_history.*' => 'nullable|integer',
                'minimum_qty_product_restock' => 'nullable',
                'minimum_qty_product_restock.*' => 'nullable|integer'
            ],
            [
                'max_quota.gt' => 'The max quota must be greater than quota per user'
            ]
        );

        $voucherCode = $request->input('voucher_code');

        $startDateVoucher = str_replace("T", " ", $request->input('start_date'));
        $endDateVoucher = str_replace("T", " ", $request->input('end_date'));

        if (($request->input('start_date_new_user') !== null) && ($request->input('end_date_new_user') !== null)) {
            $startDateNewUser = str_replace("T", " ", $request->input('start_date_new_user'));
            $endDateNewUser = str_replace("T", " ", $request->input('end_date_new_user'));
        } else {
            $startDateNewUser = null;
            $endDateNewUser = null;
        }

        if (($request->input('start_date_merchant_restock') !== null) && ($request->input('end_date_merchant_restock') !== null)) {
            $startDateMerchantRestock = str_replace("T", " ", $request->input('start_date_merchant_restock'));
            $endDateMerchantRestock = str_replace("T", " ", $request->input('end_date_merchant_restock'));
        } else {
            $startDateMerchantRestock = null;
            $endDateMerchantRestock = null;
        }

        if (($request->input('start_date_customer_tx') !== null) && ($request->input('end_date_customer_tx') !== null)) {
            $startDateCustomerTx = str_replace("T", " ", $request->input('start_date_customer_tx'));
            $endDateCustomerTx = str_replace("T", " ", $request->input('end_date_customer_tx'));
        } else {
            $startDateCustomerTx = null;
            $endDateCustomerTx = null;
        }

        $data = [
            'IsActive' => $request->input('active'),
            'VoucherCode' => $request->input('voucher_code'),
            'VoucherName' => $request->input('voucher_name'),
            'VoucherTypeID' => $request->input('voucher_type'),
            'PercentageValue' => $request->input('percentage'),
            'MaxNominalValue' => $request->input('max_nominal'),
            'IsFor' => $request->input('is_for'),
            'IsCheckPowerMerchant' => $request->input('check_power_merchant'),
            'StartDate' => $startDateVoucher,
            'EndDate' => $endDateVoucher,
            'QuotaPerUser' => $request->input('quota_per_user'),
            'MaxQuota' => $request->input('max_quota'),
            'MinimumTrx' => $request->input('minimum_transaction'),
            'MinimumQty' => $request->input('minimum_quantity'),
            'MinimumTrxAccumulative' => $request->input('minimum_tx_history'),
            'MinimumQtyAccumulative' => $request->input('minimum_qty_history'),
            'StartDateNewUser' => $startDateNewUser,
            'EndDateNewUser' => $endDateNewUser,
            'MerchantRestockStartDate' => $startDateMerchantRestock,
            'MerchantRestockEndDate' => $endDateMerchantRestock,
            'StartDateCustomerTrx' => $startDateCustomerTx,
            'EndDateCustomerTrx' => $endDateCustomerTx,
            'Details' => $request->input('details')
        ];

        if ($request->hasFile('banner')) {
            $bannerName = $voucherCode . '.' . $request->file('banner')->extension();
            $request->file('banner')->move($this->saveImageUrl . 'voucher/banner/', $bannerName);
            $data['Banner'] = $bannerName;
        }

        // Term Payment Method
        $payment = $request->input('payment_method');
        if ($payment !== null) {
            $termPayment = array_map(function () {
                return func_get_args();
            }, $payment);

            foreach ($termPayment as $key => $value) {
                $termPayment[$key][] = $voucherCode;
            }
        } else {
            $termPayment = null;
        }

        // Term Distributor Location
        $distributor = $request->input('distributor_location');
        if ($distributor !== null) {
            $termDistributor = array_map(function () {
                return func_get_args();
            }, $distributor);

            foreach ($termDistributor as $key => $value) {
                $termDistributor[$key][] = $voucherCode;
            }
        } else {
            $termDistributor = null;
        }

        // Term Brand
        $brand = $request->input('term_brand');
        if ($brand !== null) {
            $termBrand = array_map(function () {
                return func_get_args();
            }, $brand);

            foreach ($termBrand as $key => $value) {
                $termBrand[$key][] = $voucherCode;
            }
        } else {
            $termBrand = null;
        }

        // Term Category
        $category = $request->input('term_category');
        if ($category !== null) {
            $termCategory = array_map(function () {
                return func_get_args();
            }, $category);

            foreach ($termCategory as $key => $value) {
                $termCategory[$key][] = $voucherCode;
            }
        } else {
            $termCategory = null;
        }

        // Term Product
        $switch_term_product = $request->input('switch_term_product');
        $product = $request->input('term_product');
        $minimum_tx_product = $request->input('minimum_tx_product');
        $minimum_qty_product = $request->input('minimum_qty_product');
        $minimum_tx_product_history = $request->input('minimum_tx_product_history');
        $minimum_qty_product_history = $request->input('minimum_qty_product_history');
        $minimum_tx_product_restock = $request->input('minimum_tx_product_restock');
        $minimum_qty_product_restock = $request->input('minimum_qty_product_restock');

        if ($switch_term_product == "on") {
            $termProduct = array_map(function () {
                return func_get_args();
            }, $product, $minimum_tx_product, $minimum_qty_product, $minimum_tx_product_history, $minimum_qty_product_history, $minimum_tx_product_restock, $minimum_qty_product_restock);

            foreach ($termProduct as $key => $value) {
                $termProduct[$key][] = $voucherCode;
            }
        } else {
            $termProduct = null;
        }

        function deleteTermVoucher($table, $voucherCodeDB)
        {
            DB::table($table)->where('VoucherCode', $voucherCodeDB)->delete();
        }

        function updateTermVoucher($term, $column, $table, $voucherCodeDB)
        {
            DB::table($table)->where('VoucherCode', $voucherCodeDB)->delete();
            foreach ($term as &$value) {
                $value = array_combine([$column, 'VoucherCode'], $value);
                DB::table($table)
                    ->insert([
                        'VoucherCode' => $value['VoucherCode'],
                        $column => $value[$column]
                    ]);
            }
        }

        try {
            DB::transaction(function () use ($voucherCodeDB, $data, $termPayment, $termDistributor, $termBrand, $termCategory, $switch_term_product, $termProduct) {
                DB::table('ms_voucher')
                    ->where('VoucherCode', '=', $voucherCodeDB)
                    ->update($data);
                if ($termPayment == null) {
                    deleteTermVoucher("ms_voucher_term_payment_method", $voucherCodeDB);
                } else {
                    updateTermVoucher($termPayment, "PaymentMethodID", "ms_voucher_term_payment_method", $voucherCodeDB);
                }
                if ($termDistributor == null) {
                    deleteTermVoucher("ms_voucher_term_distributor_location", $voucherCodeDB);
                } else {
                    updateTermVoucher($termDistributor, "DistributorID", "ms_voucher_term_distributor_location", $voucherCodeDB);
                }
                if ($termBrand == null) {
                    deleteTermVoucher("ms_voucher_term_brand", $voucherCodeDB);
                } else {
                    updateTermVoucher($termBrand, "BrandID", "ms_voucher_term_brand", $voucherCodeDB);
                }
                if ($termCategory == null) {
                    deleteTermVoucher("ms_voucher_term_category", $voucherCodeDB);
                } else {
                    updateTermVoucher($termCategory, "ProductCategoryID", "ms_voucher_term_category", $voucherCodeDB);
                }
                if ($switch_term_product == null) {
                    deleteTermVoucher("ms_voucher_term_product", $voucherCodeDB);
                } else {
                    deleteTermVoucher("ms_voucher_term_product", $voucherCodeDB);
                    foreach ($termProduct as &$value) {
                        $value = array_combine(['ProductID', 'MinimumTrx', 'MinimumQty', 'MinimumTrxAccumulative', 'MinimumQtyAccumulative', 'MinimumTrxAccumulativeRestock', 'MinimumQtyAccumulativeRestock', 'VoucherCode'], $value);
                        DB::table('ms_voucher_term_product')
                            ->insert([
                                'VoucherCode' => $value['VoucherCode'],
                                'ProductID' => $value['ProductID'],
                                'MinimumTrx' => $value['MinimumTrx'],
                                'MinimumQty' => $value['MinimumQty'],
                                'MinimumTrxAccumulative' => $value['MinimumTrxAccumulative'],
                                'MinimumQtyAccumulative' => $value['MinimumQtyAccumulative'],
                                'MinimumTrxAccumulativeRestock' => $value['MinimumTrxAccumulativeRestock'],
                                'MinimumQtyAccumulativeRestock' => $value['MinimumQtyAccumulativeRestock']
                            ]);
                    }
                }
            });

            return redirect()->route('voucher.list')->with('success', 'Data Voucher berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('voucher.list')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function log()
    {
        return view('voucher.log.index');
    }

    public function getLog(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlGetLog = DB::table('ms_voucher_log AS vl')
            ->join('ms_voucher', 'ms_voucher.VoucherCode', '=', 'vl.VoucherCode')
            ->leftJoin('tx_merchant_order', 'tx_merchant_order.StockOrderID', 'vl.OrderID')
            ->leftJoin('tx_product_order', 'tx_product_order.OrderID', 'vl.OrderID')
            ->leftJoin('ms_status_order', function ($join) {
                $join->on('ms_status_order.StatusOrderID', 'tx_merchant_order.StatusOrderID');
                $join->orOn('ms_status_order.StatusOrderID', 'tx_product_order.StatusOrderID');
            })
            ->join('ms_voucher_type', 'ms_voucher_type.VoucherTypeID', '=', 'ms_voucher.VoucherTypeID')
            ->select('vl.*', 'ms_voucher_type.VoucherTypeName', 'ms_status_order.StatusOrder');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlGetLog->whereDate('ms_voucher_log.ProcessTime', '>=', $fromDate)
                ->whereDate('ms_voucher_log.ProcessTime', '<=', $toDate);
        }

        $data = $sqlGetLog->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('ProcessTime', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->ProcessTime));
                })
                ->editColumn('OrderID', function ($data) {
                    if (substr($data->OrderID, 0, 2) == "SO") {
                        return "<a target='_blank' href='/merchant/restock/detail/" . $data->OrderID . "'>$data->OrderID</a>";
                    } else {
                        return "<a target='_blank' href='/customer/transaction/detail/" . $data->OrderID . "'>$data->OrderID</a>";
                    }
                })
                ->rawColumns(['OrderID', 'ProcessTime'])
                ->make(true);
        }
    }
}