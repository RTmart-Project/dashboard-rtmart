@extends('layouts.master')
@section('title', 'Dashboard - Mutation Stock')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">

<meta name="base-image" content="{{ config('app.base_image_url') }}">
@endsection

@section('header-menu', 'Stok Mutasi')

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
          @if ((Auth::user()->RoleID == "IT") || (Auth::user()->RoleID == "FI"))
          <div class="card-header">
            <a href="{{ route('stock.createMutation') }}" class="btn btn-sm btn-success">
              <i class="fas fa-plus"></i> Tambah Mutasi
            </a>
          </div>
          @endif
          <div class="card-body pt-2">
            <ul class="nav nav-pills pb-2" id="tab-topup">
              <li class="nav-item">
                <a class="nav-link active" href="#mutation-stock" data-toggle="tab">
                  Mutasi
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#mutation-stock-all-product" data-toggle="tab">
                  Mutasi All Product
                </a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="mutation-stock">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Mutasi ID</th>
                          <th>Tanggal Mutasi</th>
                          <th>Sumber Purchase</th>
                          <th>Dari Distributor</th>
                          <th>Ke Distributor</th>
                          <th>Action By</th>
                          <th>Catatan</th>
                          <th>Detail</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="mutation-stock-all-product">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Mutasi ID</th>
                          <th>Tanggal Mutasi</th>
                          <th>Sumber Purchase</th>
                          <th>Dari Distributor</th>
                          <th>Ke Distributor</th>
                          <th>Action By</th>
                          <th>Catatan</th>
                          <th>Produk ID</th>
                          <th>Produk</th>
                          <th>Label</th>
                          <th>Qty</th>
                          <th>Harga Beli</th>
                          <th>Total Harga Produk</th>
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
<script src="{{url('/')}}/main/js/stock/mutation/mutation.js"></script>
<script src="{{url('/')}}/main/js/stock/mutation/mutation-all-product.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/helper/keep-tab-refresh.js"></script>
<script>
   // Recall Responsive DataTables
   $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $('.table-datatables:visible').each(function(e) {
      $(this).DataTable().columns.adjust().responsive.recalc();
    });
  });
</script>
@endsection