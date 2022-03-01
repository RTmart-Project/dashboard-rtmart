@extends('layouts.master')
@section('title', 'Dashboard - Product')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="depo" content="{{ Auth::user()->Depo }}">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Produk Distributor')

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
                    @if (Auth::user()->RoleID == "IT" || (Auth::user()->RoleID == "FI") || (Auth::user()->RoleID ==
                    "AH") || (Auth::user()->RoleID == "BM"))
                    <div class="card-header">
                        <a href="{{ route('distribution.addProduct') }}" class="btn btn-sm btn-success"><i
                                class="fas fa-plus"></i> Tambah Produk</a>
                    </div>
                    @endif
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="product-grading">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
                                            <thead>
                                                <tr>
                                                    <th>Distributor</th>
                                                    <th>Product ID</th>
                                                    <th>Nama Produk</th>
                                                    <th>Gambar</th>
                                                    <th>Kategori</th>
                                                    <th>Tipe</th>
                                                    <th>Jenis</th>
                                                    <th>Isi</th>
                                                    <th>Harga</th>
                                                    <th>Grade</th>
                                                    <th>PreOrder</th>
                                                    <th class="{{ Auth::user()->RoleID == " AD" ? 'd-none' : '' }}">
                                                        Action</th>
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
        <script src="https://unpkg.com/autonumeric"></script>
        <script src="{{url('/')}}/main/js/custom/select-filter.js"></script>
        <script src="{{url('/')}}/main/js/distribution/product-grading/product-grading.js"></script>
        <script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
        <script>
        </script>
        @endsection