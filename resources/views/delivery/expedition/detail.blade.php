@extends('layouts.master')
@section('title', 'Dashboard - Detail Ekspedisi')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Data Detail Ekspedisi')

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
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-light mb-2">
              <i class="fas fa-arrow-left"></i> Kembali
            </a><br>
            @if ($expd[0]->StatusExpd == "S032" && Auth::user()->RoleID != "HL")
            <div class="d-flex flex-column align-items-center">
              <div>
                <button class="btn btn-sm btn-success btn-finish-expedition mb-1"
                  data-expedition="{{ $expd[0]->MerchantExpeditionID }}" {{ $countStatus->DlmPengiriman > 0 ? 'disabled' : '' }}>
                  <i class="fas fa-check"></i> Selesaikan Ekspedisi
                </button>
                @if ($countStatus->Selesai == 0)
                <button class="btn btn-sm btn-danger btn-cancel-expedition mb-1"
                  data-expedition="{{ $expd[0]->MerchantExpeditionID }}"
                  {{ $countStatus->CountHaistar > 0 ? 'disabled' : '' }}>
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
                <span class="badge badge-danger">{{ $expd[0]->StatusOrder }}</span>
                @endif
                <br>
                <b>Validasi No. HP Aktif : </b>{{ $expd[0]->PhoneNumberValidation == 1 ? 'Valid' : '' }} <br>
                <b>Validasi Alamat Sesuai : </b>{{ $expd[0]->AddressValidation == 1 ? 'Valid' : ''}}
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
            <div class="text-right">
              @if ($firstLoopHaistar == true && $item->Distributor == "HAISTAR" && $item->StatusExpeditionDetail == "S034")
              <a data-delivery-order="{{ $order[0]->DeliveryOrderID }}"
                class="btn btn-sm bg-lightblue btn-resend-haistar">Resend Produk Haistar
              </a>
              @elseif ($firstLoopHaistar == true && $item->Distributor == "HAISTAR" && $item->StatusExpeditionDetail == "S030")
              <a data-delivery-order="{{ $order[0]->DeliveryOrderID }}" data-expedition="{{ $expd[0]->MerchantExpeditionID }}"
                class="btn btn-sm bg-danger btn-req-cancel-haistar">Request Cancel Haistar
              </a>
              @endif
            </div>
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
                @if ($item->StatusExpeditionDetail == "S031")
                <span class="badge badge-success">{{ $item->StatusProduct }}</span>
                <a class="lihat-bukti d-block" href="{{ config('app.base_image_url').'receipt_image_expedition/'.$item->ReceiptImage }}" target="_blank" data-product="{{ $item->ProductName }}" data-store="{{ $order[0]->StoreName }}">Lihat Bukti</a>
                @elseif ($item->StatusExpeditionDetail == "S037" || $item->StatusExpeditionDetail == "S034")
                <span class="badge badge-danger mb-2">{{ $item->StatusProduct }}</span>
                @elseif ($item->StatusExpeditionDetail == "S030")
                <span class="badge badge-warning mb-2">{{ $item->StatusProduct }}</span>
                @else
                <span class="badge badge-info mb-2">{{ $item->StatusProduct }}</span>
                @endif<br>
                @if ($item->Distributor == "RT MART" && $item->StatusExpeditionDetail == "S030" && Auth::user()->RoleID != "HL")
                <a class="btn btn-sm btn-success btn-finish-product" data-product="{{ $item->ProductName }}" data-qty="{{ $item->Qty }}"
                  data-store="{{ $order[0]->StoreName }}" data-expedition-detail="{{ $item->MerchantExpeditionDetailID }}">
                  Selesaikan
                </a>
                <a class="btn btn-sm btn-danger btn-cancel-product" data-product="{{ $item->ProductName }}"
                  data-store="{{ $order[0]->StoreName }}"
                  data-expedition-detail="{{ $item->MerchantExpeditionDetailID }}">
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

        {{-- Modal Selesaikan Produk --}}
        <form method="POST" enctype="multipart/form-data" id="form-selesaikan">
          @csrf
          <div class="modal fade" id="modal-finish-product">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Selesaikan Produk</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body pt-2">
                  <p id="detail" class="text-center"></p>
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="receipt_qty">Qty Diterima</label>
                        <input type="number" class="form-control" name="receipt_qty" id="receipt_qty" placeholder="Qty Diterima">
                        <span id="maksimum"></span>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="badstock_qty">Qty BadStock</label>
                        <input type="number" class="form-control" name="badstock_qty" id="badstock_qty" placeholder="Qty BadStock">
                        <span id="maksimum-badstock"></span>
                      </div>
                    </div>
                    <div class="col-md-4 col-12">
                      <div class="form-group">
                        <label for="receipt_image">Foto Bukti Terima</label>
                        <input type="file" class="form-control" name="receipt_image" id="receipt_image" onchange="loadFile(event)">
                      </div>
                    </div>
                    <div class="col-12 text-md-center d-none" id="img_output">
                      <img id="output" height="160" />
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-success btn-modal-selesaikan">Selesaikan</button>
                </div>
              </div>
            </div>
          </div>
  
          <div class="modal fade" id="modalKonfirmasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel2">Konfirmasi</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <h5>Apakah yakin ingin menyelesaikan produk?</h5>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#modal-finish-product">Kembali</button>
                  <button type="submit" class="btn btn-success">Ya</button>
                </div>
              </div>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')
