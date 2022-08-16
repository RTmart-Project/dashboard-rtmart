@extends('layouts.master')
@section('title', 'Dashboard - Add Promo Stock')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Tambah Stok Promo')

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
            <a href="{{ route('stockPromo.inbound') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="add-inbound-promo" method="post" action="{{ route('stockPromo.storeByPurchase') }}">
              @csrf
              <div class="row">
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="purchase">Sumber Purchase ID</label>
                    <select name="purchase" id="purchase" data-live-search="true" title="Pilih Purchase"
                      class="form-control selectpicker border @if($errors->has('purchase')) is-invalid @endif"
                      required>
                      @foreach ($purchases as $purchase)
                      <option value="{{ $purchase->PurchaseID }}">
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
                    <label for="inbound_date">Tanggal Barang Masuk</label>
                    <input type="datetime-local" name="inbound_date" id="inbound_date" value="{{ old('inbound_date') }}"
                      class="form-control @if($errors->has('inbound_date')) is-invalid @endif" required>
                    @if($errors->has('inbound_date'))
                    <span class="error invalid-feedback">{{ $errors->first('inbound_date') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <hr>
              <h4>Detail Produk</h4>
              <div id="product-detail" class="row mb-3">
                <div class="col-12 note-product-detail">
                  <p>*Pilih Sumber Purchase terlebih dahulu</p>
                </div>
              </div>

              <div class="form-group float-right mt-4">
                <button type="button" class="btn btn-success btn-simpan">Simpan</button>
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
                      <h5>Apakah data yang di-input sudah benar?</h5>
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
                      <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Stok Promo</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah yakin ingin membuat Stok Promo?</h5>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                        data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-sm btn-success btn-create-mutation" data-toggle="modal">
                        Yakin <i class="fas fa-circle-notch fa-spin d-none loader"></i>
                      </button>
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
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script>
  $("#purchase").on('change', function () {
    const purchaseID = $(this).val();
    
    $.ajax({
      type: "get",
      url: "/stock/mutation/getProductByPurchaseID/" + purchaseID,
      success: function (data) {
        let note = '';
        if (data.length > 1) {
          note = `<li>Isi dengan 0 jika tidak ingin memindahkan produk ini</li>`;
        }

        let div = '';
        $.each(data, function(index, value){
            div += `<div class="col-md-5 col-12">
                      <div class="form-group">
                        <label>Nama Produk</label>
                        <input class="form-control" value="${value.ProductID} - ${value.ProductName} (${value.ProductCategoryName} ${value.ProductUOMDesc} ${value.ProductUOMName}) - ${value.ProductLabel}" readonly>
                        <input type="hidden" name="product_id[]" value="${value.ProductID}">
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="form-group">
                        <label>Qty Tersedia</label>
                        <input class="form-control" value="${value.QtyReady}" readonly>
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="form-group">
                        <label>Harga Beli/pcs</label>
                        <input class="form-control purchase_price" type="number" name="purchase_price[]">
                      </div>
                    </div>
                    <div class="col-md-1 col-12">
                      <div class="form-group">
                        <label>Harga Jual/pcs</label>
                        <input class="form-control selling_price" type="number" name="selling_price[]">
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label>Qty u/ Stok Promo</label>
                        <input type="number" class="form-control qty_mutation" name="qty_mutation[]" placeholder="Masukkan Qty u/ stok promo" required>
                        <ul class="pl-4">
                          ${note}
                          <li>Qty yang dipindahkan akan dikalikan dengan ${value.ProductUOMDesc}</li>
                        </ul>
                      </div>
                    </div>`;
        });
        $('#product-detail').html(div);
      },
    });

    
  })

  let Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
  });

  $(".btn-simpan").on("click", function () {
    let purchaseID = $("#purchase").val();
    let mutationDate = $("#inbound_date").val();

    let arrayQtyMutation = [];
    $("#product-detail").find(".qty_mutation").each(function(){
      arrayQtyMutation.push($(this).val());
    });
    let qtyMutation = jQuery.inArray("", arrayQtyMutation)

    let arrayPurchasePrice = [];
    $("#product-detail").find(".purchase_price").each(function(){
      arrayPurchasePrice.push($(this).val());
    });
    let purchasePrice = jQuery.inArray("", arrayPurchasePrice)

    let arraySellingPrice = [];
    $("#product-detail").find(".selling_price").each(function(){
      arraySellingPrice.push($(this).val());
    });
    let sellingPrice = jQuery.inArray("", arraySellingPrice)
    
    if (purchaseID == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi Sumber Purchase!",
      });
    } else if (mutationDate == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi Tanggal!",
      });
    } else if (purchasePrice != -1) {
      Toast.fire({
        icon: "error",
        title: " Harap isi Harga Beli!",
      });
    } else if (sellingPrice != -1) {
      Toast.fire({
        icon: "error",
        title: " Harap isi Harga Jual!",
      });
    } else if (qtyMutation != -1) {
      Toast.fire({
        icon: "error",
        title: " Harap isi Qty Stok Promo!",
      });
    } else {
      $("#konfirmasi").modal("show");
    }
  })

  $(".btn-create-mutation").on("click", function (e) {
    $(this).prop("disabled", true);
    $(this).children().removeClass("d-none");
    $("#add-inbound-promo").submit();
  })
</script>
@endsection