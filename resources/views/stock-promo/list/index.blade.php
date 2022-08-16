@extends('layouts.master')
@section('title', 'Dashboard - Stock Promo')

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

@section('header-menu', 'Stock Promo')

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
            <a href="{{ route('stockPromo.create') }}" class="btn btn-sm btn-success">
              <i class="fas fa-plus"></i> Tambah Inbound dari Purchase
            </a>
          </div>
          @endif
          {{-- <div class="card-header">
            
          </div> --}}
          <div class="card-body pt-2">
            <ul class="nav nav-pills pb-2">
              <li class="nav-item">
                <a class="nav-link active" href="#inbound-stock-promo" data-toggle="tab">
                  Inbound
                </a>
              </li>
              {{-- <li class="nav-item">
                <a class="nav-link" href="#purchase-stock-all-product" data-toggle="tab">
                  Inbound All Product
                </a>
              </li> --}}
            </ul>
            <div class="tab-content">

              <div class="tab-pane active" id="inbound-stock-promo">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Tipe</th>
                          <th>Purchase ID</th>
                          <th>Distributor</th>
                          <th>Investor</th>
                          <th>Supplier</th>
                          <th>Tanggal Pembelian</th>
                          <th>Grand Total</th>
                          <th>Dibuat Oleh</th>
                          <th>Status</th>
                          <th>Dikonfirmasi oleh</th>
                          <th>Invoice Number</th>
                          <th>Invoice File</th>
                          <th>Action</th>
                          @if ((Auth::user()->RoleID == "IT") || (Auth::user()->RoleID == "FI"))
                          <th>Konfirmasi</th>
                          @endif
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              {{-- <div class="tab-pane" id="purchase-stock-all-product">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Tipe</th>
                          <th>Purchase ID</th>
                          <th>Distributor</th>
                          <th>Investor</th>
                          <th>Supplier</th>
                          <th>Tanggal Pembelian</th>
                          <th>Produk</th>
                          <th>Label</th>
                          <th>Qty</th>
                          <th>Harga Beli</th>
                          <th>Total Harga Produk</th>
                          <th>Grand Total</th>
                          <th>Dibuat Oleh</th>
                          <th>Status</th>
                          <th>Dikonfirmasi oleh</th>
                          <th>Invoice Number</th>
                          <th>Invoice File</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div> --}}

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
<script src="{{url('/')}}/main/js/stock/purchase/purchase.js"></script>
<script src="{{url('/')}}/main/js/stock/purchase/purchase-all-product.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/helper/keep-tab-refresh.js"></script>
<script>
  // Event listener saat tombol setujui diklik
  $('table').on('click', '.btn-approved', function (e) {
    e.preventDefault();
    const purchaseID = $(this).data("purchase-id");
    $.confirm({
      title: 'Setujui Purchase Stock!',
      content: `Apakah yakin ingin menyetujui pembelian dengan Purchase ID <b>${purchaseID}</b>?`,
      closeIcon: true,
      type: 'green',
      buttons: {
        setujui: {
          btnClass: 'btn-success',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
              window.location = '/stock/purchase/confirmation/approved/' + purchaseID
          }
        },
        tidak: function () {
        }
      }
    });
  });

  // Event listener saat tombol tolak diklik
  $('table').on('click', '.btn-reject', function (e) {
    e.preventDefault();
    const purchaseID = $(this).data("purchase-id");
    $.confirm({
      title: 'Tolak Purchase Stock!',
      content: `Apakah yakin ingin menolak pembelian dengan Purchase ID <b>${purchaseID}</b>?`,
      closeIcon: true,
      type: 'red',
      buttons: {
        tolak: {
          btnClass: 'btn-red',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
              window.location = '/stock/purchase/confirmation/reject/' + purchaseID
          }
        },
        tidak: function () {
        }
      }
    });
  });

  // Recall Responsive DataTables
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $('.table-datatables:visible').each(function(e) {
      $(this).DataTable().columns.adjust().responsive.recalc();
    });
  });
</script>
@endsection