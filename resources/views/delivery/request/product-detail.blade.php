<div class="callout callout-danger d-md-none py-2 my-2">
  <p><strong>Direkomendasikan untuk buka di LAPTOP / PC</strong></p>
</div>
<div class="warning-choose-product callout callout-warning p-2 my-2">
  <p>Pilih terlebih dahulu barang yang ingin dikirim</p>
</div>
@foreach ($detailProduct->groupBy('DeliveryOrderID')->all() as $item)
<div class="card card-info card-outline card-do">
  <div class="card-header">
    <h3 class="card-title">
      <b class="d-block d-md-inline">Delivery Order ID :</b>
      <span class="do-id">{{ $item[0]->DeliveryOrderID }}</span> <br>
      <a href="{{ route('distribution.restockDetail', ['stockOrderID' => $item[0]->StockOrderID]) }}"
        id="stock-order-id" target="_blank">{{ $item[0]->StockOrderID }}</a><br>
      <a href="{{ route('merchant.product', ['merchantId' => $item[0]->MerchantID ]) }}" target="_blank">
        {{ $item[0]->MerchantID }}
      </a> - {{ $item[0]->StoreName }} - {{ $item[0]->PhoneNumber }}
    </h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <div class="card-body px-3 py-2 request-do-wrapper">
    @php
    $subtotal = 0;
    $firstInLoopHaistar = true;
    $firstInLoopRTmart = true;
    @endphp

    {{-- Loop Haistar Product --}}
    @foreach ($item as $product)
    @if ($product->IsHaistarProduct == 1)
    @php
    $subtotal += $product->QtyDO * $product->PriceDO;
    @endphp
    @if ($firstInLoopHaistar == true)
    <div class="d-flex label-product">
      <label class="m-0">Produk Haistar</label>
    </div>
    @endif
    <div class="row text-center border-bottom m-0 request-do">
      <div class="col-2 align-self-center">
        <select class="form-control form-control-sm mb-2 send-by" name="send_by" id="send_by">
          <option value="HAISTAR">Kirim Haistar</option>
          <option value="RT MART">Kirim RT Mart</option>
        </select>
        <input type="checkbox" class="check_haistar larger" value="{{ $product->DeliveryOrderDetailID }}">
      </div>
      <div class="col-2 align-self-center">
        <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
        <p id="product-name">{{ $product->ProductName }}</p>
        <input type="hidden" name="product_id_haistar[]" id="product-id" value="{{ $product->ProductID }}"
          disabled="disabled">
        <input type="hidden" name="distributor[]" id="distributor" value="HAISTAR">
        <input type="hidden" name="distributor_id[]" id="distributor-id" value="{{ $product->DistributorID }}">
      </div>
      <div class="col-1 align-self-center">
        <label class="d-block">Qty DO</label>
        <p>{{ $product->QtyDO }}</p>
      </div>
      <div class="col-3 align-self-center">
        <label class="d-block">Qty Kirim</label>
        <div>
          <input type="hidden" name="max_qty_request_do_haistar[]"
            value="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}">
          <input type="number"
            class="form-control qty-request-do text-sm text-center p-0 d-inline {{ $product->ProductID }}"
            id="qty-request-do" name="qty_request_do_haistar[]" style="width: 40px; height: 30px;"
            max="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}" min="1" required
            disabled="disabled">
          <span class="price-do">{{ Helper::formatCurrency($product->PriceDO, 'x @Rp ') }}</span><br>
          <small>
            Max Qty dapat dikirim : <span id="max-qty">{{ $product->PromisedQty - $product->QtyDONotBatal }}</span>
          </small>
          <small id="exist-stock" class="d-none">
            Qty Stok Tersedia : <span id="exist-qty">{{ $product->QtyStock }}</span>
          </small>
        </div>
      </div>
      <div class="col-2 align-self-center">
        <label>Total Harga</label>
        <p class="price-total">Rp 0</p>
      </div>
      <div class="col-2 d-none select-source">
        <label class="d-block" for="label">Source Produk</label>
        <select id="investor" class="form-control form-control-sm source-investor">
          @foreach ($investors as $investor)
              <option value="{{ $investor->InvestorID }}" {{ $investor->InvestorID == 1 ? 'selected' : ''}}>{{ $investor->InvestorName }}</option>
          @endforeach
        </select>
        <select id="label" class="form-control form-control-sm source-product">
          <option value="PKP" selected>PKP</option>
          <option value="NON-PKP">NON-PKP</option>
        </select>
        <span id="exist-qty-perinvestor">Stok {{ $firstInvestor->InvestorName }} PKP : {{ $product->QtyStockPKP }}</span>
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
    @php
    $subtotal += $product->QtyDO * $product->PriceDO;
    @endphp
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
      <div class="col-3 align-self-center">
        <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
        <p id="product-name">{{ $product->ProductName }}</p>
        <input type="hidden" name="product_id_rtmart[]" id="product-id" value="{{ $product->ProductID }}"
          disabled="disabled">
        <input type="hidden" name="distributor[]" id="distributor" value="RT MART">
        <input type="hidden" name="distributor_id[]" id="distributor-id" value="{{ $product->DistributorID }}">
      </div>
      <div class="col-1 align-self-center">
        <label class="d-block">Qty DO</label>
        <p>{{ $product->QtyDO }}</p>
      </div>
      <div class="col-3 align-self-center">
        <label class="d-block">Qty Kirim</label>
        <div>
          <input type="hidden" name="max_qty_request_do_rtmart[]"
            value="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}">
          <input type="number"
            class="form-control form-control-sm qty-request-do text-sm text-center p-0 d-inline {{ $product->ProductID }}"
            id="qty-request-do" name="qty_request_do_rtmart[]" style="width: 40px; height: 30px;"
            max="{{ $product->QtyDO + $product->PromisedQty - $product->QtyDONotBatal }}" min="1" required
            disabled="disabled">
          <span class="price-do">{{ Helper::formatCurrency($product->PriceDO, 'x @Rp ') }}</span><br>
          <small>
            Max Qty dapat dikirim : <span id="max-qty">{{ $product->PromisedQty - $product->QtyDONotBatal }}</span>
          </small>
          <small class="d-block">
            Qty Stok Tersedia : <span id="exist-qty">{{ $product->QtyStock }}</span>
          </small>
        </div>
      </div>
      <div class="col-2 align-self-center">
        <label>Total Harga</label>
        <p class="price-total">Rp 0</p>
      </div>
      <div class="col-2">
        <label class="d-block" for="label">Source Produk</label>
        <select id="investor" class="form-control form-control-sm source-investor">
          @foreach ($investors as $investor)
              <option value="{{ $investor->InvestorID }}" {{ $investor->InvestorID == 1 ? 'selected' : ''}}>{{ $investor->InvestorName }}</option>
          @endforeach
        </select>
        <select id="label" class="form-control form-control-sm source-product">
          <option value="PKP" selected>PKP</option>
          <option value="NON-PKP">NON-PKP</option>
        </select>
        <span id="exist-qty-perinvestor">Stok {{ $firstInvestor->InvestorName }} PKP : {{ $product->QtyStockPKP }}</span>
      </div>
    </div>
    @php
    $firstInLoopRTmart = false;
    @endphp
    @endif
    @endforeach

    <div class="row">
      <div class="col-3 offset-9 text-center">
        <p class="mt-2 mb-1">
          <b>Max Nominal Kirim :
            @if ($item[0]->CountCreatedDO == 0)
            <span id="max-nominal">{{ Helper::formatCurrency(($item[0]->TotalPrice - $item[0]->SumPriceCreatedDO) / 1,
              'Rp ') }}</span>
            @else
            <span id="max-nominal">{{ Helper::formatCurrency(($item[0]->TotalPrice - $item[0]->SumPriceCreatedDO) /
              $item[0]->CountCreatedDO,
              'Rp ') }}</span>
            @endif
          </b>
        </p>
        <p class="mb-1">
          <b>SubTotal : </b>
          <span class="price-subtotal" id="price-subtotal">Rp 0</span>
        </p>
      </div>
    </div>
  </div>
</div>
@endforeach