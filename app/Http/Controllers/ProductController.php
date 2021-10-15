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

    public function addCategory()
    {
        return view('product.category.new');
    }

    public function insertCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|unique:ms_product_category,ProductCategoryName'
        ]);

        $data = [
            'ProductCategoryName' => $request->input('category_name')
        ];

        $insertCategory = DB::table('ms_product_category')->insert($data);

        if ($insertCategory) {
            return redirect()->route('product.category')->with('success', 'Data kategori berhasil ditambahkan');
        } else {
            return redirect()->route('product.category')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
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

    public function addUom()
    {
        return view('product.uom.new');
    }

    public function insertUom(Request $request)
    {
        $request->validate([
            'uom_name' => 'required|string|unique:ms_product_uom,ProductUOMName'
        ]);

        $data = [
            'ProductUOMName' => $request->input('uom_name')
        ];

        $insertUom = DB::table('ms_product_uom')->insert($data);

        if ($insertUom) {
            return redirect()->route('product.uom')->with('success', 'Data UOM berhasil ditambahkan');
        } else {
            return redirect()->route('product.uom')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
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

    public function addType()
    {
        return view('product.type.new');
    }

    public function insertType(Request $request)
    {
        $maxTypeProductId = DB::table('ms_product_type')
            ->max('ProductTypeID');

        if ($maxTypeProductId == null) {
            $newTypeProductId = 'T-001';
        } else {
            $maxTypeProductIdNumber = explode("-", $maxTypeProductId);
            $oldTypeProductIdNumber = end($maxTypeProductIdNumber);
            $newTypeProductIdNumber = $oldTypeProductIdNumber + 1;
            $newTypeProductId = 'T-' . str_pad($newTypeProductIdNumber, 3, '0', STR_PAD_LEFT);
        }

        $request->validate([
            'type_name' => 'required|string|unique:ms_product_type,ProductTypeName'
        ]);

        $data = [
            'ProductTypeID' => $newTypeProductId,
            'ProductTypeName' => $request->input('type_name')
        ];

        $insertType = DB::table('ms_product_type')->insert($data);

        if ($insertType) {
            return redirect()->route('product.type')->with('success', 'Data Tipe berhasil ditambahkan');
        } else {
            return redirect()->route('product.type')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
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
                    return '<img src="' . $baseImageUrl . 'brand/' . $data->BrandImage . '" alt="Brand Image" height="70">';
                })
                ->rawColumns(['BrandImage'])
                ->make(true);
        }
    }

    public function addBrand()
    {
        return view('product.brand.new');
    }

    public function insertBrand(Request $request)
    {
        $baseImageUrl = config('app.base_image_url');

        $request->validate([
            'brand_name' => 'required|string|unique:ms_brand_type,Brand',
            'brand_image' => 'image'
        ]);

        $imageName = time() . '.' . $request->file('brand_image')->extension();

        dd($imageName);

        $request->file('brand_image')->move('/home/rtmartindonesia/mobile/image/brand/', $imageName);

        $data = [
            'Brand' => $request->input('brand_name'),
            'BrandImage' => $imageName
        ];

        $insertBrand = DB::table('ms_brand_type')->insert($data);

        if ($insertBrand) {
            return redirect()->route('product.brand')->with('success', 'Data Merek berhasil ditambahkan');
        } else {
            return redirect()->route('product.brand')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}
