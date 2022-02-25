@extends('layouts.master')
@section('title', 'Dashboard - Edit Merchant')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Ubah Merchant')

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
                        <a href="{{ route('merchant.account') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="edit-merchant" method="post" action="{{ route('merchant.updateAccount', ['merchantId' => $merchantById->MerchantID]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label>Merchant ID</label>
                                        <input type="text" class="form-control" value="{{ $merchantById->MerchantID }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="store_name">Nama Toko</label>
                                        <input type="text" name="store_name"
                                            class="form-control @if($errors->has('store_name')) is-invalid @endif" id="store_name"
                                            placeholder="Masukkan Nama Toko" value="{{ $merchantById->StoreName }}" required>
                                        @if($errors->has('store_name'))
                                        <span class="error invalid-feedback">{{ $errors->first('store_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="owner_name">Nama Pemilik</label>
                                        <input type="text" name="owner_name"
                                            class="form-control @if($errors->has('owner_name')) is-invalid @endif" id="owner_name"
                                            placeholder="Masukkan Nama Pemilik" value="{{ $merchantById->OwnerFullName }}" required>
                                        @if($errors->has('owner_name'))
                                        <span class="error invalid-feedback">{{ $errors->first('owner_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="phone_number">No. Telp</label>
                                        <input type="number" name="phone_number"
                                            class="form-control @if($errors->has('phone_number')) is-invalid @endif" id="phone_number"
                                            placeholder="Masukkan No, Telp" value="{{ $merchantById->PhoneNumber }}" required>
                                        @if($errors->has('phone_number'))
                                        <span class="error invalid-feedback">{{ $errors->first('phone_number') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="distributor">Distributor</label>
                                        <select class="form-control selectpicker border @if($errors->has('distributor')) is-invalid @endif"
                                            name="distributor" id="distributor" data-live-search="true" required>
                                        @foreach ($distributor as $value)
                                            <option value="{{ $value->DistributorID }}" {{ ($merchantById->DistributorID) == ($value->DistributorID) ? 'selected' : '' }}>{{ $value->DistributorName }}</option>
                                        @endforeach
                                        </select>
                                        @if($errors->has('distributor'))
                                        <span class="error invalid-feedback">{{ $errors->first('distributor') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="grade">Grade</label>
                                        <select class="form-control selectpicker border @if($errors->has('grade')) is-invalid @endif"
                                            name="grade" id="grade" required title="Pilih Grade">
                                            @foreach ($grade as $value)
                                                <option value="{{ $value->GradeID }}" {{ ($merchantById->GradeID) == ($value->GradeID) ? 'selected' : '' }}>{{ $value->Grade }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('grade'))
                                        <span class="error invalid-feedback">{{ $errors->first('grade') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if (Auth::user()->RoleID == "IT" || (Auth::user()->RoleID == "BM"))
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="referral_code">Kode Referral</label>
                                        <input type="text" name="referral_code"
                                            class="form-control @if($errors->has('referral_code')) is-invalid @endif" id="referral_code"
                                            placeholder="Masukkan Kode Referral" value="{{ $merchantById->ReferralCode }}" autocomplete="off" onkeyup="this.value = this.value.toUpperCase();">
                                        @if($errors->has('referral_code'))
                                        <span class="error invalid-feedback">{{ $errors->first('referral_code') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <textarea name="address" id="address" rows="5" class="form-control 
                                            @if($errors->has('address')) is-invalid @endif">{{ $merchantById->StoreAddress }}</textarea>
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
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script>
    $('#distributor').change(function(){
        let distributorID = $(this).val();
        if(distributorID){
            $.ajax({
                type: "GET",
                url: "/merchant/account/grade/get/" + distributorID,
                dataType: 'JSON',
                success:function(res){
                    if(res){
                        let option = '';
                        $.each(res, function(index, value){
                            option += `<option value="${value.GradeID}">${value.Grade}</option>`;
                        });
                        $('#grade').html(option);
                        $('#grade').selectpicker('refresh');
                    }
                }
            });
        }
    });
</script>
@endsection