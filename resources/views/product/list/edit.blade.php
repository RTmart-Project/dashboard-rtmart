@extends('layouts.master')
@section('title', 'Dashboard - Edit Product List')

@section('css-pages')
    <link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Ubah Daftar Produk')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <!-- left -->
            <div class="col-sm-6">
            </div>
            <!-- Right -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">

                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <!-- Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('product.list') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="edit-product" method="post" action="{{ route('product.updateList', ['product' => $productById->ProductID]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="product_name">Nama Produk</label>
                                        <input type="text" name="product_name" id="product_name" placeholder="Masukan Nama Produk" value="{{ $productById->ProductName }}"
                                            class="form-control @if($errors->has('product_name')) is-invalid @endif" required>
                                        @if($errors->has('product_name'))
                                            <span class="error invalid-feedback">{{ $errors->first('product_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="product_category">Kategori</label>
                                        <select name="product_category" id="product_category" class="form-control selectpicker border
                                            @if ($errors->has('product_category')) is-invalid @endif" data-live-search="true" required>
                                            @foreach ($categoryProduct as $value)
                                                <option value="{{ $value->ProductCategoryID }}" {{ ($productById->ProductCategoryID) == ($value->ProductCategoryID) ? 'selected' : '' }}>{{ $value->ProductCategoryName }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('product_category'))
                                            <span class="error invalid-feedback">{{ $errors->first('product_category') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="product_type">Tipe</label>
                                        <select name="product_type" id="product_type" class="form-control selectpicker border
                                            @if ($errors->has('product_type')) is-invalid @endif" data-live-search="true" required>
                                            @foreach ($typeProduct as $value)
                                                <option value="{{ $value->ProductTypeID }}" {{ ($productById->ProductTypeID) == ($value->ProductTypeID) ? 'selected' : '' }}>{{ $value->ProductTypeName }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('product_type'))
                                            <span class="error invalid-feedback">{{ $errors->first('product_type') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="product_brand">Merek</label>
                                        <select name="product_brand" id="product_brand" class="form-control selectpicker border
                                            @if ($errors->has('product_brand')) is-invalid @endif" data-live-search="true" required>
                                            @foreach ($brandProduct as $value)
                                                <option value="{{ $value->BrandID }}" {{ ($productById->BrandTypeID) == ($value->BrandID) ? 'selected' : '' }}>{{ $value->Brand }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('product_brand'))
                                            <span class="error invalid-feedback">{{ $errors->first('product_brand') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="product_uom">Jenis</label>
                                        <select name="product_uom" id="product_uom" class="form-control selectpicker border
                                            @if ($errors->has('product_uom')) is-invalid @endif" data-live-search="true" required>
                                            @foreach ($uomProduct as $value)
                                                <option value="{{ $value->ProductUOMID }}" {{ ($productById->ProductUOMID) == ($value->ProductUOMID) ? 'selected' : '' }}>{{ $value->ProductUOMName }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('product_uom'))
                                            <span class="error invalid-feedback">{{ $errors->first('product_uom') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="uom_desc">Isi</label>
                                        <input type="number" name="uom_desc" id="uom_desc" placeholder="Masukan Jumlah" value="{{ $productById->ProductUOMDesc }}"
                                            class="form-control @if($errors->has('uom_desc')) is-invalid @endif" required>
                                        @if($errors->has('uom_desc'))
                                            <span class="error invalid-feedback">{{ $errors->first('uom_desc') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="price">Harga Default</label>
                                        <input type="text" name="price" id="price" placeholder="Masukkan Harga Default" value="{{ $productById->Price }}"
                                            class="form-control autonumeric @if($errors->has('price')) is-invalid @endif" required autocomplete="off">
                                        @if($errors->has('price'))
                                            <span class="error invalid-feedback">{{ $errors->first('price') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="product_image">Upload Foto Produk</label>
                                        <input type="file" name="product_image" id="product_image" accept="image/*" onchange="loadFile(event)" class="form-control 
                                            @if($errors->has('product_image')) is-invalid @endif">
                                        @if($errors->has('product_image'))
                                            <span class="error invalid-feedback">{{ $errors->first('product_image') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <img src="{{ config('app.base_image_url') . 'product/'. $productById->ProductImage }}" id="output" height="150"/>
                                </div>
                            </div>

                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-warning">Ubah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-pages')
<script src="{{url('/')}}/main/js/helper/input-image-view.js"></script>
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
    new AutoNumeric(".autonumeric", {
        allowDecimalPadding: false,
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        unformatOnSubmit: true
    });
</script>
@endsection