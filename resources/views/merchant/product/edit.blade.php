@extends('layouts.master')
@section('title', 'Dashboard - Edit Product ' . $merchantProduct->StoreName)

@section('header-menu', 'Ubah Produk ' . $merchantProduct->StoreName)

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
                        <a href="{{ route('merchant.product', ['merchantId' => $merchantId]) }}" class="btn btn-sm btn-light mb-2"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <div class="col-12 d-flex align-items-stretch flex-column">
                            <div class="card d-flex flex-fill">
                                <div class="card-body pt-3 pb-3">
                                    <div class="row">
                                        <div class="col-12 col-md-2 text-center">
                                            <img src="{{ config('app.base_image_url') . '/merchant/'. $merchantProduct->StoreImage }}" alt="Store Image" class="rounded img-fluid pb-2 pb-md-0" style="object-fit: cover; width: 130px; height: 130px;">
                                        </div>
                                        <div class="col-12 col-md-10 align-self-center">
                                            <h6><strong>Merchant ID : </strong>{{ $merchantId }}</h6>
                                            <h6><strong>Nama Toko : </strong>{{ $merchantProduct->StoreName }}</h6>
                                            <h6><strong>Nama Pemilik : </strong>{{ $merchantProduct->OwnerFullName }}</h6>
                                            <h6><strong>No. Telp : </strong><a href="tel:{{ $merchantProduct->PhoneNumber }}">{{ $merchantProduct->PhoneNumber }}</a></h6>
                                            <h6><strong>Alamat : </strong>{{ $merchantProduct->StoreAddress }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="edit-product-merchant" method="post" action="{{ route('merchant.updateProduct', ['merchantId' => $merchantId, 'productId' => $productId]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-12 text-center">
                                    <img src="{{ config('app.base_image_url') . 'product/'. $merchantProduct->ProductImage }}" id="output" height="130px"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>ID Produk</label>
                                        <input type="text" value="{{ $productId }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Nama Produk</label>
                                        <input type="text" value="{{ $merchantProduct->ProductName }}" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="price">Harga Jual</label>
                                        <input type="number" name="price" id="price" class="form-control @if($errors->has('price')) is-invalid @endif" value="{{ $merchantProduct->Price }}" placeholder="Masukkan Harga Jual">
                                        @if($errors->has('price'))
                                            <span class="error invalid-feedback">{{ $errors->first('price') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="purchase_price">Harga Beli</label>
                                        <input type="number" name="purchase_price" id="purchase_price" class="form-control @if($errors->has('purchase_price')) is-invalid @endif" value="{{ $merchantProduct->PurchasePrice }}" placeholder="Masukkan Harga Beli">
                                        @if($errors->has('purchase_price'))
                                            <span class="error invalid-feedback">{{ $errors->first('purchase_price') }}</span>
                                        @endif
                                    </div>
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
@endsection