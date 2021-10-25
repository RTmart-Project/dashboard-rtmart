@extends('layouts.master')
@section('title', 'Dashboard - PPOB Topup')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('header-menu', 'PPOB Topup')

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
        <!-- Saldo Information -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">MobilePulsa Balance</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($balanceMobilePulsa)}}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Merchant Saldo</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($sumMerchantSaldo)}}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sisa Margin Deposit</span>
                        <span class="info-box-number">
                            {{Helper::formatCurrency($marginDeposit)}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topup -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="tab-topup">
                            <li class="nav-item"><a class="nav-link active" href="#semua-data" data-toggle="tab">Semua
                                    Data
                                    @if ($countSemuaData>0)
                                    <span class="badge badge-pill badge-danger">
                                        {{$countSemuaData}}
                                    </span>
                                    @endif</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#menunggu-pembayaran"
                                    data-toggle="tab">Menunggu Pembayaran
                                    @if ($countMenungguPembayaran>0)
                                    <span class="badge badge-pill badge-danger">
                                        {{$countMenungguPembayaran}}
                                    </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link @if(session('status')) active @endif"
                                    href="#menunggu-validasi" data-toggle="tab">Menunggu
                                    Validasi
                                    @if ($countMenungguValidasi>0)
                                    <span class="badge badge-pill badge-danger">
                                        {{$countMenungguValidasi}}
                                    </span>
                                    @endif</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#selesai" data-toggle="tab">Selesai
                                    @if ($countSelesai>0)
                                    <span class="badge badge-pill badge-danger">
                                        {{$countSelesai}}
                                    </span>
                                    @endif</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#gagal-validasi" data-toggle="tab">Dibatalkan
                                    @if ($countGagalValidasi>0)
                                    <span class="badge badge-pill badge-danger">
                                        {{$countGagalValidasi}}
                                    </span>
                                    @endif</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body mt-2">
                        <div class="tab-content">
                            <!-- All -->
                            <div class="tab-pane active" id="semua-data">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama Toko</th>
                                                    <th>No. Telp</th>
                                                    <th>Merchant ID</th>
                                                    <th>Topup ID</th>
                                                    <th>Jumlah Topup</th>
                                                    <th>Kode</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Menunggu Pembayaran -->
                            <div class="tab-pane" id="menunggu-pembayaran">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama Toko</th>
                                                    <th>Merchant ID</th>
                                                    <th>No. Telp</th>
                                                    <th>Topup ID</th>
                                                    <th>Jumlah Topup</th>
                                                    <th>Kode</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Menunggu Validasi -->
                            <div class="tab-pane" id="menunggu-validasi">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama Toko</th>
                                                    <th>Merchant ID</th>
                                                    <th>No. Telp</th>
                                                    <th>Topup ID</th>
                                                    <th>Jumlah Topup</th>
                                                    <th>Kode</th>
                                                    <th>Bukti Transfer</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Selesai -->
                            <div class="tab-pane" id="selesai">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama Toko</th>
                                                    <th>Merchant ID</th>
                                                    <th>No. Telp</th>
                                                    <th>Topup ID</th>
                                                    <th>Jumlah Topup</th>
                                                    <th>Kode</th>
                                                    <th>Bukti Transfer</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Gagal Validasi -->
                            <div class="tab-pane" id="gagal-validasi">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama Toko</th>
                                                    <th>Merchant ID</th>
                                                    <th>No. Telp</th>
                                                    <th>Topup ID</th>
                                                    <th>Jumlah Topup</th>
                                                    <th>Kode</th>
                                                    <th>Bukti Transfer</th>
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

<!-- Modal -->
<div class="modal fade" id="konfirmasi-topup-modal" tabindex="-1" aria-labelledby="konfirmasi-topup-modallabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 style="font-weight: bold;" class="modal-title" id="konfirmasi-topup-modallabel">Topup Saldo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Jumlah Topup:</label>
                        <input autocomplete="off" type="text" class="form-control" id="jumlah-topup" name="jumlahTopup">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success submit-topup-saldo"><i class="fas fa-donate"></i>
                        Topup</button>
                </div>
            </form>
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
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/ppob/topup/semua-data.js"></script>
<script src="{{url('/')}}/main/js/ppob/topup/menunggu-pembayaran.js"></script>
<script src="{{url('/')}}/main/js/ppob/topup/menunggu-validasi.js"></script>
<script src="{{url('/')}}/main/js/ppob/topup/selesai.js"></script>
<script src="{{url('/')}}/main/js/ppob/topup/gagal-validasi.js"></script>
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