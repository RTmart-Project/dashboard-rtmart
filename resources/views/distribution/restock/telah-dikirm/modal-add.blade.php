<div class="modal-header">
  <h4 class="modal-title">Buat Delivery Order</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body py-1">
  @if ($promisedQty == $deliveryOrderQty )
  <div class="callout callout-info my-2">
  </div>
  @else
  <form action="" method="post" id="form-add-do">
    @csrf
    <div class="row m-0">
      <div class="col-12">
        <div class="form-group">
          <label class="my-0" for="created_date_do">Plan Waktu Pengiriman :</label>
          <input type="datetime-local" class="form-control" name="created_date_do" id="created_date_do" required>
        </div>
      </div>
    </div>
    <div class="callout callout-danger d-md-none py-2 mb-1">
      <p><strong>Direkomendasikan untuk buka di LAPTOP / PC</strong></p>
    </div>
    <div class="callout callout-warning py-2">
      <p>Pilih terlebih dahulu barang yang ingin dikirim</p>
    </div>

    {{-- Loop Haistar Product --}}
    @php
    $firstInLoop = true;
    @endphp

    @if ($isHasHaistar == 1)
    @foreach ($productAddDO as $item)
    @if ($item->PromisedQuantity != $item->QtyDO && $item->IsHaistarProduct == 1)
    @if ($firstInLoop == true)
    <label class="m-0">Produk Haistar</label>
    @endif
    <div class="row text-center border-bottom m-0 add-do">
      <div class="col-1 align-self-center">
        <input type="checkbox" class="check_haistar">
      </div>
      <div class="col-3 align-self-center">
        <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}" alt="" width="80">
        <p class="mb-1">{{ $item->ProductName }}</p>
        <input type="hidden" name="product_id[]" id="product_id" value="{{ $item->ProductID }}" disabled="disabled">
      </div>
      <div class="col-2 align-self-center">
        <label>Qty Beli</label>
        <p>{{ $item->PromisedQuantity }}x
          <span class="nett-price">{{ Helper::formatCurrency($item->Nett, '@Rp ') }}</span>
        </p>
      </div>
      <div class="col-2 align-self-center">
        <label>Qty Belum Dikirim</label>
        <p>{{ $item->PromisedQuantity - $item->QtyDO }}</p>
        <input type="hidden" name="max_qty_do[]" id="max_qty_do" value="{{ $item->PromisedQuantity - $item->QtyDO }}"
          disabled="disabled">
      </div>
      <div class="col-2 align-self-center">
        <label>Qty Kirim</label>
        <input type="number" name="qty_do[]" id="qty_do" class="form-control text-center qty-do"
          max="{{ $item->PromisedQuantity - $item->QtyDO }}" min="1" disabled="disabled" required>
      </div>
      <div class="col-2 align-self-center">
        <label>Total Harga</label>
        <p>Rp <span class="total-price">0</span></p>
      </div>
    </div>
    @php
    $firstInLoop = false;
    @endphp
    @endif
    @endforeach
    @endif

    {{-- Loop RTmart Product --}}
    @php
    $firstInLoop = true;
    @endphp

    @foreach ($productAddDO as $item)
    @if ($item->PromisedQuantity != $item->QtyDO && $item->IsHaistarProduct == 0 && ($item->PromisedQuantity -
    $item->QtyDO > 0)) @if ($firstInLoop==true) <label class="m-0">Produk RTmart</label>
    @endif
    <div class="row text-center border-bottom m-0 add-do">
      <div class="col-1 align-self-center">
        <input type="checkbox" class="check_rtmart">
      </div>
      <div class="col-3 align-self-center">
        <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}" alt="" width="80">
        <p class="mb-1">{{ $item->ProductName }}</p>
        <input type="hidden" name="product_id[]" id="product_id" value="{{ $item->ProductID }}" disabled="disabled">
      </div>
      <div class="col-2 align-self-center">
        <label>Qty Beli</label>
        <p>{{ $item->PromisedQuantity }}x
          <span class="nett-price">{{ Helper::formatCurrency($item->Nett, '@Rp ') }}</span>
        </p>
      </div>
      <div class="col-2 align-self-center">
        <label>Qty Belum Dikirim</label>
        <p>{{ $item->PromisedQuantity - $item->QtyDO }}</p>
        <input type="hidden" name="max_qty_do[]" id="max_qty_do" value="{{ $item->PromisedQuantity - $item->QtyDO }}"
          disabled="disabled">
      </div>
      <div class="col-2 align-self-center">
        <label>Qty Kirim</label>
        <input type="number" name="qty_do[]" id="qty_do" class="form-control text-center qty-do"
          max="{{ $item->PromisedQuantity - $item->QtyDO }}" min="1" disabled="disabled" required>
      </div>
      <div class="col-2 align-self-center">
        <label>Total Harga</label>
        <p>Rp <span class="total-price">0</span></p>
      </div>
    </div>
    @php
    $firstInLoop = false;
    @endphp
    @endif
    @endforeach
    <p class="my-2 mr-md-4 text-right"><b>Subtotal : </b>Rp <span class="subtotal-do">0</span></p>
    <div class="d-flex justify-content-between">
      <div class="callout callout-danger my-2 p-2">
        <p>Setelah berhasil membuat DO, masuk ke menu Delivery Plan untuk melakukan pengiriman.</p>
      </div>
      <button type="submit" id="btn-do" disabled="disabled" class="btn btn-primary my-2 float-right">Buat DO</button>
    </div>
  </form>
  @endif
</div>