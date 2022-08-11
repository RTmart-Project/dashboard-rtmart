@extends('layouts.master')
@section('title', 'Dashboard - Restock Validation - Detail Toko')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">

@endsection

@section('header-menu', 'Restock Validation - Detail Toko')

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
            <a href="{{ route('distribution.validationRestock') }}" class="btn btn-sm btn-light"><i
                class="fas fa-arrow-left"></i> Kembali</a>
          </div>
        </div>

        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Informasi PO</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 col-md-6">
                <div class="row">
                  <div class="col-6">
                    <strong>Stock Order ID :</strong>
                    <p class="mb-2">{{ $data->StockOrderID }}</p>
                  </div>
                  <div class="col-6">
                    <strong>Tanggal Order :</strong>
                    <p class="mb-2">{{ date('d M Y, H:i', strtotime($data->CreatedDate)) }}</p>
                  </div>
                  <div class="col-6">
                    <strong>Distributor :</strong>
                    <p class="mb-2">{{ $data->DistributorName }}</p>
                  </div>
                  <div class="col-6">
                    <strong>Metode Pembayaran :</strong>
                    <p class="mb-2">{{ $data->PaymentMethodName }}</p>
                  </div>
                  <div class="col-6">
                    <strong>Sales :</strong>
                    <p class="mb-2">{{ $data->Sales }}</p>
                  </div>
                  <div class="col-6">
                    <strong>Status Validitas PO :</strong>
                    <p class="mb-2">
                      <span
                        class="badge @if ($data->IsValid === 1) badge-success @elseif ($data->IsValid === 0) badge-danger @else badge-info @endif">
                        {{ $data->Validation }}
                      </span>
                    </p>
                  </div>
                  <div class="col-6">
                    <strong>Catatan Validasi :</strong>
                    <p class="mb-2">{{ $data->ValidationNotes }}</p>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <strong>Produk :</strong>
                <div class="row">
                  @foreach ($data->OrderDetail as $item)
                  <div class="col-3 d-flex align-items-center">
                    <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}"
                      alt="{{ $item->ProductName }}" height="80">
                  </div>
                  <div class="col-3 d-flex align-items-center">
                    <p>{{ $item->ProductName }}</p>
                  </div>
                  <div class="col-3 d-flex align-items-center">
                    <p class="text-center w-100">{{ $item->PromisedQuantity }}x {{ Helper::formatCurrency($item->Nett,
                      '@Rp ') }}</p>
                  </div>
                  <div class="col-3 d-flex align-items-center">
                    <p class="text-right w-100">{{ Helper::formatCurrency($item->TotalPriceProduct, 'Rp ') }}</p>
                  </div>
                  @endforeach
                </div>
                <div class="row justify-content-end text-right">
                  <div class="col-3 text-center">SubTotal</div>
                  <div class="col-3">{{ Helper::formatCurrency($data->TotalPrice, 'Rp') }}</div>
                </div>
                <div class="row justify-content-end text-right">
                  @if ($data->Discount != 0)
                  <div class="col-3 text-center">Diskon</div>
                  <div class="col-3">{{ Helper::formatCurrency($data->Discount, 'Rp') }}</div>
                  @endif
                </div>
                <div class="row justify-content-end text-right">
                  @if ($data->ServiceChargeNett != 0)
                  <div class="col-3 text-center">Biaya Layanan</div>
                  <div class="col-3">{{ Helper::formatCurrency($data->ServiceChargeNett, 'Rp') }}</div>
                  @endif
                </div>
                <div class="row justify-content-end text-right">
                  @if ($data->DeliveryFee != 0)
                  <div class="col-3 text-center">Biaya Pengiriman</div>
                  <div class="col-3">{{ Helper::formatCurrency($data->DeliveryFee, 'Rp') }}</div>
                  @endif
                </div>
                <div class="row justify-content-end text-right">
                  <div class="col-3 text-center">Grand Total</div>
                  <div class="col-3">{{ Helper::formatCurrency($data->NettPrice + $data->DeliveryFee +
                    $data->ServiceChargeNett, 'Rp') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Informasi Toko</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-4 col-md-2">
                <img src="{{ config('app.base_image_url') . '/merchant/' . $data->StoreImage }}" alt="Store Image"
                  class="rounded img-fluid pb-2 pb-md-0" style="object-fit: cover; width: 130px; height: 130px;">
              </div>
              <div class="col-8 col-md-2">
                <div class="row">
                  <div class="col-6 col-md-12">
                    <strong>Merchant ID :</strong>
                    <p class="mb-2">{{ $data->MerchantID }}</p>
                  </div>
                  <div class="col-6 col-md-12">
                    <strong>Nama Toko :</strong>
                    <p class="mb-2">{{ $data->StoreName }}</p>
                  </div>
                  <div class="col-6 col-md-12">
                    <strong>Nama Pemilik :</strong>
                    <p class="mb-2">{{ $data->OwnerFullName }}</p>
                  </div>
                  <div class="col-6 col-md-12">
                    <strong>No. HP :</strong>
                    <p class="mb-2">
                      <i class="fab fa-whatsapp"></i>
                      <a href="https://wa.me/{{ $data->PhoneNumber }}" target="_blank">
                        {{ $data->PhoneNumber }}
                      </a>
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="row">
                  <div class="col-4">
                    <strong>Latitude :</strong>
                    <p class="mb-2" id="latitude">{{ $data->Latitude }}</p>
                  </div>
                  <div class="col-4">
                    <strong>Longitude :</strong>
                    <p class="mb-2" id="longitude">{{ $data->Longitude }}</p>
                  </div>
                  <div class="col-4">
                    <strong>Patokan</strong>
                    <p class="mb-2">{{ $data->StoreAddressNote }}</p>
                  </div>
                  <div class="col-12">
                    <strong>Alamat :</strong>
                    <p class="mb-2" id="address">{{ $data->StoreAddress }}</p>
                  </div>
                  <div class="col-12">
                    <a id="open-maps" target="_blank" class="btn btn-sm btn-success my-2">Buka di Google Maps</a>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div id="google-maps" style="width: 100%; height: 200px;"></div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-6 col-md-3">
                <strong>Tanggal Daftar :</strong>
                <p class="mb-2">{{ date('d F Y, H:i', strtotime($data->RegisterDate)) }}</p>
              </div>
              <div class="col-6 col-md-3">
                <strong>Lama Bergabung :</strong>
                <p class="mb-2">{{ $data->JoinedDuration }}</p>
              </div>
              <div class="col-6 col-md-3">
                <strong>Terakhir Aktif :</strong>
                <p class="mb-2">{{ date('d F Y, H:i', strtotime($data->LastPing)) }}</p>
              </div>
              <div class="col-12 col-md-3">
                <strong>Pembayaran yang pernah digunakan :</strong>
                <p class="mb-2">{{ $data->OrderPaymentMethod }}</p>
              </div>
              <div class="col-12 col-md-6">
                <strong>PO telah diproses</strong> (Selesai & Dikirim) <b>:</b>
                <table class="table table-sm table-bordered">
                  <thead>
                    <tr>
                      <th>Jumlah PO</th>
                      <th>Value PO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center">{{ $data->OrderProcessed->CountOrder }}</td>
                      <td class="text-center">{{ $data->OrderProcessed->ValueOrder }}</td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <div class="card card-info m-0 card-outline collapsed-card">
                          <div class="card-header py-2 pl-2 pr-3">
                            <h3 class="card-title">Detail Produk ({{ count($data->OrderProcessed->DetailProduct) }}
                              produk)</h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                  class="fas fa-plus"></i>
                              </button>
                            </div>
                          </div>
                          <div class="card-body p-2">
                            <table class="table table-sm">
                              <tbody>
                                @foreach ($data->OrderProcessed->DetailProduct as $item)
                                <tr>
                                  <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                  <td class="text-center">
                                    <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}"
                                      alt="{{ $item->ProductName }}" height="60">
                                  </td>
                                  <td class="align-middle">{{ $item->ProductID }} - {{ $item->ProductName }}</td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-12 col-md-6">
                <strong>PO belum diproses</strong> (Pesanan Baru, Dikonfirmasi, Dalam Proses, & Dibatalkan) <b>:</b>
                <table class="table table-sm table-bordered">
                  <thead>
                    <tr>
                      <th>Jumlah PO</th>
                      <th>Value PO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center">{{ $data->OrderNotProcessed->CountOrder }}</td>
                      <td class="text-center">{{ $data->OrderNotProcessed->ValueOrder }}</td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <div class="card card-info m-0 card-outline collapsed-card">
                          <div class="card-header py-2 pl-2 pr-3">
                            <h3 class="card-title">Detail Produk ({{ count($data->OrderNotProcessed->DetailProduct) }}
                              produk)</h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                  class="fas fa-plus"></i>
                              </button>
                            </div>
                          </div>
                          <div class="card-body p-2">
                            <table class="table table-sm">
                              <tbody>
                                @foreach ($data->OrderNotProcessed->DetailProduct as $item)
                                <tr>
                                  <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                  <td class="text-center">
                                    <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}"
                                      alt="{{ $item->ProductName }}" height="60">
                                  </td>
                                  <td class="align-middle">{{ $item->ProductID }} - {{ $item->ProductName }}</td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-12 col-md-6">
                <strong>Status Toko Saat Ini : 
                  {!! $data->IsBlocked === 1 ? 
                    "<span class='badge badge-danger' style='font-size:12px'>Block</span>" : 
                    "<span class='badge badge-success' style='font-size:12px'>Not Blocked</span>" !!}
                </strong>
                <div class="card card-info card-outline collapsed-card">
                  <div class="card-header py-2 pl-2 pr-3">
                    <h3 class="card-title">Log Perubahan Status Block</h3>
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  <div class="card-body p-2">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Status Block</th>
                          <th>Catatan</th>
                          <th>Tanggal Perubahan</th>
                          <th>Diubah Oleh</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if (count($data->LogBlocked) > 0)
                          @foreach ($data->LogBlocked as $value)
                          <tr>
                            <td>{{ $value->IsBlocked == 1 ? 'Block' : 'Unblock' }}</td>
                            <td>{{ $value->BlockedMessage }}</td>
                            <td>{{ date('d M Y, H:i:s', strtotime($value->CreatedDate)) }}</td>
                            <td>{{ $value->ActionBy }}</td>
                          </tr>
                          @endforeach
                        @else
                          <tr>
                            <td colspan="4" class="text-center py-4">Belum ada log perubahan status block</td>
                          </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>

            <div class="row mt-4 w-100 justify-content-center">
              <a class="btn btn-warning update-validitas" data-stock-order-id="{{ $data->StockOrderID }}"
                data-is-valid="{{ $data->IsValid }}" data-validation-notes="{{ $data->ValidationNotes }}">
                Update Validitas
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<script defer type="text/javascript"
  src="https://maps.googleapis.com/maps/api/js?callback=initMap&v=3&key=AIzaSyC9kPfmVtf71uGeDfHMMHDzHAl-FEBtOEw&libraries=places">
  </script>
