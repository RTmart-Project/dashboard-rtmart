<div class="modal-header">
  <h4 class="modal-title" id="modal-detail">Request Delivery Order</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <div class="callout callout-danger d-md-none py-2 mb-2">
    <p><strong>Direkomendasikan untuk buka di LAPTOP / PC</strong></p>
  </div>

  @php
  $count = 0;
  @endphp

  @foreach ($deliveryOrder as $item)
  @if ($item->StatusDO == "S028")
  @php
  $count++;
  @endphp
  <div class="card card-info card-request-do">
    <div class="card-header">
      <h3 class="card-title">
        <b class="d-block d-md-inline">Delivery Order ID :</b> <span class="do-id">{{ $item->DeliveryOrderID }}</span>
      </h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body py-1 px-2 request-do-wrapper">
      <form method="post" class="form-request-do">
        @csrf
        <input type="hidden" name="stock_order_id" value="{{ $stockOrderID }}">

        @php
        $firstInLoopHaistar = true;
        $firstInLoopRTmart = true;
        @endphp

        {{-- Loop Haistar Product --}}
        @foreach ($item->DetailProduct as $product)
        @if ($product->IsHaistarProduct == 1)
        @if ($firstInLoopHaistar == true)
        <div class="d-flex">
          <input type="checkbox" class="align-self-center mr-2 check_haistar_request" required
            id="haistar{{ $item->DeliveryOrderID }}{{ $loop->iteration }}">
          <label for="haistar{{ $item->DeliveryOrderID }}{{ $loop->iteration }}" class="m-0">Produk Haistar</label>
        </div>
        @endif
        <div class="row text-center border-bottom m-0 request-do">
          <div class="col-3 align-self-center">
            <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
          </div>
          <div class="col-3 align-self-center">
            <label>Produk</label>
            <p>{{ $product->ProductName }}</p>
            <input type="hidden" name="product_id_haistar[]" id="product-id" value="{{ $product->ProductID }}">
          </div>
          <div class="col-3 align-self-center">
            <label class="d-block">Qty</label>
            <p>
              <input type="hidden" name="max_qty_request_do_haistar[]"
                value="{{ $product->OrderQty - $product->QtyDOSelesai - $product->QtyDODlmPengiriman }}">
              <input type="number" class="form-control qty-request-do text-sm text-center p-0 d-inline"
                value="{{ $product->Qty }}" id="qty-request-do" name="qty_request_do_haistar[]"
                style="width: 40px; height: 30px;"
                max="{{ $product->OrderQty - $product->QtyDOSelesai - $product->QtyDODlmPengiriman }}" min="1" required>
              <span class="price-do">{{ Helper::formatCurrency($product->Price, 'x @Rp ') }}</span>
              <input type="hidden" name="price_haistar[]" value="{{ $product->Price }}">
            </p>
          </div>
          <div class="col-3 align-self-center">
            <label>Total Harga</label>
            <p class="price-total">{{ Helper::formatCurrency($product->Qty * $product->Price, 'Rp ') }}</p>
          </div>
        </div>
        @php
        $firstInLoopHaistar = false;
        @endphp
        @endif
        @endforeach

        {{-- Loop RTmart Product --}}
        @foreach ($item->DetailProduct as $product)
        @if ($product->IsHaistarProduct == 0)
        @if ($firstInLoopRTmart == true)
        <div class="d-flex">
          <input type="checkbox" class="align-self-center mr-2 check_rtmart_request" required
            id="rtmart{{ $item->DeliveryOrderID }}{{ $loop->iteration }}">
          <label for="rtmart{{ $item->DeliveryOrderID }}{{ $loop->iteration }}" class="m-0">Produk RTmart</label>
        </div>
        @endif
        <div class="row text-center border-bottom m-0 request-do">
          <div class="col-3 align-self-center">
            <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
          </div>
          <div class="col-3 align-self-center">
            <label>Produk</label>
            <p>{{ $product->ProductName }}</p>
            <input type="hidden" name="product_id_rtmart[]" id="product-id" value="{{ $product->ProductID }}">
          </div>
          <div class="col-3 align-self-center">
            <label class="d-block">Qty</label>
            <p>
              <input type="hidden" name="max_qty_request_do_rtmart[]"
                value="{{ $product->OrderQty - $product->QtyDOSelesai - $product->QtyDODlmPengiriman }}">
              <input type="number" class="form-control qty-request-do text-sm text-center p-0 d-inline"
                value="{{ $product->Qty }}" id="qty-request-do" name="qty_request_do_rtmart[]"
                style="width: 40px; height: 30px;"
                max="{{ $product->OrderQty - $product->QtyDOSelesai - $product->QtyDODlmPengiriman }}" min="1" required>
              <span class="price-do">{{ Helper::formatCurrency($product->Price, 'x @Rp ') }}</span>
              <input type="hidden" name="price_rtmart[]" value="{{ $product->Price }}">
            </p>
          </div>
          <div class="col-3 align-self-center">
            <label>Total Harga</label>
            <p class="price-total">{{ Helper::formatCurrency($product->Qty * $product->Price, 'Rp ') }}</p>
          </div>
        </div>
        @php
        $firstInLoopRTmart = false;
        @endphp
        @endif

        @endforeach

        <div class="row m-0 border-bottom">
          <div class="col-6 col-md-8 pt-2">
            <div class="row m-0">
              <div class="col-md-6 col-12 pl-0">
                <div class="form-group m-0">
                  <label class="my-0" for="driver">Driver</label>
                  <select name="driver" id="driver" class="form-control border selectpicker" data-live-search="true"
                    title="Pilih Driver" required>
                    @foreach ($drivers as $driver)
                    <option value="{{ $driver->UserID }}" {{ collect($item->DriverID)->contains($driver->UserID) ?
                      'selected' : '' }}>
                      {{ $driver->Name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-12 pl-0">
                <div class="form-group m-0">
                  <label class="my-0" for="helper">Helper</label>
                  <select name="helper" id="helper" class="form-control border selectpicker" data-live-search="true"
                    title="Pilih Helper" required>
                    @foreach ($helpers as $helper)
                    <option value="{{ $helper->UserID }}" {{ collect($item->HelperID)->contains($helper->UserID) ?
                      'selected' : '' }}>
                      {{ $helper->Name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-12 pl-0">
                <div class="form-group m-0">
                  <label class="my-0" for="vehicle">Jenis Kendaraan</label>
                  <select name="vehicle" id="vehicle" class="form-control border selectpicker" data-live-search="true"
                    title="Pilih Jenis Kendaraan" required>
                    @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->VehicleID }}" {{ collect($item->
                      VehicleID)->contains($vehicle->VehicleID) ? 'selected' : '' }}>
                      {{ $vehicle->VehicleName }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-12 pl-0">
                <div class="form-group m-0">
                  <label class="my-0" for="license_plate">Plat Nomor Kendaraan</label>
                  <input type="text" name="license_plate" id="license_plate" class="form-control mb-2"
                    value="{{ $item->VehicleLicensePlate }}" onkeyup="this.value = this.value.toUpperCase();"
                    autocomplete="off" placeholder="Cth : B 4321 JKT" required>
                </div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4 d-flex justify-content-between flex-column">
            <p class="text-center mt-3">
              <b>SubTotal : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->SubTotal, 'Rp ') }}</span>
            </p>
          </div>
        </div>
        <div class="row m-0 pt-2 text-center konfirmasi-request">
          <div class="col-6 align-self-center">
            <div class="d-flex flex-column flex-wrap">
              <b class="mb-2">{{ $item->StatusOrder }}</b>
              <div class="d-flex justify-content-center" style="gap: 8px">
                <button href="#" class="btn btn-xs btn-success btn-confirm-request-do mb-1"
                  data-do-id="{{ $item->DeliveryOrderID }}">
                  Konfirmasi Pesanan
                </button>
                <a href="#" class="btn btn-xs btn-danger btn-cancel-request-do mb-1"
                  data-do-id="{{ $item->DeliveryOrderID }}" data-stockorder-id="{{ $stockOrderID }}">
                  Batalkan Pesanan
                </a>
              </div>
            </div>
          </div>
          <div class="col-6 align-self-center">
            Dikirim {{ date('d M Y H:i', strtotime($item->CreatedDate)) }}
            <input type="hidden" name="created_date" value="{{ $item->CreatedDate }}">
          </div>
        </div>
      </form>
    </div>
  </div>
  @endif
  @endforeach

  @if ($count == 0)
  <div class="callout callout-info my-2">
    <h5>Tidak ada request delivery order.</h5>
    <button type="button" class="btn btn-primary" data-target="#add-do" data-toggle="modal">
      Buat Delivery Order
    </button>
  </div>
  @endif
