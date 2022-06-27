@extends('layouts.master')
@section('title', 'Dashboard - Edit Merchant Assessment')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Ubah Assessment Merchant')

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
            <form id="edit-assessment" method="post" action="{{ route('merchant.updateAssessment', ['assessmentID' => $assessmentID]) }}"
              enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label>Store ID</label>
                    <input type="text" class="form-control" value="{{ $assessment->StoreID }}" readonly>
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label>Nama Store</label>
                    <input type="text" class="form-control" value="{{ $assessment->StoreName }}" readonly>
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label>Np. HP Store</label>
                    <input type="text" class="form-control" value="{{ $assessment->StorePhoneNumber }}" readonly>
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label>Merchant ID</label>
                    <input type="text" class="form-control" value="{{ $assessment->MerchantID }}" readonly>
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label>Nama Merchant</label>
                    <input type="text" class="form-control" value="{{ $assessment->MerchantName }}" readonly>
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label>Np. HP Merchant</label>
                    <input type="text" class="form-control" value="{{ $assessment->MerchantPhoneNumber }}" readonly>
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="merchant_front_photo">Foto Toko (Tampak Depan)</label>
                    <input type="file" name="merchant_front_photo" id="merchant_front_photo"
                      onchange="loadFileMultiple(event, 'front_view')"
                      class="form-control @if($errors->has('merchant_front_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('merchant_front_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('merchant_front_photo') }}</span>
                    @endif
                    <img src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoMerchantFront }}" 
                      id="front_view" class="mw-100 mt-3" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="merchant_side_photo">Foto Toko (Tampak Samping)</label>
                    <input type="file" name="merchant_side_photo" id="merchant_side_photo"
                      onchange="loadFileMultiple(event, 'side_view')"
                      class="form-control @if($errors->has('merchant_side_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('merchant_side_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('merchant_side_photo') }}</span>
                    @endif
                    <img src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoMerchantSide }}"
                      id="side_view" class="mw-100 mt-3" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="struck_photo">Bukti Bon</label>
                    <input type="file" name="struck_photo" id="struck_photo"
                      onchange="loadFileMultiple(event, 'struck_view')"
                      class="form-control @if($errors->has('struck_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('struck_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('struck_photo') }}</span>
                    @endif
                    <img src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->StruckDistribution }}"
                      id="struck_view" class="mw-100 mt-3" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="stock_photo">Foto Stok Toko</label>
                    <input type="file" name="stock_photo" id="stock_photo"
                      onchange="loadFileMultiple(event, 'stock_view')"
                      class="form-control @if($errors->has('stock_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('stock_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('stock_photo') }}</span>
                    @endif
                    <img src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoStockProduct }}"
                      id="stock_view" class="mw-100 mt-3" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="id_card_photo">Foto KTP</label>
                    <input type="file" name="id_card_photo" id="id_card_photo"
                      onchange="loadFileMultiple(event, 'id_card_view')"
                      class="form-control @if($errors->has('id_card_photo')) is-invalid @endif" accept="image/*">
                    @if($errors->has('id_card_photo'))
                    <span class="error invalid-feedback">{{ $errors->first('id_card_photo') }}</span>
                    @endif
                    <img src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoIDCard }}"
                      id="id_card_view" class="mw-100 mt-3" height="220" style="object-fit: cover" />
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="id_card_number">No. KTP</label>
                    <input type="number" name="id_card_number" id="id_card_number" placeholder="Masukan No. KTP"
                      value="{{ old('id_card_number') ? old('id_card_number') : $assessment->NumberIDCard }}" required autocomplete="off"
                      class="form-control @if($errors->has('id_card_number')) is-invalid @endif" required>
                    @if($errors->has('id_card_number'))
                    <span class="error invalid-feedback">{{ $errors->first('id_card_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="full_name">Nama lengkap</label>
                    <input type="text" name="full_name" id="full_name" placeholder="Masukan Nama Lengkap"
                      value="{{ old('full_name') ? old('full_name') : $assessment->NameIDCard }}" required autocomplete="off"
                      class="form-control @if($errors->has('full_name')) is-invalid @endif" required>
                    @if($errors->has('full_name'))
                    <span class="error invalid-feedback">{{ $errors->first('full_name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="birth_date">Tanggal Lahir</label>
                    <input type="text" name="birth_date" id="birth_date" placeholder="Masukan Tanggal Lahir"
                      value="{{ old('birth_date') ? old('birth_date') : $assessment->BirthDateIDCard }}" required autocomplete="off"
                      class="form-control @if($errors->has('birth_date')) is-invalid @endif" required>
                    @if($errors->has('birth_date'))
                    <span class="error invalid-feedback">{{ $errors->first('birth_date') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group float-right">
                <button type="submit" class="btn btn-warning">Simpan</button>
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
<script>

  // $("#merchant_front_photo").change(function () {
  //   $("#front_view").removeClass("d-none");
  // });
  // $("#merchant_side_photo").change(function () {
  //   $("#side_view").removeClass("d-none");
  // });
  // $("#struck_photo").change(function () {
  //   $("#struck_view").removeClass("d-none");
  // });
  // $("#stock_photo").change(function () {
  //   $("#stock_view").removeClass("d-none");
  // });
  // $("#id_card_photo").change(function () {
  //   $("#id_card_view").removeClass("d-none");
  // });
</script>
@endsection