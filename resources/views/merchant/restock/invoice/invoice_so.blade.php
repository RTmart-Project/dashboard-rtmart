<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

		<title>Restock Invoice</title>

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ url('/') }}/dist/img/rtmart_logo.png" type="image/x-icon">

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
			<table>
				<tr class="top">
					<td colspan="4">
						<table>
							<tr>
								<td class="title">
									<img src="{{ url('/') }}/dist/img/rtmart.png" alt="Company logo" style="width: 100%; max-width: 220px" />
								</td>

								<td>
									@if ($merchant->StatusOrderID == "S012" || $merchant->StatusOrderID == "S018")
										<b>INVOICE</b>
									@else
										<b>PROFORMA INVOICE</b>
									@endif<br>
									#{{ $stockOrderId }} <br>
									Tgl Pesanan: {{ date('d M Y H:i', strtotime($merchant->CreatedDate)) }}<br>
									{{ $merchant->StatusOrder }}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="4">
						<table>
							<tr>
								<td>
									{{ $merchant->MerchantID }} <br>
									{{ $merchant->StoreName }} <br>
									{{ $merchant->OwnerFullName }} <br>
									{{ $merchant->PhoneNumber }}
								</td>

								<td class="responsive-td">
									{{ $merchant->StoreAddress }}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td colspan="4">Metode Pembayaran</td>
				</tr>

				<tr class="details">
					<td colspan="4">{{ $merchant->PaymentMethodName }}</td>
				</tr>

				<tr class="heading">
					<td>Produk</td>
					<td class="text-center">Qty</td>
					<td class="text-right">Harga Satuan</td>
					<td class="text-right">Total Harga</td>
				</tr>

				@foreach ($stockOrderById as $item)
				<tr class="item">
					<td>{{ $item->ProductName }} ( x{{ $item->PromisedQuantity }} item )</td>
					<td class="text-center">{{ $item->PromisedQuantity }}</td>
					<td class="text-right">{{ Helper::formatCurrency($item->Nett, 'Rp ') }}</td>
					<td class="text-right">{{ Helper::formatCurrency($item->Nett * $item->PromisedQuantity, 'Rp ') }}</td>
				</tr>		
				@endforeach
				<tr>
					<td></td>
				</tr>

				<tr class="total">
					<th colspan="3" class="text-right pt">SubTotal</th>
					<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($subTotal, 'Rp ') }}</th>
				</tr>
				@if ($merchant->DiscountPrice != 0)
				<tr class="total">
					<th colspan="3" class="text-right pt">Diskon</th>
					<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->DiscountPrice, 'Rp ') }}</th>
				</tr>
				@endif
				@if ($merchant->DiscountVoucher != 0)
				<tr class="total">
					<th colspan="3" class="text-right pt">Voucher</th>
					<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->DiscountVoucher, 'Rp ') }}</th>
				</tr>
				@endif
				@if ($merchant->ServiceChargeNett != 0)
				<tr class="total">
					<th colspan="3" class="text-right pt">Biaya Layanan</th>
					<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->ServiceChargeNett, 'Rp ') }}</th>
				</tr>
				@endif
				@if ($merchant->DeliveryFee != 0)
				<tr class="total">
					<th colspan="3" class="text-right pt">Biaya Pengiriman</th>
					<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($merchant->DeliveryFee, 'Rp ') }}</th>
				</tr>
				@endif
				<tr class="total">
					<th colspan="3" class="text-right pt">Grand Total</th>
					<th colspan="1" class="text-right pt">{{ Helper::formatCurrency($subTotal - $merchant->DiscountPrice - $merchant->DiscountVoucher + $merchant->ServiceChargeNett, 'Rp ') }}</th>
				</tr>
			</table>
		</div>
		<script>
			window.addEventListener("load", window.print());
		</script>
	</body>
</html>