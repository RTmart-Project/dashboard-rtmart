@extends('layouts.master')
@section('title', 'Dashboard - Stock Order')

@section('css-pages')
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
<meta name="csrf_token" content="{{ csrf_token() }}">
@endsection

@section('header-menu', 'Stock Order')

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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="tab-restock">
                            {{-- <li class="nav-item">
                                <a class="nav-link active" href="#restock" data-toggle="tab">
                                    Restock
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a class="nav-link active" href="#restock-all-product" data-toggle="tab">
                                    Restock All Product
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#semua-restock" data-toggle="tab">Semua Data</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pesanan-baru" data-toggle="tab">Pesanan Baru</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#telah-dikonfirmasi" data-toggle="tab">Telah Dikonfirmasi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#dalam-proses" data-toggle="tab">Dalam Proses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#telah-dikirim" data-toggle="tab">Telah Dikirim</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#telah-selesai" data-toggle="tab">Telah Selesai</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#telah-dibatalkan" data-toggle="tab">Telah Dibatalkan</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body mt-2">
                        <div class="tab-content">
                            <!-- Restock -->
                            {{-- <div class="tab-pane active" id="restock">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Status Restock</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Semua Data -->
                            <div class="tab-pane active" id="restock-all-product">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>No. Telp</th>
                                                    <th>Partner</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Status Order</th>
                                                    <th>Alamat</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Diskon</th>
                                                    <th>Voucher</th>
                                                    <th>Biaya Layanan</th>
                                                    <th>Biaya Pengiriman</th>
                                                    <th>GrandTotal Transaksi</th>
                                                    <th>Referral</th>
                                                    <th>Nama Sales</th>
                                                    <th>Product ID</th>
                                                    <th>Deskripsi</th>
                                                    <th>Qty</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Diskon</th>
                                                    <th>Harga stlh Diskon</th>
                                                    <th>Total Harga Produk</th>
                                                    {{-- @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") --}}
                                                    <th>Harga Beli Estimasi</th>
                                                    <th>Margin Estimasi (Rp)</th>
                                                    <th>Margin Estimasi (%)</th>
                                                    <th>Harga Beli Real</th>
                                                    <th>Margin Real (Rp)</th>
                                                    <th>Margin Real (%)</th>
                                                    <th>Total Margin (Rp)</th>
                                                    <th>Total Margin (%)</th>
                                                    {{-- @endif --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Semua Data -->
                            <div class="tab-pane" id="semua-restock">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Total Transaksi PO</th>
                                                    <th>No. Telp</th>
                                                    <th>Partner</th>
                                                    <th>Status Order PO</th>
                                                    <th>Delivery Order ID</th>
                                                    <th>Tanggal Kirim DO</th>
                                                    <th>Urutan DO</th>
                                                    <th>Produk</th>
                                                    <th>Qty</th>
                                                    <th>Harga Jual per Produk</th>
                                                    <th>Harga Total per Produk & Qty</th>
                                                    <th class="d-none">Harga Beli</th>
                                                    <th class="d-none">Margin Real (Rp)</th>
                                                    <th class="d-none">Margin Real (%)</th>
                                                    <th>Status DO</th>
                                                    <th>Biaya Pengiriman DO</th>
                                                    <th>Biaya Layanan DO</th>
                                                    <th>Disc. Voucher DO</th>
                                                    <th>Driver</th>
                                                    <th>Kendaraan</th>
                                                    <th>Nopol</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pesanan Baru -->
                            <div class="tab-pane" id="pesanan-baru">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Telah dikonfirmasi -->
                            <div class="tab-pane" id="telah-dikonfirmasi">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                    <th>Invoice</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Dalam Proses -->
                            <div class="tab-pane" id="dalam-proses">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                    <th>Invoice</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Telah dikirim -->
                            <div class="tab-pane" id="telah-dikirim">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Tgl Kirim</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                    <th>Invoice</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Telah Selesai -->
                            <div class="tab-pane" id="telah-selesai">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Transaksi</th>
                                                    <th>Tgl Kirim</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                    <th>Invoice</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Telah Dibatalkan -->
                            <div class="tab-pane" id="telah-dibatalkan">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Stock Order ID</th>
                                                    <th>Tgl Pesanan</th>
                                                    <th>Distributor</th>
                                                    <th>Merchant ID</th>
                                                    <th>Nama Toko</th>
                                                    <th>Grade</th>
                                                    <th>Sales</th>
                                                    <th>Partner</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>No. Telp</th>
                                                    <th>Alamat</th>
                                                    <th>Alasan</th>
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
<script src="{{url('/')}}/plugins/datatables-styles/export-datatable-styles.min.js"></script>
<!-- Main JS -->
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/custom/select-filter.js"></script>
{{-- <script src="{{url('/')}}/main/js/distribution/restock/restock.js"></script> --}}
<script src="{{url('/')}}/main/js/distribution/restock/restock-product.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/semua-restock.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/pesanan-baru.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/telah-dikonfirmasi.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/dalam-proses.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/telah-dikirim.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/telah-selesai.js"></script>
<script src="{{url('/')}}/main/js/distribution/restock/telah-dibatalkan.js"></script>
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