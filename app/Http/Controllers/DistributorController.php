<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class DistributorController extends Controller
{
    protected $baseImageUrl;

    public function __construct()
    {
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function account()
    {
        return view('distributor.account.index');
    }

    public function getAccounts(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlAllAccount = DB::table('ms_distributor')
            ->where('Ownership', '=', 'RTMart')
            ->where('Email', '!=', NULL)
            ->select('DistributorID', 'DistributorName', 'Email', 'Address', 'CreatedDate')
            ->orderByDesc('CreatedDate');

        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('CreatedDate', '>=', $fromDate)
                ->whereDate('CreatedDate', '<=', $toDate);
        }

        $data = $sqlAllAccount->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->addColumn('Product', function ($data) {
                    $productBtn = '<a href="/distributor/account/product/' . $data->DistributorID . '" class="btn-sm btn-info">Detail</a>';
                    return $productBtn;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/distributor/account/edit/' . $data->DistributorID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->rawColumns(['CreatedDate', 'Product', 'Action'])
                ->make(true);
        }
    }

    public function editAccount($distributorId)
    {
        $distributorById = DB::table('ms_distributor')
            ->where('DistributorID', '=', $distributorId)
            ->select('DistributorID', 'DistributorName', 'Email', 'Address')
            ->first();

        return view('distributor.account.edit', [
            'distributorById' => $distributorById
        ]);
    }

    public function updateAccount(Request $request, $distributorId)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('ms_distributor', 'Email')->ignore($distributorId, 'DistributorID')
            ],
            'address' => 'max:500'
        ]);

        $data = [
            'DistributorName' => $request->input('name'),
            'Email' => $request->input('email'),
            'Address' => $request->input('address')
        ];

        $updateDistributor = DB::table('ms_distributor')
            ->where('DistributorID', '=', $distributorId)
            ->update($data);

        if ($updateDistributor) {
            return redirect()->route('distributor.account')->with('success', 'Data distributor telah diubah');
        } else {
            return redirect()->route('distributor.account')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function productDetails($distributorId)
    {
        $distributor = DB::table('ms_distributor')
            ->where('ms_distributor.DistributorID', '=', $distributorId)
            ->select('DistributorName', 'Address')
            ->first();

        return view('distributor.product.index', [
            'distributorId' => $distributorId,
            'distributor' => $distributor
        ]);
    }

    public function getProductDetails(Request $request, $distributorId)
    {
        $distributorProducts = DB::table('ms_distributor_product_price')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_distributor_product_price.ProductID')
            ->join('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_product_price.GradeID')
            ->join('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->join('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->where('ms_distributor_product_price.DistributorID', '=', $distributorId)
            ->select('ms_distributor_product_price.DistributorID', 'ms_distributor_product_price.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_product_uom.ProductUOMName', 'ms_product.ProductUOMDesc', 'ms_distributor_product_price.Price', 'ms_distributor_product_price.GradeID', 'ms_distributor_grade.Grade', 'ms_distributor_product_price.IsPreOrder');

        $data = $distributorProducts->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="90">';
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade == "Retail") {
                        $grade = '<span class="badge badge-success">' . $data->Grade . '</span>';
                    } elseif ($data->Grade == "SO") {
                        $grade = '<span class="badge badge-warning">' . $data->Grade . '</span>';
                    } elseif ($data->Grade == "WS") {
                        $grade = '<span class="badge badge-primary">' . $data->Grade . '</span>';
                    } else {
                        $grade = $data->Grade;
                    }
                    return $grade;
                })
                ->editColumn('IsPreOrder', function ($data) {
                    if ($data->IsPreOrder == 1) {
                        $preOrder = "Ya";
                    } else {
                        $preOrder = "Tidak";
                    }
                    return $preOrder;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/distributor/account/product/edit/' . $data->DistributorID . '/' . $data->ProductID . '/' . $data->GradeID . '" class="btn btn-sm btn-warning mr-1">Edit</a>
                    <a data-distributor-id="' . $data->DistributorID . '" data-product-id="' . $data->ProductID . '" data-grade-id="' . $data->GradeID . '" data-product-name="' . $data->ProductName . '" data-grade-name="' . $data->Grade . '" href="#" class="btn-delete btn btn-sm btn-danger">Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Grade', 'ProductImage', 'Action'])
                ->make(true);
        }
    }

    public function editProduct($distributorId, $productId, $gradeId)
    {
        $distributorProduct = DB::table('ms_distributor_product_price')
            ->leftJoin('ms_distributor', 'ms_distributor.DistributorID', 'ms_distributor_product_price.DistributorID')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_distributor_product_price.ProductID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_product_price.GradeID')
            ->where('ms_distributor_product_price.DistributorID', '=', $distributorId)
            ->where('ms_distributor_product_price.ProductID', '=', $productId)
            ->where('ms_distributor_product_price.GradeID', '=', $gradeId)
            ->select('ms_distributor_product_price.*', 'ms_distributor.DistributorName', 'ms_distributor.Address', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_distributor_grade.Grade', 'ms_distributor_product_price.IsPreOrder')
            ->first();

        return view('distributor.product.edit', [
            'distributorId' => $distributorId,
            'productId' => $productId,
            'gradeId' => $gradeId,
            'distributorProduct' => $distributorProduct
        ]);
    }

    public function updateProduct(Request $request, $distributorId, $productId, $gradeId)
    {
        $request->validate([
            'price' => 'required|integer',
            'is_pre_order' => 'required|in:1,0'
        ]);

        $updateDistributorProduct = DB::table('ms_distributor_product_price')
            ->where('DistributorID', '=', $distributorId)
            ->where('ProductID', '=', $productId)
            ->where('GradeID', '=', $gradeId)
            ->update([
                'Price' => $request->input('price'),
                'IsPreOrder' => $request->input('is_pre_order')
            ]);

        if ($updateDistributorProduct) {
            return redirect()->route('distributor.productDetails', ['distributorId' => $distributorId])->with('success', 'Data produk distributor telah diubah');
        } else {
            return redirect()->route('distributor.productDetails', ['distributorId' => $distributorId])->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteProduct($distributorId, $productId, $gradeId)
    {
        $deleteProduct = DB::table('ms_distributor_product_price')
            ->where('DistributorID', '=', $distributorId)
            ->where('ProductID', '=', $productId)
            ->where('GradeID', '=', $gradeId)
            ->delete();

        if ($deleteProduct) {
            return redirect()->route('distributor.productDetails', ['distributorId' => $distributorId])->with('success', 'Data produk distributor telah dihapus');
        } else {
            return redirect()->route('distributor.productDetails', ['distributorId' => $distributorId])->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }
}