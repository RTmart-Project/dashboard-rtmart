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
                    <label for="distributor">Distributor</label>
                    <select name="distributor" id="distributor" data-live-search="true" title="Pilih Distributor"
                      class="form-control selectpicker border @if($errors->has('distributor')) is-invalid @endif" required>
                      @foreach ($distributors as $distributor)
                      <option value="{{ $distributor->DistributorID }}" {{ old('distributor')==$distributor->DistributorID ? 'selected' : '' }}>
                        {{ $distributor->DistributorName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('distributor'))
                    <span class="error invalid-feedback">{{ $errors->first('distributor') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="opname_date">Tanggal Opname</label>
                    <input type="datetime-local" name="opname_date" id="opname_date"
                      placeholder="Masukan Nama Produk" value="{{ old('opname_date') }}"
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
                      class="form-control selectpicker border @if($errors->has('opname_officer')) is-invalid @endif"
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
              <div id="wrapper-opname-detail">
                <div id="opname-detail" class="row mb-3">
                  <div class="col-12">
                    <a class="btn btn-sm float-right remove"><i class="far fa-times-circle fa-lg text-danger"></i></a>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="product">Nama Produk</label>
                      <select title="Pilih Produk" name="product[]" id="product" data-live-search="true"
                        class="form-control selectpicker border select-product" required>
                        @foreach ($products as $product)
                        <option value="{{ $product->ProductID }}" {{ collect(old('product'))->contains($product->ProductID) ? 'selected' : '' }}>
                          {{ $product->ProductName.' -- Isi: '. $product->ProductUOMDesc . ' ' . $product->ProductUOMName }}
                        </option>
                        @endforeach
                      </select>
                      <span id="no-distributor"></span>
                    </div>
                  </div>                  
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="old_good_stock">Kuantiti Lama (Good Stock)</label>
                      <input type="number" id="old_good_stock" name="old_good_stock[]" class="form-control" autocomplete="off"
                        value="{{ collect(old('old_good_stock')) }}" placeholder="Jumlah Kuantiti Lama (Good Stock)" required readonly>
                    </div>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="new_good_stock">Kuantiti Baru (Good Stock)</label>
                      <input type="text" id="new_good_stock" name="new_good_stock[]" class="form-control autonumeric" autocomplete="off"
                        value="{{ collect(old('new_good_stock')) }}" placeholder="Masukkan Jumlah Kuantiti Baru (Good Stock)" required>
                    </div>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="old_bad_stock">Kuantiti Lama (Bad Stock)</label>
                      <input type="number" id="old_bad_stock" name="old_bad_stock[]" class="form-control" autocomplete="off"
                        value="{{ collect(old('old_bad_stock')) }}" placeholder="Jumlah Kuantiti Lama (Bad Stock)" required readonly>
                    </div>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="new_bad_stock">Kuantiti Baru (Bad Stock)</label>
                      <input type="text" id="new_bad_stock" name="new_bad_stock[]" class="form-control autonumeric" autocomplete="off"
                        value="{{ collect(old('new_bad_stock')) }}" placeholder="Masukkan Jumlah Kuantiti Baru (Bad Stock)" required>
                    </div>
                  </div>
                </div>
                <div id="opname-detail-append"></div>
              </div>
              <div class="clearfix">
                <a class="btn btn-sm add float-right"><i class="fas fa-plus-circle fa-lg"></i></a>
              </div>

              <div class="form-group float-right mt-4">
                <button type="submit" class="btn btn-success">Simpan</button>
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

  $("#wrapper-opname-detail").on('change', '.select-product select', function () {
    const distributorID = $('#distributor').val();
    const selectProduct = $(this);
    const productID = selectProduct.val();
    if (distributorID != "") {
      $.ajax({
        type: "get",
        url: `/stock/opname/sumOldProduct/${distributorID}/${productID}`,
        success: function (response) {
          const res = $.parseJSON(response);
          selectProduct.closest('#opname-detail').find('#old_good_stock').val(res.goodStock);
          selectProduct.closest('#opname-detail').find('#old_bad_stock').val(res.badStock);
          selectProduct.closest('#opname-detail').find('#no-distributor').html('');
        }
      });
    } else {
      selectProduct.closest('#opname-detail').find('#no-distributor').html('Harap pilih distributor terlebih dahulu');
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