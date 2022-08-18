@extends('layouts.master')
@section('title', 'Dashboard - Detail Stock Promo')

@section('css-pages')
@endsection

@section('header-menu', 'Detail Stock Promo')

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
            <a href="{{ route('stockPromo.inbound') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active">
                <div class="row">
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-invoice mr-1"></i> Inbound Stock Promo ID</strong>
                    <p class=" m-0">
                      {{ $data->StockPromoInboundID }} <br>
                      {{ $data->PurchaseID != null ? "dari " . $data->PurchaseID : '' }}
                    </p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Distributor</strong>
                    <p>{{ $data->DistributorName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-money-bill-wave mr-1"></i> Investor</strong>
                    @if ($data->InvestorName)
                    <p>{{ $data->InvestorName }}</p>
                    @else
                    <p>-</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-truck-loading mr-1"></i> Supplier</strong>
                    <p>{{ $data->SupplierName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-calendar-day mr-1"></i> Tanggal</strong>
                    <p>{{ date('d F Y\, H:i', strtotime($data->InboundDate)) }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-alt mr-1"></i> Invoice</strong><br>
                    <p class="m-0">{{ $data->InvoiceNumber }}</p>
                    {{-- @if ($data->InvoiceFile != NULL)
                    <a href="{{ config('app.base_image_url').'stock_invoice/'.$data->InvoiceFile }}"
                      target="_blank">{{ $data->InvoiceFile }}</a>
                    @else
                    <p>-</p>
                    @endif --}}
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-info mr-1"></i> Status</strong><br>
                    @if ($data->StatusID == 1)
                    <p style="font-size: 13px" class="badge badge-warning">{{ $data->StatusName }}</p>
                    @elseif($data->StatusID == 2)
                    <p style="font-size: 13px" class="badge badge-success">{{ $data->StatusName }}</p>
                    @else
                    <p style="font-size: 13px" class="badge badge-danger">{{ $data->StatusName }}</p>
                    @endif
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-user-edit mr-1"></i> Dibuat oleh</strong>
                    <p class="m-0">{{ $data->CreatedBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($data->CreatedDate)) }}</small>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-user-check mr-1"></i> Dikonfirmasi oleh</strong><br>
                    @if ($data->StatusBy)
                    <p class="m-0">{{ $data->StatusBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($data->StatusDate)) }}</small>
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
                          <th>Label Produk</th>
                          <th>Qty</th>
                          <th>Harga Beli</th>
                          <th>Harga Jual</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($data->Detail as $detail)
                        <tr>
                          <td>{{ $detail->ProductID }}</td>
                          <td>{{ $detail->ProductName }}</td>
                          <td>{{ $detail->ProductLabel }}</td>
                          <td>{{ $detail->Qty }}</td>
                          <td>{{ Helper::formatCurrency($detail->PurchasePrice, 'Rp ') }}</td>
                          <td>{{ Helper::formatCurrency($detail->SellingPrice, 'Rp ') }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                      {{-- <tfoot>
                        <tr>
                          <td colspan="4"></td>
                          <th class="text-center">GrandTotal</th>
                          <th>{{ Helper::formatCurrency($data->GrandTotal, 'Rp ') }}</th>
                        </tr>
                      </tfoot> --}}
                    </table>
                  </div>
                </div>
                {{-- @if ($data->StatusBy == null && (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI"))
                <div class="text-center mt-4">
                  <strong>Konfirmasi</strong>
                  <div class="d-flex justify-content-center mt-2" style="gap:10px">
                    <a class="btn btn-sm btn-success btn-approved"
                      data-purchase-id="{{ $data->PurchaseID }}">Setujui</a>
                    <a class="btn btn-sm btn-danger btn-reject"
                      data-purchase-id="{{ $data->PurchaseID }}">Tolak</a>
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