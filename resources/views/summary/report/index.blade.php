@extends('layouts.master')
@section('title', 'Dashboard - Summary Report')

@section('css-pages')
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.css">
<meta name="csrf_token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/overlay-summary.css">
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
              @if (count($distributors) > 1)
              <div class="col-md-2 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="distributor" id="distributor"
                  title="Pilih Depo" multiple data-live-search="true">
                  @foreach ($distributors as $distributor)
                  <option value="{{ $distributor->DistributorID }}">{{ $distributor->DistributorName }}</option>
                  @endforeach
                </select>
              </div>
              @endif
              <div class="col-md-2 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="sales" id="sales"
                  title="Pilih Sales" multiple data-live-search="true">
                  @foreach ($sales as $item)
                  <option value="{{ $item->SalesCode }}">{{ $item->SalesCode }} - {{ $item->SalesName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="type-po" id="type-po"
                  title="Pilih Tipe PO" multiple>
                  <option value="#" disabled>-- Filter Tipe PO --</option>
                  @foreach ($typePO as $item)
                  <option value="{{ $item->Type }}">{{ $item->Type }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="partner" id="partner"
                  title="Filter Partner" multiple>
                  @foreach ($partners as $partner)
                  <option value="{{ $partner->PartnerID }}">{{ $partner->Name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 d-flex justify-content-end p-1">
                <button type="submit" id="filter" class="btn btn-sm btn-primary">Filter</button>
                <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-1">Refresh</button>
              </div>
            </div>
          </div>
        </div>

        <div class="position-absolute overlay">
          <h3>loading <i class="fas fa-spinner fa-spin"></i></h3>
        </div>
        <h5 class="mb-2">PO Summary</h5>
        <div class="row">
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="total-value-po-all-status-link" target="_blank">Total
                    PO (Semua Status)</a></span>
                <span class="info-box-number h6 m-0" id="total-value-po-all-status"></span>
                <span class="m-0" id="count-merchant-po-all-status"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="total-value-po-link" target="_blank">Total PO (Belum
                    di Kirim)</a></span>
                <span class="info-box-number h6 m-0" id="total-value-po"></span>
                <span class="m-0" id="count-merchant-po"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="total-value-po-cancelled-link" target="_blank">Total
                    PO (Dibatalkan)</a></span>
                <span class="info-box-number h6 m-0" id="total-value-po-cancelled"></span>
                <span class="m-0" id="count-merchant-po-cancelled"></span>
              </div>
            </div>
          </div>
          {{-- <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="count-total-po-link" target="_blank">Jumlah
                    PO</a></span>
                <span class="info-box-number h6 m-0" id="count-total-po"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-store"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="count-merchant-po-link" target="_blank">Jumlah
                    Toko</a></span>
                <span class="info-box-number h6 m-0" id="count-merchant-po"></span>
              </div>
            </div>
          </div> --}}
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Value Margin Estimasi (before disc)</span>
                <span class="info-box-number h6 m-0" id="value-margin-estimasi"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Total Voucher / Discount PO</span>
                <span class="info-box-number h6 m-0" id="voucher-po"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Total Margin Estimasi</span>
                <span class="info-box-number h6 m-0" id="margin-estimasi"></span>
              </div>
            </div>
          </div>
        </div>

        <h5 class="my-2">DO Summary</h5>
        <div class="row">
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Total PO of DO (Value)</span>
                <span class="info-box-number h6 m-0" id="total-value-po-by-do"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-truck"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="total-value-do-link" target="_blank">Total DO
                    (Selesai)</a></span>
                <span class="info-box-number h6 m-0" id="total-value-do"></span>
                <span class="m-0" id="count-merchant-do"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cart-arrow-down"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Outstanding DO (Value)</span>
                <span class="info-box-number h6 m-0" id="outstanding-do"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cart-arrow-down"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Total DO (Dibatalkan)</span>
                <span class="info-box-number h6 m-0" id="total-value-do-cancelled"></span>
                <span class="m-0" id="count-merchant-do-cancelled"></span>
              </div>
            </div>
          </div>
          {{-- <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-truck"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="count-total-do-link" target="_blank">Jumlah
                    DO</a></span>
                <span class="info-box-number h6 m-0" id="count-total-do"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2"><a id="count-merchant-do-link" target="_blank">Jumlah
                    Toko</a></span>
                <span class="info-box-number h6 m-0" id="count-merchant-do"></span>
              </div>
            </div>
          </div> --}}
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Value Margin Real (before disc)</span>
                <span class="info-box-number h6 m-0" id="value-margin"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Total Voucher / Discount DO</span>
                <span class="info-box-number h6 m-0" id="voucher-do"></span>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-search-dollar"></i></span>
              <div class="info-box-content">
                <span class="info-box-text no-wrap h6 mb-2">Total Margin Real</span>
                <span class="info-box-number h6 m-0" id="margin-real"></span>
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
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
@endsection