@extends('layouts.master')
@section('title', 'Dashboard - Delivery Request')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="depo" content="{{ Auth::user()->Depo }}">

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

@section('header-menu', 'Delivery Request')

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
            <button type="button" id="create-expedition" class="btn btn-success btn-sm" disabled="disabled">
              <i class="fas fa-truck"></i>
              Kirim Barang
            </button>
          </div>
          <div class="card-body mt-2">
            <div class="tab-content">
              <!-- All -->
              <div class="tab-pane active" id="delivery-request">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th></th>
                          <th></th>
                          <th>Delivery Order ID</th>
                          <th>Area</th>
                          <th>Tanggal Request</th>
                          <th>Produk</th>
                          <th>Distributor</th>
                          <th>Merchant ID</th>
                          <th>Nama Toko</th>
                          <th>Sales</th>
                          <th>No. Telp</th>
                          <th>Grade</th>
                          <th>Partner</th>
                          <th>Alamat</th>
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

        {{-- Modal --}}
        <div class="modal fade" id="modal-expedition" tabindex="-1" role="dialog" aria-labelledby="modalExpeditionLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title" id="modalExpeditionLabel"><i class="fas fa-info-circle"></i> Informasi</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <h6 id="text"></h6><br>
                <div class="form-group row">

                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal"
                  data-target="#modalKonfirmasi">Update Status</button>
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
<script src="{{url('/')}}/main/js/delivery/request/request.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
  $('table').on('change', ':checkbox', function () {
      if ($(':checkbox:checked').length > 0) {
          $("#create-expedition").prop('disabled', false);
      } else {
          $("#create-expedition").prop('disabled', true);
      } 
  });

  $("#create-expedition").click(function() {
      let deliveryOrder = [];

      $.each($("input[name='confirm[]']:checked"), function() {
        deliveryOrder.push($(this).val());
      });
      $('#modal-expedition').modal('show').on('shown.bs.modal', function() {
        $("#text").html("Anda akan mengirim sebanyak " + deliveryOrder.length + " pesanan");
      });
    });
</script>
@endsection