</div>
{{-- <div class="modal-footer justify-content-end">

</div> --}}

@section('js-detail-restock')
<script>
  // Event listener saat mengetik qty request delivery order
  $('.qty-request-do').on('keyup', function (e) {
      e.preventDefault();
      const priceProduct = $(this).next().text().replaceAll("x @Rp ", "").replaceAll(".", "");
      const qtyDO = $(this).val();
      
      const totalPriceProduct = Number(qtyDO) * Number(priceProduct);
      $(this).parent().parent().next().children().last().html('Rp ' + thousands_separators(totalPriceProduct));
      
      const totalPriceAllProductArr = $(this).closest('.request-do-wrapper').find('.price-total').text().replace("Rp ", "").replaceAll("Rp ", ",").replaceAll(".", "").split(",");

      let priceAllProductNumber = totalPriceAllProductArr.map(Number);
      let subTotalDO = 0;
      $.each(priceAllProductNumber, function() {
          subTotalDO += this;
      });

      $(this).closest('.request-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(subTotalDO));
  });

  $('.check_rtmart_request').change(function() {
      if ($('.check_rtmart_request:checked').length > 0) {
          $('.check_haistar_request').prop('disabled', true);
          let deliveryOrderID = $(this).closest('.card-request-do').find('.do-id').text();
          $(this).closest('.request-do-wrapper').find('.form-request-do').attr('action', `/distribution/restock/confirm/request/${deliveryOrderID}/rtmart`);
      } else {
          $('.check_haistar_request').prop('disabled', false);
      }
  });

  $('.check_haistar_request').change(function() {
      if ($('.check_haistar_request:checked').length > 0) {
          $('.check_rtmart_request').prop('disabled', true);
          let deliveryOrderID = $(this).closest('.card-request-do').find('.do-id').text();
          $(this).closest('.request-do-wrapper').find('.form-request-do').attr('action', `/distribution/restock/confirm/request/${deliveryOrderID}/haistar`);
      } else {
          $('.check_rtmart_request').prop('disabled', false);
      }
  });

  // Event listener saat tombol batal diklik
  $('.konfirmasi-request').on('click', '.btn-cancel-request-do', function (e) {
      e.preventDefault();
      const deliveryOrderId = $(this).data("do-id");
      const stockOrderId = $(this).data("stockorder-id");
      $.confirm({
          type: 'red',
          typeAnimated: true,
          title: 'Batalkan Pesanan Delivery Order',
          content: `Yakin ingin membatalkan pesanan <b>${deliveryOrderId}</b>? <br>
              <label class="mt-2 mb-0">Alasan Batal:</label>
              <form action="/distribution/restock/reject/request/${deliveryOrderId}" method="post">
                  @csrf
                  <input type="hidden" value="${stockOrderId}" name="stock_order_id">
                  <input type="text" class="form-control cancel_reason" name="cancel_reason" autocomplete="off">
              </form>`,
          closeIcon: true,
          buttons: {
              batalkan: {
                  btnClass: 'btn-red',
                  draggable: true,
                  dragWindowGap: 0,
                  action: function () {
                      let cancel_reason = this.$content.find('.cancel_reason').val();
                      if (!cancel_reason) {
                          $.alert('Alasan tidak boleh kosong', 'Alasan Batal');
                          return false;
                      }
                      let form = this.$content.find('form').submit();
                  }
              },
              kembali: function () {
              }
          }
      });
  });
</script>
@endsection