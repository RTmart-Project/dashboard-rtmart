@extends('layouts.master')
@section('title', 'Dashboard - Summary Report')

@section('css-pages')
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.css">
<meta name="csrf_token" content="{{ csrf_token() }}">
@endsection

@section('header-menu', 'Summary Report')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
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
    <!-- Information -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header" id="summary-report">
            <div class="row">
              <div class="col-md-2 col-6 p-1">
                <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-2 col-6 p-1">
                <input type="text" name="to_date" id="to_date" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-3 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="distributor" id="distributor"
                  title="Pilih Depo" multiple data-live-search="true">
                  @foreach ($distributors as $distributor)
                    <option value="{{ $distributor->DistributorID }}">{{ $distributor->DistributorName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="sales" id="sales"
                  title="Pilih Sales" multiple data-live-search="true">
                  @foreach ($sales as $item)
                    <option value="{{ $item->SalesCode }}">{{ $item->SalesCode }} - {{ $item->SalesName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 p-1">
                <button type="submit" id="filter" class="btn btn-sm btn-primary">Filter</button>
                <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-1">Refresh</button>
              </div>
            </div>
          </div>
        </div>

        <h5 class="mb-2">PO Summary</h5>
        <div class="row">
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Total PO (Value)</span>
                <span class="info-box-number h6 m-0">Rp 1.000.000</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Jumlah PO</span>
                <span class="info-box-number h6 m-0">12</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-store"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Jumlah Toko</span>
                <span class="info-box-number h6 m-0">10</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Total Margin Estimasi</span>
                <span class="info-box-number h6 m-0">Rp 300.000</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-coins"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Total Margin</span>
                <span class="info-box-number h6 m-0">Rp 350.000</span>
              </div>
            </div>
          </div>
        </div>

        <h5 class="my-2">DO Summary</h5>
        <div class="row">
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-bill-wave-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Total DO (Value)</span>
                <span class="info-box-number h6 m-0">Rp 1.000.000</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Jumlah DO</span>
                <span class="info-box-number h6 m-0">12</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Jumlah Toko</span>
                <span class="info-box-number h6 m-0">10</span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-coins"></i></span>
              <div class="info-box-content">
                <span class="info-box-text h6 mb-2">Total Margin</span>
                <span class="info-box-number h6 m-0">Rp 350.000</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<script src="{{url('/')}}/plugins/moment/moment.min.js"></script>
<script src="{{url('/')}}/main/js/summary/report/report.js"></script>
<script src="{{url('/')}}/plugins/daterangepicker/daterangepicker.js"></script>
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
@endsection