@extends('layouts.master')
@section('title', 'Dashboard - Merchant Assessment')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="depo" content="{{ Auth::user()->Depo }}">
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Merchant Assessment')

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
          <div class="card-header d-flex flex-wrap">
            @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI")
            <div class="mr-auto">
              <a href="{{ route('merchant.createAssessment') }}" class="btn btn-sm btn-success mb-1"><i class="fas fa-plus"></i> Tambah Assessment</a><br>
            </div>
            @endif
            @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM")
            <div>
              <a class="btn btn-sm btn-info btn-download-ktp">Download</a><br>
            <small>*Anda akan mendownload file FOTO KTP yang datanya ter-ceklis</small>
            </div>
            @endif
          </div>
          <div class="card-body mt-2">
            <div class="tab-content">
              <div class="tab-pane active" id="merchant-assessment">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Valid</th>
                          <th>Tanggal</th>
                          <th>Store ID</th>
                          <th>Nama Store</th>
                          <th>No. HP Store</th>
                          <th>Merchant ID</th>
                          <th>Nama Merchant</th>
                          <th>No. HP Merchant</th>
                          <th>Jml PO (25Mei'22 - skrg)</th>
                          <th>No. KTP</th>
                          <th>Nama Lengkap</th>
                          <th>Tanggal Lahir</th>
                          <th>Keterangan</th>
                          <th>Omset /bulan</th>
                          <th>Transaksi</th>
                          <th>Kode Sales</th>
                          <th>Nama Sales</th>
                          <th>Foto Toko</th>
                          <th>Bukti Bon</th>
                          <th>Foto Stok Toko</th>
                          <th>Foto KTP</th>
                          <th>Action</th>
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
<script src="{{url('/')}}/main/js/merchant/assessment/assessment.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
</script>
@endsection