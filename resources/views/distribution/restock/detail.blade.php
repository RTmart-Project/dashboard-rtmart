@extends('layouts.master')
@section('title', 'Dashboard - Restock Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css"
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
                            <form action="{{ route('distribution.updateStatusRestock', ['stockOrderID' => $stockOrderID, 'status' => 'approved']) }}" method="post">
                                @csrf
                                <h6 class="mb-3">Daftar Barang</h6>
                                <div class="row">
                                    <div class="col-md-3 col-12">
                                        <label class="mb-0">Stock Order ID</label>
                                        <p>{{ $merchantOrder->StockOrderID }}</p>
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
                                        <label class="mb-0">Merchant Note</label>
                                        <p>@if ($merchantOrder->MerchantNote)
                                            {{ $merchantOrder->MerchantNote }}
                                        @else
                                            -
                                        @endif</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-12">
                                        <label class="mb-0">Distributor Note</label>
                                        <p>@if ($merchantOrder->DistributorNote)
                                            {{ $merchantOrder->DistributorNote }}
                                        @else
                                            -
                                        @endif</p>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <label class="mb-0">Pesanan Dibuat</label>
                                        <p>{{ date('d F Y H:i', strtotime($merchantOrder->CreatedDate)) }}</p>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <label class="mb-0">Estimasi Sampai</label>
                                        <p>{{ date('d F Y', strtotime($merchantOrder->ShipmentDate)) }}</p>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <label for="shipment_date" class="mb-0">Shipment Date</label>
                                        @if ($merchantOrder->StatusOrderID == "S009")
                                            <input type="date" name="shipment_date" class="form-control form-control-sm
                                            @if($errors->has('shipment_date')) is-invalid @endif" 
                                            value="{{ date('Y-m-d',strtotime($merchantOrder->ShipmentDate)) }}" autocomplete="off">
                                            @if($errors->has('shipment_date'))
                                                <span class="error invalid-feedback mt-0">{{ $errors->first('shipment_date') }}</span>
                                            @endif
                                        @else
                                            <p>{{ date('d F Y', strtotime($merchantOrder->ShipmentDate)) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @foreach ($merchantOrderDetail as $key => $value)
                                    <div class="row mt-3 mt-md-2 detail-product">
                                        <div class="col-md-3 col-12 text-center align-self-center">
                                            <img src="{{ config('app.base_image_url') . '/product/'. $value->ProductImage }}" alt="" width="100">
                                            <p class="mb-0">{{ $value->ProductName }}</p>
                                            <input type="hidden" name="product_id[]" value="{{ $value->ProductID }}">
                                        </div>
                                        <div class="col-md-9 col-12 align-self-center">
                                            <div class="row">
                                                @if ($merchantOrder->StatusOrderID == "S009")
                                                    <div class="col-md-4 col-12">
                                                        <label class="mb-0">Kuantitas Beli</label>
                                                        <p>{{ $value->Quantity }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-12">
                                                        <label class="mb-0">Kuantitas Dikirim</label>
                                                        <input type="text" name="promised_qty[]" class="form-control form-control-sm autonumeric promised_qty
                                                        @if($errors->has('promised_qty.'.$key)) is-invalid @endif" value="{{ $value->PromisedQuantity }}" autocomplete="off">
                                                        @if ($errors->has('promised_qty.'.$key))
                                                            <span class="error invalid-feedback mt-0">{{ $errors->first('promised_qty.'.$key) }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="col-md-3 col-12">
                                                        <label class="mb-0">Kuantitas Beli</label>
                                                        <p>{{ $value->Quantity }}</p>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <label class="mb-0">Kuantitas Dikirim</label>
                                                        <p>{{ $value->PromisedQuantity }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="row">
                                                @if ($merchantOrder->StatusOrderID == "S009")
                                                    <div class="col-md-4 col-12">
                                                        <label class="mb-0">Harga Satuan</label>
                                                        <p class="price">{{ Helper::formatCurrency($value->Price, 'Rp ') }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-12">
                                                        <label class="mb-0" for="">Potongan</label>
                                                        <input type="text" name="discount_product[]" class="form-control form-control-sm autonumeric discount_product
                                                        @if($errors->has('discount_product.'.$key)) is-invalid @endif" value="{{ $value->Discount }}" autocomplete="off">
                                                        @if ($errors->has('discount_product.'.$key))
                                                            <span class="error invalid-feedback mt-0">{{ $errors->first('discount_product.'.$key) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 col-12">
                                                        <label class="mb-0">Total Harga Produk</label>
                                                        <p class="font-weight-bold total_price_product">{{ Helper::formatCurrency($value->PromisedQuantity * ($value->Price - $value->Discount), 'Rp ') }}</p>
                                                    </div>
                                                @else
                                                    <div class="col-md-3 col-12">
                                                        <label class="mb-0">Harga Satuan</label>
                                                        <p>{{ Helper::formatCurrency($value->Price, 'Rp ') }}</p>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <label class="mb-0" for="">Potongan</label>
                                                        <p>{{ Helper::formatCurrency($value->Discount, 'Rp ') }}</p>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <label class="mb-0" for="">Nett Satuan</label>
                                                        <p>{{ Helper::formatCurrency($value->Nett, 'Rp ') }}</p>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <label class="mb-0">Total Harga Produk</label>
                                                        <p class="font-weight-bold">{{ Helper::formatCurrency($value->PromisedQuantity * ($value->Nett), 'Rp ') }}</p>
                                                    </div>
                                                @endif
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
                                                    @if ($merchantOrder->StatusOrderID == "S009")
                                                        <input type="text" name="discount_price" id="discount_price" class="form-control form-control-sm autonumeric
                                                        @if ($errors->has('discount_price')) is-invalid mb-md-0 @endif" value="{{ $merchantOrder->DiscountPrice }}" autocomplete="off">
                                                        @if($errors->has('discount_price'))
                                                            <span class="error invalid-feedback mt-0">{{ $errors->first('discount_price') }}</span>
                                                        @endif
                                                    @else
                                                        <p class="font-weight-bold mb-0 text-danger">
                                                            {{ Helper::formatCurrency($merchantOrder->DiscountPrice, 'Rp ') }}
                                                        </p>
                                                    @endif
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
                                <div class="row konfirmasi">
                                    <div class="col-12">
                                        @if ($merchantOrder->StatusOrderID == "S009") {{-- Pesanan Baru --}}
                                            <div class="row d-md-flex justify-content-end">
                                                <div class="col-md-6 col-12 text-center">
                                                    <a href="#" class="btn btn-danger btn-batal mr-5" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
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
                                                    <a href="#" class="btn btn-danger btn-batal mr-5" data-order-id="{{ $stockOrderID }}" data-store-name="{{ $merchantOrder->StoreName }}">
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
                            </form>
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
    const currencyJumlahTopup = new AutoNumeric.multiple('.autonumeric', {
        allowDecimalPadding: false,
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        unformatOnSubmit: true
    });

    $('.detail-product').on('keyup', function (e) {
        // event listener saat protongan harga diinput
        const grandTotal = $('#grand_total');
        const grandTotalValue = grandTotal.text().replaceAll("Rp ", "").replaceAll(".", "");
        $('#discount_price').on('keyup', function (e) {
            const newGrandTotal = Number(grandTotalValue) - Number(this.value.replaceAll(".", ""));
            grandTotal.html("Rp " + thousands_separators(newGrandTotal));
        });

        // event listener saat qty product di input
        $('.promised_qty').on('keyup', function (e){
            e.preventDefault();
            const indexProduct = $(this).closest('.detail-product').index();
            const qtyProduct = e.target.value.replaceAll(".", "");
            const priceProduct = $('.detail-product').find('.price').eq(indexProduct).text().replaceAll("Rp ", "").replaceAll(".", "");
            const discountProduct = $('.detail-product').find('.discount_product').eq(indexProduct).val().replaceAll(".", "");

            const newTotalPriceProduct = (Number(priceProduct) - Number(discountProduct)) * Number(qtyProduct);
            const totalPriceProductElm = $('.detail-product').find('.total_price_product').eq(indexProduct);
            const subTotalPriceElm = $('#sub_total');
            const subTotalPrice = $('#sub_total').text().replaceAll("Rp ", "").replaceAll(".", "");
            const oldTotalPriceProduct = totalPriceProductElm.text().replaceAll("Rp ", "").replaceAll(".", "");
            
            const oldSubTotalPrice = subTotalPriceElm.text().replaceAll("Rp ", "").replaceAll(".", "");
            const newSubTotalPrice = Number(subTotalPrice) - Number(oldTotalPriceProduct) + Number(newTotalPriceProduct);
            subTotalPriceElm.html("Rp " + thousands_separators(newSubTotalPrice));
            
            totalPriceProductElm.html("Rp " + thousands_separators(newTotalPriceProduct));

            const grandTotalValue = grandTotal.text().replaceAll("Rp ", "").replaceAll(".", "");
            
            const newGrandTotal = Number(grandTotalValue) - Number(oldSubTotalPrice) + Number(newSubTotalPrice);
            grandTotal.html("Rp " + thousands_separators(newGrandTotal));
        });

        // event listener saat potongan di input
        $('.discount_product').on('keyup', function (e){
            e.preventDefault();
            const indexProduct = $(this).closest('.detail-product').index();
            const discountProduct = e.target.value.replaceAll(".", "");
            const priceProduct = $('.detail-product').find('.price').eq(indexProduct).text().replaceAll("Rp ", "").replaceAll(".", "");
            const qtyProduct = $('.detail-product').find('.promised_qty').eq(indexProduct).val().replaceAll(".", "");

            const newTotalPriceProduct = (Number(priceProduct) - Number(discountProduct)) * Number(qtyProduct);
            const totalPriceProductElm = $('.detail-product').find('.total_price_product').eq(indexProduct);
            const subTotalPriceElm = $('#sub_total');
            const subTotalPrice = $('#sub_total').text().replaceAll("Rp ", "").replaceAll(".", "");
            const oldTotalPriceProduct = totalPriceProductElm.text().replaceAll("Rp ", "").replaceAll(".", "");
            
            const oldSubTotalPrice = subTotalPriceElm.text().replaceAll("Rp ", "").replaceAll(".", "");
            const newSubTotalPrice = Number(subTotalPrice) - Number(oldTotalPriceProduct) + Number(newTotalPriceProduct);
            subTotalPriceElm.html("Rp " + thousands_separators(newSubTotalPrice));
            
            totalPriceProductElm.html("Rp " + thousands_separators(newTotalPriceProduct));

            const grandTotalValue = grandTotal.text().replaceAll("Rp ", "").replaceAll(".", "");
            
            const newGrandTotal = Number(grandTotalValue) - Number(oldSubTotalPrice) + Number(newSubTotalPrice);
            grandTotal.html("Rp " + thousands_separators(newGrandTotal));
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
            title: 'Terima Barang',
            content: `Apakah pesanan <b>${orderID}</b> dari <b>${storeName}</b> sudah sesuai?`,
            closeIcon: true,
            buttons: {
                terima: {
                    btnClass: 'btn-success',
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        form.submit();
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