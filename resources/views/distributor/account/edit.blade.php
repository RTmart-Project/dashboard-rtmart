@extends('layouts.master')
@section('title', 'Dashboard - Edit Distributor')

@section('css-pages')

@endsection

@section('header-menu', 'Ubah Distributor')

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
                        <a href="{{ route('distributor.account') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="edit-distributor" method="post"
                            action="{{ route('distributor.updateAccount', ['distributorId' => $distributorById->DistributorID]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label>Distributor ID</label>
                                        <input type="text" class="form-control"
                                            value="{{ $distributorById->DistributorID }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="name">Nama Distributor</label>
                                        <input type="text" name="name"
                                            class="form-control @if($errors->has('name')) is-invalid @endif" id="name"
                                            placeholder="Masukkan Nama Distributor"
                                            value="{{ $distributorById->DistributorName }}" required>
                                        @if($errors->has('name'))
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email"
                                            class="form-control @if($errors->has('email')) is-invalid @endif" id="email"
                                            placeholder="Masukkan Email" value="{{ $distributorById->Email }}" required>
                                        @if($errors->has('email'))
                                        <span class="error invalid-feedback">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <textarea name="address" id="address" rows="5"
                                            class="form-control 
                                            @if($errors->has('address')) is-invalid @endif">{{ $distributorById->Address }}</textarea>
                                        @if($errors->has('address'))
                                        <span class="error invalid-feedback">{{ $errors->first('address') }}</span>
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

@endsection