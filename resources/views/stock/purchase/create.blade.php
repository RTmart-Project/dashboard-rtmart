@extends('layouts.master')
@section('title', 'Dashboard - Add Purchase Stock')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Purchase Stock')

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
            <a href="{{ route('stock.purchase') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="add-purchase" method="post" action="{{ route('stock.storePurchase') }}"
              enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="distributor">Distributor</label>
                    <select name="distributor" id="distributor" data-live-search="true" title="Pilih Distributor"
                      class="form-control selectpicker border @if($errors->has('distributor')) is-invalid @endif"
                      required>
                      @foreach ($distributors as $distributor)
                      <option value="{{ $distributor->DistributorID }}" {{ old('distributor')==$distributor->
                        DistributorID ?
                        'selected'
                        : '' }}>
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
                    <label for="supplier">Supplier</label>
                    <select name="supplier" id="supplier" data-live-search="true" title="Pilih Supplier"
                      class="form-control selectpicker border @if($errors->has('supplier')) is-invalid @endif" required>
                      @foreach ($suppliers as $supplier)
                      <option value="{{ $supplier->SupplierID }}" {{ old('supplier')==$supplier->SupplierID ? 'selected'
                        : '' }}>
                        {{ $supplier->SupplierName }}
                      </option>
                      @endforeach
                      <option value="Lainnya" {{ old('supplier')=='Lainnya' ? 'selected' : '' }}>- Tambah Baru -</option>
                    </select>
                    @if($errors->has('supplier'))
                    <span class="error invalid-feedback">{{ $errors->first('supplier') }}</span>
                    @endif

                    <input type="text" name="other_supplier" id="other_supplier"
                      class="form-control mt-2 {{ old('other_supplier') ? '' : 'd-none' }} @if($errors->has('other_supplier')) is-invalid @endif"
                      placeholder="Isi Nama Supplier" value="{{ old('other_supplier') }}" autocomplete="off">
                    @if($errors->has('other_supplier'))
                    <span class="error invalid-feedback">{{ $errors->first('other_supplier') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="purchase_date">Tanggal</label>
                    <input type="datetime-local" name="purchase_date" id="purchase_date"
                      placeholder="Masukan Nama Produk" value="{{ old('purchase_date') }}"
                      class="form-control @if($errors->has('purchase_date')) is-invalid @endif" required>
                    @if($errors->has('purchase_date'))
                    <span class="error invalid-feedback">{{ $errors->first('purchase_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="invoice_number">No. Invoice</label>
                    <input type="text" name="invoice_number" id="invoice_number" placeholder="Masukkan Nomor Invoice"
                      class="form-control @if($errors->has('invoice_number')) is-invalid @endif" required>
                    @if($errors->has('invoice_number'))
                    <span class="error invalid-feedback">{{ $errors->first('invoice_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="invoice_image">File Invoice</label>
                    <input type="file" name="invoice_image" id="invoice_image"
                      class="form-control @if($errors->has('invoice_image')) is-invalid @endif">
                    @if($errors->has('invoice_image'))
                    <span class="error invalid-feedback">{{ $errors->first('invoice_image') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <hr>
              <h4>Detail Produk</h4>
              <div id="purchase-detail" class="row mb-3">
                <div class="col-12">
                  <div class="form-group">
                    <label for="product">Nama Produk</label>
                    <a class="btn btn-sm float-right remove"><i class="far fa-times-circle fa-lg text-danger"></i></a>
                    <select title="Pilih Produk" name="product[]" data-live-search="true"
                      class="form-control selectpicker border select-product" required>
                      @foreach ($products as $product)
                      <option value="{{ $product->ProductID }}" {{ collect(old('product'))->
                        contains($product->ProductID) ? 'selected' : '' }}>
                        {{ $product->ProductName.' -- Isi: '. $product->ProductUOMDesc . ' ' .
                        $product->ProductUOMName
                        }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="quantity">Kuantiti</label>
                    <input type="number" id="quantity" name="quantity[]" class="form-control"
                      value="{{ collect(old('quantity')) }}" placeholder="Masukkan Jumlah Kuantiti" required>
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="purchase_price">Harga Beli</label>
                    <input type="number" id="purchase_price" name="purchase_price[]" class="form-control "
                      value="{{ collect(old('purchase_price')) }}" placeholder="Masukkan Harga Beli" required>
                  </div>
                </div>
                <br>
              </div>
              <div id="purchase-detail-append"></div>
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
<script>
  $('#supplier').on('change', function() {
    if ($(this).val() == 'Lainnya') {
      $('#other_supplier').removeClass('d-none');
    } else {
      $('#other_supplier').addClass('d-none');
    }
  });

  // Cloning Form Term Product
  $('#purchase-detail:first .remove').css("visibility", "hidden");
  $('.add').on('click', function () {
      $('#purchase-detail:first .remove').css("visibility", "visible");
      cloneElement('#purchase-detail:first', '#purchase-detail-append');
      $('#purchase-detail:first .remove').css("visibility", "hidden");
  });

  $('body').on('click', '.remove', function() {
      let closest = $(this).closest('#purchase-detail').remove();
  });
</script>
@endsection