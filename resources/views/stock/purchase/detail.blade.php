@extends('layouts.master')
@section('title', 'Dashboard - Detail Purchase Stock')

@section('css-pages')
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
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-invoice mr-1"></i> Purchase ID</strong>
                    <p class=" m-0">{{ $purchaseByID->PurchaseID }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Distributor</strong>
                    <p>{{ $purchaseByID->DistributorName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-money-bill-wave mr-1"></i> Investor</strong>
                    @if ($purchaseByID->InvestorName)
                    <p>{{ $purchaseByID->InvestorName }}</p>
                    @else
                    <p>-</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-truck-loading mr-1"></i> Supplier</strong>
                    <p>{{ $purchaseByID->SupplierName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-calendar-day mr-1"></i> Tanggal Pembelian</strong>
                    <p>{{ date('d F Y\, H:i', strtotime($purchaseByID->PurchaseDate)) }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-alt mr-1"></i> Invoice</strong><br>
                    <p class="m-0">{{ $purchaseByID->InvoiceNumber }}</p>
                    @if ($purchaseByID->InvoiceFile != NULL)
                    <a href="{{ config('app.base_image_url').'stock_invoice/'.$purchaseByID->InvoiceFile }}"
                      target="_blank">{{ $purchaseByID->InvoiceFile }}</a>
                    @else
                    <p>-</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-info mr-1"></i> Status</strong><br>
                    @if ($purchaseByID->StatusID == 1)
                    <p style="font-size: 13px" class="badge badge-warning">{{ $purchaseByID->StatusName }}</p>
                    @elseif($purchaseByID->StatusID == 2)
                    <p style="font-size: 13px" class="badge badge-success">{{ $purchaseByID->StatusName }}</p>
                    @else
                    <p style="font-size: 13px" class="badge badge-danger">{{ $purchaseByID->StatusName }}</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-user-edit mr-1"></i> Dibuat oleh</strong>
                    <p class="m-0">{{ $purchaseByID->CreatedBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($purchaseByID->CreatedDate)) }}</small>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-user-check mr-1"></i> Dikonfirmasi oleh</strong><br>
                    @if ($purchaseByID->StatusBy)
                    <p class="m-0">{{ $purchaseByID->StatusBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($purchaseByID->StatusDate)) }}</small>
                    @else
                    -
                    @endif
                  </div>
                  <div class="col-12">
                    <strong><i class="fas fa-cubes mr-1"></i> Detail Produk</strong><br>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Produk ID</th>
                          <th>Nama Produk</th>
                          <th>Qty</th>
                          <th>Harga Beli</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($purchaseByID->Detail as $detail)
                        <tr>
                          <td>{{ $detail->ProductID }}</td>
                          <td>{{ $detail->ProductName }}</td>
                          <td>{{ $detail->Qty }}</td>
                          <td>{{ Helper::formatCurrency($detail->PurchasePrice, 'Rp ') }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                @if ($purchaseByID->StatusBy == null)
                <div class="text-center mt-4">
                  <strong>Konfirmasi</strong>
                  <div class="d-flex justify-content-center mt-2" style="gap:10px">
                    <a class="btn btn-sm btn-success btn-approved"
                      data-purchase-id="{{ $purchaseByID->PurchaseID }}">Setujui</a>
                    <a class="btn btn-sm btn-danger btn-reject"
                      data-purchase-id="{{ $purchaseByID->PurchaseID }}">Tolak</a>
                  </div>
                </div>
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
<script>
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