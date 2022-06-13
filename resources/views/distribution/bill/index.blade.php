@extends('layouts.master')
@section('title', 'Dashboard - Delivery Order')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Delivery Order')

@section('content')
<!-- Main content -->
<div class="content">
  <div class="container-fluid">

    <!-- Table -->
    <div class="row">
      <div class="col-12">
        <div class="card mt-3">
          <div class="card-body mt-2">
            <div class="tab-content">
              <!-- All -->
              <div class="tab-pane active" id="bill-paylater">
                <div class="row">
                  <div class="col-12">
                    <table class="table table-datatables">
                      <thead>
                        <tr>
                          <th>Delivery Order ID</th>
                          <th>Urutan DO</th>
                          <th>Stock Order ID</th>
                          <th>Distributor</th>
                          <th>Nama Toko</th>
                          <th>No. Telepon</th>
                          <th>Tgl DO</th>
                          <th>Tgl Selesai</th>
                          <th>Tgl Jatuh Tempo</th>
                          <th>Tenggat Waktu</th>
                          <th>Tagihan</th>
                          <th>Status DO</th>
                          <th>Status Pelunasan</th>
                          <th>Tgl Pelunasan</th>
                          <th>Nominal Bayar</th>
                          <th>Bukti Bayar</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              {{-- Modal Pelunasan --}}
              <form method="POST" enctype="multipart/form-data" id="form-pelunasan">
                @csrf
                <div class="modal fade" id="modal-payment">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Pelunasan PayLater RTmart</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <p id="info"></p>
                        <div class="row">
                          <div class="col-12 col-md-4">
                            <div class="form-group">
                              <label for="payment_date">Tanggal Pelunasan</label>
                              <input type="date" name="payment_date" class="form-control" id="payment_date" required>
                            </div>
                          </div>
                          <div class="col-12 col-md-4">
                            <div class="form-group">
                              <label for="nominal">Nominal Bayar</label>
                              <input type="text" name="nominal" class="form-control autonumeric" id="nominal" placeholder="Masukkan Nominal Bayar" required>
                            </div>
                          </div>
                          <div class="col-12 col-md-4">
                            <div class="form-group">
                              <label for="payment_slip">Bukti Bayar</label>
                              <input type="file" name="payment_slip" class="form-control" id="payment_slip" onchange="loadFile(event)" required>
                            </div>
                          </div>
                          <div class="col-12 text-md-center d-none" id="img_output">
                            <img id="output" height="160" />
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-warning btn-pelunasan">Simpan</button>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Modal Konfirmasi --}}
                <div class="modal fade" id="modalKonfirmasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Konfirmasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <h5>Apakah data pelunasan yang dimasukkan sudah benar?</h5>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#modal-payment">Kembali</button>
                        <button type="submit" class="btn btn-success">Ya</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<script src="{{url('/')}}/main/js/custom/select-filter.js"></script>
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
<script src="{{url('/')}}/main/js/distribution/bill/billPayLater.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/main/js/helper/input-image-view.js"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
</script>
@endsection