<script>
  const latitude = $("#latitude").text();
  const longitude = $("#longitude").text();
  const address = $("#address").text();

  function initMap() {
    map = new google.maps.Map(document.getElementById('google-maps'), {
      center: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
      zoom: 17,
      mapTypeControl: false,
      streetViewControl: false
    });
    infoWindow = new google.maps.InfoWindow;

    marker = new google.maps.Marker({
      map: map,
      draggable: false,
      position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
      url: 'https://www.google.co.id/maps/place/' + latitude + ',' + longitude
    });
    google.maps.event.addListener(marker, 'mouseover', function () {
      infoWindow.setContent(address);
      infoWindow.open(map, marker);
    });

    google.maps.event.addListener(marker, 'mouseout', function () {
      //
    });
    google.maps.event.addListener(marker, 'click', function () {

      window.open(this.url, '_blank');
    });
  }

  $("#open-maps").prop("href", 'https://www.google.co.id/maps/place/' + latitude + ',' + longitude);

  let csrf = $('meta[name="csrf_token"]').attr("content");

  $(".update-validitas").on("click", function (e) {
    e.preventDefault();
    const stockOrderID = $(this).data("stock-order-id");
    const isValid = $(this).data("is-valid");
    const validationNotes = $(this).data("validation-notes");

    $.confirm({
      title: "Update Validitas",
      content: `Apakah Order <b>${stockOrderID}</b> valid?<br>
          <form action="/distribution/validation/update/${stockOrderID}" method="post">
            <input type="hidden" name="_token" value="${csrf}">
            <label class="mt-2 mb-0">Status Validitas:</label>
            <select class="form-control" name="is_valid">
              <option value="" hidden disabled selected>-- Pilih Status Validitas --</option>
              <option value="1" ${isValid === 1 && "selected"
        }>Valid</option>
              <option value="0" ${isValid === 0 && "selected"
        }>Tidak Valid</option>
            </select>
            <label class="mt-2 mb-0">Catatan:</label>
            <input type="text" class="form-control price" value="${validationNotes}"
              name="validation_notes" autocomplete="off" placeholder="Tambahkan Catatan (opsional)">
          </form>`,
      closeIcon: true,
      buttons: {
        simpan: {
          btnClass: "btn-success",
          draggable: true,
          dragWindowGap: 0,
          action: function () {
            this.$content.find("form").submit();
          },
        },
        batal: function () { },
      },
    });
  });
</script>
@endsection