<script src="{{url('/')}}/main/js/helper/input-image-view.js"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
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
                      window.location = '/delivery/on-going/resendHaistar/' + deliveryOrder
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  // Event listener saat tombol selesaikan ekspedisi diklik
  $('.btn-req-cancel-haistar').on('click', function (e) {
      e.preventDefault();
      const deliveryOrder = $(this).data("delivery-order");
      const expedition = $(this).data("expedition");
      $.confirm({
          title: 'Request Cancel Haistar',
          content: `Apakah yakin ingin mengajukan pembatalan order haistar dengan ID <b>${deliveryOrder}</b>?`,
          closeIcon: true,
          type: 'red',
          typeAnimated: true,
          buttons: {
              ya: {
                  btnClass: 'btn-danger',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      window.location = '/delivery/on-going/requestCancelHaistar/' + deliveryOrder + '/' + expedition
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
                      window.location = '/delivery/on-going/confirmExpedition/finish/' + expedition
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
                      window.location = '/delivery/on-going/confirmExpedition/cancel/' + expedition
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  $(".btn-finish-product").click(function() {
    const product = $(this).data("product");
    const qty = $(this).data("qty");
    const store = $(this).data("store");
    const expedition = $(this).data("expedition-detail");

    const a = $("#form-selesaikan").attr("action", `/delivery/on-going/confirmProduct/finish/${expedition}`);

    $('#modal-finish-product').modal('show').on('shown.bs.modal', function() {
      $("#detail").html(`Selesaikan produk <b>${product}</b> dari <b>${store}</b> <br> Jumlah dikirim : <b id="qty-kirim">${qty}</b>`);
    });
  });
  
  $("#receipt_image").change(function (){
    $("#img_output").removeClass("d-none");
  })

  let Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
  });

  $('#receipt_qty').keyup(function () {
    const maxQty = $("#qty-kirim").text();
    const qtyVal = $(this).val();

    const maxBadStock = maxQty - qtyVal;
    $("#badstock_qty").attr({"max" : maxBadStock, "min" : 0});
    $("#maksimum-badstock").html(`Maksimum  : ${maxBadStock}`);
  });
  
  $('#badstock_qty').keyup(function () {
    const maxQty = $("#qty-kirim").text();
    const qtyVal = $(this).val();
    
    const maxQtyDiterima = maxQty - qtyVal;
    $("#receipt_qty").attr({"max" : maxQtyDiterima, "min" : 0});
    $("#maksimum").html(`Maksimum  : ${maxQtyDiterima}`);
  });

  $(".btn-modal-selesaikan").click(function () {
    const form = $(this).parent().prev();
    const qtyVal = form.find("#receipt_qty").val();
    const maxQty = form.find("#receipt_qty").attr("max");
    const qtyBadstockVal = form.find("#badstock_qty").val();
    const maxQtyBadStock = form.find("#badstock_qty").attr("max");
    const imgVal = form.find("#receipt_image").val();

    let next = true;
    if (!qtyVal) {
      Toast.fire({
        icon: "error",
        title: "Harap isi Qty Diterima!",
      });
      return (next = false);
    }
    if (qtyVal <= 0) {
      Toast.fire({
        icon: "error",
        title: "Qty Diterima harus lebih dari 0!",
      });
      return (next = false);
    }
    if (Number(qtyVal) > Number(maxQty)) {
      Toast.fire({
        icon: "error",
        title: "Qty Diterima melebihi maksimum!",
      });
      return (next = false);
    }
    if (!qtyBadstockVal) {
      Toast.fire({
        icon: "error",
        title: "Harap isi Qty BadStock!",
      });
      return (next = false);
    }
    if (qtyBadstockVal < 0) {
      Toast.fire({
        icon: "error",
        title: "Qty BadStock minimum 0!",
      });
      return (next = false);
    }
    if (Number(qtyBadstockVal) > Number(maxQtyBadStock)) {
      Toast.fire({
        icon: "error",
        title: "Qty BadStock melebihi maksimum!",
      });
      return (next = false);
    }
    if (!imgVal) {
      Toast.fire({
        icon: "error",
        title: "Harap isi Foto Bukti Terima!",
      });
      return (next = false);
    }

    if (next == true) {
      $('#modal-finish-product').modal('hide');
      $('#modalKonfirmasi').modal('show');
    }
  });

  // Event listener saat tombol batalkan product diklik
  let csrf = $('meta[name="csrf_token"]').attr("content");
  $('.btn-cancel-product').on('click', function (e) {
      e.preventDefault();
      const product = $(this).data("product");
      const store = $(this).data("store");
      const expedition = $(this).data("expedition-detail");
      $.confirm({
          title: 'Konfirmasi Order',
          content: `Apakah yakin ingin membatalkan produk <b>${product}</b> dari <b>${store}</b>?
              <form action="/delivery/on-going/confirmProduct/cancel/${expedition}" method="post">
                <input type="hidden" name="_token" value="${csrf}">
              </form>`,
          closeIcon: true,
          type: 'red',
          typeAnimated: true,
          buttons: {
              batalkan: {
                  btnClass: 'btn-danger',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                    this.$content.find("form").submit();
                  }
              },
              tidak: function () {
              }
          }
      });
  });

  $('.lihat-bukti').on('click', function (e) {
        e.preventDefault();
        const urlImg = $(this).attr("href");
        const storeName = $(this).data("store");
        const product = $(this).data("product");
        $.dialog({
            title: `${product} - ${storeName}`,
            content: `<img  style="object-fit: contain; height: 330px; width: 100%;" src="${urlImg}">`,
        });
    });
</script>
@endsection