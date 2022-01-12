@extends('layouts.master')
@section('title', 'Dashboard - Restock Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Detail Restock')

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
                        <a href="{{ route('distribution.restock') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <div class="post">
                            <div class="row">
                                <div class="col-md-8 col-12">
                                    <h6>Informasi Pesanan</h6>
                                    <div class="row">
                                        <div class="col-6 col-md-3">
                                            <img src="{{ config('app.base_image_url') . '/merchant/'. $merchantOrder->StoreImage }}" alt="{{ $merchantOrder->StoreName }}" style="object-fit: cover; width: 130px; height: 130px;">
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <p>{{ $merchantOrder->StoreName }}</p>
                                            <p>{{ $merchantOrder->OwnerFullName }}</p>
                                            <p>{{ $merchantOrder->MerchantID }}</p>
                                            <p class="mb-md-0">{{ $merchantOrder->PhoneNumber }}</p>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="mb-0">Alamat:</label>
                                            <p class="mb-1 address">{{ $merchantOrder->StoreAddress }}</p>
                                            <label class="mb-0">Patokan:</label>
                                            @if ($merchantOrder->StoreAddressNote)
                                                <p class="mb-1">{{ $merchantOrder->StoreAddressNote }}</p>
                                            @else
                                                <p class="mb-1">-</p>
                                            @endif
                                            <label class="mb-0">Lat: </label>
                                            <p class="mb-1 latitude">{{ $merchantOrder->Latitude }}</p>
                                            <label class="mb-0">Long: </label>
                                            <p class="mb-1 longitude">{{ $merchantOrder->Longitude }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="row">
                                        <div class="col-md-8 col-12">
                                            <h6 class="mt-3 mt-md-0">Peta Alamat Pemesanan</h6>
                                        </div>
                                        <div class="col-md-4 col-12 p-md-0">
                                            <button type="button" class="btn btn-sm btn-success float-md-right" id="open-maps">Buka di Maps</button>
                                        </div>
                                    </div>
                                    <div id="google-maps" style="width: 100%; height: 200px; margin: 20px 0;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="post">
                            <h6 class="mb-3">Daftar Barang</h6>
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <label class="mb-0">Stock Order ID</label>
                                    <p>{{ $merchantOrder->StockOrderID }}</p>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="mb-0">Status Pesanan</label>
                                    <p>
                                        @if ($merchantOrder->StatusOrderID == "S009")
                                            <span class="badge badge-secondary">{{ $merchantOrder->StatusOrder }}</span>
                                        @elseif ($merchantOrder->StatusOrderID == "S010")
                                            <span class="badge badge-primary">{{ $merchantOrder->StatusOrder }}</span>
                                        @elseif ($merchantOrder->StatusOrderID == "S023")
                                            <span class="badge badge-warning">{{ $merchantOrder->StatusOrder }}</span>
                                        @elseif ($merchantOrder->StatusOrderID == "S012")
                                            <span class="badge badge-info">{{ $merchantOrder->StatusOrder }}</span>
                                        @elseif ($merchantOrder->StatusOrderID == "S018")
                                            <span class="badge badge-success">{{ $merchantOrder->StatusOrder }}</span>
                                        @elseif ($merchantOrder->StatusOrderID == "S011")
                                            <span class="badge badge-danger">{{ $merchantOrder->StatusOrder }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="mb-0">Metode Pembayaran</label>
                                    <p>{{ $merchantOrder->PaymentMethodName }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <label class="mb-0">Merchant Note</label>
                                    <p>@if ($merchantOrder->MerchantNote)
                                        {{ $merchantOrder->MerchantNote }}
                                    @else
                                        -
                                    @endif</p>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="mb-0">Distributor Note</label>
                                    <p>@if ($merchantOrder->DistributorNote)
                                        {{ $merchantOrder->DistributorNote }}
                                    @else
                                        -
                                    @endif</p>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="mb-0">Pesanan Dibuat</label>
                                    <p>{{ date('d F Y H:i', strtotime($merchantOrder->CreatedDate)) }}</p>
                                </div>
                            </div>
                            <div>
                                @foreach ($merchantOrderDetail as $key => $value)
                                <div class="row detail-product border-top">
                                    <div class="col-md-3 col-12 text-center align-self-center mt-2">
                                        @if (!$value->ProductImage)
                                            asas
                                        @else
                                        <img src="{{ config('app.base_image_url') . '/product/'. $value->ProductImage }}" alt="" width="100">    
                                        @endif
                                        
                                        <p class="mb-0">{{ $value->ProductName }}</p>
                                        <input type="hidden" name="product_id[]" value="{{ $value->ProductID }}">
                                    </div>
                                    <div class="col-md-9 col-12 align-self-center">
                                        <div class="row">
                                            <div class="col-md-4 col-12">
                                                <label class="mb-0">Kuantitas Beli</label>
                                                <p>{{ $value->PromisedQuantity }}</p>
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label class="mb-0">Harga Satuan</label>
                                                <p class="price">{{ Helper::formatCurrency($value->Nett, 'Rp ') }}</p>
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label class="mb-0">Total Harga Produk</label>
                                                <p class="font-weight-bold">{{ Helper::formatCurrency($value->PromisedQuantity * ($value->Nett), 'Rp ') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 d-md-flex justify-content-end">
                                    <div class="col-md-6 col-12">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>SubTotal :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold mb-0" id="sub_total">
                                                    {{ Helper::formatCurrency($merchantOrder->TotalPrice, 'Rp ') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>Potongan :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold mb-0 text-danger">
                                                    {{ Helper::formatCurrency($merchantOrder->DiscountPrice, 'Rp ') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>Biaya Layanan :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold mb-0">
                                                    {{ Helper::formatCurrency($merchantOrder->ServiceChargeNett, 'Rp ') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>GrandTotal :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold text-success mb-0" id="grand_total">
                                                    {{ Helper::formatCurrency($merchantOrder->NettPrice + $merchantOrder->ServiceChargeNett, 'Rp ') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row konfirmasi m-0">
                                <div class="col-12 ">
                                    @if ($merchantOrder->StatusOrderID == "S009") {{-- Pesanan Baru --}}
                                        <div class="row d-md-flex justify-content-end">
                                            <div class="col-md-6 col-12 text-center">
                                                <a href="#" class="btn btn-danger btn-batal mr-3" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
                                                    Tolak Pesanan
                                                </a>
                                                <a href="#" class="btn btn-success btn-terima" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
                                                    Terima Pesanan
                                                </a>        
                                            </div>
                                        </div>
                                    @elseif ($merchantOrder->StatusOrderID == "S023") {{-- Dalam Proses --}}
                                        @if ($merchantOrder->PaymentMethodID == 1) {{-- Kalo pake tunai --}}
                                        <div class="row d-md-flex justify-content-end">
                                            <div class="col-md-6 col-12 text-center">
                                                <a href="#" class="btn btn-danger btn-batal mr-4" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
                                                    Batalkan Pesanan
                                                </a>
                                                <a href="#" class="btn btn-success btn-kirim" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
                                                    Kirim Pesanan
                                                </a>
                                            </div>
                                        </div>
                                        @else {{-- Selain Tunai --}}
                                        <div class="row d-md-flex justify-content-end">
                                            <div class="col-md-6 col-12 text-center">
                                                <a href="#" class="btn btn-success btn-kirim" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
                                                    Kirim Pesanan
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    @elseif ($merchantOrder->StatusOrderID == "S012") {{-- Telah Dikirim --}}
                                        <div class="row d-md-flex justify-content-end">
                                            <div class="col-md-6 col-12 text-center">
                                                <button type="button" class="btn btn-info mr-md-4" data-toggle="modal" data-target="#detail-do">
                                                    Detail Delivery Order
                                                </button>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-do">
                                                    Buat Delivery Order
                                                </button>
                                            </div>
                                            {{-- Modal Detail Delivery Order --}}
                                            <div class="modal fade" id="detail-do">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Detail Delivery Order</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if ($deliveryOrder->count() > 0)
                                                                @foreach ($deliveryOrder as $item)
                                                                <div class="card card-{{ $item->StatusOrder == "Selesai" ? 'success' : 'warning' }}" detail-do>
                                                                    <div class="card-header">
                                                                        <h3 class="card-title"><b class="d-block d-md-inline">Delivery Order ID :</b> {{ $item->DeliveryOrderID }}</h3>
                                                                        <div class="card-tools">
                                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                                                <i class="fas fa-minus"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body py-1 px-2 detail-do-wrapper">
                                                                        <form action="{{ route('distribution.updateQtyDO', ['deliveryOrderId' => $item->DeliveryOrderID]) }}" method="get" id="edit-qty-do{{ $loop->iteration }}">
                                                                            @csrf
                                                                            @foreach ($item->DetailProduct as $product)
                                                                            <div class="row text-center border-bottom m-0 edit-do">
                                                                                <div class="col-3 align-self-center">
                                                                                    <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}" alt="" width="80">
                                                                                </div>
                                                                                <div class="col-3 align-self-center">
                                                                                    <label>Produk</label>
                                                                                    <p>{{ $product->ProductName }}</p>
                                                                                    <input type="hidden" name="product_id[]" value="{{ $product->ProductID }}">
                                                                                </div>
                                                                                <div class="col-3 align-self-center">
                                                                                    <label class="d-block">Qty</label>
                                                                                    @if ($item->StatusOrder == "Selesai")
                                                                                    <p>{{ $product->Qty }}x {{ Helper::formatCurrency($product->Price, '@Rp ') }}</p>
                                                                                    @else
                                                                                    <p>
                                                                                        <input type="number" class="form-control edit-qty-do text-sm text-center p-0 d-inline" value="{{ $product->Qty }}" name="edit_qty_do[]" style="width: 40px; height: 30px;" max="{{ $product->OrderQty }}" min="1" required>
                                                                                        <span class="price-do">{{ Helper::formatCurrency($product->Price, 'x @Rp ') }}</span>
                                                                                    </p>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="col-3 align-self-center">
                                                                                    <label>Total Harga</label>
                                                                                    <p class="price-total">{{ Helper::formatCurrency($product->Qty * $product->Price, 'Rp ') }}</p>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                            <div class="row m-0 border-bottom">
                                                                                <div class="col-12 d-flex justify-content-end">
                                                                                    <p class="text-center my-2 mr-md-4">
                                                                                        <b>SubTotal : </b>
                                                                                        <span class="price-subtotal">{{ Helper::formatCurrency($item->SubTotal, 'Rp ') }}</span>
                                                                                    </p>
                                                                                </div>
                                                                                @if ($item->StatusOrder != "Selesai")
                                                                                <div class="col-11 d-flex justify-content-end">
                                                                                    <button type="submit" id="update_qty" class="btn btn-xs btn-primary text-white mb-2">Simpan</button>
                                                                                </div>
                                                                                @endif
                                                                            </div>
                                                                        </form>
                                                                        <div class="row m-0 pt-2">
                                                                            <div class="col-3 col-md-4 align-self-center">
                                                                                <b>{{ $item->StatusOrder }}</b> <br>
                                                                                @if ($item->StatusOrder == "Dalam Pengiriman")
                                                                                <a href="#" class="btn btn-xs btn-success btn-finish-do mb-2" data-do-id="{{ $item->DeliveryOrderID }}">Selesaikan Order</a>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-6 col-md-5 align-self-center">
                                                                                Dikirim {{ date('d M Y H:i', strtotime($item->CreatedDate)) }}<br>
                                                                                @if ($item->StatusOrder == "Selesai")
                                                                                Selesai {{ date('d M Y H:i', strtotime($item->FinishDate)) }}
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-3 align-self-center">
                                                                                <a href="{{ route('restockDeliveryOrder.invoice', ['deliveryOrderId' => $item->DeliveryOrderID]) }}" target="_blank" class="btn btn-sm btn-info">Delivery Invoice</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            @else
                                                            <div class="callout callout-info my-2">
                                                                <h5>Belum ada delivery order.</h5>
                                                                <button type="button" class="btn btn-primary" data-target="#add-do" data-toggle="modal">
                                                                    Buat Delivery Order
                                                                </button>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        {{-- <div class="modal-footer justify-content-end">
                                                            
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Modal Add Delivery Order --}}
                                            <div class="modal fade" id="add-do">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Buat Delivery Order</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body py-1">
                                                            @if ($promisedQty == $deliveryOrderQty)
                                                                <div class="callout callout-info my-2">
                                                                    <h5 class="py-2">Semua Barang Telah Dikirim.</h5>
                                                                </div>
                                                            @else
                                                            <div class="callout callout-danger d-md-none py-2 mb-1">
                                                                <p><strong>Direkomendasikan untuk buka di LAPTOP / PC</strong></p>
                                                            </div>
                                                            <div class="callout callout-warning py-2">
                                                                <p>Pilih terlebih dahulu barang yang ingin dikirim</p>
                                                            </div>
                                                            <form action="{{ route('distribution.createDeliveryOrder', ['stockOrderID' => $stockOrderID]) }}" method="post">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <div class="row m-0">
                                                                        <div class="col-md-4 col-12 align-self-center text-md-right">
                                                                            <label class="my-0" for="created_date_do">Waktu Pengiriman :</label>
                                                                        </div>
                                                                        <div class="col-md-8 col-12">
                                                                            <input type="datetime-local" class="form-control" name="created_date_do" id="created_date_do" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @foreach ($productAddDO as $item)
                                                                    @if ($item->PromisedQuantity != $item->QtyDO)
                                                                    <div class="row text-center border-bottom m-0 add-do">
                                                                        <div class="col-1 align-self-center">
                                                                            <input type="checkbox" id="check_do">
                                                                        </div>
                                                                        <div class="col-3 align-self-center">
                                                                            <img src="{{ config('app.base_image_url') . '/product/'. $item->ProductImage }}" alt="" width="80">
                                                                            <p class="mb-1">{{ $item->ProductName }}</p>
                                                                            <input type="hidden" name="product_id[]" id="product_id" value="{{ $item->ProductID }}" disabled="disabled">
                                                                            <input type="hidden" name="price[]" id="price" value="{{ $item->Nett }}" disabled="disabled">
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
                                                                            <input type="hidden" name="max_qty_do[]" id="max_qty_do" value="{{ $item->PromisedQuantity - $item->QtyDO }}" disabled="disabled">
                                                                        </div>
                                                                        <div class="col-2 align-self-center">
                                                                            <label>Qty Kirim</label>
                                                                            <input type="number" name="qty_do[]" id="qty_do" class="form-control text-center qty-do" max="{{ $item->PromisedQuantity - $item->QtyDO }}" min="1" disabled="disabled" required>
                                                                        </div>
                                                                        <div class="col-2 align-self-center">
                                                                            <label>Total Harga</label>
                                                                            <p>Rp <span class="total-price">0</span></p>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                @endforeach
                                                                <p class="my-2 mr-md-4 text-right"><b>Subtotal : </b>Rp <span class="subtotal-do">0</span></p>
                                                                <button type="submit" id="btn-do" disabled="disabled" class="btn btn-primary float-right my-3">Buat DO</button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                        {{-- <div class="modal-footer justify-content-between">
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($merchantOrder->StatusOrderID == "S018") {{-- Telah Selesai --}}
                                        <div class="col-6">
                                            <label class="mb-0">Rating: </label>
                                            {{ $merchantOrder->Rating }} / 5
                                        </div>
                                        <div class="col-6">
                                            <label class="mb-0">Komentar: </label>
                                            {{ $merchantOrder->Feedback }}
                                        </div>
                                    @elseif ($merchantOrder->StatusOrderID == "S011") {{-- Telah Dibatalkan --}}
                                        <label class="mb-0">Alasan dibatalkan:</label>
                                        {{ $merchantOrder->CancelReasonNote }}
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
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
<script src="https://unpkg.com/autonumeric"></script>
<script defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?callback=initMap&v=3&key=AIzaSyC9kPfmVtf71uGeDfHMMHDzHAl-FEBtOEw&libraries=places"></script>
<script>
    // const currencyJumlahTopup = new AutoNumeric.multiple('.autonumeric', {
    //     allowDecimalPadding: false,
    //     decimalCharacter: ',',
    //     digitGroupSeparator: '.',
    //     unformatOnSubmit: true
    // });

    // Event listener saat mengetik qty edit delivery order
    $('.edit-qty-do').on('keyup', function (e) {
        e.preventDefault();
            const priceProduct = $(this).next().text().replaceAll("x @Rp ", "").replaceAll(".", "");
            const qtyDO = $(this).val();
            
            const totalPriceProduct = Number(qtyDO) * Number(priceProduct);
            $(this).parent().parent().next().children().last().html('Rp ' + thousands_separators(totalPriceProduct));
            
            const totalPriceAllProductArr = $(this).closest('.detail-do-wrapper').find('.price-total').text().replace("Rp ", "").replaceAll("Rp ", ",").replaceAll(".", "").split(",");

            let priceAllProductNumber = totalPriceAllProductArr.map(Number);
            let subTotalDO = 0;
            $.each(priceAllProductNumber, function() {
                subTotalDO += this;
            });

            $(this).closest('.detail-do-wrapper').find('.price-subtotal').html('Rp ' + thousands_separators(subTotalDO));
    });

    function updatePrice() {
        let subTotal = 0;
        $('.add-do').each(function() {
            const qty = $(this).find('.qty-do').val();
            const price = $(this).find('.nett-price').text().replaceAll("@Rp ", "").replaceAll(".", "");
            let totalPriceProduct = (Number(qty) * Number(price));
            subTotal += totalPriceProduct;
        });
        $('.subtotal-do').text(thousands_separators(subTotal));
    }

    // Event listener saat mengetik qty create delivery order
    $('.qty-do').on('keyup', function (e) {
        e.preventDefault();
        const indexProduct = $(this).closest('.add-do').index()-2;
        const priceProduct = $('.add-do').find('.nett-price').eq(indexProduct).text().replaceAll("@Rp ", "").replaceAll(".", "");
        const qtyDO = $('.add-do').find('.qty-do').eq(indexProduct).val();
        const totalPriceElm = $('.add-do').find('.total-price').eq(indexProduct);
        const totalPriceProduct = Number(qtyDO) * Number(priceProduct);
        totalPriceElm.html(thousands_separators(totalPriceProduct));

        updatePrice();
    });

    // $('.detail-product').on('keyup', function (e) {
    //     // event listener saat protongan harga diinput
    //     const grandTotal = $('#grand_total');
    //     const grandTotalValue = grandTotal.text().replaceAll("Rp ", "").replaceAll(".", "");
    //     $('#discount_price').on('keyup', function (e) {
    //         const newGrandTotal = Number(grandTotalValue) - Number(this.value.replaceAll(".", ""));
    //         grandTotal.html("Rp " + thousands_separators(newGrandTotal));
    //     });

    //     // event listener saat qty product di input
    //     $('.promised_qty').on('keyup', function (e){
    //         e.preventDefault();
    //         const indexProduct = $(this).closest('.detail-product').index();
    //         const qtyProduct = e.target.value.replaceAll(".", "");
    //         const priceProduct = $('.detail-product').find('.price').eq(indexProduct).text().replaceAll("Rp ", "").replaceAll(".", "");
    //         const discountProduct = $('.detail-product').find('.discount_product').eq(indexProduct).val().replaceAll(".", "");

    //         const newTotalPriceProduct = (Number(priceProduct) - Number(discountProduct)) * Number(qtyProduct);
    //         const totalPriceProductElm = $('.detail-product').find('.total_price_product').eq(indexProduct);
    //         const subTotalPriceElm = $('#sub_total');
    //         const subTotalPrice = $('#sub_total').text().replaceAll("Rp ", "").replaceAll(".", "");
    //         const oldTotalPriceProduct = totalPriceProductElm.text().replaceAll("Rp ", "").replaceAll(".", "");
            
    //         const oldSubTotalPrice = subTotalPriceElm.text().replaceAll("Rp ", "").replaceAll(".", "");
    //         const newSubTotalPrice = Number(subTotalPrice) - Number(oldTotalPriceProduct) + Number(newTotalPriceProduct);
    //         subTotalPriceElm.html("Rp " + thousands_separators(newSubTotalPrice));
            
    //         totalPriceProductElm.html("Rp " + thousands_separators(newTotalPriceProduct));

    //         const grandTotalValue = grandTotal.text().replaceAll("Rp ", "").replaceAll(".", "");
            
    //         const newGrandTotal = Number(grandTotalValue) - Number(oldSubTotalPrice) + Number(newSubTotalPrice);
    //         grandTotal.html("Rp " + thousands_separators(newGrandTotal));
    //     });

    //     // event listener saat potongan di input
    //     $('.discount_product').on('keyup', function (e){
    //         e.preventDefault();
    //         const indexProduct = $(this).closest('.detail-product').index();
    //         const discountProduct = e.target.value.replaceAll(".", "");
    //         const priceProduct = $('.detail-product').find('.price').eq(indexProduct).text().replaceAll("Rp ", "").replaceAll(".", "");
    //         const qtyProduct = $('.detail-product').find('.promised_qty').eq(indexProduct).val().replaceAll(".", "");

    //         const newTotalPriceProduct = (Number(priceProduct) - Number(discountProduct)) * Number(qtyProduct);
    //         const totalPriceProductElm = $('.detail-product').find('.total_price_product').eq(indexProduct);
    //         const subTotalPriceElm = $('#sub_total');
    //         const subTotalPrice = $('#sub_total').text().replaceAll("Rp ", "").replaceAll(".", "");
    //         const oldTotalPriceProduct = totalPriceProductElm.text().replaceAll("Rp ", "").replaceAll(".", "");
            
    //         const oldSubTotalPrice = subTotalPriceElm.text().replaceAll("Rp ", "").replaceAll(".", "");
    //         const newSubTotalPrice = Number(subTotalPrice) - Number(oldTotalPriceProduct) + Number(newTotalPriceProduct);
    //         subTotalPriceElm.html("Rp " + thousands_separators(newSubTotalPrice));
            
    //         totalPriceProductElm.html("Rp " + thousands_separators(newTotalPriceProduct));

    //         const grandTotalValue = grandTotal.text().replaceAll("Rp ", "").replaceAll(".", "");
            
    //         const newGrandTotal = Number(grandTotalValue) - Number(oldSubTotalPrice) + Number(newSubTotalPrice);
    //         grandTotal.html("Rp " + thousands_separators(newGrandTotal));
    //     });
    // });

    $(':checkbox').change(function() {
        $(this).closest(".add-do").find("#qty_do, #product_id, #price, #max_qty_do").prop('disabled', !$(this).is(':checked'));
        $("#btn-do").prop('disabled', !$(this).is(':checked'));
    });

    // Event listener saat tombol batal diklik
    $('.konfirmasi').on('click', '.btn-batal', function (e) {
        e.preventDefault();
        const orderID = $(this).data("order-id");
        const storeName = $(this).data("store-name");
        $.confirm({
            type: 'red',
            typeAnimated: true,
            title: 'Batalkan Pesanan',
            content: `Yakin ingin membatalkan pesanan <b>${orderID}</b> dari <b>${storeName}</b>? <br>
                <label class="mt-2 mb-0">Alasan Batal:</label>
                <form action="/distribution/restock/update/${orderID}/reject" method="post">
                    @csrf
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

    // Event listener saat tombol terima diklik
    $('.konfirmasi').on('click', '.btn-terima', function (e) {
        e.preventDefault();
        const orderID = $(this).data("order-id");
        const storeName = $(this).data("store-name");
        const form = $('form');
        $.confirm({
            type: 'green',
            typeAnimated: true,
            title: 'Terima Barang',
            content: `Apakah pesanan <b>${orderID}</b> dari <b>${storeName}</b> sudah sesuai?
                <form action="/distribution/restock/update/${orderID}/approved" method="post">
                    @csrf
                </form>`,
            closeIcon: true,
            buttons: {
                terima: {
                    btnClass: 'btn-success',
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        this.$content.find('form').submit();
                    }
                },
                kembali: function () {
                }
            }
        });
    });

    // Event listener saat tombol kirim diklik
    $('.konfirmasi').on('click', '.btn-kirim', function (e) {
        e.preventDefault();
        const orderID = $(this).data("order-id");
        const storeName = $(this).data("store-name");
        $.confirm({
            type: 'green',
            typeAnimated: true,
            title: 'Kirim Pesanan',
            content: `Apakah yakin ingin mengirim pesanan <b>${orderID}</b> dari <b>${storeName}</b>?
                <label class="mt-2 mb-0">Masukkan catatan atas barang yang Anda kirim:</label>
                <form action="/distribution/restock/update/${orderID}/send" method="post">
                    @csrf
                    <input type="text" class="form-control distributor_note" name="distributor_note" autocomplete="off">
                </form>`,
            closeIcon: true,
            buttons: {
                kirim: {
                    btnClass: 'btn-success',
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        this.$content.find('form').submit();
                    }
                },
                kembali: function () {
                }
            }
        });
    });

    // Event listener saat tombol selesaikan order diklik
    $('.btn-finish-do').on('click', function (e) {
        e.preventDefault();
        const deliveryOrderId = $(this).data("do-id");
        $.confirm({
            title: 'Selesaikan Order',
            content: `Apakah order <b>${deliveryOrderId}</b> telah selesai?`,
            closeIcon: true,
            buttons: {
                ya: {
                    btnClass: 'btn-success',
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = '/distribution/restock/update/deliveryOrder/' + deliveryOrderId
                    }
                },
                tidak: function () {
                }
            }
        });
    });

    const latitude = document.querySelector('.latitude').innerHTML;
    const longitude = document.querySelector('.longitude').innerHTML;
    const address = document.querySelector('.address').innerHTML;

    function initMap() {
		map = new google.maps.Map(document.getElementById('google-maps'), {
			center: {lat: parseFloat(latitude), lng: parseFloat(longitude)},
			zoom: 17,
			mapTypeControl: false,
			streetViewControl: false
		});
		infoWindow = new google.maps.InfoWindow;
		
		marker = new google.maps.Marker({
			map: map,
			draggable: false,
			position: {lat: parseFloat(latitude), lng: parseFloat(longitude)},
			url: 'https://www.google.co.id/maps/place/'+latitude+','+longitude
		});
		 google.maps.event.addListener(marker, 'mouseover', function(){
			 infoWindow.setContent(address);
				infoWindow.open(map, marker);
		    });

		    google.maps.event.addListener(marker, 'mouseout', function(){
		        //
		    });
		google.maps.event.addListener(marker, 'click', function() {
			
			window.open(this.url, '_blank');
		});
	}

    $('#open-maps').on("click", function(e) {
        window.open('https://www.google.com/maps/search/'+latitude+','+longitude+'/@'+latitude+','+longitude+',17z','address','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=yes,width=600,height=400');
    })
</script>
@endsection