@extends('layouts.master')
@section('title', 'Dashboard - Detail Delivery Order')

@section('css-pages')
@endsection

@section('header-menu', 'Data Detail Delivery Order')

@section('content')
<!-- Main content -->
<div class="content">
  <div class="container-fluid">

    <!-- Table -->
    <div class="row">
      <div class="col-12 ">
        @foreach ($expedition->groupBy('MerchantExpeditionID') as $expd)
        <div class="card mt-3">
          <div class="card-body">
            <a href="{{ route('delivery.expedition') }}" class="btn btn-sm btn-light mb-2">
              <i class="fas fa-arrow-left"></i> Kembali
            </a><br>
            @if ($expd[0]->StatusExpd == "S032")
            <div class="d-flex flex-column align-items-center">
              <div>
                <button class="btn btn-sm btn-success btn-finish-expedition mb-1"
                  data-expedition="{{ $expd[0]->MerchantExpeditionID }}" {{ $countStatus->DlmPengiriman > 0 ? 'disabled'
                  :
                  ''
                  }}>
                  <i class="fas fa-check"></i> Selesaikan Ekspedisi
                </button>
                @if ($countStatus->Selesai == 0)
                <button class="btn btn-sm btn-danger btn-cancel-expedition mb-1"
                  data-expedition="{{ $expd[0]->MerchantExpeditionID }}">
                  <i class="fas fa-times"></i> Batalkan Ekspedisi
                </button>
                @endif
              </div>
              @if ($countStatus->DlmPengiriman > 0)
              <small class="text-center">
                *Ekspedisi dapat diselesaikan jika semua produk telah dikonfirmasi (Selesai / Batal)
              </small>
              @endif
            </div>
            @endif
            <div class="row mt-2">
              <div class="col-12 col-md-6">
                <b>Ekspedisi ID : </b>{{ $expd[0]->MerchantExpeditionID }} <br>
                <b>Tanggal Kirim : </b>{{ date('d F Y H:i', strtotime($expd[0]->CreatedDate)) }} <br>
                <b>Status Pengiriman : </b>
                @if ($expd[0]->StatusExpd == 'S035')
                <span class="badge badge-success">{{ $expd[0]->StatusOrder }}</span>
                @elseif ($expd[0]->StatusExpd == 'S032')
                <span class="badge badge-warning">{{ $expd[0]->StatusOrder }}</span>
                @else
                <span class="badge badge-info">{{ $expd[0]->StatusOrder }}</span>
                @endif
              </div>
              <div class="col-12 col-md-6">
                <b>Driver : </b>{{ $expd[0]->DriverName }} <br>
                <b>Helper : </b>{{ $expd[0]->HelperName }} <br>
                <b>Kendaraan : </b>{{ $expd[0]->VehicleName }}<br>
                <b>Nopol : </b> {{ $expd[0]->VehicleLicensePlate }}
              </div>
            </div>
          </div>
        </div>

        @foreach ($expd->groupBy('DeliveryOrderID') as $order)
        <div class="card @if($expd[0]->StatusExpd == 'S035') card-success @else card-warning @endif card-outline">
          <div class="card-header">
            <h3 class="card-title">
              {{ $order[0]->Distributor }}
              <b>Delivery Order ID :</b> {{ $order[0]->DeliveryOrderID }} <br>
              {{ $order[0]->StockOrderID }} <br>
              {{ $order[0]->MerchantID }} - {{ $order[0]->StoreName }} - {{ $order[0]->PhoneNumber }}
            </h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            @php
            $subtotal = 0;
            $firstLoopHaistar = true;
            @endphp
            @foreach ($order as $item)
            @if ($firstLoopHaistar == true && $item->Distributor == "HAISTAR" && $item->StatusExpedition == "S034")
            <div class="text-right">
              <a data-delivery-order="{{ $order[0]->DeliveryOrderID }}"
                class="btn btn-sm bg-lightblue btn-resend-haistar">Resend Produk Haistar
              </a>
            </div>
            @endif
            <div class="row text-center align-items-center">
              <div class="col-6 col-md-3">
                <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}" alt="" width="80">
                <p class="m-0">{{ $item->ProductName }}</p>
                @if ($item->Distributor == "HAISTAR")
                <span class="badge badge-info">{{ $item->Distributor }}</span>
                @endif
              </div>
              <div class="col-6 col-md-3">
                <label>Jumlah dikirim</label>
                <p>{{ Helper::formatCurrency($item->Price, ''.$item->Qty.' x @Rp ') }}</p>
              </div>
              <div class="col-6 col-md-3">
                <label>Jumlah harga</label>
                <p>{{ Helper::formatCurrency($item->Qty * $item->Price, 'Rp ') }}</p>
              </div>
              <div class="col-6 col-md-3">
                <label class="m-0">Status Produk</label><br>
                @if ($item->StatusExpedition == "S031")
                <span class="badge badge-success mb-2">{{ $item->StatusProduct }}</span>
                @elseif ($item->StatusExpedition == "S037" || $item->StatusExpedition == "S034")
                <span class="badge badge-danger mb-2">{{ $item->StatusProduct }}</span>
                @else
                <span class="badge badge-warning mb-2">{{ $item->StatusProduct }}</span>
                @endif<br>
                @if ($item->Distributor == "RT MART" && $item->StatusExpedition == "S030")
                <a class="btn btn-sm btn-success btn-finish-product" data-product="{{ $item->ProductName }}"
                  data-store="{{ $order[0]->StoreName }}" data-detail-do="{{ $item->DeliveryOrderDetailID }}">
                  Selesaikan
                </a>
                <a class="btn btn-sm btn-danger btn-cancel-product" data-product="{{ $item->ProductName }}"
                  data-store="{{ $order[0]->StoreName }}" data-detail-do="{{ $item->DeliveryOrderDetailID }}">
                  Batalkan
                </a>
                @endif
              </div>
            </div>
            <hr class="m-2">
            @php
            $subtotal += $item->Qty * $item->Price;
            $firstLoopHaistar = false;
            @endphp
            @endforeach
            <div class="row">
              <div class="col-12 col-md-3 offset-md-6 text-center">
                <b>Subtotal : </b>{{ Helper::formatCurrency($subtotal, 'Rp ') }}
              </div>
            </div>
          </div>
        </div>
        @endforeach

        @endforeach


      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<script>
  // Event listener saat tombol selesaikan ekspedisi diklik
