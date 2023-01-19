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
              <span class="price-do">{{ Helper::formatCurrency($product->Price, $product->Qty .' x @Rp ') }}</span><br>
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
              <span class="price-do">{{ Helper::formatCurrency($product->Price, $product->Qty . ' x @Rp ') }}</span><br>
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

        <div class="row m-0 border-bottom justify-content-end">
          <div class="col-6 col-md-4 d-flex justify-content-between flex-column">
            <p class="text-center mt-3">
              <b>SubTotal : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->SubTotal, 'Rp ') }}</span>
            </p>
          </div>
        </div>
        <div class="row m-0 pt-2 text-center konfirmasi-request justify-content-center">
          <div class="col-6 align-self-center">
            Rencana kirim {{ date('d F Y', strtotime($item->CreatedDate)) }}
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