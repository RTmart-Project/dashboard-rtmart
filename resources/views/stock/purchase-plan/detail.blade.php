@extends('layouts.master')
@section('title', 'Dashboard - Detail Purchase Plan '. $data->PurchasePlanID)

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Detail Purchase Plan')

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
          <div class="card-header pb-0">
            <a href="{{ route('stock.purchasePlan') }}" class="btn btn-sm btn-light mb-3"><i class="fas fa-arrow-left"></i>
              Kembali</a>
            <div class="row">
              <div class="col-12 col-md-4 mb-3">
                <strong><i class="fas fa-file-invoice mr-1"></i> Purchase Plan ID</strong>
                <p class=" m-0">{{ $data->PurchasePlanID }}</p>
              </div>
              <div class="col-12 col-md-4 mb-3">
                <strong><i class="fas fa-money-bill-wave-alt mr-1"></i> InvestorName</strong>
                <p>{{ $data->InvestorName }}</p>
              </div>
              <div class="col-12 col-md-4 mb-3">
                <strong><i class="fas fa-calendar-alt mr-1"></i> Tanggal Rencana Pembelian</strong>
                <p>{{ date('d F Y', strtotime($data->PlanDate)) }}</p>
              </div>
              <div class="col-12 col-md-4 mb-3">
                <strong><i class="fas fa-user-edit mr-1"></i> Dibuat Oleh</strong>
                <p class="m-0">{{ $data->CreatedBy }}</p>
                <small>pada : {{ date('d F Y\, H:i', strtotime($data->CreatedDate)) }}</small>
              </div>
              <div class="col-12 col-md-4 mb-3">
                <strong><i class="fas fa-info mr-1"></i> Status</strong><br>
                @if ($data->StatusID == 8)
                <p style="font-size: 13px" class="badge badge-warning">{{ $data->StatusName }}</p>
                @elseif($data->StatusID == 9)
                <p style="font-size: 13px" class="badge badge-success">{{ $data->StatusName }}</p>
                @else
                <p style="font-size: 13px" class="badge badge-danger">{{ $data->StatusName }}</p>
                @endif
                @if ($data->StatusID == 8 && (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "CEO" || Auth::user()->RoleID == "FI"))
                <br>
                <a class="btn btn-sm btn-danger btn-reject" data-purchase-plan-id="{{ $data->PurchasePlanID }}">Tolak</a>
                <a class="btn btn-sm btn-success btn-approve" data-purchase-plan-id="{{ $data->PurchasePlanID }}">Setujui</a>
                @endif
              </div>
              <div class="col-12 col-md-4 mb-3">
                <strong><i class="fas fa-user-check mr-1"></i> Dikonfirmasi oleh</strong><br>
                @if ($data->ConfirmBy)
                <p class="m-0">{{ $data->ConfirmBy }}</p>
                <small>pada : {{ date('d F Y\, H:i', strtotime($data->ConfirmDate)) }}</small>
                @else
                -
                @endif
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="purchase-plan-detail">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Tanggal</th>
                          <th>Distributor</th>
                          <th>Supplier</th>
                          <th>Keterangan</th>
                          <th>Produk ID</th>
                          <th>Produk</th>
                          <th>Produk Label</th>
                          <th>Qty</th>
                          <th>Qty PO</th>
                          <th>% PO</th>
                          <th>Harga Beli</th>
                          <th>Value Beli</th>
                          <th>% Bunga</th>
                          <th>Bunga</th>
                          <th>Harga Jual</th>
                          <th>Value Jual</th>
                          <th>% Voucher</th>
                          <th>Value Voucher</th>
                          <th>Gross Margin</th>
                          <th>Margin /ctn</th>
                          <th>Nett Margin</th>
                          <th>% Margin</th>
                          <th>Stock</th>
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
<script src="{{url('/')}}/main/js/stock/purchase-plan/purchase-plan-detail.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
  // Event listener saat tombol setujui diklik
  $('.btn-approve').on('click', function (e) {
    e.preventDefault();
    const purchasePlanID = $(this).data("purchase-plan-id");
    $.confirm({
      title: 'Setujui Purchase Plan!',
      content: `Apakah yakin ingin menyetujui rencana pembelian dengan Purchase Plan ID <b>${purchasePlanID}</b>?`,
      closeIcon: true,
      type: 'green',
      buttons: {
        setujui: {
          btnClass: 'btn-success',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
            window.location = '/stock/plan-purchase/confirm/' + purchasePlanID + '/approve'
          }
        },
        tidak: function () {
        }
      }
    });
  });

  // Event listener saat tombol tolak diklik
  $('.btn-reject').on('click', function (e) {
    e.preventDefault();
    const purchasePlanID = $(this).data("purchase-plan-id");
    $.confirm({
      title: 'Tolak Purchase Plan!',
      content: `Apakah yakin ingin menolak rencana pembelian dengan Purchase Plan ID <b>${purchasePlanID}</b>?`,
      closeIcon: true,
      type: 'red',
      buttons: {
        tolak: {
          btnClass: 'btn-red',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
            window.location = '/stock/plan-purchase/confirm/' + purchasePlanID + '/reject'
          }
        },
        tidak: function () {
        }
      }
    });
  });
</script>
@endsection