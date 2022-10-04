<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
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
        return view('product.list.index');
    }

    public function getLists(Request $request)
    {
        $sqlAllProduct = DB::table('ms_product')
            ->leftJoin('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->leftJoin('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->leftJoin('ms_brand_type', 'ms_brand_type.BrandID', '=', 'ms_product.BrandTypeID')
            ->leftJoin('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->leftJoin('ms_distributor', 'ms_distributor.DistributorID', 'ms_product.ProductOwner')
            ->where('ms_product.IsActive', 1)
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product.ProductUOMDesc', 'ms_product.Price', 'ms_product.ProductOwner', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_brand_type.Brand', 'ms_product_uom.ProductUOMName', 'ms_distributor.DistributorName');

        $depoUser = Auth::user()->Depo;
        if ($depoUser != "ALL") {
            $sqlAllProduct->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllProduct;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('ProductOwner', function ($data) {
                    if ($data->ProductOwner == "ALL") {
                        $productOwner = "ALL";
                    } else {
                        $productOwner = $data->DistributorName;
                    }
                    return $productOwner;
                })
                ->editColumn('ProductImage', function ($data) {
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="90">';
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/master/product/list/edit/' . $data->ProductID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->filterColumn('ms_product.ProductOwner', function ($query, $keyword) {
                    $query->whereRaw("ms_distributor.DistributorName like ?", ["%$keyword%"]);
                })
                ->rawColumns(['ProductImage', 'Action'])
                ->make(true);
        }
    }

    public function getProductById($productId)
    {
        $product = DB::table('ms_product')->where('ProductID', $productId)->select('*')->first();
        return $product;
    }

    public function addList()
    {
        $categoryProduct = DB::table('ms_product_category')->select('*')->get();
        $typeProduct = DB::table('ms_product_type')->select('*')->get();
        $brandProduct = DB::table('ms_brand_type')->select('*')->get();
        $uomProduct = DB::table('ms_product_uom')->select('*')->get();

        return view('product.list.new', [
            'categoryProduct' => $categoryProduct,
            'typeProduct' => $typeProduct,
            'brandProduct' => $brandProduct,
            'uomProduct' => $uomProduct
        ]);
    }

    public function insertList(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'product_category' => 'required|integer|exists:ms_product_category,ProductCategoryID',
            'product_type' => 'required|string|exists:ms_product_type,ProductTypeID',
            'product_brand' => 'required|integer|exists:ms_brand_type,BrandID',
            'product_uom' => 'required|integer|exists:ms_product_uom,ProductUOMID',
            'uom_desc' => 'required|numeric',
            'product_image' => 'required|image',
            'price' => 'required'
        ]);

        $maxProductId = DB::table('ms_product')
            ->max('ProductID');

        if ($maxProductId == null) {
            $maxProductId = 'P-000001';
        } else {
            $maxProductIdNumber = explode("-", $maxProductId);
            $oldProductIdNumber = end($maxProductIdNumber);
            $newProductIdNumber = $oldProductIdNumber + 1;
            $newProductId = 'P-' . str_pad($newProductIdNumber, 6, '0', STR_PAD_LEFT);
        }

        $imageName = time() . '_' . $newProductId . '.' . $request->file('product_image')->extension();

        $request->file('product_image')->move($this->saveImageUrl . 'product/', $imageName);

        $depoUser = Auth::user()->Depo;
        if ($depoUser == "ALL") {
            $productOwner = "ALL";
        } else {
            $sql = DB::table('ms_distributor')->where('Depo', $depoUser)->select('DistributorID')->first();
            $productOwner = $sql->DistributorID;
        }

        $data = [
            'ProductID' => $newProductId,
            'ProductName' => $request->input('product_name'),
            'ProductImage' => $imageName,
            'ProductDescription' => $request->input('product_name'),
            'ProductCategoryID' => $request->input('product_category'),
            'ProductTypeID' => $request->input('product_type'),
            'BrandTypeID' => $request->input('product_brand'),
            'ProductUOMID' => $request->input('product_uom'),
            'ProductUOMDesc' => $request->input('uom_desc'),
            'Price' => $request->input('price'),
            'IsCustom' => 0,
            'IsActive' => 1,
            'IsDefault' => 0,
            'ProductOwner' => $productOwner
        ];

        $insertProduct = DB::table('ms_product')->insert($data);

        if ($insertProduct) {
            return redirect()->route('product.list')->with('success', 'Data Produk berhasil ditambahkan');
        } else {
            return redirect()->route('product.list')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editList($product)
    {
        $categoryProduct = DB::table('ms_product_category')->select('*')->get();
        $typeProduct = DB::table('ms_product_type')->select('*')->get();
        $brandProduct = DB::table('ms_brand_type')->select('*')->get();
        $uomProduct = DB::table('ms_product_uom')->select('*')->get();

        $productById = DB::table('ms_product')
            ->where('ProductID', '=', $product)
            ->select('*')->first();

        return view('product.list.edit', [
            'productById' => $productById,
            'categoryProduct' => $categoryProduct,
            'typeProduct' => $typeProduct,
            'brandProduct' => $brandProduct,
            'uomProduct' => $uomProduct
        ]);
    }

    public function updateList(Request $request, $product)
    {
        $request->validate([
            'product_name' => 'required|string',
            'product_category' => 'required|integer|exists:ms_product_category,ProductCategoryID',
            'product_type' => 'required|string|exists:ms_product_type,ProductTypeID',
            'product_brand' => 'required|integer|exists:ms_brand_type,BrandID',
            'product_uom' => 'required|integer|exists:ms_product_uom,ProductUOMID',
            'uom_desc' => 'required|numeric',
            'product_image' => 'image',
            'price' => 'required'
        ]);

        if ($request->hasFile('product_image')) {
            $imageName = time() . '_' . $product . '.' . $request->file('product_image')->extension();
            $request->file('product_image')->move($this->saveImageUrl . 'product/', $imageName);
            $data = [
                'ProductName' => $request->input('product_name'),
                'ProductImage' => $imageName,
                'ProductDescription' => $request->input('product_name'),
                'ProductCategoryID' => $request->input('product_category'),
                'ProductTypeID' => $request->input('product_type'),
                'BrandTypeID' => $request->input('product_brand'),
                'ProductUOMID' => $request->input('product_uom'),
                'ProductUOMDesc' => $request->input('uom_desc'),
                'Price' => $request->input('price')
            ];
        } else {
            $data = [
                'ProductName' => $request->input('product_name'),
                'ProductDescription' => $request->input('product_name'),
                'ProductCategoryID' => $request->input('product_category'),
                'ProductTypeID' => $request->input('product_type'),
                'BrandTypeID' => $request->input('product_brand'),
                'ProductUOMID' => $request->input('product_uom'),
                'ProductUOMDesc' => $request->input('uom_desc'),
                'Price' => $request->input('price')
            ];
        }

        $updateProduct = DB::table('ms_product')
            ->where('ProductID', '=', $product)
            ->update($data);

        if ($updateProduct) {
            return redirect()->route('product.list')->with('success', 'Data Produk berhasil diubah');
        } else {
            return redirect()->route('product.list')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
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
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/master/product/category/edit/' . $data->ProductCategoryID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Action'])
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

    public function editCategory($category)
    {
        $categoryById = DB::table('ms_product_category')
            ->where('ProductCategoryID', '=', $category)
            ->select('*')->first();

        return view('product.category.edit', [
            'categoryById' => $categoryById
        ]);
    }

    public function updateCategory(Request $request, $category)
    {
        $request->validate([
            'category_name' => 'required|string'
        ]);

        $data = [
            'ProductCategoryName' => $request->input('category_name')
        ];

        $updateCategory = DB::table('ms_product_category')
            ->where('ProductCategoryID', '=', $category)
            ->update($data);

        if ($updateCategory) {
            return redirect()->route('product.category')->with('success', 'Data kategori berhasil diubah');
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
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/master/product/uom/edit/' . $data->ProductUOMID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Action'])
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

    public function editUom($uom)
    {
        $uomById = DB::table('ms_product_uom')
            ->where('ProductUOMID', '=', $uom)
            ->select('*')->first();

        return view('product.uom.edit', [
            'uomById' => $uomById
        ]);
    }

    public function updateUom(Request $request, $uom)
    {
        $request->validate([
            'uom_name' => 'required|string'
        ]);

        $data = [
            'ProductUOMName' => $request->input('uom_name')
        ];

        $updateUom = DB::table('ms_product_uom')
            ->where('ProductUOMID', '=', $uom)
            ->update($data);

        if ($updateUom) {
            return redirect()->route('product.uom')->with('success', 'Data UOM berhasil diubah');
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
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/master/product/type/edit/' . $data->ProductTypeID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Action'])
                ->make(true);
        }
    }

    public function addType()
    {
        return view('product.type.new');
    }

    public function insertType(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|unique:ms_product_type,ProductTypeName'
        ]);

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

    public function editType($type)
    {
        $typeById = DB::table('ms_product_type')
            ->where('ProductTypeID', '=', $type)
            ->select('*')->first();

        return view('product.type.edit', [
            'typeById' => $typeById
        ]);
    }

    public function updateType(Request $request, $type)
    {
        $request->validate([
            'type_name' => 'required|string'
        ]);

        $data = [
            'ProductTypeName' => $request->input('type_name')
        ];

        $updateType = DB::table('ms_product_type')
            ->where('ProductTypeID', '=', $type)
            ->update($data);

        if ($updateType) {
            return redirect()->route('product.type')->with('success', 'Data Tipe berhasil diubah');
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
                    if ($data->BrandImage == null) {
                        $data->BrandImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'brand/' . $data->BrandImage . '" alt="Brand Image" height="70">';
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/master/product/brand/edit/' . $data->BrandID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->rawColumns(['BrandImage', 'Action'])
                ->make(true);
        }
    }

    public function addBrand()
    {
        return view('product.brand.new');
    }

    public function insertBrand(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|unique:ms_brand_type,Brand',
            'brand_image' => 'image'
        ]);

        $imageName = time() . '_' . str_replace(' ', '', $request->input('brand_name')) . '.' . $request->file('brand_image')->extension();

        $request->file('brand_image')->move($this->saveImageUrl . 'brand/', $imageName);

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

    public function editBrand($brand)
    {
        $brandById = DB::table('ms_brand_type')
            ->where('BrandID', '=', $brand)
            ->select('*')->first();

        if ($brandById->BrandImage == null) {
            $brandById->BrandImage = 'not-found.png';
        }

        return view('product.brand.edit', [
            'brandById' => $brandById
        ]);
    }

    public function updateBrand(Request $request, $brand)
    {
        $request->validate([
            'brand_name' => 'required|string',
            'brand_image' => 'image'
        ]);

        if ($request->hasFile('brand_image')) {
            $imageName = time() . '_' . str_replace(' ', '', $request->input('brand_name')) . '.' . $request->file('brand_image')->extension();
            $request->file('brand_image')->move($this->saveImageUrl . 'brand/', $imageName);
            $data = [
                'Brand' => $request->input('brand_name'),
                'BrandImage' => $imageName
            ];
        } else {
            $data = [
                'Brand' => $request->input('brand_name')
            ];
        }

        $updateBrand = DB::table('ms_brand_type')
            ->where('BrandID', '=', $brand)
            ->update($data);

        if ($updateBrand) {
            return redirect()->route('product.brand')->with('success', 'Data Merek berhasil diubah');
        } else {
            return redirect()->route('product.brand')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}
