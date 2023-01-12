<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />

	<title>Invoice #{{ $merchant->DeliveryOrderID }}</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ url('/') }}/dist/img/rtmart_logo.png" type="image/x-icon">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{url('/')}}/dist/css/adminlte.min.css">

	<!-- Invoice styling -->
	<style>
		body {
			font-family: Arial, sans-serif;
			text-align: center;
			color: #777;
		}

		body h1 {
			font-weight: 300;
			margin-bottom: 0px;
			padding-bottom: 0px;
			color: #000;
		}

		body h3 {
			font-weight: 300;
			margin-top: 10px;
			margin-bottom: 20px;
			font-style: italic;
			color: #555;
		}

		body a {
			color: #06f;
		}

		.responsive-td {
			width: 40%;
		}

		.invoice-box {
			max-width: 800px;
			margin: auto;
			padding: 30px;
			border: 1px solid #eee;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
			font-size: 14px;
			line-height: 17px;
			font-family: Arial, sans-serif;
			color: #555;
		}

		.invoice-box table {
			width: 100%;
			line-height: inherit;
			text-align: left;
			border-collapse: collapse;
		}

		.invoice-box table td {
			padding: 5px;
			vertical-align: top;
		}

		.invoice-box table tr td:nth-child(2) {
			text-align: right;
		}

		.invoice-box table tr.top table td {
			padding-bottom: 20px;
		}

		.invoice-box table tr.top table td.title {
			font-size: 45px;
			line-height: 45px;
			color: #333;
		}

		.invoice-box table tr.information table td {
			padding-bottom: 40px;
		}

		.invoice-box table tr.heading td {
			background: #eee;
			border-bottom: 1px solid #ddd;
			font-weight: bold;
		}

		.invoice-box table tr.details td {
			padding-bottom: 20px;
		}

		.invoice-box table tr.item td {
			border-bottom: 1px solid #eee;
		}

		.invoice-box table tr.item.last td {
			border-bottom: none;
		}

		.invoice-box table tr.total td:nth-child(2) {
			border-top: 2px solid #eee;
			font-weight: bold;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center !important;
		}

		.pt {
			padding-top: 7px;
			padding-right: 5px;
		}

		.watermark {
			text-align: center;
			position: absolute;
			width: calc(100% - 80px);
			height: calc(100% - 60px);
			background-size: 82%;
			background-repeat: no-repeat;
			background-position: center;
			transform: rotate(-25deg);
		}

		.lunas {
			background-image: url("/dist/img/lunas.png");
			opacity: 0.25;
		}

		.belum-lunas {
			background-image: url("/dist/img/belumlunas.png");
			opacity: 0.15;
		}
	</style>
</head>

