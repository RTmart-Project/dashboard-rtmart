@extends('layouts.master')
@section('title', 'Dashboard - Tambah Supplier')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Supplier')

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
                        <a href="{{ route('supplier.account') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="add-supplier" method="post" action="{{ route('supplier.insertSupplier') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="suppliername">Nama Supplier</label>
                                        <input type="text" name="suppliername"
                                            class="form-control @if($errors->has('suppliername')) is-invalid @endif"
                                            id="suppliername" placeholder="Masukkan Nama Supplier">
                                        @if($errors->has('suppliername'))
                                        <span class="error invalid-feedback">{{ $errors->first('suppliername') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-success">Tambah Supplier</button>
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
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
@endsection