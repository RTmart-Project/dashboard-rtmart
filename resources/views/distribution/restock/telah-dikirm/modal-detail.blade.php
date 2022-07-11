<div class="modal-header">
  <h4 class="modal-title">Detail Delivery Order</h4>
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
  @if ($item->StatusDO != "S028")
  @php
  $count++;
  @endphp
  <div
    class="card 
    @if ($item->StatusDO == 'S025') card-success @elseif($item->StatusDO == 'S024') card-warning @else card-danger @endif detail-do">
    <div class="card-header">
      <h3 class="card-title">
        <b class="d-block d-md-inline">Delivery Order ID :</b> {{ $item->DeliveryOrderID }}
      </h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body py-1 px-2 detail-do-wrapper">
      <form action="{{ route('distribution.updateQtyDO', ['deliveryOrderId' => $item->DeliveryOrderID]) }}" method="get"
        id="edit-qty-do{{ $loop->iteration }}">
        @csrf
        @foreach ($item->DetailProduct as $product)
        <div class="row text-center border-bottom m-0 edit-do">
          <div class="col-3 align-self-center">
            <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
            <br>
            <span class="badge badge-info">{{ $product->Distributor == "HAISTAR" ? "HAISTAR" : "" }}</span>
          </div>
          <div class="col-3 align-self-center">
            <label>Produk</label>
            <p class="mb-1">{{ $product->ProductName }}</p>
            <input type="hidden" name="product_id[]" value="{{ $product->ProductID }}">
            @if ($product->StatusOrder == "Selesai")
                <span class="badge badge-success">{{ $product->StatusOrder }}</span>
            @elseif ($product->StatusOrder == "Dibatalkan")
                <span class="badge badge-danger">{{ $product->StatusOrder }}</span>
            @else
                <span class="badge badge-warning">{{ $product->StatusOrder }}</span>
            @endif
          </div>
          <div class="col-3 align-self-center">
            <label class="d-block">Qty</label>
            @if ($item->StatusOrder != "Dalam Pengiriman" || $item->Distributor == "HAISTAR")
            <p>{{ $product->Qty }}x {{ Helper::formatCurrency($product->Price, '@Rp ') }}</p>
            @else
            <p>
              <input type="hidden" name="max_edit_qty_do[]"
                value="{{ $product->OrderQty - $product->QtyDOSelesai - $product->QtyDODlmPengiriman + $product->Qty }}">
              {{-- <input type="number" class="form-control edit-qty-do text-sm text-center p-0 d-inline"
                value="{{ $product->Qty }}" name="edit_qty_do[]" style="width: 40px; height: 30px;"
                max="{{ $product->OrderQty - $product->QtyDOSelesai - $product->QtyDODlmPengiriman + $product->Qty }}"
                min="0" required> --}}
              <span class="price-do">{{ Helper::formatCurrency($product->Price, $product->Qty . ' x @Rp ') }}</span><br>
              {{-- <small>Max Qty dapat diubah : {{ $product->OrderQty - $product->QtyDOSelesai -
                $product->QtyDODlmPengiriman + $product->Qty }}</small> --}}
            </p>
            @endif
          </div>
          <div class="col-3 align-self-center">
            <label>Total Harga</label>
            <p class="price-total">{{ Helper::formatCurrency($product->Qty * $product->Price, 'Rp ') }}</p>
          </div>
        </div>
        @endforeach
        <div class="row m-0 border-bottom d-flex justify-content-end">
          {{-- <div class="col-6 col-md-8 pt-2">
            @if ($item->StatusOrder != "Dalam Pengiriman" || $item->Distributor == "HAISTAR")
            <p class="m-0"><b>Driver : </b>{{ $item->Name }}</p>
            <p class="m-0"><b>Helper : </b>{{ $item->HelperName }}</p>
            <p class="m-0"><b>Kendaraan : </b>{{ $item->VehicleName }} {{ $item->VehicleLicensePlate }}</p>
            @if ($item->Distributor == "HAISTAR")
            <span class="badge badge-info">{{ $item->Distributor }}</span>
            @endif
            @else
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
                    autocomplete="off" required>
                </div>
              </div>
            </div>
            @endif
          </div> --}}
          <div class="col-4">
            <div class="justify-content-between my-1 d-flex">
              <b>SubTotal : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->SubTotal, 'Rp ') }}</span>
            </div>
            
            @if ($item->StatusDO == "S024" || $item->StatusDO == "S025")
            @if ($item->Discount != null && $item->Discount != 0)
            <div class="justify-content-between mb-1 d-flex">
              <b>Diskon : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->Discount, 'Rp ') }}</span>
            </div>
            @endif
            
            @if ($item->ServiceCharge != null && $item->ServiceCharge != 0)
            <div class="justify-content-between mb-1 d-flex">
              <b>Biaya Layanan : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->ServiceCharge, 'Rp ') }}</span>
            </div>
            @endif
            
            @if ($item->DeliveryFee != null && $item->DeliveryFee != 0)
            <div class="justify-content-between mb-1 d-flex">
              <b>Biaya Pengiriman : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->DeliveryFee, 'Rp ') }}</span>
            </div>
            @endif

            @if ($item->LateFee != 0 && $merchantOrder->PaymentMethodID == 14)
            <div class="justify-content-between mb-1 d-flex">
              <b>Denda : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->LateFee, 'Rp ') }}</span>
            </div>
            @endif

            <div class="justify-content-between mb-1 d-flex">
              <b>Grand Total : </b>
              <span class="price-subtotal">{{ Helper::formatCurrency($item->GrandTotal, 'Rp ') }}</span>
            </div>
            @endif

            @if ($item->StatusOrder == "Dalam Pengiriman" && $item->Distributor != "HAISTAR")
            {{-- <div class="text-center">
              <button type="submit" id="update_qty" class="btn btn-xs btn-primary text-white mb-2 w-50">Simpan</button>
            </div> --}}
            @endif
          </div>
        </div>
      </form>
      <div class="row m-0 pt-2">
        <div class="col-3 col-md-4 align-self-center">
          <b>{{ $item->StatusOrder }}</b> <br>
          {{-- @if ($item->StatusOrder == "Dalam Pengiriman" && $item->Distributor != "HAISTAR")
          <a href="#" class="btn btn-xs btn-success btn-finish-do mb-2"
            data-do-id="{{ $item->DeliveryOrderID }}">Selesaikan Order</a>
          @elseif ($item->StatusOrder == "Dalam Pengiriman" && $item->Distributor == "HAISTAR")
          <a href="#" class="btn btn-xs btn-danger btn-cancel-do-haistar mb-2"
            data-do-id="{{ $item->DeliveryOrderID }}">Batalkan Order Haistar</a>
          @endif --}}
        </div>
        <div class="col-6 col-md-5 align-self-center">
          Dikirim {{ date('d M Y H:i', strtotime($item->DateKirim)) }}<br>
          @if ($item->StatusOrder == "Selesai")
          Selesai {{ date('d M Y H:i', strtotime($item->FinishDate)) }}
          @endif
        </div>
        <div class="col-3 align-self-center">
          <a href="{{ route('restockDeliveryOrder.invoice', ['deliveryOrderId' => $item->DeliveryOrderID]) }}"
            target="_blank" class="btn btn-sm btn-info">Delivery Invoice</a>
        </div>
      </div>
    </div>
  </div>
  @endif
  @endforeach

  @if ($count == 0)
  <div class="callout callout-info my-2">
    <h5>Belum ada delivery order.</h5>
    {{-- <h6 class="d-inline mr-2">Lihat</h6>
    <button type="button" class="btn btn-warning mb-2" data-toggle="modal" data-target="#request-do"
      data-dismiss="modal">
      Request Delivery Order
    </button>
    <h6 class="d-inline mx-2">atau</h6>
    <button type="button" class="btn btn-primary mb-2" data-target="#add-do" data-toggle="modal" data-dismiss="modal">
      Buat Delivery Order
    </button> --}}
  </div>
  @endif
</div>
{{-- <div class="modal-footer justify-content-end">

</div> --}}