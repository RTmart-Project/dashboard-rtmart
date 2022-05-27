@extends('layouts.master')
@section('title', 'Dashboard - Merchant Restock')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Restock Merchant')

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
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Merchant Restock</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($countTotalRestock, '')}}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Merchant Restock Bulan Ini</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($countRestockThisMonth, '+', ' Merchant Restock')}}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Merchant Restock Hari Ini</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($countRestockThisDay, '+', ' Merchant Restock')}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-outline collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Merchant Restock per Distributor</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant Restock (RTmart Bandung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalRestockBandung, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Bulan Ini (RTmart Bandung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockBandungThisMonth, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Hari Ini (RTmart Bandung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockBandungThisDay, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant Restock (RTmart Cakung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalRestockCakung, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Bulan Ini (RTmart Cakung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockCakungThisMonth, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Hari Ini (RTmart Cakung)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockCakungThisDay, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant Restock (RTmart Ciracas)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalRestockCiracas, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Bulan Ini (RTmart Ciracas)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockCiracasThisMonth, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Hari Ini (RTmart Ciracas)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockCiracasThisDay, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Merchant Restock (RTmart Semarang)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countTotalRestockSemarang, '')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Bulan Ini (RTmart Semarang)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockSemarangThisMonth, '+', ' Merchant
                                            Restock')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i
                                            class="fas fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Merchant Restock Hari Ini (RTmart Semarang)</span>
                                        <span class="info-box-number">
                                            {{Helper::formatCurrency($countRestockSemarangThisDay, '+', ' Merchant
                                            Restock')}}
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
                        <ul class="nav nav-pills" id="tab-merchant-restock">
                            <li class="nav-item">
                                <a class="nav-link active" href="#merchant-restock" data-toggle="tab">Restock
                                    Merchant</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#product-restock" data-toggle="tab">Restock Merchant All
                                    Product</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body mt-2">
                        <div class="tab-content">
                            <div class="tab-pane active" id="merchant-restock">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Partner</th>
                                                    <th>No. Telp</th>
                                                    <th>Nama Distributor</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Status Order</th>
                                                    <th>Total Harga</th>
                                                    <th>Diskon</th>
                                                    <th>Voucher</th>
                                                    <th>Biaya Layanan</th>
                                                    <th>Biaya Pengiriman</th>
                                                    <th>Total Harga Bersih</th>
                                                    <th>Margin Estimasi (Rp)</th>
                                                    <th>Margin Estimasi (%)</th>
                                                    <th>Margin Real (Rp)</th>
                                                    <th>Margin Real (%)</th>
                                                    <th>Total Margin (Rp)</th>
                                                    <th>Total Margin (%)</th>
                                                    <th>Referral</th>
                                                    <th>Nama Sales</th>
                                                    <th>Invoice</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="product-restock">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Partner</th>
                                                    <th>No. Telp</th>
                                                    <th>Nama Distributor</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Status Order</th>
                                                    <th>Total Harga</th>
                                                    <th>Diskon</th>
                                                    <th>Voucher</th>
                                                    <th>Biaya Layanan</th>
                                                    <th>Biaya Pengiriman</th>
                                                    <th>Total Harga Bersih</th>
                                                    <th>Referral</th>
                                                    <th>Nama Sales</th>
                                                    <th>Product ID</th>
                                                    <th>Deskripsi</th>
                                                    <th>Qty</th>
                                                    <th>Qty dikirim</th>
                                                    <th>Qty DO Selesai</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Diskon</th>
                                                    <th>Harga stlh Diskon</th>
                                                    <th>Total Harga Produk</th>
                                                    <th>Harga Beli Estimasi</th>
                                                    <th>Margin Estimasi (Rp)</th>
                                                    <th>Margin Estimasi (%)</th>
                                                    <th>Harga Beli Real</th>
                                                    <th>Margin Real (Rp)</th>
                                                    <th>Margin Real (%)</th>
                                                    <th>Total Margin (Rp)</th>
                                                    <th>Total Margin (%)</th>
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
<script src="{{url('/')}}/main/js/merchant/restock/restock.js"></script>
<script src="{{url('/')}}/main/js/merchant/restock/product.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/helper/keep-tab-refresh.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
    // Recall Responsive DataTables
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $('.table-datatables:visible').each(function(e) {
        $(this).DataTable().columns.adjust().responsive.recalc();
    });
});
</script>
@endsection