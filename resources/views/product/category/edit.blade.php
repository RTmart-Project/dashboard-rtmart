@extends('layouts.master')
@section('title', 'Dashboard - Edit Product Category')

@section('header-menu', 'Ubah Kategori Produk')

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
                        <a href="{{ route('product.category') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="edit-category" method="post" action="{{ route('product.updateCategory', ['category' => $categoryById->ProductCategoryID]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="nama-kategori" class="col-form-label">Nama Kategori Produk</label>
                                        <input type="text" name="category_name" value="{{ $categoryById->ProductCategoryName }}" class="form-control @if($errors->has('category_name')) is-invalid @endif" id="nama-kategori" placeholder="Masukkan Nama Kategori Produk" required>
                                        @if($errors->has('category_name'))
                                            <span class="error invalid-feedback">{{ $errors->first('category_name') }}</span>
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