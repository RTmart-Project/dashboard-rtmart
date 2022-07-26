@extends('layouts.master')
@section('title', 'Dashboard - Customer Order with Courier')

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

@section('header-menu', 'Transaksi Customer - Kurir')

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
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="tab-merchant-restock">
                            <li class="nav-item">
                                <a class="nav-link active" href="#order-baru" data-toggle="tab">Order Baru</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ambil-orderan" data-toggle="tab">Ambil Orderan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#menuju-konsumen" data-toggle="tab">Menuju Konsumen</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#selesai" data-toggle="tab">Selesai</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body mt-2">
                        <div class="tab-content">

                            <div class="tab-pane active" id="order-baru">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Kurir Kode</th>
                                                    <th>Nama Kurir</th>
                                                    <th>Customer ID</th>
                                                    <th>Nama Customer</th>
                                                    <th>Alamat Customer</th>
                                                    <th>No. HP Customer</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Alamat Toko</th>
                                                    <th>No. HP Toko</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Status Order</th>
                                                    <th>Total Price</th>
                                                    {{-- <th>Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="ambil-orderan">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                  <th>Order ID</th>
                                                  <th>Tgl Transaksi</th>
                                                  <th>Kurir Kode</th>
                                                  <th>Nama Kurir</th>
                                                  <th>Customer ID</th>
                                                  <th>Nama Customer</th>
                                                  <th>Alamat Customer</th>
                                                  <th>No. HP Customer</th>
                                                  <th>Merchant ID</th>
                                                  <th>Nama Toko</th>
                                                  <th>Alamat Toko</th>
                                                  <th>No. HP Toko</th>
                                                  <th>Metode Pembayaran</th>
                                                  <th>Status Order</th>
                                                  <th>Total Price</th>
                                                  {{-- <th>Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="menuju-konsumen">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                  <th>Order ID</th>
                                                  <th>Tgl Transaksi</th>
                                                  <th>Kurir Kode</th>
                                                  <th>Nama Kurir</th>
                                                  <th>Customer ID</th>
                                                  <th>Nama Customer</th>
                                                  <th>Alamat Customer</th>
                                                  <th>No. HP Customer</th>
                                                  <th>Merchant ID</th>
                                                  <th>Nama Toko</th>
                                                  <th>Alamat Toko</th>
                                                  <th>No. HP Toko</th>
                                                  <th>Metode Pembayaran</th>
                                                  <th>Status Order</th>
                                                  <th>Total Price</th>
                                                  {{-- <th>Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="selesai">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                  <th>Order ID</th>
                                                  <th>Tgl Transaksi</th>
                                                  <th>Kurir Kode</th>
                                                  <th>Nama Kurir</th>
                                                  <th>Customer ID</th>
                                                  <th>Nama Customer</th>
                                                  <th>Alamat Customer</th>
                                                  <th>No. HP Customer</th>
                                                  <th>Merchant ID</th>
                                                  <th>Nama Toko</th>
                                                  <th>Alamat Toko</th>
                                                  <th>No. HP Toko</th>
                                                  <th>Metode Pembayaran</th>
                                                  <th>Status Order</th>
                                                  <th>Total Price</th>
                                                  {{-- <th>Action</th> --}}
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
<script src="{{url('/')}}/main/js/rtcourier/order/order-baru.js"></script>
<script src="{{url('/')}}/main/js/rtcourier/order/ambil-orderan.js"></script>
<script src="{{url('/')}}/main/js/rtcourier/order/menuju-konsumen.js"></script>
<script src="{{url('/')}}/main/js/rtcourier/order/selesai.js"></script>
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