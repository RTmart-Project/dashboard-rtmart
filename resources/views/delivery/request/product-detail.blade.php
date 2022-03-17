<div class="callout callout-danger d-md-none py-2 my-2">
  <p><strong>Direkomendasikan untuk buka di LAPTOP / PC</strong></p>
</div>
<div class="warning-choose-product callout callout-warning p-2 my-2">
  <p>Pilih terlebih dahulu barang yang ingin dikirim</p>
</div>
@foreach ($detailProduct->groupBy('DeliveryOrderID')->all() as $item)
<div class="card card-info">
  <div class="card-header">
    <h3 class="card-title">
      <b class="d-block d-md-inline">Delivery Order ID :</b>
      <span class="do-id">{{ $item[0]->DeliveryOrderID }}</span> <br>
      <span>{{ $item[0]->StockOrderID }} - {{ $item[0]->StoreName }}</span>
    </h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <div class="card-body px-3 py-2 request-do-wrapper">
    @php
    $firstInLoopHaistar = true;
    $firstInLoopRTmart = true;
    @endphp

    {{-- Loop Haistar Product --}}
    @foreach ($item as $product)
    @if ($product->IsHaistarProduct == 1)
    @if ($firstInLoopHaistar == true)
    <div class="d-flex label-product">
      <label class="m-0">Produk Haistar</label>
    </div>
    @endif
    <div class="row text-center border-bottom m-0 request-do">
      <div class="col-1 align-self-center">
        <input type="checkbox" class="check_haistar larger" value="{{ $product->DeliveryOrderDetailID }}">
      </div>
      <div class="col-3 col-md-4 align-self-center">
        <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
        <p>{{ $product->ProductName }}</p>
        <input type="hidden" name="product_id_haistar[]" id="product-id" value="{{ $product->ProductID }}"
          disabled="disabled">
      </div>
      <div class="col-5 col-md-4 align-self-center">
        <label class="d-block">Qty</label>
        <div>
          <input type="hidden" name="max_qty_request_do_haistar[]"
            value="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}">
          <input type="number" class="form-control qty-request-do text-sm text-center p-0 d-inline"
            value="{{ $product->QtyDO }}" id="qty-request-do" name="qty_request_do_haistar[]"
            style="width: 40px; height: 30px;"
            max="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}" min="1" required
            disabled="disabled">
          <span class="price-do">{{ Helper::formatCurrency($product->PriceDO, 'x @Rp ') }}</span><br>
          <small>
            Max Qty : <span id="max-qty">{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}</span>
          </small>
        </div>
      </div>
      <div class="col-3 align-self-center">
        <label>Total Harga</label>
        <p class="price-total">{{ Helper::formatCurrency($product->QtyDO * $product->PriceDO, 'Rp ') }}</p>
      </div>
    </div>
    @php
    $firstInLoopHaistar = false;
    @endphp
    @endif
    @endforeach

    {{-- Loop RTmart Product --}}
    @foreach ($item as $product)
    @if ($product->IsHaistarProduct == 0)
    @if ($firstInLoopRTmart == true)
    <div class="d-flex label-product">
      <label class="m-0">Produk RTmart</label>
    </div>
    @endif
    <div class="row text-center border-bottom m-0 request-do">
      <div class="col-1 align-self-center">
        <input type="checkbox" class="check_rtmart larger" value="{{ $product->DeliveryOrderDetailID }}">
        <input type="hidden" value="{{ $item[0]->DeliveryOrderID }}">
      </div>
      <div class="col-4 align-self-center">
        <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
        <p>{{ $product->ProductName }}</p>
        <input type="hidden" name="product_id_rtmart[]" id="product-id" value="{{ $product->ProductID }}"
          disabled="disabled">
      </div>
      <div class="col-4 align-self-center">
        <label class="d-block">Qty</label>
        <div>
          <input type="hidden" name="max_qty_request_do_rtmart[]"
            value="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}">
          <input type="number" class="form-control qty-request-do text-sm text-center p-0 d-inline"
            value="{{ $product->QtyDO }}" id="qty-request-do" name="qty_request_do_rtmart[]"
            style="width: 40px; height: 30px;"
            max="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}" min="1" required
            disabled="disabled">
          <span class="price-do">{{ Helper::formatCurrency($product->PriceDO, 'x @Rp ') }}</span><br>
          <small>
            Max Qty : <span id="max-qty">{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}</span>
          </small>
        </div>
      </div>
      <div class="col-3 align-self-center">
        <label>Total Harga</label>
        <p class="price-total">{{ Helper::formatCurrency($product->QtyDO * $product->PriceDO, 'Rp ') }}</p>
      </div>
    </div>
    @php
    $firstInLoopRTmart = false;
    @endphp
    @endif
    @endforeach

  </div>
</div>
@endforeach