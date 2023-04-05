@extends('layouts.master')
@section('title', 'Dashboard - Ubah Restock')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">

<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Ubah Restock')

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
                        <a href="{{ route('distribution.restock') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="post">
                            <div class="row">
                                <div class="col-md-8 col-12">
                                    <h6>Informasi Pesanan</h6>
                                    <div class="row">
                                        <div class="col-6 col-md-3">
                                            <img src="{{ config('app.base_image_url') . '/merchant/'. $merchantOrder->StoreImage }}"
                                                alt="{{ $merchantOrder->StoreName }}"
                                                style="object-fit: cover; width: 130px; height: 130px;">
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <p>{{ ucwords($merchantOrder->StoreName) }}</p>
                                            <p>{{ ucwords($merchantOrder->OwnerFullName) }}</p>
                                            <p>{{ $merchantOrder->MerchantID }}</p>
                                            <p>{{ $merchantOrder->PhoneNumber }}</p>
                                            <p class="mb-md-0">{{ $merchantOrder->DistributorName }}</p>
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
                                            <label class="mb-0">Latitude: </label>
                                            <p class="mb-1 latitude">{{ $merchantOrder->Latitude }}</p>
                                            <label class="mb-0">Longitude: </label>
                                            <p class="mb-1 longitude">{{ $merchantOrder->Longitude }}</p>
                                            <label class="mb-0">Jarak Radius: </label>
                                            <p class="mb-1 longitude">{{ round($merchantOrder->RadiusDistance, 2) }} km
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post">
                            <h6 class="mb-3">Daftar Barang</h6>
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Stock Order ID</label>
                                    <p class="m-0">{{ $merchantOrder->StockOrderID }}</p>
                                    @if ($merchantOrder->StatusOrderID == "S012" || $merchantOrder->StatusOrderID ==
                                    "S018")
                                    <a href="{{ route('restock.invoice', ['stockOrderId' => $merchantOrder->StockOrderID]) }}"
                                        target="_blank" class="btn btn-sm btn-info mb-2">Lihat Invoice</a>
                                    @elseif ($merchantOrder->StatusOrderID == "S023" || $merchantOrder->StatusOrderID ==
                                    "S010")
                                    <a href="{{ route('restock.invoice', ['stockOrderId' => $merchantOrder->StockOrderID]) }}"
                                        target="_blank" class="btn btn-sm btn-info mb-2">Lihat Proforma Invoice</a>
                                    @endif
                                </div>
                                <div class="col-md-3 col-12">
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
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Metode Pembayaran</label>
                                    <p>{{ $merchantOrder->PaymentMethodName }}</p>
                                </div>
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Pesanan Dibuat</label>
                                    <p>{{ date('d F Y H:i', strtotime($merchantOrder->CreatedDate)) }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Merchant Note</label>
                                    <p>
                                        @if ($merchantOrder->MerchantNote)
                                        {{ $merchantOrder->MerchantNote }}
                                        @else
                                        -
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Distributor Note</label>
                                    <p>
                                        @if ($merchantOrder->DistributorNote)
                                        {{ $merchantOrder->DistributorNote }}
                                        @else
                                        -
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Validasi PO</label>
                                    <p>
                                        @if ($merchantOrder->IsValid === 1)
                                        <span class="badge badge-success">Sudah Valid</span>
                                        @elseif ($merchantOrder->IsValid === 0)
                                        <span class="badge badge-danger">Tidak Valid</span>
                                        @else
                                        <span class="badge badge-info">Belum Divalidasi</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3 col-12">
                                    <label class="mb-0">Catatan Validasi</label>
                                    <p>
                                        {{ $merchantOrder->ValidationNotes != null ? $merchantOrder->ValidationNotes :
                                        '-'}}
                                    </p>
                                </div>
                            </div>
                            <div>
                                @foreach ($merchantOrderDetail as $key => $value)
                                <div class="row detail-product border-top">
                                    <div class="col-md-3 col-12 text-center align-self-center mt-2">
                                        @if (!$value->ProductImage)
                                        asas
                                        @else
                                        <img src="{{ config('app.base_image_url') . '/product/'. $value->ProductImage }}"
                                            alt="" width="100">
                                        @endif
                                        <p class="mb-0">{{ $value->ProductName }}</p>
                                        <input type="hidden" name="product_id[]" value="{{ $value->ProductID }}">
                                    </div>
                                    <div class="col-md-9 col-12 align-self-center">
                                        <div class="row">
                                            <div class="col-md-4 col-12">
                                                <div class="form-group">
                                                    <label class="mb-0">Kuantitas Beli</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" name="minimum_tx_product[]" class="form-control col-md-8
                                                            @if ($errors->has('minimum_tx_product')) is-invalid @endif"
                                                            value="{{ $value->PromisedQuantity }}">
                                                        @if($errors->has('minimum_tx_product'))
                                                        <span class="error invalid-feedback">{{
                                                            $errors->first('minimum_tx_product') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <div class="form-group">
                                                    <label class="mb-0">Harga Satuan</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp </span>
                                                        </div>
                                                        <input type="number" name="minimum_tx_product[]" class="form-control col-md-8
                                                            @if ($errors->has('minimum_tx_product')) is-invalid @endif"
                                                            value="{{ $value->Nett }}">
                                                        @if($errors->has('minimum_tx_product'))
                                                        <span class="error invalid-feedback">{{
                                                            $errors->first('minimum_tx_product') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label class="mb-0">Total Harga Produk</label>
                                                <p class="font-weight-bold">
                                                    {{ Helper::formatCurrency($value->PromisedQuantity * ($value->Nett),
                                                    'Rp ') }}
                                                </p>
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
                                                <label>GrandTotal :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold text-success mb-0" id="grand_total">
                                                    {{ Helper::formatCurrency($merchantOrder->NettPrice +
                                                    $merchantOrder->ServiceChargeNett + $merchantOrder->DeliveryFee, 'Rp
                                                    ') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row konfirmasi m-0">
                                <div class="col-12">
                                    <div class="row d-md-flex justify-content-center">
                                        <a href="#" class="btn btn-lg btn-warning btn-batal"
                                            data-order-id="{{ $stockOrderID }}"
                                            data-store-name="{{ $merchantOrder->StoreName }}">
                                            Ubah
                                        </a>
                                        &nbsp;
                                    </div>
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
<script defer type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?callback=initMap&v=3&key=AIzaSyC9kPfmVtf71uGeDfHMMHDzHAl-FEBtOEw&libraries=places">
</script>
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
@yield('js-detail-restock')
<script>
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
        const priceProduct = $(this).closest('.add-do').find('.nett-price').text().replaceAll("@Rp ", "").replaceAll(".", "");
        const qtyDO = $(this).closest('.add-do').find('.qty-do').val();
        const totalPriceElm = $(this).closest('.add-do').find('.total-price');
        const totalPriceProduct = Number(qtyDO) * Number(priceProduct);
        totalPriceElm.html(thousands_separators(totalPriceProduct));

        updatePrice();
    });

    $(':checkbox').change(function() {
        $(this).closest(".add-do").find("#qty_do, #product_id, #price, #max_qty_do").prop('disabled', !$(this).is(':checked'));
        if ($(':checkbox:checked').length > 0) {
            $("#btn-do").prop('disabled', false);
        } else {
            $("#btn-do").prop('disabled', true);
        }
    });

    $('.check_rtmart').change(function() {
        if ($('.check_rtmart:checked').length > 0) {
            $('.check_haistar').prop('disabled', true);
            $('#form-add-do').attr('action', '{{ route('distribution.createDeliveryOrder', ['stockOrderID' => $stockOrderID, 'depoChannel' => 'rtmart']) }}');
        } else {
            $('.check_haistar').prop('disabled', false);
        }
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
                kembali: function () {}
            }
        });
    });
</script>
@endsection