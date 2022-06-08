@extends('layouts.master')
@section('title', 'Dashboard - Add Merchant Assessment')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Assessment Merchant')

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
            <a href="{{ route('merchant.assessment') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="create-assessment" method="post" action="{{ route('merchant.storeAssessment') }}"
              enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="merchant_front_photo">Foto Toko (Tampak Depan)</label>
                    <input type="file" name="merchant_front_photo" id="merchant_front_photo"
                      onchange="loadFileMultiple(event, 'front_view')" required
                      class="form-control @if($errors->has('merchant_front_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('merchant_front_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('merchant_front_photo') }}</span>
                    @endif
                    <img id="front_view" class="mw-100 mt-3 d-none" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="merchant_side_photo">Foto Toko (Tampak Samping)</label>
                    <input type="file" name="merchant_side_photo" id="merchant_side_photo"
                      onchange="loadFileMultiple(event, 'side_view')" required
                      class="form-control @if($errors->has('merchant_side_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('merchant_side_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('merchant_side_photo') }}</span>
                    @endif
                    <img id="side_view" class="mw-100 mt-3 d-none" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="struck_photo">Bukti Bon</label>
                    <input type="file" name="struck_photo" id="struck_photo"
                      onchange="loadFileMultiple(event, 'struck_view')" required
                      class="form-control @if($errors->has('struck_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('struck_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('struck_photo') }}</span>
                    @endif
                    <img id="struck_view" class="mw-100 mt-3 d-none" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="stock_photo">Foto Stok Toko</label>
                    <input type="file" name="stock_photo" id="stock_photo"
                      onchange="loadFileMultiple(event, 'stock_view')" required
                      class="form-control @if($errors->has('stock_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('stock_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('stock_photo') }}</span>
                    @endif
                    <img id="stock_view" class="mw-100 mt-3 d-none" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="stock_photo">Foto KTP</label>
                    <input type="file" name="id_card_photo" id="id_card_photo"
                      onchange="loadFileMultiple(event, 'id_card_view')" required
                      class="form-control @if($errors->has('id_card_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('id_card_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('id_card_photo') }}</span>
                    @endif
                    <img id="id_card_view" class="mw-100 mt-3 d-none" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="id_card_number">No. KTP</label>
                    <input type="number" name="id_card_number" id="id_card_number" placeholder="Masukan No. KTP"
                      value="{{ old('id_card_number') }}" required autocomplete="off"
                      class="form-control @if($errors->has('id_card_number')) is-invalid @endif">
                    @if($errors->has('id_card_number'))
                    <span class="error invalid-feedback">{{ $errors->first('id_card_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="store">Store</label>
                    <select name="store" id="store" required data-live-search="true" title="Pilih Store"
                      class="form-control selectpicker border @if ($errors->has('store')) is-invalid @endif">
                      @foreach ($stores as $store)
                      <option value="{{ $store->StoreID }}"
                        {{ collect(old('store'))->contains($store->StoreID) ? 'selected' : '' }}>
                        {{ $store->StoreID }} - {{ $store->StoreName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('store'))
                      <span class="error invalid-feedback">{{ $errors->first('store') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="merchant">Merchant</label>
                    <select name="merchant" id="merchant" required data-live-search="true" title="Pilih Merchant" 
                      class="form-control selectpicker border @if ($errors->has('merchant')) is-invalid @endif">
                      @foreach ($merchants as $merchant)
                      <option value="{{ $merchant->MerchantID }}" class="w-25"
                        {{ collect(old('merchant'))->contains($merchant->MerchantID) ? 'selected' : '' }}>
                        {{ $merchant->MerchantID }} - {{ $merchant->StoreName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('merchant'))
                      <span class="error invalid-feedback">{{ $errors->first('merchant') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="average_omzet">Omset Rata-Rata / Bulan</label>
                    <input type="text" name="average_omzet" id="average_omzet" autocomplete="off"
                      placeholder="Masukan Omset Rata-Rata / Bulan" value="{{ old('average_omzet') }}" required
                      class="form-control autonumeric @if($errors->has('average_omzet')) is-invalid @endif">
                    @if($errors->has('average_omzet'))
                    <span class="error invalid-feedback">{{ $errors->first('average_omzet') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <label>Transaksi yg Pernah Digunakan</label>
                  <div class="form-group">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" name="transaction[]" id="tunai"
                        value="TN">
                      <label class="form-check-label" for="tunai">Tunai</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" name="transaction[]" id="uangme"
                        value="UM">
                      <label class="form-check-label" for="uangme">UangMe</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" name="transaction[]" id="kredit"
                        value="KR">
                      <label class="form-check-label" for="kredit">Kredit</label>
                    </div>
                    @if($errors->has('transaction'))
                    <span class="error invalid-feedback" style="display: block;">{{ $errors->first('transaction') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group float-right">
                <button type="submit" class="btn btn-success">Tambah</button>
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
<script src="{{url('/')}}/main/js/helper/input-image-view.js"></script>
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
  new AutoNumeric(".autonumeric", {
    allowDecimalPadding: false,
    decimalCharacter: ',',
    digitGroupSeparator: '.',
    unformatOnSubmit: true
  });

  $("#merchant_front_photo").change(function () {
    $("#front_view").removeClass("d-none");
  });
  $("#merchant_side_photo").change(function () {
    $("#side_view").removeClass("d-none");
  });
  $("#struck_photo").change(function () {
    $("#struck_view").removeClass("d-none");
  });
  $("#stock_photo").change(function () {
    $("#stock_view").removeClass("d-none");
  });
  $("#id_card_photo").change(function () {
    $("#id_card_view").removeClass("d-none");
  });
</script>
@endsection