@extends('layouts.master')
@section('title', 'Dashboard - Tambah Distributor')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Distributor')

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
                        <form id="add-distributor" method="post" action="{{ route('distributor.insertDistributor') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="distributorname">Nama Distributor</label>
                                        <input type="text" name="distributorname"
                                            class="form-control @if($errors->has('name')) is-invalid @endif"
                                            id="distributorname" placeholder="Masukkan Nama Distributor">
                                        @if($errors->has('distributorname'))
                                        <span class="error invalid-feedback">{{ $errors->first('distributorname')
                                            }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email"
                                            class="form-control @if($errors->has('email')) is-invalid @endif" id="email"
                                            placeholder="Masukkan Email">
                                        @if($errors->has('email'))
                                        <span class="error invalid-feedback">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phonenumber">Nomor Telepon</label>
                                        <input type="number" name="phonenumber"
                                            class="form-control @if($errors->has('phonenumber')) is-invalid @endif"
                                            id="phonenumber" placeholder="Masukkan Nomor Telepon Pengguna"
                                            value="{{ old('phonenumber') }}">
                                        @if($errors->has('phonenumber'))
                                        <span class="error invalid-feedback">{{ $errors->first('phonenumber') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="depo">Depo</label>
                                        <select
                                            class="form-control selectpicker border @if($errors->has('depo')) is-invalid @endif"
                                            name="depo" id="depo" data-live-search="true" title="Pilih Depo">
                                            @foreach ($depo as $item)
                                            <option value="{{ $item->Depo }}" {{ (old('depo')==$item->Depo) ? 'selected'
                                                : '' }}>
                                                {{ $item->Depo }} - {{ $item->DistributorName }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('depo'))
                                        <span class="error invalid-feedback">{{ $errors->first('depo') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <textarea name="address" id="address" rows="5" class="form-control 
                                            @if($errors->has('address')) is-invalid @endif"></textarea>
                                        @if($errors->has('address'))
                                        <span class="error invalid-feedback">{{ $errors->first('address') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-success">Tambah Distributor</button>
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