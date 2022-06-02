@extends('layouts.master')
@section('title', 'Dashboard - Restock Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">

<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
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
                        <a href="{{ route('distribution.restock') }}" class="btn btn-sm btn-light"><i
                                class="fas fa-arrow-left"></i>
                            Kembali</a>
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
                                            <button type="button" class="btn btn-sm btn-success float-md-right"
                                                id="open-maps">Buka di Maps</button>
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
                                        <img src="{{ config('app.base_image_url') . '/product/'. $value->ProductImage }}"
                                            alt="" width="100">
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
                                                <p class="font-weight-bold">{{
                                                    Helper::formatCurrency($value->PromisedQuantity * ($value->Nett),
                                                    'Rp ') }}</p>
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
                                        @if ($merchantOrder->DiscountPrice != 0)
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
                                        @endif
                                        @if ($merchantOrder->DiscountVoucher != 0)
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>Voucher :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold mb-0 text-danger">
                                                    {{ Helper::formatCurrency($merchantOrder->DiscountVoucher, 'Rp ') }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($merchantOrder->ServiceChargeNett != 0)
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>Biaya Layanan :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold mb-0">
                                                    {{ Helper::formatCurrency($merchantOrder->ServiceChargeNett, 'Rp ')
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($merchantOrder->DeliveryFee != 0)
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>Biaya Pengiriman :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold mb-0">
                                                    {{ Helper::formatCurrency($merchantOrder->DeliveryFee, 'Rp ')
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <label>GrandTotal :</label>
                                            </div>
                                            <div class="col-6">
                                                <p class="font-weight-bold text-success mb-0" id="grand_total">
                                                    {{ Helper::formatCurrency($merchantOrder->NettPrice +
                                                    $merchantOrder->ServiceChargeNett, 'Rp ') }}
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
                                            <a href="#" class="btn btn-danger btn-batal mr-3"
                                                data-order-id="{{ $stockOrderID }}"
                                                data-store-name="{{ $merchantOrder->StoreName }}">
                                                Tolak Pesanan
                                            </a>
                                            <a href="#" class="btn btn-success btn-terima"
                                                data-order-id="{{ $stockOrderID }}"
                                                data-store-name="{{ $merchantOrder->StoreName }}">
                                                Terima Pesanan
                                            </a>
                                        </div>
                                    </div>
                                    @elseif ($merchantOrder->StatusOrderID == "S012" ||
                                    $merchantOrder->StatusOrderID == "S023") {{-- Dalam Proses atau Telah Dikirim --}}
                                    <div class="text-center text-md-right">
                                        <button type="button" class="btn btn-warning ml-md-3 mb-2" data-toggle="modal"
                                            data-target="#request-do">
                                            Request Delivery Order
                                        </button>
                                        <button type="button" class="btn btn-info ml-md-3 mb-2" data-toggle="modal"
                                            data-target="#detail-do">
                                            Detail Delivery Order
                                        </button>
                                        <button type="button" class="btn btn-primary ml-md-3 mb-2" data-toggle="modal"
                                            data-target="#add-do">
                                            Buat Delivery Order
                                        </button>
                                        @if ($merchantOrder->StatusOrderID == "S012" && $merchantOrder->PaymentMethodID != 1)
                                        <div class="row d-md-flex justify-content-end">
                                            <div class="col-md-6 col-12 text-center">
                                                <a href="#" class="btn btn-secondary btn-refund"
                                                    data-order-id="{{ $stockOrderID }}"
                                                    data-store-name="{{ $merchantOrder->StoreName }}">
                                                    Refund
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @if ($merchantOrder->StatusOrderID == "S023")
                                    <div class="row d-md-flex justify-content-end">
                                        <div class="col-md-6 col-12 text-center">
                                            @if ($merchantOrder->PaymentMethodID == 1) {{-- Kalo pake tunai --}}
                                            <a href="#" class="btn btn-danger btn-batal"
                                                data-order-id="{{ $stockOrderID }}"
                                                data-store-name="{{ $merchantOrder->StoreName }}">
                                                Batalkan Pesanan
                                            </a>
                                            @endif
                                            {{-- <a href="#" class="btn btn-success btn-kirim mx-3"
                                                data-order-id="{{ $stockOrderID }}"
                                                data-store-name="{{ $merchantOrder->StoreName }}">
                                                Kirim Pesanan
                                            </a> --}}
                                            @if ($merchantOrder->PaymentMethodID != 1)
                                            <a href="#" class="btn btn-secondary btn-refund"
                                                data-order-id="{{ $stockOrderID }}"
                                                data-store-name="{{ $merchantOrder->StoreName }}">
                                                Refund
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    {{-- Modal Detail Delivery Order --}}
                                    <div class="modal fade" id="detail-do">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                @include('distribution.restock.telah-dikirm.modal-detail')
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Modal Request Delivery Order --}}
                                    <div class="modal fade" id="request-do" aria-hidden="true"
                                        aria-labelledby="modal-detail">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                @include('distribution.restock.telah-dikirm.modal-request')
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Modal Add Delivery Order --}}
                                    <div class="modal fade" id="add-do">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                @include('distribution.restock.telah-dikirm.modal-add')
                                            </div>
                                        </div>
                                    </div>
                                    @elseif ($merchantOrder->StatusOrderID == "S018") Telah Selesai
                                    <div class="col-6">
                                        <label class="mb-0">Rating: </label>
                                        {{ $merchantOrder->Rating }} / 5
                                    </div>
                                    <div class="col-6">
                                        <label class="mb-0">Komentar: </label>
                                        {{ $merchantOrder->Feedback }}
                                    </div>
                                    <div class="border-top border-secondary my-2">
                                        <h6 class="mt-2">Detail Delivery Order</h6>
                                        @foreach ($deliveryOrder as $do)
                                        <div class="card card-outline @if ($do->StatusDO == 'S025') card-success
                                            @elseif($do->StatusDO == 'S024') card-warning @else card-danger @endif">
                                            <div class="card-header">
                                                <h3 class="card-title font-weight-bold">
                                                    {{ $do->DeliveryOrderID }}
                                                    @if ($do->Distributor == "HAISTAR")
                                                    <span class="badge badge-info">HAISTAR</span>
                                                    @endif
                                                </h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool"
                                                        data-card-widget="collapse"><i
                                                            class="fas fa-minus"></i></button>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                @foreach ($do->DetailProduct as $product)
                                                <div class="row m-0 mb-2 text-center">
                                                    <div class="col-3 align-self-center">
                                                        <img src="{{ config('app.base_image_url') . '/product/'. $product->ProductImage }}"
                                                            alt="" width="60">
                                                    </div>
                                                    <div class="col-3 align-self-center">
                                                        <p class="m-0">{{ $product->ProductName }}</p>
                                                    </div>
                                                    <div class="col-3 align-self-center">
                                                        <p class="m-0">{{ $product->Qty }}x {{
                                                            Helper::formatCurrency($product->Price, '@Rp ') }}</p>
                                                    </div>
                                                    <div class="col-3 align-self-center">
                                                        <p class="m-0">{{ Helper::formatCurrency($product->Qty *
                                                            $product->Price, 'Rp ') }}</p>
                                                    </div>
                                                </div>
                                                @endforeach
                                                <div class="row m-0 border-bottom border-top">
                                                    <div class="col-8 col-md-9 pt-2">
                                                        <p class="m-0"><b>Driver : </b>{{ $do->Name }}</p>
                                                        <p class="m-0"><b>Helper : </b>{{ $do->HelperName }}</p>
                                                        <p class="m-0"><b>Kendaraan : </b>{{ $do->VehicleName }} {{
                                                            $do->VehicleLicensePlate }}</p>
                                                    </div>
                                                    <div class="col-4 col-md-3 d-flex justify-content-center">
                                                        <p class="text-center my-2">
                                                            <b>SubTotal : </b>
                                                            <span class="price-subtotal">{{
                                                                Helper::formatCurrency($do->SubTotal, 'Rp ')
                                                                }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row m-0 pt-2">
                                                    <div class="col-4 col-md-4 align-self-center">
                                                        <b>{{ $do->StatusOrder }}</b>
                                                    </div>
                                                    <div
                                                        class="col-8 col-md-5 align-self-center text-right text-md-left">
                                                        Dikirim {{ date('d M Y H:i', strtotime($do->CreatedDate))
                                                        }}<br>
                                                        @if ($do->FinishDate != null)
                                                        Selesai {{ date('d M Y H:i', strtotime($do->FinishDate)) }}
                                                        @endif
                                                    </div>
                                                    <div class="col-12 col-md-3 align-self-center text-md-center">
                                                        <a href="{{ route('restockDeliveryOrder.invoice', ['deliveryOrderId' => $do->DeliveryOrderID]) }}"
                                                            target="_blank" class="btn btn-sm btn-info">Delivery
                                                            Invoice</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        @endforeach
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

    $('.check_haistar').change(function() {
        if ($('.check_haistar:checked').length > 0) {
            $('.check_rtmart').prop('disabled', true);
            $('#form-add-do').attr('action', '{{ route('distribution.createDeliveryOrder', ['stockOrderID' => $stockOrderID, 'depoChannel' => 'haistar']) }}');
        } else {
            $('.check_rtmart').prop('disabled', false);
        }
    });

    // Event listener saat tombol refund diklik
    $('.konfirmasi').on('click', '.btn-refund', function (e) {
        e.preventDefault();
        const orderID = $(this).data("order-id");
        const storeName = $(this).data("store-name");
        const form = $('form');
        $.confirm({
            typeAnimated: true,
            title: 'Refund Order',
            content: `Apakah ykain pesanan <b>${orderID}</b> dari <b>${storeName}</b> ingin di-refund?
                <form action="/distribution/restock/update/${orderID}/refund" method="post">
                    @csrf
                </form>`,
            closeIcon: true,
            buttons: {
                ya: {
                    btnClass: 'btn-secondary',
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
            type: 'green',
            typeAnimated: true,
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

    // Event listener saat tombol selesaikan order diklik
    $('.btn-cancel-do-haistar').on('click', function (e) {
        e.preventDefault();
        const deliveryOrderId = $(this).data("do-id");
        $.confirm({
            title: 'Batalkan Order!',
            content: `Apakah yakin ingin membatalkan order <b>${deliveryOrderId}</b>? <br>
                    <label class="mt-2 mb-0">Alasan Batal:</label>
                    <form action="/distribution/restock/cancel/deliveryOrder/${deliveryOrderId}" method="post">
                        @csrf
                        <input type="text" class="form-control cancel_reason" name="cancel_reason" autocomplete="off">
                    </form>`,
            closeIcon: true,
            type: 'red',
            typeAnimated: true,
            buttons: {
                ya: {
                    btnClass: 'btn-danger',
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