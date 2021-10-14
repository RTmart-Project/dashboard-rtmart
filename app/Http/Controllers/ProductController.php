<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function list()
    {
        return view('product.list.index');
    }

    public function getLists(Request $request)
    {
        $sqlAllProduct = DB::table('ms_product')
            ->leftJoin('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->leftJoin('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->leftJoin('ms_brand_type', 'ms_brand_type.BrandID', '=', 'ms_product.BrandTypeID')
            ->leftJoin('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product.ProductUOMDesc', 'ms_product.Price', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_brand_type.Brand', 'ms_product_uom.ProductUOMName');

        // Get data response
        $data = $sqlAllProduct->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    $baseImageUrl = config('app.base_image_url');
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<a data-product-name="' . $data->ProductName . '" class="lihat-gambar" target="_blank" href="' . $baseImageUrl . 'product/' . $data->ProductImage . '">Lihat Gambar</a>';
                })
                ->rawColumns(['ProductImage'])
                ->make(true);
        }
    }

    public function category()
    {
        return view('product.category.index');
    }

    public function getCategories(Request $request)
    {
        $sqlAllCategory = DB::table('ms_product_category')
            ->select('ms_product_category.*');

        // Get data response
        $data = $sqlAllCategory->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->make(true);
        }
    }

    public function uom()
    {
        return view('product.uom.index');
    }

    public function getUoms(Request $request)
    {
        $sqlAllUOM = DB::table('ms_product_uom')
            ->select('ms_product_uom.*');

        // Get data response
        $data = $sqlAllUOM->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->make(true);
        }
    }

    public function type()
    {
        return view('product.type.index');
    }

    public function getTypes(Request $request)
    {
        $sqlAllType = DB::table('ms_product_type')
            ->select('ms_product_type.*');

        // Get data response
        $data = $sqlAllType->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->make(true);
        }
    }

    public function brand()
    {
        return view('product.brand.index');
    }

    public function getBrands(Request $request)
    {
        $sqlAllBrand = DB::table('ms_brand_type')
            ->select('ms_brand_type.*');

        // Get data response
        $data = $sqlAllBrand->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('BrandImage', function ($data) {
                    $baseImageUrl = config('app.base_image_url');
                    if ($data->BrandImage == null) {
                        $data->BrandImage = 'not-found.png';
                    }
                    return '<a data-brand-name="' . $data->Brand . '" class="lihat-gambar" target="_blank" href="' . $baseImageUrl . 'brand/' . $data->BrandImage . '">Lihat Gambar</a>';
                })
                ->rawColumns(['BrandImage'])
                ->make(true);
        }
    }
}
