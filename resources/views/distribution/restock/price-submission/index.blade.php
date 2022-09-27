@extends('layouts.master')
@section('title', 'Dashboard - Pengajuan Harga')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Data Pengajuan Harga')

@section('content')
<!-- Main content -->
<div class="content">
  <div class="container-fluid">

    <!-- Table -->
    <div class="row">
      <div class="col-12">
        <div class="card mt-3">
          <div class="card-header">
            <ul class="nav nav-pills" id="tab-topup">
              <li class="nav-item">
                <a class="nav-link active" href="#menunggu-konfirmasi" data-toggle="tab">
                  Menunggu Konfirmasi
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#disetujui" data-toggle="tab">
                  Disetujui
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#ditolak" data-toggle="tab">
                  Ditolak
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body pt-2">
            <div class="tab-content">
              <!-- All -->
              <div class="tab-pane active" id="menunggu-konfirmasi">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Stock Order ID</th>
                          <th>Tgl Order</th>
                          <th>Distributor</th>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Sales</th>
                          <th>Total Transaksi (before disc)</th>
                          <th>Est Margin (before disc)</th>
                          <th>% Est Margin (before disc)</th>
                          <th>Total Transaksi Pengajuan</th>
                          <th>Est Margin Pengajuan</th>
                          <th>% Est Margin Pengajuan</th>
                          <th>Bunga</th>
                          <th>Cost Logistic</th>
                          <th>Final Est Margin Pengajuan</th>
                          <th>Diajukan Oleh</th>
                          <th>Detail</th>
                          <th>Konfirmasi</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="disetujui">
                <div class="row">
                  <div class="col-12">
                    <label>Summary Pengajuan {{ date('d F Y') }}</label>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="info-box">
                      <div class="info-box-content">
                        <span class="info-box-text h6 mb-1" style="white-space: unset">Total Value Pengajuan</span>
                        <span class="info-box-number h-6 m-0">{{ Helper::formatCurrency($summarySubmission->TotalSubmission) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="info-box">
                      <div class="info-box-content">
                        <span class="info-box-text h6 mb-1" style="white-space: unset">Total Est Margin Pengajuan</span>
                        <span class="info-box-number h-6 m-0">{{ Helper::formatCurrency($summarySubmission->TotalEstMarginSubmission) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="info-box">
                      <div class="info-box-content">
                        <span class="info-box-text h6 mb-1" style="white-space: unset">% Est Margin Pengajuan</span>
                        <span class="info-box-number h-6 m-0">{{ $summarySubmission->PercentEstMarginSubmission }}%</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="info-box">
                      <div class="info-box-content">
                        <span class="info-box-text h6 mb-1" style="white-space: unset">Total Bunga</span>
                        <span class="info-box-number h-6 m-0">{{ Helper::formatCurrency($summarySubmission->TotalBunga) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="info-box">
                      <div class="info-box-content">
                        <span class="info-box-text h6 mb-1" style="white-space: unset">Total Cost Logistic</span>
                        <span class="info-box-number h-6 m-0">{{ Helper::formatCurrency($summarySubmission->TotalCostLogistic) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="info-box">
                      <div class="info-box-content">
                        <span class="info-box-text h6 mb-1" style="white-space: unset">Total Final Est Margin Pengajuan</span>
                        <span class="info-box-number h-6 m-0">{{ Helper::formatCurrency($summarySubmission->FinalEstMarginSubmission) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Stock Order ID</th>
                          <th>Tgl Order</th>
                          <th>Tgl Approve Pengajuan</th>
                          <th>Distributor</th>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Sales</th>
                          <th>Total Transaksi (before disc)</th>
                          <th>Est Margin (before disc)</th>
                          <th>% Est Margin (before disc)</th>
                          <th>Total Transaksi Pengajuan</th>
                          <th>Est Margin Pengajuan</th>
                          <th>% Est Margin Pengajuan</th>
                          <th>Bunga</th>
                          <th>Cost Logistic</th>
                          <th>Final Est Margin Pengajuan</th>
                          <th>Diajukan Oleh</th>
                          <th>Catatan</th>
                          <th>Detail</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="ditolak">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Stock Order ID</th>
                          <th>Tgl Order</th>
                          <th>Distributor</th>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Sales</th>
                          <th>Total Transaksi (before disc)</th>
                          <th>Est Margin (before disc)</th>
                          <th>% Est Margin (before disc)</th>
                          <th>Total Transaksi Pengajuan</th>
                          <th>Est Margin Pengajuan</th>
                          <th>% Est Margin Pengajuan</th>
                          <th>Bunga</th>
                          <th>Cost Logistic</th>
                          <th>Final Est Margin Pengajuan</th>
                          <th>Diajukan Oleh</th>
                          <th>Catatan</th>
                          <th>Detail</th>
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
<script src="{{url('/')}}/main/js/custom/select-filter.js"></script>
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/main/js/price-submission/menunggu-konfirmasi.js"></script>
<script src="{{url('/')}}/main/js/price-submission/disetujui.js"></script>
<script src="{{url('/')}}/main/js/price-submission/ditolak.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/helper/keep-tab-refresh.js"></script>
<script>
  // Recall Responsive DataTables
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $('.table-datatables:visible').each(function(e) {
      $(this).DataTable().columns.adjust().responsive.recalc();
    });
  });
</script>
@endsection