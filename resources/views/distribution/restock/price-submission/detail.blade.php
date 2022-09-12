@extends('layouts.master')
@section('title', 'Dashboard - Detail Harga Pengajuan')

@section('css-pages')
@endsection

@section('header-menu', 'Detail Harga Pengajuan')

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
            <a href="{{ route('priceSubmission') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i> Kembali</a>
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
              @if ($data->StatusPriceSubmission === 'S039' && (Auth::user()->RoleID == "CEO" || Auth::user()->RoleID == "IT"))
              <div class="col-12 mb-3 justify-content-center d-flex" style="gap: 10px">
                <a class="btn btn-xs btn-success btn-approve" 
                  data-price-submission-id="{{ $data->PriceSubmissionID }}" data-stock-order-id="{{ $data->StockOrderID }}">
                  Setujui
                </a>
                <a class="btn btn-xs btn-danger btn-reject"
                  data-price-submission-id="{{ $data->PriceSubmissionID }}" data-stock-order-id="{{ $data->StockOrderID }}">
                  Tolak
                </a>
              </div>
              @endif
              <div class="col-12 table-responsive">
                <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Produk ID</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Harga Jual</th>
                    <th>Harga Pengajuan</th>
                    <th>Harga Beli</th>
                    <th>Value Jual</th>
                    <th>Value Pengajuan</th>
                    <th>Value Beli</th>
                    <th>Est Margin Jual</th>
                    <th>Est Margin Pengajuan</th>
                    <th>Voucher</th>
                    <th>% Voucher</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $totalPriceSubmission = 0;
                  $valueBeli = 0;
                  $estMarginPrice = 0;
                  $estMarginSubmission = 0;
                  $totalVoucher = 0;
                  @endphp
                  @foreach ($data->Detail as $item)
                  <tr>
                    <td>{{ $item->ProductID }}</td>
                    <td>{{ $item->ProductName }}</td>
                    <td>{{ $item->PromisedQuantity }}</td>
                    <td class="text-right">{{ Helper::formatCurrency($item->Nett, "Rp ") }}</td>
                    <td class="text-right">{{ Helper::formatCurrency($item->PriceSubmission, "Rp ") }}</td>
                    <td class="text-right">
                      @if ($item->PurchasePrice === null)
                        {{ Helper::formatCurrency($item->Price, "Rp ") }}
                      @else
                        {{ Helper::formatCurrency($item->PurchasePrice, "Rp ") }}
                      @endif
                    </td>
                    <td class="text-right">{{ Helper::formatCurrency($item->ValueProduct, "Rp ") }}</td>
                    <td class="text-right">{{ Helper::formatCurrency($item->ValueSubmission, "Rp ") }}</td>
                    <td class="text-right">
                      @if ($item->PurchasePrice === null)
                        {{ Helper::formatCurrency($item->Price * $item->PromisedQuantity, "Rp ") }}
                      @else
                        {{ Helper::formatCurrency($item->PurchasePrice * $item->PromisedQuantity, "Rp ") }}
                      @endif
                    </td>
                    <td class="text-right">{{ Helper::formatCurrency($item->EstMarginPrice, "Rp ") }} | {{ round($item->EstMarginPrice / $item->ValueProduct * 100, 2) }}%</td>
                    <td class="text-right">{{ Helper::formatCurrency($item->EstMarginSubmission, "Rp ") }} | {{ round($item->EstMarginSubmission / $item->ValueSubmission * 100, 2) }}%</td>
                    <td class="text-right">{{ Helper::formatCurrency($item->Voucher, "Rp ") }}</td>
                    <td class="text-center">{{ round($item->Voucher / $item->ValueProduct * 100, 2) }}</td>
                  </tr>
                  @php
                  $totalPriceSubmission += $item->ValueSubmission;
                  $purchasePrice = $item->PurchasePrice === null ? $item->Price : $item->PurchasePrice;
                  $valueBeli += $purchasePrice * $item->PromisedQuantity;

                  $estMarginPrice += $item->EstMarginPrice;
                  $estMarginSubmission += $item->EstMarginSubmission;

                  $totalVoucher += $item->Voucher;
                  @endphp
                  @endforeach
                </tbody>
                <tfoot>
                  <tr class="text-right">
                    <th colspan="6"></th>
                    <th>{{ Helper::formatCurrency($data->TotalPrice, "Rp ") }}</th>
                    <th>{{ Helper::formatCurrency($totalPriceSubmission, "Rp ") }}</th>
                    <th>{{ Helper::formatCurrency($valueBeli, "Rp ") }}</th>
                    <th>{{ Helper::formatCurrency($estMarginPrice, "Rp ") }} | {{ round($estMarginPrice / $data->TotalPrice * 100, 2) }}%</th>
                    <th>{{ Helper::formatCurrency($estMarginSubmission, "Rp ") }} | {{ round($estMarginSubmission / $totalPriceSubmission * 100, 2) }}%</th>
                    <th>{{ Helper::formatCurrency($totalVoucher, "Rp ") }}</th>
                    <th class="text-center">{{ round($totalVoucher / $data->TotalPrice * 100, 2) }}</th>
                  </tr>
                </tfoot>
              </table>
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
  $(".btn-approve").on("click", function (e) {
        e.preventDefault();
        const priceSubmissionID = $(this).data("price-submission-id");
        const stockOrderID = $(this).data("stock-order-id");
        $.confirm({
            title: "Setujui Pengajuan!",
            content: `Yakin ingin menyetujui pengajuan <b>${stockOrderID}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = `/price-submission/confirm/${priceSubmissionID}/approve`;
                    },
                },
                tidak: function () {},
            },
        });
    });

    $(".btn-reject").on("click", function (e) {
        e.preventDefault();
        const priceSubmissionID = $(this).data("price-submission-id");
        const stockOrderID = $(this).data("stock-order-id");
        $.confirm({
            title: "Tolak Pengajuan!",
            content: `Yakin ingin menolak pengajuan <b>${stockOrderID}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = `/price-submission/confirm/${priceSubmissionID}/reject`;
                    },
                },
                tidak: function () {},
            },
        });
    });
</script>
@endsection