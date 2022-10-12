@extends('layouts.master')
@section('title', 'Dashboard - Add Banner Slider')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ url('/') }}/plugins/summernote/summernote-bs4.css">
@endsection

@section('header-menu', 'Tambah Banner Slider')

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
            <a href="{{ route('banner.slider') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="add-banner-slider" method="post" action="{{ route('banner.sliderStore') }}" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="title">Judul</label>
                    <input type="text" name="title" id="title" placeholder="Masukan Judul"
                      value="{{ old('title') }}" class="form-control @if($errors->has('title')) is-invalid @endif" required>
                    @if($errors->has('title'))
                    <span class="error invalid-feedback">{{ $errors->first('title') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control @if($errors->has('start_date')) is-invalid @endif" 
                      value="{{ old('start_date') }}" required>
                    @if($errors->has('start_date'))
                      <span class="error invalid-feedback">{{ $errors->first('start_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="end_date">Tanggal Berakhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control @if($errors->has('end_date')) is-invalid @endif"
                      value="{{ old('end_date') }}" required>
                    @if($errors->has('end_date'))
                      <span class="error invalid-feedback">{{ $errors->first('end_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="target">Target</label>
                    <select name="target" id="target" title="Pilih Target Banner" required
                      class="form-control selectpicker border @if ($errors->has('target')) is-invalid @endif">
                      @foreach ($targets as $target)
                        <option value="{{ $target->PromoTarget }}">{{ $target->PromoTarget }}</option>
                      @endforeach
                    </select>
                    @if($errors->has('target'))
                      <span class="error invalid-feedback">{{ $errors->first('target') }}</span>
                    @endif
                    <select name="target_id[]" id="target_id" title="Pilih Target ID" data-live-search="true" multiple
                      class="form-control selectpicker border mt-2 d-none target_id @if ($errors->has('target_id')) is-invalid @endif">
                    </select>
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="activity_button_page">Activity Button Page</label>
                    <input type="text" name="activity_button_page" id="activity_button_page" class="form-control">
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="activity_button_text">Activity Button Text</label>
                    <input type="text" name="activity_button_text" id="activity_button_text" class="form-control">
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="banner_image">Gambar Banner</label>
                    <input type="file" name="banner_image" id="banner_image" accept="image/*"
                      onchange="loadFile(event)" class="form-control 
                      @if($errors->has('banner_image')) is-invalid @endif" required>
                    @if($errors->has('banner_image'))
                      <span class="error invalid-feedback">{{ $errors->first('banner_image') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <img id="output" height="150" />
                </div>
                <div class="col-12">
                  <label for="description">Deskripsi</label>
                  <textarea name="description" class="textarea 
                    @if ($errors->has('description')) is-invalid @endif">{{ old('description') }}</textarea>
                  @if($errors->has('description'))
                      <span class="error invalid-feedback">{{ $errors->first('description') }}</span>
                  @endif
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
<script src="{{url('/')}}/plugins/moment/moment.min.js"></script>
<script src="{{url('/')}}/main/js/helper/input-image-view.js"></script>
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{ url('/') }}/plugins/summernote/summernote-bs4.min.js"></script>
<script>

  $("#target").on("change", function () {
    const target = $(this).val();
    
    if (target === "MERCHANT" || target === "CUSTOMER") {
      $("#target_id").val("");
      $("#target_id").selectpicker("refresh");
      $.ajax({
        type: "get",
        url: `/banner/slider/listTargetID/${target}`,
        success: function (response) {
          let option = "";
          if (target === "MERCHANT") {
            $("#target_id option").remove();
            $.each(response, function (index, value) {
              option += `<option value="${value.MerchantID}">${value.MerchantID} - ${value.StoreName}</option>`;
            });
          } else {
            $("#target_id option").remove();
            $.each(response, function (index, value) {
              option += `<option value="${value.CustomerID}">${value.CustomerID} - ${value.FullName}</option>`;
            });
          }
          $("#target_id").append(option);
          $("#target_id").selectpicker("refresh");
          $(".target_id").removeClass("d-none");
        }
      });
    } else {
      $(".target_id").addClass("d-none");
      $("#target_id").val("");
      $("#target_id").selectpicker("refresh");
    }
    
  });

  // Summernote
  $('.textarea').summernote({
    height: 200,
    toolbar: [
      ['font', ['bold', 'underline']],
      ['para', ['ul']],
      ['view', ['codeview']]
    ]
  });
</script>
@endsection