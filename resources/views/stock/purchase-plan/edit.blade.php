@extends('layouts.master')
@section('title', 'Dashboard - Edit Purchase Plan')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Edit Purchase Plan')

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
            <a href="{{ route('stock.purchasePlan') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <form id="edit-purchase-plan" method="post" action="{{ route('stock.updatePurchasePlan', ['purchasePlanID' => $data->PurchasePlanID]) }}">
              @csrf
              <div class="row">
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="purchase_plan_id">Purchase Plan ID</label>
                    <input type="text" value="{{ $data->PurchasePlanID }}" readonly class="form-control">
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="investor">Investor</label>
                    <select name="investor" id="investor" data-live-search="true" title="Pilih Investor"
                      class="form-control selectpicker border @if($errors->has('investor')) is-invalid @endif" required>
                      @foreach ($investors as $investor)
                      <option value="{{ $investor->InvestorID }}" {{ $data->InvestorID == $investor->InvestorID ? 'selected' : '' }}>
                        {{ $investor->InvestorName }}
                      </option>
                      @endforeach
                      {{-- <option value="Lainnya" {{ old('investor')=='Lainnya' ? 'selected' : '' }}>- Tambah Baru -</option> --}}
                    </select>
                    <input type="hidden" id="investor-interest" value="{{ $data->Interest }}">
                    @if($errors->has('investor'))
                    <span class="error invalid-feedback">{{ $errors->first('investor') }}</span>
                    @endif

                    <span id="note-purchase-detail">
                      *Jika ganti pilihan investor maka detail produk akan ter-reset
                    </span>

                    {{-- <input type="text" name="other_investor" id="other_investor"
                      class="form-control mt-2 {{ old('other_investor') ? '' : 'd-none' }} @if($errors->has('other_investor')) is-invalid @endif"
                      placeholder="Isi Nama investor" value="{{ old('other_investor') }}" autocomplete="off">
                    @if($errors->has('other_investor'))
                    <span class="error invalid-feedback">{{ $errors->first('other_investor') }}</span> --}}
                    {{-- @endif --}}
                  </div>
                </div>
                <div class="col-md-4 col-12">
                  <div class="form-group">
                    <label for="purchase_plan_date">Tanggal Purchase Plan</label>
                    <input type="datetime-local" name="purchase_plan_date" id="purchase_plan_date" value="{{ date('Y-m-d\TH:i', strtotime($data->PlanDate)) }}"
                      class="form-control @if($errors->has('purchase_plan_date')) is-invalid @endif" required>
                    @if($errors->has('purchase_plan_date'))
                    <span class="error invalid-feedback">{{ $errors->first('purchase_plan_date') }}</span>
                    @endif
                  </div>
                </div>
              </div>

              <hr>
              
              <h4>Detail Produk</h4>
              <div id="main-wrapper-purchase-detail">
                <div id="wrapper-purchase-detail">
                  @foreach ($dataDetail as $item)
                  <div id="purchase-detail" class="row mb-3 purchase-detail">
                    <div class="col-12">
                      <a class="btn btn-sm float-right remove"><i class="far fa-times-circle fa-lg text-danger"></i></a>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="distributor">Distributor</label>
                        <select title="Pilih Distributor" name="distributor[]" data-live-search="true"
                          class="form-control selectpicker border select-distributor" required>
                          @foreach ($distributors as $distributor)
                          <option value="{{ $distributor->DistributorID }}" {{ $item->DistributorID == $distributor->DistributorID ? 'selected' : '' }}>
                            {{ $distributor->DistributorName }}
                          </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="supplier">Supplier</label>
                        <select name="supplier[]" id="supplier" data-live-search="true" title="Pilih Supplier"
                          class="form-control selectpicker border select-supplier" required>
                          @foreach ($suppliers as $supplier)
                          <option value="{{ $supplier->SupplierID }}" {{ $item->SupplierID == $supplier->SupplierID ? 'selected' : '' }}>
                            {{ $supplier->SupplierName }}
                          </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="note">Keterangan</label>
                        <input type="text" id="note" name="note[]" class="form-control note" value="{{ $item->Note }}" placeholder="Masukkan Keterangan">
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="product">Nama Produk</label>
                        <select title="Pilih Produk" name="product[]" data-live-search="true"
                          class="form-control selectpicker border select-product" required>
                          @foreach ($products as $product)
                          <option value="{{ $product->ProductID }}" {{ $item->ProductID == $product->ProductID ? 'selected' : '' }}>
                            {{ $product->ProductID.' - '. $product->ProductName.' -- Isi: '. $product->ProductUOMDesc . ' ' . $product->ProductUOMName }}
                          </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="labeling">Label Produk</label>
                        <select title="Pilih Labeling Produk" name="labeling[]" id="labeling"
                          class="form-control selectpicker border select-labeling" required>
                          <option value="PKP" {{ $item->ProductLabel == 'PKP' ? 'selected' : '' }}>PKP</option>
                          <option value="NON-PKP" {{ $item->ProductLabel == 'NON-PKP' ? 'selected' : '' }}>NON PKP</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="quantity">Kuantiti</label>
                        <input type="number" id="quantity" name="quantity[]" class="form-control quantity"
                          value="{{ $item->Qty }}" placeholder="Masukkan Kuantiti" required>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="quantity_po">Kuantiti PO</label>
                        <input type="number" id="quantity_po" name="quantity_po[]" class="form-control quantity-po"
                          value="{{ $item->QtyPO }}" placeholder="Masukkan Kuantiti PO" required>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="percentage_po">Percent PO</label>
                        <div class="input-group mb-3">
                          <input type="number" id="percentage_po" name="percentage_po[]" class="form-control percentage-po"
                            value="{{ round($item->QtyPO / $item->Qty * 100, 0) }}" readonly>
                          <div class="input-group-append">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="purchase_price">Harga Beli</label>
                        <input type="text" id="purchase_price" name="purchase_price[]" class="form-control purchase-price autonumeric"
                          value="{{ $item->PurchasePrice }}" placeholder="Masukkan Harga Beli" required autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="purchase_value">Value Beli</label>
                        <input type="text" id="purchase_value" name="purchase_value[]" class="form-control purchase-value"
                          value="{{ $item->PurchaseValue }}" readonly>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="selling_price">Harga Jual</label>
                        <input type="text" id="selling_price" name="selling_price[]" class="form-control selling-price autonumeric"
                          value="{{ $item->SellingPrice }}" placeholder="Masukkan Harga Jual" required autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="selling_value">Value Jual</label>
                        <input type="text" id="selling_value" name="selling_value[]" class="form-control selling-value"
                          value="{{ $item->SellingValue }}" readonly>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="interest" class="label-interest">Bunga {{ $data->InvestorName }} ({{ $item->PercentInterest }}%)</label>
                        <input type="text" id="interest" name="interest[]" class="form-control interest"
                          value="{{ $item->InterestValue }}" readonly>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="gross_margin">Gross Margin</label>
                        <input type="text" id="gross_margin" name="gross_margin[]" class="form-control gross-margin"
                          value="{{ ($item->GrossMargin) }}" readonly>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="margin_ctn">Margin /ctn</label>
                        <input type="text" id="margin_ctn" name="margin_ctn[]" class="form-control margin-ctn"
                          value="{{ $item->MarginCtn }}" readonly>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="percent_voucher">% Voucher</label>
                        <input type="text" name="percent_voucher[]" id="percent_voucher" class="form-control percent-voucher" 
                          value="{{ $item->PercentVoucher }}">
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="value_voucher">Value Voucher</label>
                        <input type="text" name="value_voucher[]" id="value_voucher" readonly value="{{ $item->VoucherValue }}" class="form-control voucher-value">
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="nett_margin">Nett Margin</label>
                        <input type="text" id="nett_margin" name="nett_margin[]" class="form-control nett-margin"
                          value="{{ $item->GrossMargin - $item->InterestValue - $item->VoucherValue }}" readonly>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="percent_margin">Percent Margin</label>
                        <div class="input-group mb-3">
                          <input type="number" id="percent_margin" name="percent_margin[]" class="form-control percent-margin"
                            value="{{ round(($item->GrossMargin - $item->InterestValue - $item->VoucherValue) / $item->SellingValue * 100, 0) }}" readonly>
                          <div class="input-group-append">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock[]" class="form-control stock"
                          value="{{ $item->LastStock }}" required readonly>
                      </div>
                    </div>
                    <br>
                  </div>
                  @endforeach
                  <div id="purchase-detail-append"></div>
                </div>
                <div class="clearfix">
                  <a class="btn btn-sm add float-right"><i class="fas fa-plus-circle fa-lg"></i></a>
                </div>
              </div>

              @if ($data->StatusID === 8)
              <div class="form-group float-right mt-4">
                <button type="button" class="btn btn-warning" id="btn-save">Simpan</button>
              </div>
              @endif
              
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
                      <button type="button" class="btn btn-sm btn-warning" data-target="#konfirmasi2"
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
                      <h6 class="modal-title"><i class="far fa-question-circle"></i> Ubah Purchase Plan</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah yakin ingin mengubah Purchase Plan?</h5>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-sm btn-warning btn-edit-purchase-plan" data-toggle="modal">
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
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{url('/')}}/main/js/helper/clone-element.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
  // Set seperator '.' currency
  new AutoNumeric.multiple('.autonumeric', {
    allowDecimalPadding: false,
    decimalCharacter: ',',
    digitGroupSeparator: '.',
    unformatOnSubmit: true
  });
  
  $('#investor').on('change', function() {
    const investorID = $(this).val();
    $("#note-purchase-detail").addClass("d-none");
    $("#main-wrapper-purchase-detail").removeClass("d-none");

    $("#wrapper-purchase-detail select").val("");
    $("#wrapper-purchase-detail input").val("");

    $('.selectpicker').selectpicker('refresh');

    if (investorID == 'Lainnya') {
      $('#other_investor').removeClass('d-none');
      $("#investor-interest").val(0);
    } else {
      $('#other_investor').addClass('d-none');

      $.ajax({
        type: "get",
        url: `/investor/${investorID}`,
        success: function (response) {
          $("#investor-interest").val(response.Interest);
          $(".label-interest").html(`Bunga ${response.InvestorName} (${response.Interest}%)`);
        }
      });
    }
  });

  $("#wrapper-purchase-detail").on("change", ".select-distributor select, .select-product select, .select-labeling select", function () {
    const thisForm = $(this).closest(".purchase-detail");
    const investorID = $("#investor").val();
    const distributorID = thisForm.find(".select-distributor select").val();
    const productID = thisForm.find(".select-product select").val();
    const productLabel = thisForm.find(".select-labeling select").val();
    
    $.ajax({
      type: "get",
      url: `/stock/opname/sumOldProduct/${distributorID}/${investorID}/${productID}/${productLabel}`,
      success: function (response) {
        const res = $.parseJSON(response);
        thisForm.find(".stock").val(res.goodStock);
      }
    });
  });

  $("#wrapper-purchase-detail").on("keyup", ".quantity, .quantity-po, .purchase-price, .selling-price, .percent-voucher", function () {
    const investorInterest = $("#investor-interest").val();
    const thisForm = $(this).closest(".purchase-detail");
    const quantity = thisForm.find(".quantity").val();
    const quantityPO = thisForm.find(".quantity-po").val();
    const purchasePrice = thisForm.find(".purchase-price").val().replaceAll(".", "");
    const sellingPrice = thisForm.find(".selling-price").val().replaceAll(".", "");
    const percentVoucher = thisForm.find(".percent-voucher").val();

    let percentagePO;
    const purchaseValue = quantity * purchasePrice;
    const sellingValue = quantity * sellingPrice;
    const interest = (investorInterest * purchaseValue / 100).toFixed(0);
    const grossMargin = sellingValue - purchaseValue;
    const marginCtn = sellingPrice - purchasePrice;
    const voucherValue = Math.round(percentVoucher / 100 * sellingValue);
    const nettMargin = grossMargin - interest - voucherValue;
    let percentageMargin;

    if (quantityPO) {
      percentagePO = quantityPO / quantity * 100;
      percentagePO = Math.round(percentagePO * 100) / 100; // buat ngebuletin 2 angka decimal
    }
    if (sellingValue > 0) {
      percentageMargin = nettMargin / sellingValue * 100;
      percentageMargin = Math.round(percentageMargin * 100) / 100; // buat ngebuletin 2 angka decimal
    }
    thisForm.find(".percentage-po").val(percentagePO);
    thisForm.find(".purchase-value").val(thousands_separators(purchaseValue));
    thisForm.find(".selling-value").val(thousands_separators(sellingValue));
    thisForm.find(".interest").val(thousands_separators(interest));
    thisForm.find(".gross-margin").val(thousands_separators(grossMargin));
    thisForm.find(".margin-ctn").val(thousands_separators(marginCtn));
    thisForm.find(".nett-margin").val(thousands_separators(nettMargin));
    thisForm.find(".percent-margin").val(percentageMargin);
    thisForm.find(".voucher-value").val(thousands_separators(voucherValue));
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

  let Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
  });

  $("#btn-save").on("click", function () {
    const investorID = $("#investor").val();
    const purchasePlanDate = $("#purchase_plan_date").val();

    if (investorID == "") {
      Toast.fire({
        icon: "error",
        title: "Harap Pilih Investor!",
      });
    } else if (!purchasePlanDate) {
      Toast.fire({
        icon: "error",
        title: "Harap Isi Tanggal Purchase Plan!",
      });
    }
    if (investorID && purchasePlanDate) {
      $('#konfirmasi').modal('show');
    }
  })

  $("#edit-purchase-plan").on("submit", function (e) {
    $('.btn-edit-purchase-plan').prop("disabled", true);
    $('.btn-edit-purchase-plan').children().removeClass("d-none");
  })
</script>
@endsection