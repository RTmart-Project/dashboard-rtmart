@extends('layouts.master')
@section('title', 'Dashboard - Merchant Account')

@section('css-pages')
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
{{-- <link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css"> --}}
@endsection

@section('header-menu', 'Data Akun Merchant')

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
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Merchant</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($countTotalMerchant, '')}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-store"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bulan Ini</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($countNewMerchantThisMonth, '+', ' Merchant')}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-store"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Hari Ini</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($countNewMerchantThisDay, '+', ' Merchant')}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-outline collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Merchant per Distributor</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant (RTmart Bandung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalMerchantBandung, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bulan Ini (RTmart Bandung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantBandungThisMonth, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hari Ini (RTmart Bandung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantBandungThisDay, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant (RTmart Cakung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalMerchantCakung, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bulan Ini (RTmart Cakung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantCakungThisMonth, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hari Ini (RTmart Cakung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantCakungThisDay, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant (RTmart Ciracas)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalMerchantCiracas, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bulan Ini (RTmart Ciracas)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantCiracasThisMonth, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hari Ini (RTmart Ciracas)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantCiracasThisDay, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant (RTmart Semarang)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalMerchantSemarang, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bulan Ini (RTmart Semarang)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantSemarangThisMonth, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-store"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hari Ini (RTmart Semarang)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countNewMerchantSemarangThisDay, '+', ' Merchant')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
        </div>

        <!-- Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="tab-topup">
                            <li class="nav-item">
                                <a class="nav-link active" href="#merchant-account" data-toggle="tab">
                                    Akun Merchant
                                </a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body mt-2">
                        <div class="tab-content">
                            <!-- All -->
                            <div class="tab-pane active" id="merchant-account">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Partner</th>
                                                    <th>Nama Pemilik</th>
                                                    <th>No. Telp</th>
                                                    <th>Grade</th>
                                                    <th>Tgl Registrasi</th>
                                                    <th style="width: 35%">Alamat</th>
                                                    <th>Referral</th>
                                                    <th>Nama Sales</th>
                                                    <th>Distributor</th>
                                                    <th>Status Block</th>
                                                    <th>Catatan Status Block</th>
                                                    <th>Action</th>
                                                    <th>Detail</th>
                                                    <th>Assessment</th>
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
{{-- <script src="{{url('/')}}/main/js/custom/select-filter.js"></script> --}}
<script src="{{url('/')}}/main/js/merchant/account/account.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
// Recall Responsive DataTables
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $('.table-datatables:visible').each(function(e) {
        $(this).DataTable().columns.adjust().responsive.recalc();
    });
});
</script>
@endsection