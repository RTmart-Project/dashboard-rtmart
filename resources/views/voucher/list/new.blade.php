@extends('layouts.master')
@section('title', 'Dashboard - Add Voucher')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ url('/') }}/plugins/summernote/summernote-bs4.css">
@endsection

@section('header-menu', 'Tambah Voucher')

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
                        <a href="{{ route('voucher.list') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="add-voucher" method="post" action="{{ route('voucher.insertList') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="voucher_code">Kode Voucher</label>
                                        <input type="text" id="voucher_code" name="voucher_code" class="form-control
                                            @if ($errors->has('voucher_code')) is-invalid @endif" 
                                            placeholder="Masukkan Kode Voucher" value="{{ old('voucher_code') }}" required>
                                        @if($errors->has('voucher_code'))
                                            <span class="error invalid-feedback">{{ $errors->first('voucher_code') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="voucher_name">Nama Voucher</label>
                                        <input type="text" id="voucher_name" name="voucher_name" class="form-control
                                            @if ($errors->has('voucher_name')) is-invalid @endif" 
                                            placeholder="Masukkan Nama Voucher" value="{{ old('voucher_name') }}" required>
                                        @if($errors->has('voucher_name'))
                                            <span class="error invalid-feedback">{{ $errors->first('voucher_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="voucher_type">Jenis Voucher</label>
                                        <select class="form-control selectpicker border
                                            @if($errors->has('voucher_type')) is-invalid @endif"
                                            name="voucher_type" id="voucher_type" title="Pilih Jenis Voucher" required>
                                            @foreach ($voucherType as $value)
                                                <option value="{{ $value->VoucherTypeID }}"
                                                    {{ (old('voucher_type') == $value->VoucherTypeID) ? 'selected' : '' }}>
                                                    {{ $value->VoucherTypeName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('voucher_type'))
                                        <span class="error invalid-feedback">{{ $errors->first('voucher_type') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="percentage">Persentase</label>
                                        <div class="input-group mb-3">
                                            <input type="number" id="percentage" name="percentage" class="form-control
                                                @if ($errors->has('percentage')) is-invalid @endif" 
                                                placeholder="Masukkan Persentase Voucher" value="{{ old('percentage') }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                            </div>
                                            @if($errors->has('percentage'))
                                                <span class="error invalid-feedback">{{ $errors->first('percentage') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="max_nominal">Maksimum Nominal</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp </span>
                                            </div>
                                            <input type="number" id="max_nominal" name="max_nominal" class="form-control
                                                @if ($errors->has('max_nominal')) is-invalid @endif" 
                                                placeholder="Masukkan Maksimum Nominal Voucher" value="{{ old('max_nominal') }}" required>
                                            @if($errors->has('max_nominal'))
                                                <span class="error invalid-feedback">{{ $errors->first('max_nominal') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="is_for">Untuk</label>
                                        <select class="form-control selectpicker border 
                                            @if($errors->has('is_for')) is-invalid @endif"
                                            name="is_for" id="is_for" title="Pilih Voucher untuk siapa" required>
                                            <option value="Customer" {{ (old('is_for') == "Customer") ? 'selected' : '' }}>Customer</option>
                                            <option value="Merchant" {{ (old('is_for') == "Merchant") ? 'selected' : '' }}>Merchant</option>
                                            <option value="All" {{ (old('is_for') == "All") ? 'selected' : '' }}>Customer dan Merchant</option>
                                        </select>
                                        @if($errors->has('is_for'))
                                            <span class="error invalid-feedback">{{ $errors->first('is_for') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="start_date">Waktu Mulai Berlaku Voucher</label>
                                        <input type="datetime-local" class="form-control
                                            @if($errors->has('start_date')) is-invalid @endif" 
                                            name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                                        @if($errors->has('start_date'))
                                            <span class="error invalid-feedback">{{ $errors->first('start_date') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="end_date">Waktu Selesai Berlaku Voucher</label>
                                        <input type="datetime-local" class="form-control
                                            @if($errors->has('end_date')) is-invalid @endif" 
                                            name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                                        @if($errors->has('end_date'))
                                            <span class="error invalid-feedback">{{ $errors->first('end_date') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="check_power_merchant">Syarat Power Merchant</label>
                                        <select class="form-control selectpicker border 
                                            @if($errors->has('check_power_merchant')) is-invalid @endif"
                                            name="check_power_merchant" id="check_power_merchant" title="Pilih apakah syarat harus power merchant" required>
                                            <option value="1" {{ (old('check_power_merchant') == "1") ? 'selected' : '' }}>Ya</option>
                                            <option value="0" {{ (old('check_power_merchant') == "0") ? 'selected' : '' }}>Tidak</option>
                                        </select>
                                        @if($errors->has('check_power_merchant'))
                                            <span class="error invalid-feedback">{{ $errors->first('check_power_merchant') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row border-bottom border-secondary">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="quota_per_user">Kuota per User</label>
                                        <input type="number" class="form-control
                                            @if($errors->has('quota_per_user')) is-invalid @endif"
                                            name="quota_per_user" id="quota_per_user" placeholder="Masukkan Kuota per User" value="{{ old('quota_per_user') }}" required>
                                        @if($errors->has('quota_per_user'))
                                            <span class="error invalid-feedback">{{ $errors->first('quota_per_user') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="max_quota">Kuota Keseluruhan</label>
                                        <input type="number" class="form-control
                                            @if($errors->has('max_quota')) is-invalid @endif"
                                            name="max_quota" id="max_quota" placeholder="Masukkan Kuota Keseluruhan" value="{{ old('max_quota') }}" required>
                                        @if($errors->has('max_quota'))
                                            <span class="error invalid-feedback">{{ $errors->first('max_quota') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="banner">Banner</label>
                                        <input type="file" class="form-control
                                            @if($errors->has('banner')) is-invalid @endif" accept="image/*" onchange="loadFile(event)"
                                            name="banner" id="banner" value="{{ old('banner') }}" required>
                                        @if($errors->has('banner'))
                                            <span class="error invalid-feedback">{{ $errors->first('banner') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12 mb-2">
                                    <img id="output" style="max-width: 300px; max-height: 150px"/>
                                </div>
                            </div>

                            {{-- Syarat --}}
                            <h6 class="mt-3">Syarat</h6>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="minimum_transaction">Minimum Nominal Transaksi</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp </span>
                                            </div>
                                            <input type="number" id="minimum_transaction" name="minimum_transaction" class="form-control
                                                @if ($errors->has('minimum_transaction')) is-invalid @endif" 
                                                placeholder="Masukkan Minimum Nominal Transaksi" value="{{ old('minimum_transaction') }}" required>
                                            @if($errors->has('minimum_transaction'))
                                                <span class="error invalid-feedback">{{ $errors->first('minimum_transaction') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="minimum_quantity">Minimum Kuantiti Pembelian</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Qty</span>
                                            </div>
                                            <input type="number" id="minimum_quantity" name="minimum_quantity" class="form-control
                                                @if ($errors->has('minimum_quantity')) is-invalid @endif" 
                                                placeholder="Masukkan Minimum Kuantiti Pembelian" value="{{ old('minimum_quantity') }}" required>
                                            @if($errors->has('minimum_quantity'))
                                                <span class="error invalid-feedback">{{ $errors->first('minimum_quantity') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="minimum_tx_history">Minimum Nominal (History Transaksi Customer)</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp </span>
                                            </div>
                                            <input type="number" id="minimum_tx_history" name="minimum_tx_history" class="form-control
                                                @if ($errors->has('minimum_tx_history')) is-invalid @endif" 
                                                placeholder="Nominal Transaksi Secara Akumulasi" value="{{ old('minimum_tx_history') }}" required>
                                            @if($errors->has('minimum_tx_history'))
                                                <span class="error invalid-feedback">{{ $errors->first('minimum_tx_history') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="minimum_qty_history">Minimum Kuantiti (History Transaksi Customer)</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Qty</span>
                                            </div>
                                            <input type="number" id="minimum_qty_history" name="minimum_qty_history" class="form-control
                                                @if ($errors->has('minimum_qty_history')) is-invalid @endif" 
                                                placeholder="Kuantiti Pembelian Secara Akumulasi" value="{{ old('minimum_qty_history') }}" required>
                                            @if($errors->has('minimum_qty_history'))
                                                <span class="error invalid-feedback">{{ $errors->first('minimum_qty_history') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label>Spesifik Metode Pembayaran</label>
                                    <div class="row">
                                        <div class="col-md-4 col-4 pt-2">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_payment_method"
                                                        name="switch_payment_method" {{ !empty(old('switch_payment_method')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_payment_method">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-8">
                                            <div class="form-group">
                                                <select class="form-control selectpicker payment-method border
                                                    @if($errors->has('payment_method')) is-invalid @endif" 
                                                    id="payment_method" name="payment_method[]" data-live-search="true" multiple title="Pilih Metode Pembayaran" 
                                                    {{ !empty(old('payment_method')) ? '' : 'disabled' }}>
                                                    @foreach ($paymentMethod as $value)
                                                        <option value="{{ $value->PaymentMethodID }}"
                                                            {{ collect(old('payment_method'))->contains($value->PaymentMethodID) ? 'selected' : '' }}>
                                                            {{ $value->PaymentMethodName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('payment_method'))
                                                    <span class="error invalid-feedback">{{ $errors->first('payment_method') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label>Spesifik Lokasi Distributor</label>
                                    <div class="row">
                                        <div class="col-md-4 col-4 pt-2">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_distributor_location"
                                                        name="switch_distributor_location" {{ !empty(old('switch_distributor_location')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_distributor_location">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-8">
                                            <div class="form-group">
                                                <select class="form-control selectpicker distributor-location border
                                                    @if($errors->has('distributor_location')) is-invalid @endif" 
                                                    id="distributor_location" name="distributor_location[]" data-live-search="true" multiple title="Pilih Lokasi Distributor" 
                                                    {{ !empty(old('distributor_location')) ? '' : 'disabled' }}>
                                                    @foreach ($distributorLocation as $value)
                                                        <option value="{{ $value->DistributorID }}"
                                                            {{ collect(old('distributor_location'))->contains($value->DistributorID) ? 'selected' : '' }}>
                                                            {{ $value->DistributorName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('distributor_location'))
                                                    <span class="error invalid-feedback">{{ $errors->first('distributor_location') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label>Spesifik Brand</label>
                                    <div class="row">
                                        <div class="col-md-4 col-4 pt-2">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_term_brand"
                                                        name="switch_term_brand" {{ !empty(old('switch_term_brand')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_term_brand">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-8">
                                            <div class="form-group">
                                                <select class="form-control selectpicker term-brand border
                                                    @if($errors->has('term_brand')) is-invalid @endif" 
                                                    id="term_brand" name="term_brand[]" data-live-search="true" multiple title="Pilih Brand" 
                                                    {{ !empty(old('term_brand')) ? '' : 'disabled' }}>
                                                    @foreach ($termBrand as $value)
                                                        <option value="{{ $value->BrandID }}"
                                                            {{ collect(old('term_brand'))->contains($value->BrandID) ? 'selected' : '' }}>
                                                            {{ $value->Brand }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('term_brand'))
                                                    <span class="error invalid-feedback">{{ $errors->first('term_brand') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label>Spesifik Kategori</label>
                                    <div class="row">
                                        <div class="col-md-4 col-4 pt-2">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_term_category"
                                                        name="switch_term_category" {{ !empty(old('switch_term_category')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_term_category">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-8">
                                            <div class="form-group">
                                                <select class="form-control selectpicker term-category border
                                                    @if($errors->has('term_category')) is-invalid @endif" 
                                                    id="term_category" name="term_category[]" data-live-search="true" multiple title="Pilih Kategori"
                                                    {{ !empty(old('term_category')) ? '' : 'disabled' }}>
                                                    @foreach ($termCategory as $value)
                                                        <option value="{{ $value->ProductCategoryID }}"
                                                            {{ collect(old('term_category'))->contains($value->ProductCategoryID) ? 'selected' : '' }}>
                                                            {{ $value->ProductCategoryName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('term_category'))
                                                    <span class="error invalid-feedback">{{ $errors->first('term_category') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="m-0">Pengguna Baru</label>
                                    <div class="row">
                                        <div class="col-md-2 col-12 py-auto">
                                            <div class="form-group m-0 py-1 py-lg-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_new_user" name="switch_new_user" 
                                                        {{ !empty(old('switch_new_user')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_new_user">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-6">
                                            <div class="form-group">
                                                <label class="mb-0" for="start_date_new_user">Waktu Awal</label>
                                                <input type="datetime-local" class="form-control new-user
                                                    @if ($errors->has('start_date_new_user')) is-invalid @endif" 
                                                    name="start_date_new_user" id="start_date_new_user" value="{{ old('start_date_new_user') }}" 
                                                    {{ $errors->has('start_date_new_user') || $errors->has('end_date_new_user') || old('start_date_new_user') ? '' : 'disabled' }}>
                                                @if($errors->has('start_date_new_user'))
                                                    <span class="error invalid-feedback">{{ $errors->first('start_date_new_user') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-6">
                                            <div class="form-group">
                                                <label class="mb-0" for="end_date_new_user">Waktu Akhir</label>
                                                <input type="datetime-local" class="form-control new-user
                                                    @if ($errors->has('end_date_new_user')) is-invalid @endif" 
                                                    name="end_date_new_user" id="end_date_new_user" value="{{ old('end_date_new_user') }}" 
                                                    {{ $errors->has('start_date_new_user') || $errors->has('end_date_new_user') || old('end_date_new_user') ? '' : 'disabled' }}>
                                                @if($errors->has('end_date_new_user'))
                                                    <span class="error invalid-feedback">{{ $errors->first('end_date_new_user') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="m-0">Merchant Restock</label>
                                    <div class="row">
                                        <div class="col-md-2 col-12 py-auto">
                                            <div class="form-group m-0 py-1 py-lg-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_merchant_restock" name="switch_merchant_restock" {{ !empty(old('switch_merchant_restock')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_merchant_restock">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-6">
                                            <div class="form-group">
                                                <label class="mb-0" for="start_date_merchant_restock">Waktu Awal</label>
                                                <input type="datetime-local" class="form-control merchant-restock
                                                    @if ($errors->has('start_date_merchant_restock')) is-invalid @endif" 
                                                    name="start_date_merchant_restock" id="start_date_merchant_restock" value="{{ old('start_date_merchant_restock') }}" 
                                                    {{ $errors->has('start_date_merchant_restock') || $errors->has('end_date_merchant_restock') || old('start_date_merchant_restock') ? '' : 'disabled' }}>
                                                @if($errors->has('start_date_merchant_restock'))
                                                    <span class="error invalid-feedback">{{ $errors->first('start_date_merchant_restock') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-6">
                                            <div class="form-group">
                                                <label class="mb-0" for="end_date_merchant_restock">Waktu Akhir</label>
                                                <input type="datetime-local" class="form-control merchant-restock
                                                    @if ($errors->has('end_date_merchant_restock')) is-invalid @endif" 
                                                    name="end_date_merchant_restock" id="end_date_merchant_restock" value="{{ old('end_date_merchant_restock') }}"
                                                    {{ $errors->has('start_date_merchant_restock') || $errors->has('end_date_merchant_restock') || old('end_date_merchant_restock') ? '' : 'disabled' }}>
                                                @if($errors->has('end_date_merchant_restock'))
                                                    <span class="error invalid-feedback">{{ $errors->first('end_date_merchant_restock') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row border-bottom border-secondary">
                                <div class="col-12">
                                    <label class="m-0">Cek History Transaksi Customer</label>
                                    <div class="row">
                                        <div class="col-md-2 col-12 py-auto">
                                            <div class="form-group m-0 py-1 py-lg-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input form-control" id="switch_customer_tx" 
                                                        name="switch_customer_tx" {{ !empty(old('switch_customer_tx')) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch_customer_tx">Tidak / Ya</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-6">
                                            <div class="form-group">
                                                <label class="mb-0" for="start_date_customer_tx">Waktu Awal</label>
                                                <input type="datetime-local" class="form-control customer-tx
                                                    @if ($errors->has('start_date_customer_tx')) is-invalid @endif" 
                                                    name="start_date_customer_tx" id="start_date_customer_tx" value="{{ old('start_date_customer_tx') }}"
                                                    {{ $errors->has('start_date_customer_tx') || $errors->has('end_date_customer_tx') || old('start_date_customer_tx') ? '' : 'disabled' }}>
                                                @if($errors->has('start_date_customer_tx'))
                                                    <span class="error invalid-feedback">{{ $errors->first('start_date_customer_tx') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-6">
                                            <div class="form-group">
                                                <label class="mb-0" for="end_date_customer_tx">Waktu Akhir</label>
                                                <input type="datetime-local" class="form-control customer-tx
                                                    @if ($errors->has('end_date_customer_tx')) is-invalid @endif" 
                                                    name="end_date_customer_tx" id="end_date_customer_tx" value="{{ old('end_date_customer_tx') }}"
                                                    {{ $errors->has('start_date_customer_tx') || $errors->has('end_date_customer_tx') || old('end_date_customer_tx') ? '' : 'disabled' }}>
                                                @if($errors->has('end_date_customer_tx'))
                                                    <span class="error invalid-feedback">{{ $errors->first('end_date_customer_tx') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Syarat Produk --}}
                            <h6 class="mt-3">Syarat Produk</h6>
                            <div class="row">
                                <div class="col-6">
                                    <p>Apakah ada syarat spesifik pada produk tertentu?</p>
                                </div>
                                <div class="col-6">
                                    <div class="form-group m-0">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input form-control" id="switch_term_product" 
                                                name="switch_term_product" {{ !empty(old('switch_term_product')) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="switch_term_product">Tidak / Ya</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="term-product" class="{{ !empty(old('switch_term_product')) ? '' : 'd-none' }}">
                                <div id="add-term-product" class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="term_product">Produk</label>
                                            <button href="" class="btn btn-sm float-right remove"><i class="far fa-times-circle fa-lg text-danger"></i></button>
                                            <select class="form-control selectpicker border select-product
                                                @if($errors->has('term_product')) is-invalid @endif"
                                                name="term_product[]" data-live-search="true" title="Pilih Produk">
                                                @foreach ($termProduct as $value)
                                                    <option value="{{ $value->ProductID }}"
                                                        {{ collect(old('term_product'))->contains($value->ProductID) ? 'selected' : '' }}>
                                                        {{ $value->ProductName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('term_product'))
                                                <span class="error invalid-feedback">{{ $errors->first('term_product') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="minimum_tx_product">Minimum Nominal Transaksi Produk (Saat Checkout)</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp </span>
                                                </div>
                                                <input type="number" name="minimum_tx_product[]" class="form-control
                                                    @if ($errors->has('minimum_tx_product')) is-invalid @endif" 
                                                    value="{{ collect(old('minimum_tx_product')) }}">
                                                @if($errors->has('minimum_tx_product'))
                                                    <span class="error invalid-feedback">{{ $errors->first('minimum_tx_product') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="minimum_qty_product">Minimum Kuantiti Transaksi Produk (Saat Checkout)</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Qty </span>
                                                </div>
                                                <input type="number" name="minimum_qty_product[]" class="form-control
                                                    @if ($errors->has('minimum_qty_product')) is-invalid @endif" 
                                                    value="{{ collect(old('minimum_qty_product')) }}">
                                                @if($errors->has('minimum_qty_product'))
                                                    <span class="error invalid-feedback">{{ $errors->first('minimum_qty_product') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="minimum_tx_product_history">Minimum Nominal (History Transaksi Customer)</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp </span>
                                                </div>
                                                <input type="number" name="minimum_tx_product_history[]" class="form-control
                                                    @if ($errors->has('minimum_tx_product_history')) is-invalid @endif" 
                                                    value="{{ collect(old('minimum_tx_product_history')) }}">
                                                @if($errors->has('minimum_tx_product_history'))
                                                    <span class="error invalid-feedback">{{ $errors->first('minimum_tx_product_history') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="minimum_qty_product_history">Minimum Kuantiti (History Transaksi Customer)</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Qty </span>
                                                </div>
                                                <input type="number" name="minimum_qty_product_history[]" class="form-control
                                                    @if ($errors->has('minimum_qty_product_history')) is-invalid @endif" 
                                                    value="{{ collect(old('minimum_qty_product_history')) }}">
                                                @if($errors->has('minimum_qty_product_history'))
                                                    <span class="error invalid-feedback">{{ $errors->first('minimum_qty_product_history') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="minimum_tx_product_restock">Minimum Nominal (History Restock Merchant)</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp </span>
                                                </div>
                                                <input type="number" name="minimum_tx_product_restock[]" class="form-control
                                                    @if ($errors->has('minimum_tx_product_restock')) is-invalid @endif" 
                                                    value="{{ collect(old('minimum_tx_product_restock')) }}">
                                                @if($errors->has('minimum_tx_product_restock'))
                                                    <span class="error invalid-feedback">{{ $errors->first('minimum_tx_product_restock') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="minimum_qty_product_restock">Minimum Kuantiti (History Restock Merchant)</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Qty </span>
                                                </div>
                                                <input type="number" name="minimum_qty_product_restock[]" class="form-control
                                                    @if ($errors->has('minimum_qty_product_restock')) is-invalid @endif" 
                                                    value="{{ collect(old('minimum_qty_product_restock')) }}">
                                                @if($errors->has('minimum_qty_product_restock'))
                                                    <span class="error invalid-feedback">{{ $errors->first('minimum_qty_product_restock') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="add-term-product-append"></div>
                                <div class="clearfix">
                                    <button type="button" class="btn btn-secondary btn-sm add float-right"><i class="fas fa-plus"></i> Tambah Produk</button>
                                </div>
                            </div>
                            <div class="border-top border-secondary mt-3">
                                <h6 class="mt-3">Penjelasan Syarat & Ketentuan</h6>
                                <textarea name="details" class="textarea 
                                    @if ($errors->has('details')) is-invalid @endif">{{ old('details') }}</textarea>
                                @if($errors->has('details'))
                                    <span class="error invalid-feedback">{{ $errors->first('details') }}</span>
                                @endif
                            </div>
                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-success">Tambah</button>
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
<script src="{{ url('/') }}/plugins/summernote/summernote-bs4.min.js"></script>
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{ url('/') }}/main/js/helper/clone-element.js"></script>
<script>
    // Term New User
    const switchNewUser = document.querySelector('#switch_new_user');
    const startDateNewUser = document.querySelector('#start_date_new_user');
    const endDateNewUser = document.querySelector('#end_date_new_user');
    switchNewUser.onchange = function() {
        startDateNewUser.disabled = !this.checked;
        endDateNewUser.disabled = !this.checked;
        startDateNewUser.value = "";
        endDateNewUser.value = "";
    };

    // Term Merchant Restock
    const switchMerchantRestock = document.querySelector('#switch_merchant_restock');
    const startDateMerchantRestock = document.querySelector('#start_date_merchant_restock');
    const endDateMerchantRestock = document.querySelector('#end_date_merchant_restock');
    switchMerchantRestock.onchange = function() {
        startDateMerchantRestock.disabled = !this.checked;
        endDateMerchantRestock.disabled = !this.checked;
        startDateMerchantRestock.value = "";
        endDateMerchantRestock.value = "";
    };

    // Term Customer Tx
    const switchCustomerTx = document.querySelector('#switch_customer_tx');
    const startDateCustomerTx = document.querySelector('#start_date_customer_tx');
    const endDateCustomerTx = document.querySelector('#end_date_customer_tx');
    switchCustomerTx.onchange = function() {
        startDateCustomerTx.disabled = !this.checked;
        endDateCustomerTx.disabled = !this.checked;
        startDateCustomerTx.value = "";
        endDateCustomerTx.value = "";
    };

    // Spesifik Payment Method
    const switchPaymentMethod = document.querySelector('#switch_payment_method');
    const selectPaymentMethod = document.querySelector('#payment_method');
    switchPaymentMethod.onchange = function() {
        selectPaymentMethod.disabled = !this.checked;
        $('.payment-method').val('default').selectpicker('refresh');
    };

    // Spesifik Distributor Location
    const switchDistributorLocation = document.querySelector('#switch_distributor_location');
    const selectDistributorLocation = document.querySelector('#distributor_location');
    switchDistributorLocation.onchange = function () {
        selectDistributorLocation.disabled = !this.checked;
        $('.distributor-location').val('default').selectpicker('refresh');
    };

    // Spesifik Brand
    const switchBrand = document.querySelector('#switch_term_brand');
    const selectBrand = document.querySelector('#term_brand');
    switchBrand.onchange = function () {
        selectBrand.disabled = !this.checked;
        $('.term-brand').val('default').selectpicker('refresh');
    };

    // Spesifik Category
    const switchCategory = document.querySelector('#switch_term_category');
    const selectCategory = document.querySelector('#term_category');
    switchCategory.onchange = function () {
        selectCategory.disabled = !this.checked;
        $('.term-category').val('default').selectpicker('refresh');
    };

    // Term Product
    const switchProduct = document.querySelector('#switch_term_product');
    const termProduct = document.querySelector('#term-product');
    switchProduct.onchange = function () {
        if (termProduct.classList.contains('d-none')) {
            termProduct.classList.remove('d-none');
        } else {
            termProduct.classList.add('d-none');
        }
    };

    // Cloning Form Term Product
    $('#add-term-product:first .remove').css("visibility", "hidden");
    $('.add').on('click', function () {
        $('#add-term-product:first .remove').css("visibility", "visible");
        cloneElement('#add-term-product:first', '#add-term-product-append');
        $('#add-term-product:first .remove').css("visibility", "hidden");
    });

    $('body').on('click', '.remove', function() {
        let closest = $(this).closest('#add-term-product').remove();
    });

    // Summernote
    $('.textarea').summernote({
        height: 200,
        toolbar: [
            ['font', ['bold', 'underline']],
            ['para', ['ul']],
            ['view', ['codeview']]
        ]
    });
</script>
@endsection