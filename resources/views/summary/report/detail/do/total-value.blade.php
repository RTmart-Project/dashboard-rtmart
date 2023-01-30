@extends('layouts.master')
@section('title', 'Dashboard - Summary Report Detail - Total Value DO')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Summary Report Detail - Total Value DO')

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
    <!-- Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-3">
                <div class="info-box m-0">
                  <span class="info-box-icon bg-info elevation-1"><i class="fas fa-truck"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text h6 mb-2">Total DO (Value)</span>
                    <span class="info-box-number h6 m-0">
                      {{-- {{ Helper::formatCurrency(array_sum(array_column($data, 'ValueProduct')), 'Rp ') }} --}}
                      {{ Helper::formatCurrency($data, 'Rp ') }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-3">
                <h6><strong>Start Date : </strong>{{ date('d F Y', strtotime($dataFilter->startDate)) }} </h6>
                <h6><strong>End Date : </strong>{{ date('d F Y', strtotime($dataFilter->endDate)) }}</h6>
              </div>
              <div class="col-6">
                <h6><strong>Distributor : </strong>{!! $dataFilter->distributor !!}</h6>
                <h6><strong>Sales : </strong>{!! $dataFilter->sales !!}</h6>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="summary-value-do">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>DeliveryOrderDetailID</th>
                          <th>Delivery Order ID</th>
                          <th>Status DO</th>
                          <th>Stock Order ID</th>
                          <th>Tanggal PO</th>
                          <th>Expedition ID</th>
                          <th>Driver</th>
                          <th>Nopol</th>
                          <th>Tgl Kirim DO</th>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Nama Pemilik</th>
                          <th>No. Telp</th>
                          <th>Alamat Toko</th>
                          <th>Tipe</th>
                          <th>Partner</th>
                          <th>Nama Distributor</th>
                          <th>Metode Pembayaran</th>
                          <th>Status PayLater RTmart</th>
                          <th>Produk ID</th>
                          <th>Nama Produk</th>
                          <th>Qty</th>
                          <th>Harga Jual</th>
                          <th>Harga Beli</th>
                          <th>Stock Investor</th>
                          <th>Stock Label</th>
                          <th>Value Produk</th>
                          <th>Value Beli</th>
                          <th>Value Margin (before disc)</th>
                          <th>Margin (before disc)</th>
                          <th>Sub Total</th>
                          <th>Diskon / Voucher</th>
                          <th>Biaya Layanan</th>
                          <th>Biaya Pengiriman</th>
                          <th>Grand Total</th>
                          <th>Sales</th>
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
<script src="{{url('/')}}/plugins/inputmask/jquery.inputmask.min.js"></script>
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
<script src="{{url('/')}}/main/js/summary/report/detail/do/total-value.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
@endsection