<body>
	<div class="invoice-box position-relative">

		@if ($merchant->PaymentMethodID == 14 || $merchant->PaymentMethodID == 1)
		@if ($merchant->IsPaid == 1)
		<div class="watermark lunas"></div>
		@else
		<div class="watermark belum-lunas"></div>
		@endif
		@endif

		<table>
			<tr class="top">
				<td colspan="4">
					<table>
						<tr>
							<td class="title">
								<img src="{{ url('/') }}/dist/img/rtmart.png" alt="Company logo"
									style="width: 100%; max-width: 220px" />
							</td>

							<td>
								<b>DELIVERY ORDER</b><br>
								#{{ $merchant->StockOrderID }} <br>
								#{{ $merchant->DeliveryOrderID }} <br>
								Tanggal Pengiriman: {{ date('d M Y H:i', strtotime($merchant->DeliveryDate)) }}<br>
								@if ($merchant->PaymentMethodID == 14 && $merchant->IsPaid != 1)
								<p class="m-0">{{ $merchant->FinishDate != null ? "Tanggal barang diterima: ".date("d M
									Y H:i", strtotime($merchant->FinishDate)) : "" }}</p>
								Jatuh Tempo Pembayaran: {{ $merchant->FinishDate == null ? "H+5 setelah barang diterima"
								: date( "d M Y", strtotime("$merchant->FinishDate +5 day")) }} <br />
								@endif
								@if ($merchant->StatusOrder == "Selesai" && $merchant->IsPaid == 1)
								Tgl Selesai: {{ date('d M Y H:i', strtotime($merchant->FinishDate)) }}<br>
								@endif
								{{ $merchant->StatusOrder }}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<tr class="information">
			<td colspan="4">
				<table>
					<tr>
						<td>
							<label class="mb-1">Pembeli</label> <br>
							ID Toko : <b> {{ $merchant->MerchantID }} </b> <br>
							Nama Toko : <b> {{ $merchant->StoreName }} </b> <br>
							Nama Pemilik : <b> {{ $merchant->OwnerFullName }} </b> <br>
							No HP Pemilik : <b> {{ $merchant->PhoneNumber }} </b> <br>
							Nama Sales : <b> {{ $merchant->SalesName }} </b>
						</td>

						<td class="responsive-td">
							<label class="mb-1">Alamat</label><br>
							{{ $merchant->StoreAddress }}
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<table class="mb-3">
			<tr class="heading">
				<td>Metode Pembayaran</td>
			</tr>

			<tr class="item">
				<td>{{ $merchant->PaymentMethodName }}</td>
			</tr>
		</table>

		<table class="mb-3">
			<tr class="heading">
				<td>Produk</td>
				<td class="text-center">Qty</td>
				<td class="text-right">Harga Satuan</td>
				<td class="text-right">Total Harga</td>
			</tr>

			@foreach ($detailDeliveryOrder as $item)
			<tr class="item">
				<td>{{ $item->ProductName }}</td>
				<td class="text-center">{{ $item->Qty }}</td>
				<td class="text-right">{{ Helper::formatCurrency($item->Price, 'Rp ') }}</td>
				<td class="text-right">{{ Helper::formatCurrency($item->Qty * $item->Price, 'Rp ') }}</td>
			</tr>
			@endforeach

			<tr>
				<td></td>
			</tr>

			<tr class="total">
				<th colspan="3" class="text-right">SubTotal</th>
				<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($subTotal, 'Rp ') }}</th>
			</tr>

			@if ($merchant->StatusDO == "S024" || $merchant->StatusDO == "S025")
			@if ($merchant->Discount != null && $merchant->Discount != 0)
			<tr class="total">
				<th colspan="3" class="text-right">Diskon</th>
				<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->Discount, 'Rp ') }}</th>
			</tr>
			@endif

			@if ($merchant->ServiceCharge != null && $merchant->ServiceCharge != 0)
			<tr class="total">
				<th colspan="3" class="text-right">Biaya Layanan</th>
				<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->ServiceCharge, 'Rp ') }}</th>
			</tr>
			@endif

			@if ($merchant->DeliveryFee != null && $merchant->DeliveryFee != 0)
			<tr class="total">
				<th colspan="3" class="text-right">Biaya Pengiriman</th>
				<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->DeliveryFee, 'Rp ') }}</th>
			</tr>
			@endif

			@if ($lateFee != 0 && $merchant->PaymentMethodID == 14)
			<tr class="total">
				<th colspan="3" class="text-right">Denda</th>
				<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($lateFee, 'Rp ') }}</th>
			</tr>
			@endif

			<tr class="total">
				<th colspan="3" class="text-right">Grand Total</th>
				<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($grandTotal, 'Rp ') }}</th>
			</tr>
			@endif

		</table>
		<div class="border-top">
			<div class="row mt-3 text-left">
				<div class="col-4">
					@if ($merchant->Distributor == "HAISTAR")
					Pengirim : <strong>HAISTAR</strong>
					@else
					Pengirim : <strong>{{ $merchant->Name == "" ? '-' : $merchant->Name }}</strong>
					@endif
				</div>

				<tr class="information">
					<td colspan="4">
						<table>
							<tr>
								<td></td>
								<td class="responsive-td text-center">
									Security
									<br>
									<br>
									<br>
									<br>
									( ................................. )
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="4">
						<table>
							<tr>
								<td style="font-style: italic">
									<small>Invoice ini sah dan diproses oleh komputer</small> <br>
									<small class="text-danger">Pembayaran tidak boleh diberikan kepada sales.</small>
								</td>
								<td class="responsive-td" style="font-style: italic">
									<br><br>
									<small class="">Terakhir diupdate: {{ date('d F Y H:i', strtotime($processTime)) }}
										WIB</small>
								</td>
							</tr>
						</table>
					</td>
				</tr>

			</div>
		</div>
	</div>
	<script>
		window.addEventListener("load", window.print());
	</script>
</body>

</html>