@extends('layouts.master')
@section('title', 'Dashboard - Summary Merchant')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- -->
<meta name="csrf_token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Summary Merchant')

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
          <div class="card-header" id="summary-merchant">
            <div class="row">
              <div class="col-12">
                <label id="filter-date">Default Filter by Tanggal PO</label>
              </div>
              <div class="col-md-2 col-6 p-1">
                <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-2 col-6 p-1">
                <input type="text" name="to_date" id="to_date" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-3 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="distributor" id="distributor"
                  title="Filter Depo" multiple data-live-search="true">
                  @foreach ($distributors as $distributor)
                    <option value="{{ $distributor->DistributorID }}">{{ $distributor->DistributorName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="sales" id="sales"
                  title="Filter Sales" multiple data-live-search="true">
                  @foreach ($sales as $item)
                    <option value="{{ $item->SalesCode }}">{{ $item->SalesCode }} - {{ $item->SalesName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6 col-md-2 p-1">
                <select class="form-control form-control-sm selectpicker border" name="margin" id="margin"
                  title="Filter Margin">
                  <option value="high">High (> 8%)</option>
                  <option value="standart">Standart (5% - 8%)</option>
                  <option value="below">Below (< 5%)</option>
                </select>
              </div>
              <div class="col-md-12 col-6 p-1 d-flex justify-content-md-end justify-content-center">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Filter
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" id="filter-tanggal-po">Tanggal PO</a>
                    <a class="dropdown-item" id="filter-tanggal-do">Tanggal DO</a>
                </div>
                <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-1">Refresh</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">

              <div class="tab-pane active" id="summary-merchant-table">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Distributor</th>
                          <th>Sales</th>
                          <th>Total PO (kecuali Batal)</th>
                          <th>Total DO (Selesai)</th>
                          <th>Discount DO</th>
                          <th>Margin (before disc)</th>
                          <th>% Margin (before disc)</th>
                          <th>Margin (after disc)</th>
                          <th>% Margin (after disc)</th>
                          <th>Margin Status</th>
                          <th>Margin Status (after disc)</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
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
<!-- InputMask -->
<script src="{{url('/')}}/plugins/moment/moment.min.js"></script>
<!-- date-range-picker -->
<script src="{{url('/')}}/plugins/daterangepicker/daterangepicker.js"></script>
<!-- DataTables  & Plugins -->
<script src="{{url('/')}}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="{{url('/')}}/plugins/jszip/jszip.min.js"></script>
<script src="{{url('/')}}/plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{url('/')}}/plugins/pdfmake/vfs_fonts.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Main JS -->
<script src="{{url('/')}}/main/js/summary/merchant/merchant.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
</script>
@endsection