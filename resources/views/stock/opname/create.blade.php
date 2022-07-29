@extends('layouts.master')
@section('title', 'Dashboard - Add Stock Opname')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Stock Opname')

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
            <a href="{{ route('stock.opname') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="add-opname" method="post" action="{{ route('stock.storeOpname') }}">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="inbound">Sumber Inbound</label>
                    <select name="inbound" id="inbound" data-live-search="true" title="Pilih Sumber Inbound"
                      class="form-control selectpicker border @if($errors->has('inbound')) is-invalid @endif" required>
                      @foreach ($inbounds as $inbound)
                      <option value="{{ $inbound->PurchaseID }}" {{ old('inbound') == $inbound->PurchaseID ? 'selected' : '' }}>
                        {{ $inbound->PurchaseID }}
                      </option>
                      @endforeach
                      <option value="Lainnya" {{ old('inbound') == "Lainnya" }}>Lainnya</option>
                    </select>
                    @if($errors->has('inbound'))
                    <span class="error invalid-feedback">{{ $errors->first('inbound') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="distributor">Distributor</label>
                    <input id="distributor-input" class="form-control" placeholder="Distributor" readonly>
                    <select name="distributor" id="distributor"
                      class="form-control d-none @if($errors->has('distributor')) is-invalid @endif">
                      <option value="" selected hidden disabled>Pilih Distributor</option>
                    </select>
                    @if($errors->has('distributor'))
                    <span class="error invalid-feedback">{{ $errors->first('distributor') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="investor">Investor</label>
                    <input id="investor-input" class="form-control" placeholder="Investor" readonly>
                    <select name="investor" id="investor"
                      class="form-control d-none @if($errors->has('investor')) is-invalid @endif">
                      <option value="" selected hidden disabled>Pilih Investor</option>
                    </select>
                    @if($errors->has('investor'))
                    <span class="error invalid-feedback">{{ $errors->first('investor') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="opname_date">Tanggal Opname</label>
                    <input type="datetime-local" name="opname_date" id="opname_date"
                      value="{{ old('opname_date') }}"
                      class="form-control @if($errors->has('opname_date')) is-invalid @endif" required>
                    @if($errors->has('opname_date'))
                    <span class="error invalid-feedback">{{ $errors->first('opname_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="opname_officer">Petugas Opname</label>
                    <select name="opname_officer[]" id="opname_officer" data-live-search="true" title="Pilih Petugas Opname"
                      class="form-control selectpicker border opname-officer @if($errors->has('opname_officer')) is-invalid @endif"
                      required multiple>
                      @foreach ($users as $user)
                      <option value="{{ $user->UserID }}"
                        {{ collect(old('opname_officer'))->contains($user->UserID) ? 'selected' : '' }}>
                        {{ $user->Name }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('opname_officer'))
                    <span class="error invalid-feedback">{{ $errors->first('opname_officer') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea class="form-control @if($errors->has('notes')) is-invalid @endif" 
                      name="notes" id="notes" rows="3" placeholder="Masukkan Catatan"></textarea>
                    @if($errors->has('notes'))
                    <span class="error invalid-feedback">{{ $errors->first('notes') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <hr>
              <h4>Detail Produk</h4>
              <div id="wrapper-opname-detail-from-inbound">
                
              </div>
              <div id="wrapper-opname-detail" class="d-none">
                <div id="opname-detail" class="row mb-3">
                  <div class="col-12">
                    <a class="btn btn-sm float-right remove"><i class="far fa-times-circle fa-lg text-danger"></i></a>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="product">Nama Produk</label>
                      <select title="Pilih Produk" name="product[]" id="product" data-live-search="true"
                        class="form-control selectpicker border select-product">
                        {{-- @foreach ($products as $product)
                        <option value="{{ $product->ProductID }}" {{ collect(old('product'))->contains($product->ProductID) ? 'selected' : '' }}>
                          {{ $product->ProductID.' - '. $product->ProductName.' -- Isi: '. $product->ProductUOMDesc . ' ' . $product->ProductUOMName }}
                        </option>
                        @endforeach --}}
                      </select>
                      <span id="no-distributor"></span>
                    </div>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="labeling">Label Produk</label>
                      <select title="Pilih Labeling Produk" name="labeling[]" id="labeling"
                        class="form-control selectpicker border label-product">
                        <option value="PKP" {{ collect(old('labeling'))->contains('PKP') ? 'selected' : '' }}>
                          PKP
                        </option>
                        <option value="NON-PKP" {{ collect(old('labeling'))->contains('NON PKP') ? 'selected' : '' }}>
                          NON PKP
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="form-group">
                      <label for="old_good_stock">Kuantiti Lama (Good Stock)</label>
                      <input type="number" id="old_good_stock" name="old_good_stock[]" class="form-control oldGS" autocomplete="off"
                        value="{{ collect(old('old_good_stock')) }}" placeholder="Jumlah Kuantiti Lama (Good Stock)" readonly>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="form-group">
                      <label for="new_good_stock">Kuantiti Baru (Good Stock)</label>
                      <input type="text" id="new_good_stock" name="new_good_stock[]" class="form-control autonumeric newGS" autocomplete="off"
                        value="{{ collect(old('new_good_stock')) }}" placeholder="Masukkan Jumlah Kuantiti Baru (Good Stock)">
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="form-group">
                      <label for="purchase_price_good_stock">Harga Beli (Good Stock)</label>
                      <input type="text" id="purchase_price_good_stock" name="purchase_price_good_stock[]" class="form-control autonumeric newGS" autocomplete="off"
                        value="{{ collect(old('purchase_price_good_stock')) }}" placeholder="Masukkan Harga Beli (Good Stock)">
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="form-group">
                      <label for="old_bad_stock">Kuantiti Lama (Bad Stock)</label>
                      <input type="number" id="old_bad_stock" name="old_bad_stock[]" class="form-control oldBS" autocomplete="off"
                        value="{{ collect(old('old_bad_stock')) }}" placeholder="Jumlah Kuantiti Lama (Bad Stock)" readonly>
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="form-group">
                      <label for="new_bad_stock">Kuantiti Baru (Bad Stock)</label>
                      <input type="text" id="new_bad_stock" name="new_bad_stock[]" class="form-control autonumeric newBS" autocomplete="off"
                        value="{{ collect(old('new_bad_stock')) }}" placeholder="Masukkan Jumlah Kuantiti Baru (Bad Stock)">
                    </div>
                  </div>
                  <div class="col-md-4 col-12">
                    <div class="form-group">
                      <label for="purchase_price_bad_stock">Harga Beli (Bad Stock)</label>
                      <input type="text" id="purchase_price_bad_stock" name="purchase_price_bad_stock[]" class="form-control autonumeric newGS" autocomplete="off"
                        value="{{ collect(old('purchase_price_bad_stock')) }}" placeholder="Masukkan Harga Beli (Bad Stock)">
                    </div>
                  </div>
                </div>
                <div id="opname-detail-append"></div>
                <div class="clearfix">
                  <a class="btn btn-sm add float-right"><i class="fas fa-plus-circle fa-lg"></i></a>
                </div>
              </div>

              <div class="form-group float-right mt-4">
                <button type="button" class="btn btn-success" data-target="#konfirmasi"
                data-toggle="modal" data-dismiss="modal">Simpan</button>
              </div>

              <!-- Modal -->
              <div class="modal fade" id="konfirmasi" data-backdrop="static" tabindex="-1" role="dialog"
              aria-labelledby="konfirmasiLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title" id="konfirmasiLabel"><i class="fas fa-info"></i> Konfirmasi</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah produk yang di-input sudah benar?</h5>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-dismiss="modal">Kembali</button>
                      <button type="button" class="btn btn-sm btn-success" data-target="#konfirmasi2"
                        data-toggle="modal" data-dismiss="modal">Benar</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="konfirmasi2" aria-hidden="true" data-backdrop="static"
                tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Stock Opname</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah yakin ingin membuat Stock Opname?</h5>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                        data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-sm btn-success"
                        data-toggle="modal">Yakin</button>
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
@endsection

@section('js-pages')
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/main/js/helper/clone-element.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
  // Set seperator '.' currency
  new AutoNumeric.multiple('.autonumeric', {
      allowDecimalPadding: false,
      decimalCharacter: ',',
      digitGroupSeparator: '.',
      unformatOnSubmit: true,
      minimumValue: 0
  });

  $("#inbound").change(function () {
    const inbound = $(this).val();
    $.ajax({
      type: "get",
      url: `/stock/opname/getDetailFromInbound/${inbound}`,
      success: function (response) {
        
        if (inbound == "Lainnya") {
          let optionDistributor = "";
          $("#distributor-input").addClass("d-none");
          $('#distributor').removeClass("d-none");
          $.each(response.distributors, function (index, value) {
            optionDistributor += `<option value="${value.DistributorID}">${value.DistributorName}</option>`;
          })
          $('#distributor').append(optionDistributor);

          let optionInvestor = "";
          $("#investor-input").addClass("d-none");
          $('#investor').removeClass("d-none");
          $.each(response.investors, function (index, value) {
            optionInvestor += `<option value="${value.InvestorID}">${value.InvestorName}</option>`;
          });
          $('#investor').append(optionInvestor);

          $("#wrapper-opname-detail-from-inbound").addClass("d-none");
          $("#wrapper-opname-detail").removeClass("d-none");
        } else {
          $('#distributor').addClass("d-none");
          $("#distributor-input").removeClass("d-none");
          $("#distributor-input").val(response.DistributorName);

          $('#investor').addClass("d-none");
          $("#investor-input").removeClass("d-none");
          $("#investor-input").val(response.InvestorName);

          $("#wrapper-opname-detail").addClass("d-none");
          $("#wrapper-opname-detail-from-inbound").removeClass("d-none");
          $("#wrapper-opname-detail-from-inbound").empty();
          let detailProduk = "";
          $.each(response.DetailProduk, function (index, value) {
            detailProduk += `<div class="row mb-3">
                              <div class="col-12">
                                <h5 class="m-0">Produk ${index + 1}</h5>
                              </div>
                              <div class="col-12 col-md-6 mb-2">
                                <label>Produk</label>
                                <input type="text" class="form-control" value="${value.ProductName} - (${value.ProductLabel})" readonly>
                              </div>
                              <div class="col-4 col-md-2">
                                <label>Condition Stock</label>
                                <input type="text" class="form-control" value="${value.ConditionStock}" readonly>
                              </div>
                              <div class="col-4 col-md-2">
                                <label>Harga Beli</label>
                                <input type="text" class="form-control" value="${thousands_separators(value.PurchasePrice)}" readonly>
                              </div>
                              <div class="col-2 col-md-1">
                                <label>Qty Lama</label>
                                <input type="text" class="form-control" value="${value.Qty}" readonly>
                              </div>
                              <div class="col-2 col-md-1">
                                <label>Qty Baru</label>
                                <input type="hidden" name="product_id[]" value="${value.ProductID}">
                                <input type="number" name="new_qty[]" class="form-control" required>
                              </div>
                            </div>`;
          });
          $("#wrapper-opname-detail-from-inbound").append(detailProduk);
        }
        
      }
    });
  });

  $("#distributor").change(function () {
    $("#investor").val('').trigger('change');
    $(".select-product").val('').trigger('change');
    $(".opname-officer").val('').trigger('change');
    $(".label-product").val('').trigger('change');
    $(".oldGS").val('');
    $(".newGS").val('');
    $(".oldBS").val('');
    $(".newBS").val('');

    const distributorID = $(this).val();
    $.ajax({
      type: "get",
      url: `/stock/opname/getProductExcluded/${distributorID}`,
      success: function (response) {
        let optionProduct = "";
        $.each(response, function (index, value) {
          optionProduct += `<option value="${value.ProductID}">${value.ProductID} - ${value.ProductName} -- Isi: ${value.ProductUOMDesc} ${value.ProductUOMName}</option>`;
        });
        $('#product').html(optionProduct);
        $('#product').selectpicker('refresh');
      }
    });
  });

  $("#investor").change(function () {
    $(".select-product").val('').trigger('change');
    $(".opname-officer").val('').trigger('change');
    $(".label-product").val('').trigger('change');
    $(".oldGS").val('');
    $(".newGS").val('');
    $(".oldBS").val('');
    $(".newBS").val('');
  });

  $("#opname-detail").on('change', '.select-product select', function () {
    $(this).closest('#opname-detail').find('.label-product').val('').trigger('change');
    $(this).closest('#opname-detail').find('.oldGS').val('').trigger('change');
    $(this).closest('#opname-detail').find('.newGS').val('').trigger('change');
    $(this).closest('#opname-detail').find('.oldBS').val('').trigger('change');
    $(this).closest('#opname-detail').find('.newBS').val('').trigger('change');
  });

  $("#opname-detail-append").on('change', '.select-product select', function () {
    $(this).closest('#opname-detail').find('.label-product').val('').trigger('change');
    $(this).closest('#opname-detail').find('.oldGS').val('').trigger('change');
    $(this).closest('#opname-detail').find('.newGS').val('').trigger('change');
    $(this).closest('#opname-detail').find('.oldBS').val('').trigger('change');
    $(this).closest('#opname-detail').find('.newBS').val('').trigger('change');
  });

  $("#wrapper-opname-detail").on('change', '.label-product select', function () {
    const distributorID = $('#distributor').val();
    let investorID = $('#investor').val();
    const productID = $(this).closest('#opname-detail').find('#product').val();
    const label = $(this);
    const labelProduct = label.val();
    
    if (!investorID) {
      investorID = null;
    }

    if (distributorID != "") {
      $.ajax({
        type: "get",
        url: `/stock/opname/sumOldProduct/${distributorID}/${investorID}/${productID}/${labelProduct}`,
        success: function (response) {
          const res = $.parseJSON(response);
          label.closest('#opname-detail').find('#old_good_stock').val(res.goodStock);
          label.closest('#opname-detail').find('#old_bad_stock').val(res.badStock);
          label.closest('#opname-detail').find('#no-distributor').html('');
        }
      });
    } else {
      label.closest('#opname-detail').find('#no-distributor').html('Harap pilih distributor terlebih dahulu');
    }
  });

  // Cloning Form Product
  $('#opname-detail:first .remove').css("visibility", "hidden");
  $('.add').on('click', function () {
      $('#opname-detail:first .remove').css("visibility", "visible");
      cloneElement('#opname-detail:first', '#opname-detail-append');
      $('#opname-detail:first .remove').css("visibility", "hidden");
  });

  $('body').on('click', '.remove', function() {
      let closest = $(this).closest('#opname-detail').remove();
  });

</script>
@endsection