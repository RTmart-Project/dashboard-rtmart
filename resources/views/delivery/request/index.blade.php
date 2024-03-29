@extends('layouts.master')
@section('title', 'Dashboard - Delivery Request')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="depo" content="{{ Auth::user()->Depo }}">
<meta name="role-id" content="{{ Auth::user()->RoleID }}">
<link rel="stylesheet" href="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/plugins/bs-stepper/css/bs-stepper.min.css">
<style>
  input.larger {
    width: 17px;
    height: 17px;
  }
</style>
@endsection

@section('header-menu', 'Delivery Request')

@section('content')
<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <!-- Table -->
    <div class="row">
      <div class="col-12">
        <div class="card card-default mt-3">
          <div class="card-body p-0">
            <div class="bs-stepper">
              <div class="bs-stepper-header d-flex flex-wrap justify-content-center" role="tablist">
                <!-- your steps here -->
                <div class="step" data-target="#do-part">
                  <button type="button" class="step-trigger p-2" role="tab" aria-controls="do-part"
                    id="do-part-trigger">
                    @if (Auth::user()->RoleID != "HL")
                    <span class="bs-stepper-circle">1</span>
                    <span class="bs-stepper-label">Pilih Area & Kiriman</span>
                    @endif
                  </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#product-part">
                  <button type="button" class="step-trigger p-2" role="tab" aria-controls="product-part"
                    id="product-part-trigger">
                    @if (Auth::user()->RoleID != "HL")
                    <span class="bs-stepper-circle">2</span>
                    <span class="bs-stepper-label">Pilih Detail Kiriman</span>
                    @endif
                  </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#preview-part">
                  <button type="button" class="step-trigger p-2" role="tab" aria-controls="preview-part"
                    id="preview-part-trigger">
                    @if (Auth::user()->RoleID != "HL")
                    <span class="bs-stepper-circle">3</span>
                    <span class="bs-stepper-label">Preview Kiriman</span>
                    @endif
                  </button>
                </div>
              </div>
              <div class="bs-stepper-content">
                <!-- your steps content here -->
                <div id="do-part" class="content" role="tabpanel" aria-labelledby="do-part-trigger">
                  @if (Auth::user()->RoleID != "HL")
                  <div class="callout callout-info p-2 mb-2">
                    <p class="font-weight-bold mb-1">Delivery Order yang dipilih : </p>
                    <span id="do-selected"></span>
                  </div>
                  <div class="card-footer d-flex justify-content-end">
                    <button class="btn btn-sm btn-primary" id="first-next-step">Selanjutnya</button>
                  </div>
                  @endif
                  <div class="card-body p-0 pt-2">
                    <div class="row">
                      <div class="col-12 col-sm-12 col-md-12 col-xl-12">
                        <div class="tab-content">
                          <!-- All -->
                          <div class="tab-pane active" id="delivery-request">
                            <div class="row">
                              <div class="col-12">
                                <table class="table table-datatables" id="id">
                                  <thead>
                                    <tr>
                                      <th></th>
                                      <th></th>
                                      <th>Delivery Order ID</th>
                                      <th>Tanggal Plan</th>
                                      <th>Urutan</th>
                                      <th>Stock Order ID</th>
                                      <th>Nama Toko</th>
                                      <th>No. Telp</th>
                                      {{-- <th>Area</th> --}}
                                      <th>Distributor</th>
                                      <th>Sales</th>
                                      <th>Produk</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                          <div id="custom-message" style="display: none;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="product-part" class="content" role="tabpanel" aria-labelledby="product-part-trigger">
                  <div class="card-footer d-flex justify-content-end">
                    <button class="btn btn-sm btn-outline-dark mr-2" onclick="stepper.previous()">Kembali</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="second-next-step">Selanjutnya</button>
                  </div>
                  <div>
                    <div id="delivery-order-result">

                    </div>
                  </div>
                </div>
                <div id="preview-part" class="content" role="tabpanel" aria-labelledby="preview-part-trigger">
                  <div class="card-footer d-flex justify-content-end mb-2">
                    <button class="btn btn-sm btn-outline-dark mr-2" onclick="stepper.previous()">Kembali</button>
                    <button type="button" class="btn btn-sm btn-success" id="kirim-barang">
                      Kirim Barang
                    </button>
                  </div>
                  <!-- Modal -->
                  <div class="modal fade" id="modalValidasi" data-backdrop="static" tabindex="-1" role="dialog"
                    aria-labelledby="modalValidasiLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h6 class="modal-title" id="modalValidasiLabel"><i class="fas fa-check-square"></i> Validasi
                          </h6>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="phone_number_check">
                            <label for="phone_number_check" class="custom-control-label h6">No HP Toko Aktif</label>
                          </div>
                          <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="address_check">
                            <label for="address_check" class="custom-control-label h6">Alamat Toko Sudah Sesuai</label>
                          </div>
                          <div class="callout callout-warning mt-3 mb-0 p-2">
                            <p>Catatan : Admin bertanggung jawab atas validasi data tersebut</p>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-dismiss="modal">Kembali</button>
                          <button type="button" class="btn btn-sm btn-success" id="btn-validasi">Selanjutnya</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal fade" id="modalKirimBarang" data-backdrop="static" tabindex="-1" role="dialog"
                    aria-labelledby="modalKirimBarangLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h6 class="modal-title" id="modalKirimBarangLabel"><i class="fas fa-info"></i> Konfirmasi</h6>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <h5>Apakah barang yang akan dikirim sudah benar?</h5>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-sm btn-outline-secondary" data-target="#modalValidasi"
                            data-toggle="modal" data-dismiss="modal">Kembali</button>
                          <button type="button" class="btn btn-sm btn-success" data-target="#modalKirimBarang2"
                            data-toggle="modal" data-dismiss="modal">Benar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal fade" id="modalKirimBarang2" aria-hidden="true" data-backdrop="static"
                    tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Ekspedisi</h6>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <h5>Apakah yakin ingin membuat ekspedisi?</h5>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-dismiss="modal">Batal</button>
                          <button type="button" class="btn btn-sm btn-success" id="create-expedition-btn"
                            data-toggle="modal" data-dismiss="modal">Yakin</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div id="create-expedition">
                    <div class="row m-0">
                      <div class="col-md-6 col-12">
                        <div class="form-group">
                          <label class="my-0" for="created_date_do">Waktu Pengiriman</label>
                          <input type="datetime-local" class="form-control" name="created_date_do" id="created_date_do"
                            value="{{ date('Y-m-d\TH:i') }}" required>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="form-group">
                          <label class="my-0" for="vehicle">Jenis Kendaraan</label>
                          <select name="vehicle" id="vehicle" class="form-control border selectpicker"
                            data-live-search="true" title="Pilih Jenis Kendaraan" required>
                            @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->VehicleID }}">{{ $vehicle->VehicleName }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4 col-12">
                        <div class="form-group">
                          <label class="my-0" for="driver">Driver</label>
                          <select name="driver" id="driver" class="form-control border selectpicker"
                            data-live-search="true" title="Pilih Driver" required>
                            @foreach ($drivers as $driver)
                            <option value="{{ $driver->UserID }}">{{ $driver->Name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4 col-12">
                        <div class="form-group">
                          <label class="my-0" for="helper">Helper</label>
                          <select name="helper" id="helper" class="form-control border selectpicker"
                            data-live-search="true" title="Pilih Helper">
                            @foreach ($helpers as $helper)
                            <option value="{{ $helper->UserID }}">{{ $helper->Name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4 col-12">
                        <div class="form-group">
                          <label class="my-0" for="license_plate">Plat Nomor Kendaraan</label>
                          <input type="text" name="license_plate" id="license_plate" class="form-control"
                            placeholder="Masukkan Plat Nomor Kendaraan" onkeyup="this.value = this.value.toUpperCase();"
                            autocomplete="off" required>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="preview-product">

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
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/main/js/delivery/request/request.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script src="{{url('/')}}/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<script>
  // BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  })

  // this overrides `contains` to make it case insenstive
  jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
  };
  // Search Subdistrict
  /*$('#search-subdistrict').keyup(function (){
      $('.card').removeClass('d-none');
      $('.subdistrict').removeClass('d-none');
      $('.subdistrict').removeClass('d-flex');
      let filterSubdistrict = $(this).val(); // get the value of the input, which we filter on
      $('.subdistrict-wrapper').find('label:not(:contains("'+filterSubdistrict+'"))').parent().addClass('d-none');
      $('.subdistrict-wrapper').find('label:not(:contains("'+filterSubdistrict+'"))').parent().removeClass('d-flex');
      $('.city-card').each(function(){
        let dNoneSubdistrict = $(this).find('.d-none').length;
        let labelSubdistrict = $(this).find('.subdistrict label').length;
        let diffLength = dNoneSubdistrict - labelSubdistrict;
        if (diffLength == 0) {
          $(this).addClass('d-none'); 
        }
      });
  });*/

  $('#delivery-order-result').on('change', '#send_by', function () {
    let valueDistributor = $(this).val();
    $(this).closest('.request-do').find('#distributor').val(valueDistributor);
  });

  $('#delivery-order-result').on('change', '.check_rtmart', function () {
    $(this).closest(".request-do").find("#qty-request-do, #product-id").prop('disabled', !$(this).is(':checked'));

    if ($(this).is(":checked")) {
      let priceTotal = $(this).closest('.request-do').find('.price-total').text().replaceAll("Rp ", "").replaceAll(".", "");
      let subTotal = $(this).closest('.request-do-wrapper').find('.price-subtotal').text().replaceAll("Rp ", "").replaceAll(".", "");
      let newSubTotal = Number(subTotal) + Number(priceTotal);
      $(this).closest('.request-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(newSubTotal));
    } else {
      let priceTotal = $(this).closest('.request-do').find('.price-total').text().replaceAll("Rp ", "").replaceAll(".", "");
      let subTotal = $(this).closest('.request-do-wrapper').find('.price-subtotal').text().replaceAll("Rp ", "").replaceAll(".", "");
      let newSubTotal = Number(subTotal) - Number(priceTotal);
      $(this).closest('.request-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(newSubTotal));
    }
  });

  // $('#delivery-order-result').on('change', '.check_haistar', function () {
  //   $(this).closest(".request-do").find("#qty-request-do, #product-id").prop('disabled', !$(this).is(':checked'));
  //   if ($(this).is(":checked")) {
  //     let priceTotal = $(this).closest('.request-do').find('.price-total').text().replaceAll("Rp ", "").replaceAll(".", "");
  //     let subTotal = $(this).closest('.request-do-wrapper').find('.price-subtotal').text().replaceAll("Rp ", "").replaceAll(".", "");
  //     let newSubTotal = Number(subTotal) + Number(priceTotal);
  //     $(this).closest('.request-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(newSubTotal));
  //   } else {
  //     let priceTotal = $(this).closest('.request-do').find('.price-total').text().replaceAll("Rp ", "").replaceAll(".", "");
  //     let subTotal = $(this).closest('.request-do-wrapper').find('.price-subtotal').text().replaceAll("Rp ", "").replaceAll(".", "");
  //     let newSubTotal = Number(subTotal) - Number(priceTotal);
  //     $(this).closest('.request-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(newSubTotal));
  //   }
  // });

  // Event listener saat mengetik qty do
  $('#delivery-order-result').on('keyup', '.qty-request-do', function (e) {
      e.preventDefault();
      const priceProduct = $(this).next().text().replaceAll("x @Rp ", "").replaceAll(".", "");
      const qtyDO = $(this).val();
      
      const totalPriceProduct = Number(qtyDO) * Number(priceProduct);
      $(this).parent().parent().next().children().last().html('Rp ' + thousands_separators(totalPriceProduct));
      
      const totalPriceAllProductArr = $(this).closest('.request-do-wrapper').find('.price-total').text().replace("Rp ", "").replaceAll("Rp ", ",").replaceAll(".", "").split(",");

      let priceAllProductNumber = totalPriceAllProductArr.map(Number);
      let subTotalDO = 0;
      $.each(priceAllProductNumber, function() {
          subTotalDO += this;
      });

      $(this).closest('.request-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(subTotalDO));
  });
</script>
@endsection