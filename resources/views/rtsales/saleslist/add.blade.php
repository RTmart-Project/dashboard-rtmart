@extends('layouts.master')
@section('title', 'Dashboard - Add Sales')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Sales')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row">
      <!-- left -->
      <div class="col-sm-6">
        <h1 class="m-0"></h1>
      </div>
      <!-- Right -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"></li>
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
            <a href="{{ route('rtsales.saleslist') }}" class="btn btn-sm btn-light">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
          <div class="card-body">
            <form id="add-sales" method="post" action="{{ route('rtsales.insertSales') }}">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="sales_name">Nama Sales</label>
                    <input type="text" name="sales_name" class="form-control 
                      @if($errors->has('sales_name')) is-invalid @endif" id="sales_name" autocomplete="off"
                      placeholder="Masukkan Nama Sales" value="{{ old('sales_name') }}" required>
                    @if($errors->has('sales_name'))
                    <span class="error invalid-feedback">{{ $errors->first('sales_name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="sales_level">Sales Level</label>
                    <input type="number" name="sales_level" class="form-control 
                    @if($errors->has('sales_level')) is-invalid @endif" id="sales_level"
                      placeholder="Masukkan Level Sales" value="{{ old('sales_level') }}" required>
                    @if($errors->has('sales_level'))
                    <span class="error invalid-feedback">{{ $errors->first('sales_level') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="prefix_sales_code">Prefix Sales Code</label>
                    <input type="text" name="prefix_sales_code" onkeyup="this.value = this.value.toUpperCase();" class="form-control 
                      @if($errors->has('prefix_sales_code')) is-invalid @endif" id="prefix_sales_code"
                      autocomplete="off" placeholder="Masukkan Prefix Kode Sales"
                      value="{{ old('prefix_sales_code') }}">
                    @if($errors->has('prefix_sales_code'))
                    <span class="error invalid-feedback">{{ $errors->first('prefix_sales_code') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="team">Team</label>
                    <select class="form-control selectpicker border
                      @if($errors->has('team')) is-invalid @endif" id="team" name="team" title="Pilih Team">
                      @foreach ($teams as $value)
                      <option value="{{ $value->TeamCode }}" {{ collect(old('team'))->contains($value->TeamCode) ?
                        'selected' : '' }}>
                        {{ $value->TeamCode }} ({{ $value->TeamName }})
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('team'))
                    <span class="error invalid-feedback">{{ $errors->first('team') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="team_by">Team By</label>
                    <input type="text" name="team_by" class="form-control 
                      @if($errors->has('team_by')) is-invalid @endif" id="team_by" required
                      placeholder="Contoh : RTmart atau Permata atau Sklera" value="{{ old('team_by') }}">
                    @if($errors->has('team_by'))
                    <span class="error invalid-feedback">{{ $errors->first('team_by') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="phone_number">Nomor Telepon</label>
                    <input type="number" name="phone_number" class="form-control 
                    @if($errors->has('phone_number')) is-invalid @endif" id="phone_number"
                      placeholder="Masukkan Nomor Telepon" value="{{ old('phone_number') }}">
                    @if($errors->has('phone_number'))
                    <span class="error invalid-feedback">{{ $errors->first('phone_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="product_group">Product Group</label>
                    <select class="form-control selectpicker border
                      @if($errors->has('product_group')) is-invalid @endif" id="product_group" name="product_group[]"
                      multiple title="Pilih Product Group">
                      @foreach ($productGroup as $value)
                      <option value="{{ $value->ProductGroupID }}" {{ collect(old('product_group'))->
                        contains($value->ProductGroupID) ? 'selected' : '' }}>
                        {{ $value->ProductGroupName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('product_group'))
                    <span class="error invalid-feedback">{{ $errors->first('product_group') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="work_status">Work Status</label>
                    <select class="form-control selectpicker border
                      @if($errors->has('work_status')) is-invalid @endif" id="work_status" name="work_status"
                      title="Pilih Work Status">
                      @foreach ($workStatus as $value)
                      <option value="{{ $value->SalesWorkStatusID }}" {{ collect(old('work_status'))->
                        contains($value->SalesWorkStatusID) ? 'selected' : '' }}>
                        {{ $value->SalesWorkStatusName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('work_status'))
                    <span class="error invalid-feedback">{{ $errors->first('work_status') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group float-right">
                <button type="submit" class="btn btn-success">Simpan</button>
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