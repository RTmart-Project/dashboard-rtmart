@extends('layouts.master')
@section('title', 'Dashboard - Edit Product Brand')

@section('header-menu', 'Ubah Merek Produk')

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
                        <a href="{{ route('product.brand') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="edit-brand" method="post" action="{{ route('product.updateBrand', ['brand' => $brandById->BrandID]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="nama-brand">Nama Merek </label>
                                        <input type="text" name="brand_name" value="{{ $brandById->Brand }}" class="form-control @if($errors->has('brand_name')) is-invalid @endif" id="nama-brand" placeholder="Masukkan Nama Tipe Produk" required>
                                        @if($errors->has('brand_name'))
                                            <span class="error invalid-feedback">{{ $errors->first('brand_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="brand_image">Gambar Merek</label>
                                        <input type="file" name="brand_image" id="brand_image" accept="image/*" onchange="loadFile(event)" class="form-control">
                                    </div>
                                </div>
                                <img src="{{ config('app.base_image_url') . 'brand/'. $brandById->BrandImage }}" id="output" height="120"/>
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
@endsection