$('.btn-resend-haistar').on('click', function (e) {
      e.preventDefault();
      const deliveryOrder = $(this).data("delivery-order");
      $.confirm({
          title: 'Resend Produk Haistar',
          content: `Apakah yakin ingin mengirim ulang order haistar dengan ID <b>${deliveryOrder}</b>?`,
          closeIcon: true,
          type: 'blue',
          typeAnimated: true,
          buttons: {
              ya: {
                  btnClass: 'bg-lightblue',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      window.location = '/delivery/expedition/resendHaistar/' + deliveryOrder
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  // Event listener saat tombol selesaikan ekspedisi diklik
$('.btn-finish-expedition').on('click', function (e) {
      e.preventDefault();
      const expedition = $(this).data("expedition");
      $.confirm({
          title: 'Konfirmasi Order',
          content: `Apakah yakin ingin menyelesaikan ekspedisi <b>${expedition}</b>?`,
          closeIcon: true,
          type: 'green',
          typeAnimated: true,
          buttons: {
              ya: {
                  btnClass: 'btn-success',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      window.location = '/delivery/expedition/confirmExpedition/finish/' + expedition
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  // Event listener saat tombol selesaikan ekspedisi diklik
$('.btn-cancel-expedition').on('click', function (e) {
      e.preventDefault();
      const expedition = $(this).data("expedition");
      $.confirm({
          title: 'Konfirmasi Order',
          content: `Apakah yakin ingin membatalkan ekspedisi <b>${expedition}</b>?`,
          closeIcon: true,
          type: 'red',
          typeAnimated: true,
          buttons: {
              ya: {
                  btnClass: 'btn-danger',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      window.location = '/delivery/expedition/confirmExpedition/cancel/' + expedition
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  // Event listener saat tombol selesaikan product diklik
$('.btn-finish-product').on('click', function (e) {
      e.preventDefault();
      const product = $(this).data("product");
      const store = $(this).data("store");
      const detailDO = $(this).data("detail-do");
      $.confirm({
          title: 'Konfirmasi Order',
          content: `Apakah produk <b>${product}</b> telah diterima oleh <b>${store}</b>?`,
          closeIcon: true,
          type: 'green',
          typeAnimated: true,
          buttons: {
              ya: {
                  btnClass: 'btn-success',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      window.location = '/delivery/expedition/confirmProduct/finish/' + detailDO
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  // Event listener saat tombol batalkan product diklik
$('.btn-cancel-product').on('click', function (e) {
      e.preventDefault();
      const product = $(this).data("product");
      const store = $(this).data("store");
      const detailDO = $(this).data("detail-do");
      $.confirm({
          title: 'Konfirmasi Order',
          content: `Apakah yakin ingin membatalkan produk <b>${product}</b> dari <b>${store}</b>?`,
          closeIcon: true,
          type: 'red',
          typeAnimated: true,
          buttons: {
              batalkan: {
                  btnClass: 'btn-danger',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      window.location = '/delivery/expedition/confirmProduct/cancel/' + detailDO
                  }
              },
              tidak: function () {
              }
          }
      });
  });
</script>
@endsection