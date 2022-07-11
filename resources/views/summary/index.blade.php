@extends('layouts.master')
@section('title', 'Dashboard - Summary')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<meta name="csrf_token" content="{{ csrf_token() }}">
@endsection

@section('header-menu', 'Summary')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row">
      <!-- left -->
      <div class="col-sm-6">
      </div>
      <!-- Right -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item">

          </li>
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
            {{-- <form action="{{ route('summary.dataSummary') }}" method="POST"> --}}
              <div class="row filter">
                {{-- @csrf --}}
                <div class="col-12 col-md-3">
                  <input type="date" class="form-control form-control-sm start-date">
                </div>
                <div class="col-2 col-md-1 d-none d-md-flex align-items-center justify-content-center">
                  <span>sampai</span>
                </div>
                <div class="col-12 col-md-3 py-2 py-md-0">
                  <input type="date" class="form-control form-control-sm end-date">
                </div>
                <div class="col-3 col-md-1 text-md-center pt-md-0 w-100">
                  <button class="btn btn-primary btn-block btn-sm btn-filter">Filter</button>
                </div>
                <div class="col-3 col-md-1 text-md-center pt-md-0">
                  <button class="btn btn-warning btn-block btn-sm btn-refresh">Refresh</button>
                </div>
              </div>
            {{-- </form> --}}
          </div>
          <div class="card-body mt-2">
            <div class="tab-content">

              <div class="tab-pane active" id="summary">
                <div class="row">
                  <div class="col-12">
                    <div class="card-body summary-table table-responsive p-0">
                      <table class="table table-hover table-bordered text-nowrap table-sm">
                        <thead class="bg-lightblue">
                          <tr class="text-center" id="tanggal">
                            <th colspan="2">Area</th>
                            <td class="w-50 text-center loader-tanggal"><i class="fas fa-spinner fa-spin"></i></td>
                          </tr>
                        </thead>
                        <tbody>
                          {{-- CAKUNG --}}
                          <tr style="background-color: rgb(216,216,216);">
                            <th class="text-center align-middle" rowspan="8">Cakung</th>
                          </tr>
                          <tr id="purchase-order-cakung" style="background-color: rgb(216,216,216);">
                            <th>Purchase Order</th>
                          </tr>
                          <tr id="purchasing-cakung" style="background-color: rgb(216,216,216);">
                            <th>Purchasing</th>
                          </tr>
                          <tr id="voucher-cakung" style="background-color: rgb(216,216,216);">
                            <th>Voucher</th>
                          </tr>
                          <tr id="delivery-order-cakung" style="background-color: rgb(216,216,216);">
                            <th>Delivery Order</th>
                          </tr>
                          <tr id="bill-real-cakung" style="background-color: rgb(216,216,216);">
                            <th>Bill Real</th>
                          </tr>
                          <tr id="bill-target-cakung" style="background-color: rgb(216,216,216);">
                            <th>Bill Target</th>
                          </tr>
                          <tr id="ending-inventory-cakung" style="background-color: rgb(216,216,216);">
                            <th>Ending Inventory</th>
                          </tr>

                          {{-- BANDUNG --}}
                          <tr style="background-color: rgb(252,213,180);">
                            <th class="text-center align-middle" rowspan="8">Bandung</th>
                          </tr>
                          <tr id="purchase-order-bandung" style="background-color: rgb(252,213,180);">
                            <th>Purchase Order</th>
                          </tr>
                          <tr id="purchasing-bandung" style="background-color: rgb(252,213,180);">
                            <th>Purchasing</th>
                          </tr>
                          <tr id="voucher-bandung" style="background-color: rgb(252,213,180);">
                            <th>Voucher</th>
                          </tr>
                          <tr id="delivery-order-bandung" style="background-color: rgb(252,213,180);">
                            <th>Delivery Order</th>
                          </tr>
                          <tr id="bill-real-bandung" style="background-color: rgb(252,213,180);">
                            <th>Bill Real</th>
                          </tr>
                          <tr id="bill-target-bandung" style="background-color: rgb(252,213,180);">
                            <th>Bill Target</th>
                          </tr>
                          <tr id="ending-inventory-bandung" style="background-color: rgb(252,213,180);">
                            <th>Ending Inventory</th>
                          </tr>

                          {{-- CIRACAS --}}
                          <tr style="background-color: rgb(219,238,243);">
                            <th class="text-center align-middle" rowspan="8">Ciracas</th>
                          </tr>
                          <tr id="purchase-order-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Purchase Order</th>
                          </tr>
                          <tr id="purchasing-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Purchasing</th>
                          </tr>
                          <tr id="voucher-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Voucher</th>
                          </tr>
                          <tr id="delivery-order-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Delivery Order</th>
                          </tr>
                          <tr id="bill-real-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Bill Real</th>
                          </tr>
                          <tr id="bill-target-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Bill Target</th>
                          </tr>
                          <tr id="ending-inventory-ciracas" style="background-color: rgb(219,238,243);">
                            <th>Ending Inventory</th>
                          </tr>

                          {{-- GRAND TOTAL --}}
                          <tr style="background-color: rgb(255,255,0);">
                            <th class="text-center align-middle" rowspan="8">Grand Total</th>
                          </tr>
                          <tr id="purchase-order-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Purchase Order</th>
                          </tr>
                          <tr id="purchasing-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Purchasing</th>
                          </tr>
                          <tr id="voucher-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Voucher</th>
                          </tr>
                          <tr id="delivery-order-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Delivery Order</th>
                          </tr>
                          <tr id="bill-real-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Bill Real</th>
                          </tr>
                          <tr id="bill-target-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Bill Target</th>
                          </tr>
                          <tr id="ending-inventory-grand-total" style="background-color: rgb(255,255,0);">
                            <th>Ending Inventory</th>
                          </tr>
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
</div>
@endsection

@section('js-pages')
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{url('/')}}/main/js/summary/summary.js"></script>
<script>
  let Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
  });

  let csrf = $('meta[name="csrf_token"]').attr("content");

  getSummary();

  $(".btn-filter").on("click", function () {
    $(".summary-table .data").remove();
    const startDate = $(this).closest(".filter").find(".start-date").val();
    const endDate = $(this).closest(".filter").find(".end-date").val();
    if (startDate == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi Start Date!",
      });
    } else if (endDate == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi End Date!",
      });
    } else if (startDate > endDate) {
      Toast.fire({
        icon: "error",
        title: " Start Date harus lebih kecil dari End Date!",
      });
    } else {
      getSummary(startDate, endDate);
    }
  })

  $(".btn-refresh").on("click", function () {
    $(".summary-table .data").remove();
    $(".start-date").val("");
    $(".end-date").val("");
    getSummary();
  })
</script>
<script src="{{url('/')}}/plugins/freeze-table/freeze-table.js"></script>
<script>
  // $(".summary-table").freezeTable();
</script>
@endsection