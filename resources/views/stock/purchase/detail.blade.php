@extends('layouts.master')
@section('title', 'Dashboard - Detail Purchase Stock')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Detail Purchase Stock')

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
            <a href="{{ route('stock.purchase') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="purchase-stock">
                <div class="row">
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-file-invoice mr-1"></i> Purchase ID</strong>
                    <p class=" m-0">{{ $purchaseByID->PurchaseID }}</p>
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Distributor</strong>
                    <p class="m-0">{{ $purchaseByID->DistributorName ? $purchaseByID->DistributorName : $purchaseByID->DistributorCombined }}</p>
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-money-bill-wave mr-1"></i> Investor</strong>
                    @if ($purchaseByID->InvestorName)
                    <p class="m-0">{{ $purchaseByID->InvestorName }}</p>
                    @else
                    <p class="m-0">-</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-truck-loading mr-1"></i> Supplier</strong>
                    <p class="m-0">{{ $purchaseByID->SupplierName ? $purchaseByID->SupplierName : $purchaseByID->SupplierCombined }}</p>
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-calendar-day mr-1"></i> Tanggal Pembelian</strong>
                    <p class="m-0">{{ date('d F Y\, H:i', strtotime($purchaseByID->PurchaseDate)) }}</p>
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-calendar-day mr-1"></i> Tanggal Estimasi Tiba</strong>
                    <p class="m-0">{{ date('d F Y\, H:i', strtotime($purchaseByID->EstimationArrive)) }}</p>
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-file-alt mr-1"></i> Invoice</strong><br>
                    <p class="m-0">{{ $purchaseByID->InvoiceNumber }}</p>
                    @if ($purchaseByID->InvoiceFile != NULL)
                    <a href="{{ config('app.base_image_url').'stock_invoice/'.$purchaseByID->InvoiceFile }}"
                      target="_blank">{{ $purchaseByID->InvoiceFile }}</a>
                    @else
                    <p>-</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-info mr-1"></i> Status</strong><br>
                    @if ($purchaseByID->StatusID == 1)
                    <p style="font-size: 13px" class="badge badge-warning">{{ $purchaseByID->StatusName }}</p>
                    @elseif($purchaseByID->StatusID == 2)
                    <p style="font-size: 13px" class="badge badge-success">{{ $purchaseByID->StatusName }}</p>
                    @elseif($purchaseByID->StatusID == 4)
                    <p style="font-size: 13px" class="badge badge-info">{{ $purchaseByID->StatusName }}</p>
                    @else
                    <p style="font-size: 13px" class="badge badge-danger">{{ $purchaseByID->StatusName }}</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-user-edit mr-1"></i> Dibuat oleh</strong>
                    <p class="m-0">{{ $purchaseByID->CreatedBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($purchaseByID->CreatedDate)) }}</small>
                  </div>
                  <div class="col-12 col-md-3 mb-2">
                    <strong><i class="fas fa-user-check mr-1"></i> Dikonfirmasi oleh</strong><br>
                    @if ($purchaseByID->StatusBy)
                    <p class="m-0">{{ $purchaseByID->StatusBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($purchaseByID->StatusDate)) }}</small>
                    @else
                    -
                    @endif
                  </div>
                  <div class="col-12 mt-2 table-responsive">
                    <strong><i class="fas fa-cubes mr-1"></i> Detail Produk</strong><br>
                    @if ($purchaseByID->StatusID === 4)
                        @foreach ($purchaseByID->Detail as $detail)
                        <div class="border-bottom border-secondary mb-2 wrapper-product">
                          <form method="POST">
                            @csrf
                            <h6>Produk {{ $loop->iteration }}</h6>
                            <div class="row">
                              <div class="col-12 col-md-11">
                                <div class="row">
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="distributor" class="m-0">Distributor</label>
                                      <input type="text" readonly class="form-control-plaintext p-0" id="distributor" 
                                        value="{{ $detail->DistributorName === null ? $detail->Distributor : $detail->DistributorName }}">
                                    </div>
                                  </div>
                                  <div class="col-11 col-md-3">
                                    <div class="form-group">
                                      <label for="supplier" class="m-0">Supplier</label>
                                      <select name="supplier" id="supplier" class="form-control selectpicker border" data-live-search="true" title="Pilih Supplier" required>
                                        @foreach ($suppliers as $supplier)
                                          <option value="{{ $supplier->SupplierID }}"
                                            {{ $supplier->SupplierID == ($detail->SingleSupplierID === null ? $detail->SupplierID : $detail->SingleSupplierID) ? 'selected' : '' }}>
                                            {{ $supplier->SupplierName }}
                                          </option>
                                        @endforeach
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="product_id" class="m-0">Produk ID</label>
                                      <input type="text" readonly class="form-control-plaintext p-0" id="product_id" name="product_id"
                                        value="{{ $detail->ProductID }}">
                                    </div>
                                  </div>
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="product_name" class="m-0">Nama Produk</label>
                                      <input type="text" readonly class="form-control-plaintext p-0" id="product_name" 
                                        value="{{ $detail->ProductName }} | {{ $detail->ProductLabel }}">
                                    </div>
                                  </div>
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="confirm_date">Tanggal Barang Tiba</label>
                                      <input type="datetime-local" class="form-control" id="confirm_date" name="confirm_date" required>
                                    </div>
                                  </div>
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="qty">Qty</label>
                                      <input type="number" min="0" class="form-control" id="qty" name="qty" value="{{ $detail->Qty }}" required>
                                    </div>
                                  </div>
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="purchase_price">Harga Beli</label>
                                      <input type="text" min="0" class="form-control autonumeric" id="purchase_price" name="purchase_price" value="{{ $detail->PurchasePrice }}" required>
                                    </div>
                                  </div>
                                  <div class="col-12 col-md-3">
                                    <div class="form-group">
                                      <label for="note">Catatan</label>
                                      <textarea name="note" id="note" rows="2" class="form-control"></textarea>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-12 col-md-1 mb-3 d-flex justify-content-center align-items-center">
                                <div class="text-center">
                                  <a class="btn btn-xs mb-1 btn-success btn-approve-product" 
                                    data-purchase-detail-id="{{ $detail->PurchaseDetailID }}" data-product="{{ $detail->ProductName }}" 
                                    data-distributor="{{ $detail->DistributorName === null ? $detail->Distributor : $detail->DistributorName }}">
                                    Terima
                                  </a>
                                  <a class="btn btn-xs mb-1 btn-danger btn-cancel-product"
                                    data-purchase-detail-id="{{ $detail->PurchaseDetailID }}" data-product="{{ $detail->ProductName }}" 
                                    data-distributor="{{ $detail->DistributorName === null ? $detail->Distributor : $detail->DistributorName }}">
                                    Batalkan
                                  </a>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                        @endforeach
                    @else
                    <table class="table table-bordered text-nowrap">
                      <thead>
                        <tr>
                          <th>Distributor</th>
                          <th>Supplier</th>
                          <th>Produk ID</th>
                          <th>Nama Produk</th>
                          <th>Label Produk</th>
                          <th>Qty</th>
                          <th>Harga Beli</th>
                          <th>Total Harga</th>
                          <th>GIT</th>
                          <th>Note</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($purchaseByID->Detail as $detail)
                        <tr>
                          <td>{{ $detail->DistributorName === null ? $detail->Distributor : $detail->DistributorName }}</td>
                          <td>{{ $detail->SupplierName === null ? $detail->Supplier : $detail->SupplierName }}</td>
                          <td>{{ $detail->ProductID }}</td>
                          <td>{{ $detail->ProductName }}</td>
                          <td>{{ $detail->ProductLabel }}</td>
                          <td>{{ $detail->Qty }}</td>
                          <td>{{ Helper::formatCurrency($detail->PurchasePrice, 'Rp ') }}</td>
                          <td>{{ Helper::formatCurrency($detail->Qty * $detail->PurchasePrice, 'Rp ') }}</td>
                          <td>{!! $detail->IsGIT === 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                          <td>{{ $detail->Note }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="6"></td>
                          <th class="text-center">GrandTotal</th>
                          <th>{{ Helper::formatCurrency($purchaseByID->GrandTotal, 'Rp ') }}</th>
                          <td colspan="2"></td>
                        </tr>
                      </tfoot>
                    </table>
                    @endif
                  </div>
                </div>
                {{-- @if ($purchaseByID->StatusBy == null && (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI"))
                <div class="text-center mt-4">
                  <strong>Konfirmasi</strong>
                  <div class="d-flex justify-content-center mt-2" style="gap:10px">
                    <a class="btn btn-sm btn-success btn-approved"
                      data-purchase-id="{{ $purchaseByID->PurchaseID }}">Setujui</a>
                    <a class="btn btn-sm btn-danger btn-reject"
                      data-purchase-id="{{ $purchaseByID->PurchaseID }}">Tolak</a>
                  </div>
                </div>
                @endif --}}
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
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
  let Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
  });

  // Set seperator '.' currency
  new AutoNumeric.multiple('.autonumeric', {
    allowDecimalPadding: false,
    decimalCharacter: ',',
    digitGroupSeparator: '.',
    unformatOnSubmit: true
  });

  // CONFIRM PRODUCT
  $(".btn-approve-product").on("click", function (e) {
    e.preventDefault();
    const purchaseDetailID = $(this).data("purchase-detail-id");
    const product = $(this).data("product");
    const distributor = $(this).data("distributor");

    const thisWrapper = $(this).closest(".wrapper-product");
    const confirmDate = thisWrapper.find("#confirm_date").val();
    const qty = thisWrapper.find("#qty").val();

    thisWrapper.find("form").prop("action", `/stock/purchase/confirmProduct/approve/${purchaseDetailID}`)

    if (!confirmDate) {
      Toast.fire({
        icon: "error",
        title: ` Harap Isi Tanggal ${product} Tiba!`,
      });
    } else if (qty < 1) {
      Toast.fire({
        icon: "error",
        title: ` Qty ${product} Harus Lebih dari 0!`,
      });
    } else {
      $.confirm({
        title: "Terima Produk Purchase!",
        content: `Yakin ingin menerima <b>${product}</b> depo <b>${distributor}</b> ?`,
        closeIcon: true,
        buttons: {
          Yakin: {
            btnClass: "btn-success",
            draggable: true,
            dragWindowGap: 0,
            action: function () {
              thisWrapper.find("form").submit();
            },
          },
          tidak: function () {},
        },
      });
    }
  });

  // CONFIRM PRODUCT
  $(".btn-cancel-product").on("click", function (e) {
    e.preventDefault();
    const purchaseDetailID = $(this).data("purchase-detail-id");
    const product = $(this).data("product");
    const distributor = $(this).data("distributor");

    const thisWrapper = $(this).closest(".wrapper-product");
    const note = thisWrapper.find("#note").val();

    thisWrapper.find("form").prop("action", `/stock/purchase/confirmProduct/cancel/${purchaseDetailID}`)

    if (!note) {
      Toast.fire({
        icon: "error",
        title: ` Harap Isi Alasan Pembatalan ${product} di Catatan!`,
      });
    } else {
      $.confirm({
        title: "Batalkan Produk Purchase!",
        content: `Yakin ingin membatalkan <b>${product}</b> depo <b>${distributor}</b> ?`,
        closeIcon: true,
        buttons: {
          Yakin: {
            btnClass: "btn-red",
            draggable: true,
            dragWindowGap: 0,
            action: function () {
              thisWrapper.find("form").submit();
            },
          },
          tidak: function () {},
        },
      });
    }
  });


  // Event listener saat tombol setujui diklik
  $('.btn-approved').on('click', function (e) {
    e.preventDefault();
    const purchaseID = $(this).data("purchase-id");
    $.confirm({
      title: 'Setujui Purchase Stock!',
      content: `Apakah yakin ingin menyetujui pembelian dengan Purchase ID <b>${purchaseID}</b>?`,
      closeIcon: true,
      type: 'green',
      buttons: {
        setujui: {
          btnClass: 'btn-success',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
              window.location = '/stock/purchase/confirmation/approved/' + purchaseID
          }
        },
        tidak: function () {
        }
      }
    });
  });

  // Event listener saat tombol tolak diklik
  $('.btn-reject').on('click', function (e) {
    e.preventDefault();
    const purchaseID = $(this).data("purchase-id");
    $.confirm({
      title: 'Tolak Purchase Stock!',
      content: `Apakah yakin ingin menolak pembelian dengan Purchase ID <b>${purchaseID}</b>?`,
      closeIcon: true,
      type: 'red',
      buttons: {
        tolak: {
          btnClass: 'btn-red',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
              window.location = '/stock/purchase/confirmation/reject/' + purchaseID
          }
        },
        tidak: function () {
        }
      }
    });
  });
</script>
@endsection