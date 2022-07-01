@extends('layouts.master')
@section('title', 'Dashboard - Add Mutation Stock')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Mutasi Stok')

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
            <a href="{{ route('stock.mutation') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="add-mutation" method="post" action="{{ route('stock.storeMutation') }}">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="purchase">Sumber Purchase ID</label>
                    <select name="purchase" id="purchase" data-live-search="true" title="Pilih Purchase"
                      class="form-control selectpicker border @if($errors->has('purchase')) is-invalid @endif"
                      required>
                      @foreach ($purchases as $purchase)
                      <option value="{{ $purchase->PurchaseID }}" data-distributor-id="{{ $purchase->DistributorID }}">
                        {{ $purchase->PurchaseID }} - {{ $purchase->DistributorName }} - {{ $purchase->InvestorName }}
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('purchase'))
                    <span class="error invalid-feedback">{{ $errors->first('purchase') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="distributor">Mutasi ke Distributor</label>
                    <select name="distributor" id="distributor" data-live-search="true" title="Pilih Distributor"
                      class="form-control selectpicker border @if($errors->has('distributor')) is-invalid @endif"
                      required>
                    </select>
                    @if($errors->has('distributor'))
                    <span class="error invalid-feedback">{{ $errors->first('distributor') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="mutation_date">Tanggal Mutasi</label>
                    <input type="datetime-local" name="mutation_date" id="mutation_date" value="{{ old('mutation_date') }}"
                      class="form-control @if($errors->has('mutation_date')) is-invalid @endif" required>
                    @if($errors->has('mutation_date'))
                    <span class="error invalid-feedback">{{ $errors->first('mutation_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea class="form-control @if($errors->has('notes')) is-invalid @endif" 
                      name="notes" id="notes" rows="3" placeholder="Masukkan Catatan (opsional)"></textarea>
                    @if($errors->has('notes'))
                    <span class="error invalid-feedback">{{ $errors->first('notes') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <hr>
              {{-- <h4>Detail Produk</h4>
              <div id="wrapper-purchase-detail">
                <div id="purchase-detail" class="row mb-3">
                  <div class="col-12">
                    <a class="btn btn-sm float-right remove"><i class="far fa-times-circle fa-lg text-danger"></i></a>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="product">Nama Produk</label>
                      <select title="Pilih Produk" name="product[]" data-live-search="true"
                        class="form-control selectpicker border select-product" required>
                        @foreach ($products as $product)
                        <option value="{{ $product->ProductID }}" 
                          {{ collect(old('product'))->contains($product->ProductID) ? 'selected' : '' }}>
                          {{ $product->ProductID.' - '. $product->ProductName.' -- Isi: '. $product->ProductUOMDesc . ' ' . $product->ProductUOMName }}
                        </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 col-12">
                    <div class="form-group">
                      <label for="labeling">Label Produk</label>
                      <select title="Pilih Labeling Produk" name="labeling[]" id="labeling"
                        class="form-control selectpicker border" required>
                        <option value="PKP"
                          {{ collect(old('labeling'))->contains('PKP') ? 'selected' : '' }}>
                          PKP
                        </option>
                        <option value="NON-PKP"
                          {{ collect(old('labeling'))->contains('NON PKP') ? 'selected' : '' }}>
                          NON PKP
                        </option>
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
                        value="{{ collect(old('purchase_price')) }}" placeholder="Masukkan Harga Beli" required autocomplete="off">
                    </div>
                  </div>
                  <br>
                </div>
                <div id="purchase-detail-append"></div>
              </div>
              <div class="clearfix">
                <a class="btn btn-sm add float-right"><i class="fas fa-plus-circle fa-lg"></i></a>
              </div> --}}

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
                      <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Purchase Stock</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah yakin ingin membuat Purchase Stock?</h5>
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
<script>
  $("#purchase").on('change', function () {
    const distributorID = $(this).find(':selected').data('distributor-id');
    
    $.ajax({
      type: "get",
      url: "/stock/mutation/getExcludeDistributorID/" + distributorID,
      success: function (data) {
        let option = '';
        for (const item of data) {
            option += `<option value="${item.DistributorID}">${item.DistributorName}</option>`;
        }
        $("#distributor").html(option);
        $('.selectpicker').selectpicker('refresh');
      },
    });
  })
</script>
@endsection