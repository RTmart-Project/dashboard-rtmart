@extends('layouts.master')
@section('title', 'Dashboard - Add Mutation Stock')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
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
                      <h5>Apakah Qty Mutasi yang di-input sudah benar?</h5>
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
                      <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Mutasi Stok</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah yakin ingin membuat Mutasi Stok?</h5>
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
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/main/js/helper/clone-element.js"></script>
<script>
  $("#purchase").on('change', function () {
    const purchaseID = $(this).val();
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
    
    $.ajax({
      type: "get",
      url: "/stock/mutation/getProductByPurchaseID/" + purchaseID,
      success: function (data) {
        let note = '';
        if (data.length > 1) {
          note = '<small class="form-text text-muted">Isi dengan 0 jika tidak ingin mutasi produk ini</small>';
        }

        let div = '';
        $.each(data, function(index, value){
            div += `<div class="col-md-5 col-12">
                      <div class="form-group">
                        <label>Nama Produk</label>
                        <input class="form-control" value="${value.ProductID} - ${value.ProductName}" readonly>
                        <input type="hidden" name="product_id[]" value="${value.ProductID}">
                      </div>
                    </div>
                    <div class="col-md-2 col-12">
                      <div class="form-group">
                        <label>Qty Tersedia</label>
                        <input class="form-control" value="${value.QtyReady}" readonly>
                      </div>
                    </div>
                    <div class="col-md-2 col-12">
                      <div class="form-group">
                        <label>Harga Beli</label>
                        <input class="form-control" value="${thousands_separators(value.PurchasePrice)}" readonly>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <label>Qty Mutasi</label>
                        <input type="number" class="form-control qty_mutation" name="qty_mutation[]" placeholder="Masukkan Qty yang ingin di-mutasi" required>
                        ${note}
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
    let distributorID = $("#distributor").val();
    let mutationDate = $("#mutation_date").val();

    let arrayQtyMutation = [];
    $("#product-detail").find(".qty_mutation").each(function(){
      arrayQtyMutation.push($(this).val());
    });
    let qtyMutation = jQuery.inArray("", arrayQtyMutation)
    
    if (purchaseID == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi Sumber Purchase!",
      });
    } else if (distributorID == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi Distributor!",
      });
    } else if (mutationDate == "") {
      Toast.fire({
        icon: "error",
        title: " Harap isi Tanggal Mutasi!",
      });
    } else if (qtyMutation != -1) {
      Toast.fire({
        icon: "error",
        title: " Harap isi Qty Mutasi!",
      });
    } 
    else {
      $("#konfirmasi").modal("show");
    }
  })
</script>
@endsection