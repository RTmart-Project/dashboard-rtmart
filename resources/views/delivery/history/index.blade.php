@extends('layouts.master')
@section('title', 'Dashboard - Ekspedisi')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Data Ekspedisi')

@section('content')
<!-- Main content -->
<div class="content">
  <div class="container-fluid">

    <!-- Table -->
    <div class="row">
      <div class="col-12">
        <div class="card mt-3">
          <div class="card-header">
            <ul class="nav nav-pills" id="tab-topup">
              <li class="nav-item">
                <a class="nav-link active" href="#expedition" data-toggle="tab">
                  Delivery History
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#expedition-all-product" data-toggle="tab">
                  Delivery History All Product
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body pt-3">
            <div class="tab-content">
              <!-- All -->
              <div class="tab-pane active" id="expedition">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Ekspedisi ID</th>
                          <th>Distributor</th>
                          <th>Tanggal Kirim</th>
                          <th>Jumlah DO</th>
                          <th>Driver</th>
                          <th>Helper</th>
                          <th>Kendaraan</th>
                          <th>Nopol Kendaraan</th>
                          <th>Status</th>
                          <th>Detail</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="expedition-all-product">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Ekspedisi ID</th>
                          <th>Distributor</th>
                          <th>Tanggal Kirim</th>
                          <th>Jumlah DO</th>
                          <th>Driver</th>
                          <th>Helper</th>
                          <th>Kendaraan</th>
                          <th>Nopol Kendaraan</th>
                          <th>Status Ekspedisi</th>
                          <th>Stock Order ID</th>
                          <th>Delivery Order ID</th>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>No HP Toko</th>
                          <th>Produk ID</th>
                          <th>Nama Produk</th>
                          <th>Qty</th>
                          <th>Harga</th>
                          <th>Total Harga Produk</th>
                          <th>Status Produk</th>
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
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/main/js/delivery/history/history.js"></script>
<script src="{{url('/')}}/main/js/delivery/history/all-product.js"></script>
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