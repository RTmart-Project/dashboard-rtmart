@extends('layouts.master')
@section('title', 'Dashboard - Detail Harga Pengajuan')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
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
            <a href="{{ route('priceSubmission') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
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
                <p>{{ $data->MerchantID }} - {{ ucwords($data->StoreName) }} - {{ ucwords($data->OwnerFullName) }} - {{ $data->PhoneNumber
                  }}</p>
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
              <div class="col-12 col-md-3">
                <strong>Diajukan oleh</strong>
                <p>{{ $data->CreatedBy }} pada {{ date('d F Y H:i', strtotime($data->CreatedDate)) }}</p>
              </div>
              <div class="col-12 col-md-3">
                <strong>Dikonfirmasi oleh</strong>
                @if ($data->ConfirmBy != null)
                <p>{{ $data->ConfirmBy }} pada {{ date('d F Y H:i', strtotime($data->ConfirmDate)) }}</p>
                @else
                <p>-</p>
                @endif
              </div>
              <div class="col-12 col-md-3">
                <strong>Catatan</strong>
                @if ($data->Note != null)
                <p>{{ $data->Note }}</p>
                @else
                <p>-</p>
                @endif
              </div>
              <div class="col-12 col-md-3">
                <strong>Cycle Restock</strong>
                <p>{{ $countPOselesai === 0 ? 'Belum pernah' : $countPOselesai . ' kali' }} <br> sejak {{ date('d F
                  Y',strtotime('-31 days',strtotime($data->CreatedDate))) }}</p>
              </div>
              @if ($data->StatusPriceSubmission === 'S039' && (Auth::user()->RoleID == "CEO" || Auth::user()->RoleID ==
              "IT"))
              <div class="col-12 mb-3 justify-content-center d-flex" style="gap: 10px">
                <a class="btn btn-xs btn-success btn-approve" data-price-submission-id="{{ $data->PriceSubmissionID }}"
                  data-stock-order-id="{{ $data->StockOrderID }}">
                  Setujui
                </a>
                <a class="btn btn-xs btn-danger btn-reject" data-price-submission-id="{{ $data->PriceSubmissionID }}"
                  data-stock-order-id="{{ $data->StockOrderID }}">
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
                      <th>Cost Logistic</th>
                      <th>Value Jual</th>
                      <th>Value Pengajuan</th>
                      <th>Value Beli</th>
                      <th>Total Cost Logistic</th>
                      <th>Est Margin Jual</th>
                      <th>Est Margin Pengajuan</th>
                      <th>Voucher</th>
                      {{-- <th>% Voucher</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $totalPriceSubmission = 0;
                    $valueBeli = 0;
                    $estMarginPrice = 0;
                    $estMarginSubmission = 0;
                    $totalVoucher = 0;
                    $qty = 0;
                    $totalCostLogistic = 0;
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
                      <td class="text-right">{{ $data->PaymentMethodID == 13 ? "-" :
                        Helper::formatCurrency($item->CostLogistic, "Rp ") }}</td>
                      <td class="text-right">{{ Helper::formatCurrency($item->ValueProduct, "Rp ") }}</td>
                      <td class="text-right">{{ Helper::formatCurrency($item->ValueSubmission, "Rp ") }}</td>
                      <td class="text-right">
                        @if ($item->PurchasePrice === null)
                        {{ Helper::formatCurrency($item->Price * $item->PromisedQuantity, "Rp ") }}
                        @else
                        {{ Helper::formatCurrency($item->PurchasePrice * $item->PromisedQuantity, "Rp ") }}
                        @endif
                      </td>
                      <td class="text-right">{{ $data->PaymentMethodID == 13 ? "-" :
                        Helper::formatCurrency($item->CostLogistic * $item->PromisedQuantity, "Rp ") }}</td>
                      <td class="text-right">
                        @if ($item->PurchasePrice === null)
                        {{ Helper::formatCurrency(($item->Nett - $item->Price) * $item->PromisedQuantity, "Rp ") }} | {{
                        round((($item->Nett - $item->Price) * $item->PromisedQuantity) / $item->ValueProduct * 100, 2)
                        }}%
                        @else
                        {{ Helper::formatCurrency(($item->Nett - $item->PurchasePrice) * $item->PromisedQuantity, "Rp ")
                        }} | {{ round((($item->Nett - $item->PurchasePrice) * $item->PromisedQuantity) /
                        $item->ValueProduct * 100, 2) }}%
                        @endif
                      </td>
                      <td class="text-right">
                        @if ($item->PurchasePrice === null)
                        {{ Helper::formatCurrency(($item->PriceSubmission - $item->Price) * $item->PromisedQuantity, "Rp
                        ") }} | {{ round((($item->PriceSubmission - $item->Price) * $item->PromisedQuantity) /
                        $item->ValueSubmission * 100, 2) }}%
                        @else
                        {{ Helper::formatCurrency(($item->PriceSubmission - $item->PurchasePrice) *
                        $item->PromisedQuantity, "Rp ") }} | {{ round((($item->PriceSubmission - $item->PurchasePrice) *
                        $item->PromisedQuantity) / $item->ValueSubmission * 100, 2) }}%
                        @endif
                      </td>
                      <td class="text-right">{{ Helper::formatCurrency($item->Voucher, "Rp ") }}</td>
                      {{-- <td class="text-center">{{ round($item->Voucher / $item->ValueProduct * 100, 2) }}</td> --}}
                    </tr>
                    @php
                    $totalPriceSubmission += $item->ValueSubmission;
                    $purchasePrice = $item->PurchasePrice === null ? $item->Price : $item->PurchasePrice;
                    $valueBeli += $purchasePrice * $item->PromisedQuantity;

                    $estMarginPrice += $item->EstMarginPrice;
                    $estMarginSubmission += $item->EstMarginSubmission;

                    $totalVoucher += $item->Voucher;
                    $qty += $item->PromisedQuantity;
                    $totalCostLogistic += $item->CostLogistic * $item->PromisedQuantity;
                    @endphp
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="text-right">
                      <th colspan="7"></th>
                      <th>{{ Helper::formatCurrency($data->TotalPrice, "Rp ") }}</th>
                      <th>{{ Helper::formatCurrency($totalPriceSubmission, "Rp ") }}</th>
                      <th>{{ Helper::formatCurrency($valueBeli, "Rp ") }}</th>
                      <th>{{ $data->PaymentMethodID == 13 ? "-" : Helper::formatCurrency($totalCostLogistic, "Rp ") }}
                      </th>
                      <th>
                        {{ Helper::formatCurrency($data->TotalPrice - $valueBeli, "Rp ") }} |
                        {{ round(($data->TotalPrice - $valueBeli) / $data->TotalPrice * 100, 2) }}%
                      </th>
                      <th>
                        {{ Helper::formatCurrency($totalPriceSubmission - $valueBeli, "Rp ") }} |
                        {{ round(($totalPriceSubmission - $valueBeli) / $totalPriceSubmission * 100, 2) }}%
                      </th>
                      <th>{{ Helper::formatCurrency($totalVoucher, "Rp ") }}</th>
                      {{-- <th class="text-center">{{ round($totalVoucher / $data->TotalPrice * 100, 2) }}</th> --}}
                    </tr>
                    <tr class="text-right">
                      <th colspan="10"></th>
                      <th colspan="2">Bunga BPR (2.4% / {{ $data->CountPOselesai }}) x Value Pengajuan</th>
                      <th>{{ Helper::formatCurrency($data->Bunga / 100 * $totalPriceSubmission, "Rp ") }}</th>
                    </tr>
                    @if ($data->PaymentMethodID != 13)
                    <tr class="text-right">
                      <th colspan="10"></th>
                      <th colspan="2">Total Cost Logistic</th>
                      <th>{{ Helper::formatCurrency($totalCostLogistic, "Rp ") }}</th>
                    </tr>
                    @endif
                    <tr class="text-right">
                      <th colspan="10"></th>
                      <th colspan="2">Final Est Margin Pengajuan</th>
                      <th>
                        @if ($data->PaymentMethodID == 13)
                        {{ Helper::formatCurrency(($totalPriceSubmission - $valueBeli) - round($data->Bunga / 100 *
                        $totalPriceSubmission), "Rp ") }} |
                        {{ round((($totalPriceSubmission - $valueBeli) - round($data->Bunga / 100 *
                        $totalPriceSubmission)) / $totalPriceSubmission * 100, 2) }}%
                        @else
                        {{ Helper::formatCurrency(($totalPriceSubmission - $valueBeli) - round($data->Bunga / 100 *
                        $totalPriceSubmission) - ($totalCostLogistic), "Rp ") }} |
                        {{ round((($totalPriceSubmission - $valueBeli) - round($data->Bunga / 100 *
                        $totalPriceSubmission) - $totalCostLogistic) / $totalPriceSubmission * 100, 2) }}%
                        @endif
                      </th>
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
  let csrf = $('meta[name="csrf_token"]').attr("content");
  
  $(".btn-approve").on("click", function (e) {
      e.preventDefault();
      const priceSubmissionID = $(this).data("price-submission-id");
      const stockOrderID = $(this).data("stock-order-id");
      $.confirm({
          title: "Setujui Pengajuan!",
          content: `<p>Yakin ingin menyetujui pengajuan <b>${stockOrderID}</b>?</p>
                      <label class="mt-2 mb-0">Catatan</label>
                      <form action="/price-submission/confirm/${priceSubmissionID}/approve" method="post">
                          <input type="hidden" name="_token" value="${csrf}">
                          <textarea class="form-control" name="note"></textarea>
                      </form>`,
          closeIcon: true,
          buttons: {
              Yakin: {
                  btnClass: "btn-success",
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                    this.$content.find("form").submit();
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
          content: `<p>Yakin ingin menolak pengajuan <b>${stockOrderID}</b>?</p>
                      <label class="mt-2 mb-0">Catatan</label>
                      <form action="/price-submission/confirm/${priceSubmissionID}/reject" method="post">
                          <input type="hidden" name="_token" value="${csrf}">
                          <textarea class="form-control" name="note"></textarea>
                      </form>`,
          closeIcon: true,
          buttons: {
              Yakin: {
                  btnClass: "btn-red",
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                    this.$content.find("form").submit();
                  },
              },
              tidak: function () {},
          },
      });
  });
</script>
@endsection