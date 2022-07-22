@extends('layouts.master')
@section('title', 'Dashboard - ' . $merchant->StoreName. ' Product Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Detail Product ' . $merchant->StoreName)

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
                        <a href="{{ route('merchant.account') }}" class="btn btn-sm btn-light mb-2"><i
                                class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <div class="col-12 d-flex align-items-stretch flex-column">
                            <div class="card d-flex flex-fill">
                                <div class="card-body pt-3 pb-3">
                                    <div class="row">
                                        <div class="col-12 col-md-2 text-center">
                                            <img src="{{ config('app.base_image_url') . '/merchant/'. $merchant->StoreImage }}"
                                                alt="Store Image" class="rounded img-fluid pb-2 pb-md-0"
                                                style="object-fit: cover; width: 130px; height: 130px;">
                                        </div>
                                        <div class="col-12 col-md-10 align-self-center">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <h6><strong>Merchant ID : </strong>{{ $merchantId }}</h6>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <h6><strong>Nama Toko : </strong>{{ $merchant->StoreName }}</h6>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <h6><strong>Nama Pemilik : </strong>{{ $merchant->OwnerFullName }}
                                                    </h6>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <h6><strong>No. Telp : </strong><a
                                                            href="tel:{{ $merchant->PhoneNumber }}">{{
                                                            $merchant->PhoneNumber }}</a></h6>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <h6><strong>Latitude : </strong>{{ $merchant->Latitude }}</h6>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <h6><strong>Longitude : </strong>{{ $merchant->Longitude }}</h6>
                                                </div>
                                                <div class="col-12">
                                                    <h6><strong>Alamat : </strong>{{ $merchant->StoreAddress }}</h6>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-12 mt-1">
                            <div class="card card-primary card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">Operational Hour</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                                class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card-body p-0">
                                                <div class="row">
                                                    @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" ||
                                                    Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "AH" ||
                                                    Auth::user()->RoleID == "HR")
                                                    <div class="ml-auto pr-2">
                                                        <a href="{{ route('merchant.editOperationalHour', ['merchantId' => $merchantId]) }}"
                                                            class="btn btn-warning btn-sm">Edit</a>
                                                    </div>
                                                    @endif
                                                </div>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Hari</th>
                                                            <th>Jam Buka</th>
                                                            <th>Jam Tutup</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operationalHour as $value)
                                                        <tr>
                                                            <td>{{ $value->DayOfWeek }}</td>
                                                            <td>{{ date('H:i', strtotime($value->OpeningHour)) }}</td>
                                                            <td>{{ date('H:i', strtotime($value->ClosingHour)) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-12 mt-1">
                            <div class="card card-warning card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">Log Perubahan Sales</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Sales Sebelumnya</th>
                                                <th>Sales Setelahnya</th>
                                                <th>Tanggal Perubahan</th>
                                                <th>Diubah Oleh</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($logSales->count() > 0)
                                                @foreach ($logSales as $value)
                                                <tr>
                                                    <td>{{ $value->SalesCodeBefore }} - {{ $value->SalesNameBefore }}</td>
                                                    <td>{{ $value->SalesCodeAfter }} - {{ $value->SalesNameAfter }}</td>
                                                    <td>{{ date('d M Y, H:i:s', strtotime($value->CreatedDate)) }}</td>
                                                    <td>{{ $value->ActionBy }}</td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center py-4">Belum ada log perubahan sales</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="merchant-product-details">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Product ID</th>
                                                    <th>Nama Produk</th>
                                                    <th>Gambar</th>
                                                    <th>Stok</th>
                                                    <th>Kategori</th>
                                                    <th>Tipe</th>
                                                    <th>Jenis</th>
                                                    <th>Isi</th>
                                                    <th>Harga Jual</th>
                                                    <th>Harga Beli</th>
                                                    @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" ||
                                                    Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "AH" ||
                                                    Auth::user()->RoleID == "HR")
                                                    <th>Action</th>
                                                    @endif
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
<script src="{{url('/')}}/main/js/merchant/product/product.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
</script>
@endsection