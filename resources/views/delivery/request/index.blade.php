@extends('layouts.master')
@section('title', 'Dashboard - Delivery Request')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="depo" content="{{ Auth::user()->Depo }}">
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
                    <span class="bs-stepper-circle">1</span>
                    <span class="bs-stepper-label">Pilih Area & Kiriman</span>
                  </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#product-part">
                  <button type="button" class="step-trigger p-2" role="tab" aria-controls="product-part"
                    id="product-part-trigger">
                    <span class="bs-stepper-circle">2</span>
                    <span class="bs-stepper-label">Pilih Detail Kiriman</span>
                  </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#preview-part">
                  <button type="button" class="step-trigger p-2" role="tab" aria-controls="preview-part"
                    id="preview-part-trigger">
                    <span class="bs-stepper-circle">3</span>
                    <span class="bs-stepper-label">Preview Kiriman</span>
                  </button>
                </div>
              </div>
              <div class="bs-stepper-content">
                <!-- your steps content here -->
                <div id="do-part" class="content" role="tabpanel" aria-labelledby="do-part-trigger">
                  <div class="callout callout-info p-2 mb-2">
                    <p class="font-weight-bold mb-1">Delivery Order yang dipilih : </p>
                    <span id="do-selected"></span>
                  </div>
                  <div class="card-footer d-flex justify-content-end">
                    <button class="btn btn-sm btn-primary" id="first-next-step">Selanjutnya</button>
                  </div>
                  <div class="card-body p-0 pt-2">
                    <div class="row">
                      <div class="col-5 col-sm-4 col-md-3 col-xl-2">
                        <input class="form-control form-control-sm mb-2" type="search" placeholder="Cari Kecamatan"
                          aria-label="Search" id="search-subdistrict">
                        <div class="card-wrapper">
                          @foreach ($areas->groupBy('City')->all() as $area)
                          <div class="city-card card card-outline card-primary mb-2">
                            <div class="card-header d-flex justify-content-between p-2">
                              <h3 class="card-title city-title font-weight-bolder flex-grow-1"
                                data-card-widget="collapse" style="cursor: pointer">
                                {{ $area[0]->City }}
                              </h3>
                              <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                  <i class="fas fa-minus"></i>
                                </button>
                              </div>
                            </div>
                            <div class="subdistrict-wrapper card-body p-2">
                              @foreach ($area as $item)
                              <div class="subdistrict form-group d-flex align-items-center m-0" style="gap:5px;">
                                <input type="checkbox" class="check-subdistrict"
                                  id="{{ Str::slug($item->Subdistrict) }}" value="{{ $item->Subdistrict }}"
                                  style="cursor: pointer">
                                <label for="{{ Str::slug($item->Subdistrict) }}" class="font-weight-normal m-0"
                                  style="cursor: pointer">{{ $item->Subdistrict }}</label>
                              </div>
                              @endforeach
                            </div>
                          </div>
                          @endforeach
                        </div>
                      </div>
                      <div class="col-7 col-sm-8 col-md-9 col-xl-10">
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
                                      <th>Stock Order ID</th>
                                      <th>Area</th>
                                      <th>Tanggal Request</th>
                                      <th>Tenggat Waktu</th>
                                      <th>Nama Toko</th>
                                      <th>Produk</th>
                                      <th>Distributor</th>
                                      <th>Sales</th>
                                      <th>No. Telp</th>
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
                          <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-dismiss="modal">Kembali</button>
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
  $('#search-subdistrict').keyup(function (){
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
  });   

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

  $('#delivery-order-result').on('change', '.check_haistar', function () {
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