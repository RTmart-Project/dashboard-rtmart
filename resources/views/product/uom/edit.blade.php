@extends('layouts.master')
@section('title', 'Dashboard - Edit Product UOM')

@section('header-menu', 'Ubah UOM Produk')

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
                        <a href="{{ route('product.uom') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="edit-uom" method="post" action="/master/product/uom/update/{{ $uomById->ProductUOMID }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="nama-uom" class="col-form-label">Nama UOM Produk</label>
                                        <input type="text" name="uom_name" value="{{ $uomById->ProductUOMName }}" class="form-control @if($errors->has('uom_name')) is-invalid @endif" id="nama-uom" placeholder="Masukkan Nama UOM Produk" required>
                                        @if($errors->has('uom_name'))
                                            <span class="error invalid-feedback">{{ $errors->first('uom_name') }}</span>
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