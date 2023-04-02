@extends('layouts.master')

@section('title', 'Dashboard - Edit Operational Hour')

@section('header-menu', 'Ubah Jam Operasional')

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
                                            <img src="{{ config('app.base_image_url') . '/merchant/'. $merchant->StoreImage }}" alt="Store Image" class="rounded img-fluid pb-2 pb-md-0" style="object-fit: cover; width: 130px; height: 130px;">
                                        </div>
                                        <div class="col-12 col-md-10 align-self-center">
                                            <h6><strong>Merchant ID : </strong>{{ $merchantId }}</h6>
                                            <h6><strong>Nama Toko : </strong>{{ $merchant->StoreName }}</h6>
                                            <h6><strong>Nama Pemilik : </strong>{{ $merchant->OwnerFullName }}</h6>
                                            <h6><strong>No. Telp : </strong><a href="tel:{{ $merchant->PhoneNumber }}">{{ $merchant->PhoneNumber }}</a></h6>
                                            <h6><strong>Alamat : </strong>{{ $merchant->StoreAddress }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="edit-operational-hour-merchant" method="post" action="{{ route('merchant.updateOperationalHour', ['merchantId' => $merchantId]) }}">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-2 col-form-label text-center">Hari</label>
                                <label class="col-5 col-form-label text-center">Jam Buka</label>
                                <label class="col-5 col-form-label text-center">Jam Tutup</label>
                            </div>
                            @foreach ($merchantOperationalHour as $key => $value)
                            <div class="row mb-3">
                                <input type="text" class="form-control-plaintext col-form-label col-2 text-center" name="day[]" id="day" value="{{ $value->DayOfWeek }}" readonly>
                                <div class="col-5">
                                    <input type="time" class="form-control @if($errors->has('opening_hour.'.$key)) is-invalid @endif" name="opening_hour[]" id="opening_hour" value="{{ date('H:i', strtotime($value->OpeningHour)) }}">
                                    @if ($errors->has('opening_hour.'.$key))
                                        <span class="error invalid-feedback">{{ $errors->first('opening_hour.'.$key) }}</span>
                                    @endif
                                </div>
                                <div class="col-5">
                                    <input type="time" class="form-control @if($errors->has('closing_hour.'.$key)) is-invalid @endif" name="closing_hour[]" id="closing_hour" value="{{ date('H:i', strtotime($value->ClosingHour)) }}">
                                    @if ($errors->has('closing_hour.'.$key))
                                        <span class="error invalid-feedback">{{ $errors->first('closing_hour.'.$key) }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach

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