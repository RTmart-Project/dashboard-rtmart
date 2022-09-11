@extends('layouts.master')
@section('title', 'Dashboard - Buat Harga Pengajuan')

@section('css-pages')
@endsection

@section('header-menu', 'Buat Harga Pengajuan')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <!-- left -->
      <div class="col-sm-6">
        <h1 class="m-0"></h1>
      </div>
      <!-- Right -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"></li>
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
            <a href="{{ route('distribution.restock') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i> Kembali</a>
          </div>
          <div class="card-body mt-2">
            <div class="row">
              <div class="col-12 col-md-3 mb-2">
                <strong>Stock Order ID</strong>
                <p>{{ $data->StockOrderID }} <br> ({{ $data->StatusOrder }})</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Tanggal Order</strong>
                <p>{{ date('d F Y H:i', strtotime($data->CreatedDate)) }}</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Distributor</strong>
                <p>{{ $data->DistributorName }}</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Toko</strong>
                <p>{{ $data->MerchantID }} - {{ $data->StoreName }} - {{ $data->OwnerFullName }} - {{ $data->PhoneNumber }}</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Alamat Toko</strong>
                <p>{{ $data->StoreAddress }}</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Partner</strong>
                <p>{{ $data->Partner ? $data->Partner : '-' }}</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Metode Pembayaran</strong>
                <p>{{ $data->PaymentMethodName }}</p>
              </div>
              <div class="col-12 col-md-3 mb-2">
                <strong>Sales</strong>
                <p>{{ $data->SalesCode }} {{ $data->SalesName }}</p>
              </div>
              <div class="col-12">
                @if ($data->StatusOrderID === "S009" || $data->StatusOrderID === "S010" || $data->StatusOrderID === "S023")
                <form action="{{ route('distribution.storePriceSubmission', ['stockOrderID' => $data->StockOrderID]) }}" method="POST" id="add-price-submission">
                  @csrf
                  <strong>Detail Produk</strong>
                  @foreach ($data->Detail as $item)
                  <div class="row wrapper-product">
                    <div class="col-12 col-md-2">
                      <div class="form-group">
                        <label for="product_id">Produk ID</label>
                        <input type="text" class="form-control" name="product_id[]" id="product_id" value="{{ $item->ProductID }}" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-2">
                      <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" class="form-control" value="{{ $item->ProductName }}" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-1">
                      <div class="form-group">
                        <label>Quantity</label>
                        <input type="text" class="form-control qty" value="{{ $item->PromisedQuantity }}" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-2">
                      <div class="form-group">
                        <label>Harga Asli</label>
                        <input type="text" class="form-control price autonumeric" value="{{ $item->Nett }}" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-2">
                      <div class="form-group">
                        <label for="price_submission">Harga Pengajuan</label>
                        <input type="text" class="form-control price_submission autonumeric" name="price_submission[]" id="price_submission" required autocomplete="off">
                        @if ($data->Detail->count() > 1)
                        <small>Jika tidak ada harga pengajuan, masukkan harga asli</small>
                        @endif
                      </div>
                    </div>
                    <div class="col-12 col-md-2">
                      <div class="form-group">
                        <label>Voucher</label>
                        <input type="text" class="form-control voucher_product" value="0" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-1">
                      <div class="form-group">
                        <label>% Voucher</label>
                        <input type="text" class="form-control percent_voucher" readonly>
                      </div>
                    </div>
                  </div>
                  @endforeach
                  
                  <div class="row">
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                        <label>Total Price</label>
                        <input type="text" class="form-control total_price autonumeric" name="total_price" value="{{ $data->TotalPrice }}" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                        <label>Total Voucher</label>
                        <input type="text" class="form-control total_voucher" name="total_voucher" value="0" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="form-group">
                        <label>Nett Price</label>
                        <input type="text" class="form-control nett_price" name="nett_price" value="{{ Helper::formatCurrency($data->NettPrice, "") }}" readonly>
                      </div>
                    </div>
                  </div>

                  <div class="form-group float-right mt-4">
                    <button type="button" class="btn btn-success" id="btn-save">Simpan</button>
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
                          <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Harga Pengajuan</h6>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <h5>Apakah yakin ingin membuat Harga Pengajuan?</h5>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-sm btn-success btn-create-price-submission" data-toggle="modal">
                            Yakin <i class="fas fa-circle-notch fa-spin d-none loader"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<script src="https://unpkg.com/autonumeric"></script>
<script>
  // Set seperator '.' currency
  new AutoNumeric.multiple('.autonumeric', {
    allowDecimalPadding: false,
    decimalCharacter: ',',
    digitGroupSeparator: '.',
    unformatOnSubmit: true
  });

  $(".price_submission").on("keyup", function () {
    const thisForm = $(this).closest('.wrapper-product');

    const priceSubmission = $(this).val().replaceAll(".", "");
    const sellingPrice = thisForm.find('.price').val().replaceAll(".", "");
    const qty = thisForm.find('.qty').val();
    
    const valueSelling = qty * sellingPrice;
    const valueSubmission = qty * priceSubmission;

    const voucher = valueSelling - valueSubmission;
    const percentVoucher = Math.round(voucher / valueSelling * 100 * 100) / 100;
    
    thisForm.find('.voucher_product').val(thousands_separators(voucher));
    thisForm.find('.percent_voucher').val(percentVoucher);

    let totalVoucher = 0;
    $(".wrapper-product").each(function () {
      const voucherProduct = $(this).find('.voucher_product').val().replaceAll(".", "");
      totalVoucher += Number(voucherProduct);
    })
    
    const totalPrice = $(".total_price").val().replaceAll(".", "");
    const nettPrice = totalPrice - totalVoucher;

    $(".total_voucher").val(thousands_separators(totalVoucher));
    $(".nett_price").val(thousands_separators(nettPrice));
  });

  $("#btn-save").on("click", function () {
    $('#konfirmasi').modal('show');
  })

  $("#add-price-submission").on("submit", function (e) {
    $('.btn-create-price-submission').prop("disabled", true);
    $('.btn-create-price-submission').children().removeClass("d-none");
  })
</script>
@endsection