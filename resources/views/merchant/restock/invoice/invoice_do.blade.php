<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

		<title>Restock Invoice</title>

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ url('/') }}/dist/img/rtmart_logo.png" type="image/x-icon">
		<!-- Theme style -->
    <link rel="stylesheet" href="{{url('/')}}/dist/css/adminlte.min.css">

		<!-- Invoice styling -->
		<style>
			body {
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
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
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
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

			.text-center{
				text-align: center !important;
			}

			.pt {
				padding-top: 7px;
				padding-right: 5px;
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">

			<div class="row m-1 mb-3">
				<div class="col-6 text-left">
					<img src="{{ url('/') }}/dist/img/rtmart.png" alt="Company logo" style="width: 100%; max-width: 220px" />
				</div>
				<div class="col-6 text-right">
					<b>DELIVERY ORDER INVOICE</b><br>
					#{{ $merchant->StockOrderID }} <br>
					#{{ $merchant->DeliveryOrderID }} <br>
					Tgl Pengiriman: {{ date('d M Y H:i', strtotime($merchant->CreatedDate)) }}<br>
					@if ($merchant->StatusOrder == "Selesai")
					Tgl Selesai: {{ date('d M Y H:i', strtotime($merchant->FinishDate)) }}<br>
					@endif
					{{ $merchant->StatusOrder }}
				</div>
			</div>

			<div class="row m-1 mb-4">
				<div class="col-6 text-left">
					<label class="mb-1">Pembeli</label>
				</div>
				<div class="col-6 text-right">
					<label class="mb-1">Alamat</label>
				</div>
				<div class="col-6 text-left">
					<div class="row">
						<div class="col-4">ID Toko</div>
						<div class="col-1 text-center">:</div>
						<div class="col-7">{{ $merchant->MerchantID }}</div>

						<div class="col-4">Nama Toko</div>
						<div class="col-1 text-center">:</div>
						<div class="col-7">{{ $merchant->StoreName }} ({{ $merchant->OwnerFullName }})</div>

						<div class="col-4">No. Telepon</div>
						<div class="col-1 text-center">:</div>
						<div class="col-7">{{ $merchant->PhoneNumber }}</div>
					</div>
				</div>
				<div class="col-6 text-right">
					{{ $merchant->StoreAddress }}
				</div>
			</div>

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
			</table>
			<div class="border-top">
				<div class="row mt-4 text-left">
					<div class="col-7">
						Pengirim : <strong>{{ $merchant->Name == "" ? '-' : $merchant->Name }}</strong>
					</div>
					<div class="col-5">
						Metode Pembayaran : <strong>{{ $merchant->PaymentMethodName }}</strong>
					</div>
					<div class="col-7 mt-5">
						<small>Invoice ini sah dan diproses oleh komputer</small>
					</div>
					<div class="col-5 mt-5 font-italic">
						<small class="">Terakhir diupdate: {{ date('d F Y H:i', strtotime($processTime)) }} WIB</small>
					</div>
				</div>
			</div>
		</div>
		<script>
			window.addEventListener("load", window.print());
		</script>
	</body>
</html>