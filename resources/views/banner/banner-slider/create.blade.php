@extends('layouts.master')
@section('title', 'Dashboard - Add Banner Slider')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
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
            <form id="add-banner-slider" method="post" action="{{ route('product.insertList') }}"
              enctype="multipart/form-data">
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
                    <input type="text" name="start_date" id="start_date" class="form-control @if($errors->has('start_date')) is-invalid @endif" readonly>
                    @if($errors->has('start_date'))
                      <span class="error invalid-feedback">{{ $errors->first('start_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="end_date">Tanggal Berakhir</label>
                    <input type="text" name="end_date" id="end_date" class="form-control @if($errors->has('end_date')) is-invalid @endif" readonly>
                    @if($errors->has('end_date'))
                      <span class="error invalid-feedback">{{ $errors->first('end_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="product_image">Upload Foto Produk</label>
                    <input type="file" name="product_image" id="product_image" accept="image/*"
                      onchange="loadFile(event)" class="form-control 
                                            @if($errors->has('product_image')) is-invalid @endif" required>
                    @if($errors->has('product_image'))
                    <span class="error invalid-feedback">{{ $errors->first('product_image')
                      }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <img id="output" height="150" />
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
<script src="{{url('/')}}/plugins/daterangepicker/daterangepicker.js"></script>
<script>
  // Setting Awal Daterangepicker
  $("#start_date").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    locale: {
        format: "YYYY-MM-DD",
    },
  });

  // Setting Awal Daterangepicker
  $("#end_date").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    locale: {
      format: "YYYY-MM-DD",
    },
  });

  var bCodeChange = false;

  function dateStartChange() {
    if (bCodeChange == true) return;
    else bCodeChange = true;

    $("#end_date").daterangepicker({
      minDate: $("#start_date").val(),
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: "YYYY-MM-DD",
      },
    });
    bCodeChange = false;
  }

  function dateEndChange() {
    if (bCodeChange == true) return;
    else bCodeChange = true;

    $("#start_date").daterangepicker({
      maxDate: $("#end_date").val(),
      singleDatePicker: true,
      showDropdowns: true,
      locale: {
        format: "YYYY-MM-DD",
      },
    });
    bCodeChange = false;
  }

  // Menyisipkan Placeholder Date
  $("#start_date").val("");
  $("#end_date").val("");
  $("#start_date").attr("placeholder", "Pilih Tanggal Mulai");
  $("#end_date").attr("placeholder", "Pilih Tanggal Berakhir");

  // Disabled input to date ketika from date berubah
  $("#start_date").on("change", function () {
    dateStartChange();
  });
  // Disabled input from date ketika to date berubah
  $("#end_date").on("change", function () {
    dateEndChange();
  });
</script>
@endsection