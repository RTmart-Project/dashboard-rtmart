@extends('layouts.master')
@section('title', 'Dashboard - Detail Stock')

@section('css-pages')
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('header-menu', 'Detail Stock')

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
            <a href="{{ route('stock.listStock') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i> Kembali</a>
          </div>
          <div class="card-body mt-2">
            <div class="row">
              <div class="col-12 col-md-3 mb-3">
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Distributor</strong>
                <p>{{ $distributor->DistributorName }}</p>
              </div>
              <div class="col-12 col-md-3 mb-3">
                <strong><i class="fas fa-money-bill-wave-alt mr-1"></i> Investor</strong>
                <p>{{ $investor }}</p>
              </div>
              <div class="col-12 col-md-3 mb-3">
                <strong><i class="far fa-images mr-1"></i> Gambar Produk</strong><br>
                <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="Store Image" height="130">
              </div>
              <div class="col-12 col-md-3 mb-3">
                <strong><i class="fas fa-dice-d6 mr-1"></i> Nama Produk</strong>
                <p>{{ $product->ProductName }} ({{ $product->ProductLabel }})</p>
              </div>
              <div class="table-responsive" id="detail-stock">
                <table class="table table-datatables table-bordered text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Qty Sebelum</th>
                      <th>Qty Action</th>
                      <th>Qty Sesudah</th>
                      <th>Harga Beli</th>
                      <th>Tanggal</th>
                      <th>Kondisi Barang</th>
                      <th>Tipe</th>
                      <th>Action By</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
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
<script src="{{url('/')}}/main/js/stock/list/detail-stock.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
@endsection