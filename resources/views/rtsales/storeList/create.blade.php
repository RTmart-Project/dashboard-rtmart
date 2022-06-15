@extends('layouts.master')
@section('title', 'Dashboard - Add Store')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Store')

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
            <a href="{{ route('rtsales.storeList') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="create-store" method="post" action="{{ route('rtsales.storeStore') }}">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="store_name">Nama Store</label>
                    <input type="text" name="store_name" id="store_name" placeholder="Masukan Nama Store"
                      value="{{ old('store_name') }}" required autocomplete="off"
                      class="form-control @if($errors->has('store_name')) is-invalid @endif">
                    @if($errors->has('store_name'))
                    <span class="error invalid-feedback">{{ $errors->first('store_name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="owner_name">Nama Pemilik</label>
                    <input type="text" name="owner_name" id="owner_name" placeholder="Masukan Nama Pemilik"
                      value="{{ old('owner_name') }}" required autocomplete="off"
                      class="form-control @if($errors->has('owner_name')) is-invalid @endif">
                    @if($errors->has('owner_name'))
                    <span class="error invalid-feedback">{{ $errors->first('owner_name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="phone_number">No. HP</label>
                    <input type="number" name="phone_number" id="phone_number" placeholder="Masukan No. HP"
                      value="{{ old('phone_number') }}" required autocomplete="off"
                      class="form-control @if($errors->has('phone_number')) is-invalid @endif">
                    @if($errors->has('phone_number'))
                    <span class="error invalid-feedback">{{ $errors->first('phone_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="address">Alamat</label>
                    <input type="text" name="address" id="address" autocomplete="off"
                      placeholder="Masukan Alamat" value="{{ old('address') }}" required
                      class="form-control autonumeric @if($errors->has('address')) is-invalid @endif">
                    @if($errors->has('address'))
                    <span class="error invalid-feedback">{{ $errors->first('address') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" name="latitude" id="latitude" autocomplete="off"
                      placeholder="Masukan Latitude" value="{{ old('latitude') }}" required
                      class="form-control autonumeric @if($errors->has('latitude')) is-invalid @endif">
                    @if($errors->has('latitude'))
                    <span class="error invalid-feedback">{{ $errors->first('latitude') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" name="longitude" id="longitude" autocomplete="off"
                      placeholder="Masukan Longitude" value="{{ old('longitude') }}" required
                      class="form-control autonumeric @if($errors->has('longitude')) is-invalid @endif">
                    @if($errors->has('longitude'))
                    <span class="error invalid-feedback">{{ $errors->first('longitude') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="merchant">Merchant</label>
                    <select name="merchant" id="merchant" required data-live-search="true" title="Pilih Merchant"
                      class="form-control selectpicker border @if ($errors->has('merchant')) is-invalid @endif">
                      @foreach ($merchants as $merchant)
                      <option value="{{ $merchant->MerchantID }}"
                        {{ collect(old('merchant'))->contains($merchant->MerchantID) ? 'selected' : '' }}>
                        {{ $merchant->MerchantID }} - {{ $merchant->StoreName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('merchant'))
                      <span class="error invalid-feedback">{{ $errors->first('merchant') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="sales">Sales</label>
                    <select name="sales" id="sales" required data-live-search="true" title="Pilih Sales" 
                      class="form-control selectpicker border @if ($errors->has('sales')) is-invalid @endif">
                      @foreach ($sales as $value)
                      <option value="{{ $value->SalesCode }}"
                        {{ collect(old('sales'))->contains($value->SalesCode) ? 'selected' : '' }}>
                        {{ $value->SalesCode }} - {{ $value->SalesName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('sales'))
                      <span class="error invalid-feedback">{{ $errors->first('sales') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="grade">Grade</label>
                    <select name="grade" id="grade" required title="Pilih Grade" 
                      class="form-control selectpicker border @if ($errors->has('grade')) is-invalid @endif">
                      <option value="RETAIL" {{ (old('grade') == "RETAIL") ? 'selected' : '' }}>RETAIL</option>
                      <option value="SO" {{ (old('grade') == "SO") ? 'selected' : '' }}>SO</option>
                      <option value="WS" {{ (old('grade') == "WS") ? 'selected' : '' }}>WS</option>
                    </select>
                    @if($errors->has('grade'))
                      <span class="error invalid-feedback">{{ $errors->first('grade') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="store_type">Store Type</label>
                    <select name="store_type" id="store_type" required title="Pilih Store Type" 
                      class="form-control selectpicker border @if ($errors->has('store_type')) is-invalid @endif">
                      <option value="NEW" {{ (old('store_type') == "NEW") ? 'selected' : '' }}>NEW</option>
                      <option value="EXISTING" {{ (old('store_type') == "EXISTING") ? 'selected' : '' }}>EXISTING</option>
                    </select>
                    @if($errors->has('store_type'))
                      <span class="error invalid-feedback">{{ $errors->first('store_type') }}</span>
                    @endif
                  </div>
                </div>
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
<script>
</script>
@endsection