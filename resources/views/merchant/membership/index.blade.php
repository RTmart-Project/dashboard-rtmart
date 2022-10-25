@extends('layouts.master')
@section('title', 'Dashboard - Merchant Membership')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- -->
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="base-image" content="{{ config('app.base_image_url') }}">
<link rel="stylesheet" href="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Merchant Membership')

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
          <div class="card-header" id="merchant-membership">
            <div class="row">
              <div class="col-md-2 col-6 p-1">
                <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-2 col-6 p-1">
                <input type="text" name="to_date" id="to_date" class="form-control form-control-sm" readonly>
              </div>
              <div class="col-md-3 col-6 p-1">
                <select class="form-control form-control-sm selectpicker border" name="status" id="status"
                  title="Filter Status Membership" multiple>
                  @foreach ($statusMembership as $status)
                    <option value="{{ $status->StatusCouplePreneurID }}">{{ $status->StatusName }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 col-6 p-1">
                <button type="button" name="filter" id="filter" class="btn btn-sm btn-primary">Filter</button>
                <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-1">Refresh</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="tab-content">

              <div class="tab-pane active" id="merchant-membership-table">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Nama Pemilik</th>
                          <th>No. HP</th>
                          <th>No. KTP</th>
                          <th>Nama KTP</th>
                          <th>No. KTP Pasangan</th>
                          <th>Nama KTP Pasangan</th>
                          <th>Total Transaksi</th>
                          <th>Alamat</th>
                          <th>Kelurahan</th>
                          <th>Kecamatan</th>
                          <th>Kabupaten / Kota</th>
                          <th>Provinsi</th>
                          <th>Kode Pos</th>
                          <th>Distributor</th>
                          <th>Sales</th>
                          <th>Status Crowdo</th>
                          <th>Status Membership</th>
                          <th>Tanggal Submit</th>
                          <th>Tanggal Konfirmasi</th>
                          <th>Dikonfirmasi Oleh</th>
                          <th>Catatan</th>
                          <th>Photo</th>
                          <th>Action</th>
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

<div class="modal fade" id="modal-photo">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Membership Photo - <span id="merchant"></span></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="photo" class="d-flex justify-content-center flex-wrap"></div>
        <div id="confirm" class="d-flex justify-content-center"></div>
      </div>
      <div class="modal-footer justify-content-end">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<!-- InputMask -->
<script src="{{url('/')}}/plugins/moment/moment.min.js"></script>
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
<script src="{{url('/')}}/main/js/merchant/membership/membership.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
</script>
@endsection