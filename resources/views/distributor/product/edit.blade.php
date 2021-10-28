@extends('layouts.master')
@section('title', 'Dashboard - Edit Product ' . $distributorProduct->DistributorName)

@section('header-menu', 'Ubah Produk ' . $distributorProduct->DistributorName)

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
                        <a href="{{ route('distributor.productDetails', ['distributorId' => $distributorId]) }}" class="btn btn-sm btn-light mb-2"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <h6><strong>Distributor ID : </strong>{{ $distributorId }}</h6>
                        <h6><strong>Nama Distributor : </strong>{{ $distributorProduct->DistributorName }}</h6>
                        <h6><strong>Alamat : </strong>{{ $distributorProduct->Address }}</h6>
                    </div>
                    <div class="card-body">
                        <form id="edit-product-distributor" method="post" action="{{ route('distributor.updateProduct', ['distributorId' => $distributorId, 'productId' => $productId, 'gradeId' => $gradeId]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12 text-center">
                                    <img src="{{ config('app.base_image_url') . 'product/'. $distributorProduct->ProductImage }}" id="output" height="130px"/>
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
                                        <input type="text" value="{{ $distributorProduct->ProductName }}" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Grade</label>
                                        <input type="text" value="{{ $distributorProduct->Grade }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="price">Harga</label>
                                        <input type="number" name="price" id="price" class="form-control @if($errors->has('price')) is-invalid @endif" value="{{ $distributorProduct->Price }}" placeholder="Masukkan Harga Produk" required>
                                        @if($errors->has('price'))
                                            <span class="error invalid-feedback">{{ $errors->first('price') }}</span>
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