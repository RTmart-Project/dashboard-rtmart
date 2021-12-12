@extends('layouts.master')
@section('title', 'Dashboard - Voucher Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Detail Voucher')

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
                        <a href="{{ route('voucher.list') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <div class="post">
                            <h6><img src="{{ config('app.base_image_url') . '/voucher/icon/'. $voucher->VoucherImage }}" alt="Icon" width="30">
                                <strong>&nbsp;{{ $voucher->VoucherCode }}</strong>
                                <small> (untuk @if ($voucher->IsFor == "All")
                                    Customer dan Merchant
                                @else
                                    {{ $voucher->IsFor }}
                                @endif)</small></h6>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Nama Voucher :</strong> {{ $voucher->VoucherName }}</p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Jenis Voucher : </strong>{{ $voucher->VoucherTypeName }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Persentase : </strong>{{ $voucher->PercentageValue }}%</p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Maksimum Nominal : </strong>{{ Helper::formatCurrency($voucher->MaxNominalValue, 'Rp ') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Masa Berlaku : </strong>{{ date('d F Y H:i', strtotime($voucher->StartDate)) . " - " . date('d F Y H:i', strtotime($voucher->EndDate)) }}</p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Aktif : </strong>@if ($voucher->IsActive == 1)
                                        Ya, Voucher aktif
                                    @else
                                        Tidak, Voucher tidak aktif
                                    @endif</p>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Kuota per user : </strong>{{ $voucher->QuotaPerUser }} kali penggunaan</p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Kuota Keseluruhan : </strong>{{ $voucher->MaxQuota }} kali penggunaan</p>
                                </div>
                            </div>
                        </div>
                        <div class="post">
                            <h6>Syarat</h6>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Minimum Nominal Transaksi : </strong>{{ Helper::formatCurrency($voucher->MinimumTrx, 'Rp ')}}</p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Minimum Kuantiti Pembelian : </strong>{{ $voucher->MinimumQty }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Minimum Nominal Transaksi (History) : </strong>{{ Helper::formatCurrency($voucher->MinimumTrxAccumulative, 'Rp ')}}</p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Minimum Kuantiti Pembelian (History) : </strong>{{ $voucher->MinimumQtyAccumulative }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Metode Pembayaran : </strong>
                                        @if ($termPaymentMethod->count() == 0)
                                            Tidak ada
                                        @else
                                            @foreach ($termPaymentMethod as $value)
                                                {{ $value->PaymentMethodName }} @if (! $loop->last) atau @endif
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Lokasi Distributor : </strong>
                                        @if ($termDistributorLocation->count() == 0)
                                            Tidak ada
                                        @else
                                            @foreach ($termDistributorLocation as $value)
                                                {{ $value->DistributorName }} @if (! $loop->last) atau @endif
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Spesifik Brand : </strong>
                                        @if ($termBrand->count() == 0)
                                            Tidak ada
                                        @else
                                            @foreach ($termBrand as $value)
                                                {{ $value->Brand }} @if (! $loop->last) atau @endif
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p><strong>Spesifik Kategori : </strong>
                                        @if ($termCategory->count() == 0)
                                            Tidak ada
                                        @else
                                            @foreach ($termCategory as $value)
                                                {{ $value->ProductCategoryName }} @if (! $loop->last) atau @endif
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <p class="mb-1"><strong>Pengguna baru : </strong>
                                @if (($voucher->StartDateNewUser == null) && ($voucher->EndDateNewUser == null))
                                    Tidak
                                @else
                                    Ya, dengan pendaftar baru dari {{ date('d F Y H:i', strtotime($voucher->StartDateNewUser)) . " sampai " . date('d F Y H:i', strtotime($voucher->EndDateNewUser)) }}
                                @endif
                            </p>
                            <p class="mb-1"><strong>Merchant Restock : </strong>
                                @if (($voucher->MerchantRestockStartDate == null) && ($voucher->MerchantRestockEndDate == null))
                                    Tidak
                                @else
                                    Ya, dengan rentang waktu {{ date('d F Y H:i', strtotime($voucher->MerchantRestockStartDate)) . " sampai " . date('d F Y H:i', strtotime($voucher->MerchantRestockEndDate)) }}
                                @endif
                            </p>
                            <p class="mb-1"><strong>Cek History Transaksi Customer : </strong>
                                @if (($voucher->StartDateCustomerTrx == null) && ($voucher->EndDateCustomerTrx == null))
                                    Tidak
                                @else
                                    Ya, dengan rentang waktu {{ date('d F Y H:i', strtotime($voucher->StartDateCustomerTrx)) . " sampai " . date('d F Y H:i', strtotime($voucher->EndDateCustomerTrx)) }}
                                @endif
                            </p>
                        </div>
                        <div class="post">
                            <h6>Syarat Produk</h6>
                            @if ($termProduct->count() == 0)
                                <strong>Tidak ada</strong>
                            @else
                                <div class="table-responsive p-0">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Produk ID</th>
                                            <th>Nama Produk</th>
                                            <th>Minimum Transaksi</th>
                                            <th>Minimum Kuantiti</th>
                                            <th>Minimum Transaksi (History)</th>
                                            <th>Minimum Kuantiti (History)</th>
                                        </tr>
                                        @foreach ($termProduct as $value)
                                            <tr>
                                                <td>{{ $value->ProductID }}</td>
                                                <td>{{ $value->ProductName }}</td>
                                                <td>{{ Helper::formatCurrency($value->MinimumTrx, 'Rp ') }}</td>
                                                <td>{{ $value->MinimumQty }}</td>
                                                <td>{{ Helper::formatCurrency($value->MinimumTrxAccumulative, 'Rp ') }}</td>
                                                <td>{{ $value->MinimumQtyAccumulative }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="post">
                            <h6>Syarat & Ketentuan</h6>
                            {!! $voucher->Details !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-pages')
<script>
</script>
@endsection