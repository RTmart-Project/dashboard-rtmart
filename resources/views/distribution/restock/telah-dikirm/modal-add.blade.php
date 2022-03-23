<div class="modal-header">
  <h4 class="modal-title">Buat Delivery Order</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body py-1">
  @if ($promisedQty == $deliveryOrderQty )
  <div class="callout callout-info my-2">
    <h5 class="py-2">Semua Delivery Order Telah Dibuat.</h5>
    {{-- <h6 class="d-inline mr-2">Lihat</h6>
    <button type="button" class="btn btn-warning mb-2" data-toggle="modal" data-target="#request-do">
      Request Delivery Order
    </button>
    <h6 class="d-inline mx-2">atau</h6>
    <button type="button" class="btn btn-info mb-2" data-target="#detail-do" data-toggle="modal">
      Detail Delivery Order
    </button> --}}
  </div>
  @else
  <form action="" method="post" id="form-add-do">
    @csrf
    <div class="row m-0">
      <div class="col-md-6 col-12">
        <div class="form-group">
          <label class="my-0" for="created_date_do">Waktu Pengiriman :</label>
          <input type="datetime-local" class="form-control" name="created_date_do" id="created_date_do" required>
        </div>
      </div>
      <div class="col-md-6 col-12">
        <div class="form-group">
          <label class="my-0" for="vehicle">Jenis Kendaraan</label>
          <select name="vehicle" id="vehicle"
            class="form-control border selectpicker @if($errors->has('vehicle')) is-invalid @endif"
            data-live-search="true" title="Pilih Jenis Kendaraan" required>
            @foreach ($vehicles as $vehicle)
            <option value="{{ $vehicle->VehicleID }}">{{ $vehicle->VehicleName }}</option>
            @endforeach
          </select>
          @if($errors->has('vehicle'))
          <span class="error invalid-feedback">{{ $errors->first('vehicle') }}</span>
          @endif
        </div>
      </div>
    </div>
    <div class="row m-0">
      <div class="col-md-4 col-12">
        <div class="form-group">
          <label class="my-0" for="driver">Driver</label>
          <select name="driver" id="driver"
            class="form-control border selectpicker @if($errors->has('driver')) is-invalid @endif"
            data-live-search="true" title="Pilih Driver" required>
            @foreach ($drivers as $driver)
            <option value="{{ $driver->UserID }}">{{ $driver->Name }}</option>
            @endforeach
          </select>
          @if($errors->has('driver'))
          <span class="error invalid-feedback">{{ $errors->first('driver') }}</span>
          @endif
        </div>
      </div>
      <div class="col-md-4 col-12">
        <div class="form-group">
          <label class="my-0" for="helper">Helper</label>
          <select name="helper" id="helper"
            class="form-control border selectpicker @if($errors->has('helper')) is-invalid @endif"
            data-live-search="true" title="Pilih Helper">
            @foreach ($helpers as $helper)
            <option value="{{ $helper->UserID }}">{{ $helper->Name }}</option>
            @endforeach
          </select>
          @if($errors->has('helper'))
          <span class="error invalid-feedback">{{ $errors->first('helper') }}</span>
          @endif
        </div>
      </div>
      <div class="col-md-4 col-12">
        <div class="form-group">
          <label class="my-0" for="license_plate">Plat Nomor Kendaraan</label>
          <input type="text" name="license_plate" id="license_plate"
            class="form-control @if($errors->has('license_plate')) is-invalid @endif"
            placeholder="Masukkan Plat Nomor Kendaraan" onkeyup="this.value = this.value.toUpperCase();"
            autocomplete="off" required>
          @if($errors->has('license_plate'))
          <span class="error invalid-feedback">{{ $errors->first('license_plate') }}</span>
          @endif
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
    <button type="submit" id="btn-do" disabled="disabled" class="btn btn-primary float-right my-3">Buat DO</button>
  </form>
  @endif
</div>
{{-- <div class="modal-footer justify-content-between">
</div> --}}