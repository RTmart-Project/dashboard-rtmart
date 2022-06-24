@extends('layouts.master')
@section('title', 'Dashboard - Add Distributor Product')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Produk Distributor')

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
                        <a href="{{ route('distribution.product') }}" class="btn btn-sm btn-light"><i
                                class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="add-distribution-product" method="post"
                            action="{{ route('distribution.insertProduct') }}">
                            @csrf
                            <div class="row">
                                @if (Auth::user()->RoleID == "AD")
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="distributor">Distributor</label>
                                        <input class="form-control-plaintext" value="{{ $depo->DistributorName }}"
                                            readonly>
                                        <input type="hidden" name="distributor" value="{{ $depo->DistributorID }}">
                                    </div>
                                </div>
                                <div class="col-md-8 col-12">
                                    <div class="form-group">
                                        <label for="product">Produk</label>
                                        <select
                                            class="form-control selectpicker border @if($errors->has('product')) is-invalid @endif"
                                            name="product" id="product" data-live-search="true" title="Pilih Produk"
                                            required>
                                            @foreach ($productNotInDistributor as $item)
                                            <option value="{{ $item->ProductID }}">{{ $item->ProductName }} -- Isi: {{
                                                $item->ProductUOMDesc }} {{ $item->ProductUOMName }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('product'))
                                        <span class="error invalid-feedback">{{ $errors->first('product') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="grade_price">Set Harga</label>
                                    <div class="row">
                                        @foreach ($gradeDistributor as $item)
                                        <div class="col-4 d-flex justify-content-center">
                                            <label>{{ $item->Grade }}</label>
                                            <input type="hidden" name="grade_id[]" value="{{ $item->GradeID }}">
                                        </div>
                                        <div class="col-8">
                                            <div class="form-group">
                                                <input type="text" name="grade_price[]"
                                                    class="form-control autonumeric @if($errors->has('grade_price')) is-invalid @endif"
                                                    value="{{ collect(old('grade_price')) }}" autocomplete="off"
                                                    required>
                                                @if($errors->has('grade_price'))
                                                <span class="error invalid-feedback">{{ $errors->first('grade_price')
                                                    }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="distributor">Distributor</label>
                                        <select
                                            class="form-control selectpicker border @if($errors->has('distributor')) is-invalid @endif"
                                            name="distributor" id="distributor" data-live-search="true"
                                            title="Pilih Distributor" required>
                                            @foreach ($distributor as $item)
                                            <option value="{{ $item->DistributorID }}">{{ $item->DistributorName }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('distributor'))
                                        <span class="error invalid-feedback">{{ $errors->first('distributor') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-8 col-12">
                                    <div class="form-group">
                                        <label for="product">Produk</label>
                                        <select
                                            class="form-control selectpicker border @if($errors->has('product')) is-invalid @endif"
                                            name="product" id="product" data-live-search="true" title="Pilih Produk"
                                            required>
                                        </select>
                                        @if($errors->has('product'))
                                        <span class="error invalid-feedback">{{ $errors->first('product') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="grade_price">Set Harga</label>
                                    <div class="row grade-price">

                                    </div>
                                </div>
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
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
    // Set seperator '.' currency
    new AutoNumeric.multiple('.autonumeric', {
        allowDecimalPadding: false,
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        unformatOnSubmit: true
    });
    
    $('#distributor').change(function(){
        let distributorID = $(this).val();
        if(distributorID){
            $.ajax({
                type: "GET",
                url: "/distribution/product/ajax/get/" + distributorID,
                dataType: 'JSON',
                success:function(res){
                    if(res){
                        let option = '';
                        $.each(res, function(index, value){
                            option += `<option value="${value.ProductID}">${value.ProductID} - ${value.ProductName} -- Isi ${value.ProductUOMDesc} ${value.ProductUOMName}</option>`;
                        });
                        $('#product').html(option);
                        $('#product').selectpicker('refresh');
                    }
                }
            });
            $.ajax({
                type: "GET",
                url: "/merchant/account/grade/get/" + distributorID,
                dataType: 'JSON',
                success:function(res){
                    if(res){
                        let div = '';
                        $.each(res, function(index, value){
                            div += `<div class="col-4 d-flex justify-content-center">
                                            <label>${value.Grade}</label>
                                            <input type="hidden" name="grade_id[]" value="${value.GradeID}">
                                        </div>
                                        <div class="col-8">
                                            <div class="form-group">
                                                <input type="text" name="grade_price[]"
                                                    class="form-control autonumeric @if($errors->has('grade_price')) is-invalid @endif" id="grade_price" value="{{ collect(old('grade_price')) }}" autocomplete="off" required>
                                                @if($errors->has('grade_price'))
                                                    <span class="error invalid-feedback">{{ $errors->first('grade_price') }}</span>
                                                @endif
                                            </div>
                                        </div>`;
                        });
                        $('.grade-price').html(div);
                        // Set seperator '.' currency
                        const currencyJumlahTopup = new AutoNumeric.multiple('.autonumeric', {
                            allowDecimalPadding: false,
                            decimalCharacter: ',',
                            digitGroupSeparator: '.',
                            unformatOnSubmit: true
                        });
                    }
                }
            });
        }
    });
</script>
@endsection