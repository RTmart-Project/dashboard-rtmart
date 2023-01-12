<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{url('/')}}/dist/css/adminlte.min.css">
  <title>Invoice Restock</title>
</head>
<body>
  <div class="wrapper">
    <section class="invoice">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <img src="{{ url('/') }}/dist/img/rtmart.png" alt="RT Mart" class="mb-3" width="150">
              <div class="row">
                <div class="col-md-4 col-12">
                  <h6><strong>Stock Order ID : </strong>{{ $stockOrderId }}</h6>
                  <h6><strong>Tanggal Pesanan : </strong>{{ date('d-M-Y H:i', strtotime($merchant->CreatedDate)) }}</h6>
                  <h6><strong>Status Restock : </strong>{{ $merchant->StatusOrder }}</h6>
                  <h6><strong>Metode Pembayaran : </strong>{{ $merchant->PaymentMethodName }}</h6>
                </div>
                <div class="col-md-4 col-12">
                  <h6><strong>ID Toko : </strong>{{ $merchant->MerchantID }}</h6>
                  <h6><strong>Nama Toko : </strong>{{ $merchant->StoreName }}</h6>
                  <h6><strong>Nama Pemilik : </strong>{{ $merchant->OwnerFullName }}</h6>
                  <h6><strong>No. Telp : </strong><a href="tel:{{ $merchant->PhoneNumber }}">{{ $merchant->PhoneNumber }}</a></h6>
                </div>
                <div class="col-md-4 col-12">
                  <h6><strong>Alamat : </strong><br>{{ $merchant->StoreAddress }}</h6>
                </div>
              </div>
            </div>
            <div class="card-body mt-2">
              <div class="tab-content">
                <div class="tab-pane active" id="merchant-restock-details">
                  <div class="row">
                    <div class="col-12 table-responsive">
                      <table class="table text-nowrap">
                        <thead>
                          <tr>
                            <th>Product ID</th>
                            <th>Deskripsi</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Diskon</th>
                            <th>Harga stlh Diskon</th>
                            <th>Total Harga</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($stockOrderById as $item)
                          <tr>
                            <td>{{ $item->ProductID }}</td>
                            <td>{{ $item->ProductName }}</td>
                            <td>{{ $item->PromisedQuantity }}</td>
                            <td>{{ Helper::formatCurrency($item->Price, 'Rp ') }}</td>
                            <td>{{ Helper::formatCurrency($item->Discount, 'Rp ') }}</td>
                            <td>{{ Helper::formatCurrency($item->Nett, 'Rp ') }}</td>
                            <td>{{ Helper::formatCurrency($item->Nett * $item->PromisedQuantity, 'Rp ') }}</td>
                          </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <th class="p-1" colspan="5"></th>
                            <th class="p-1 text-center">SubTotal</th>
                            <th class="py-1 px-2">{{ Helper::formatCurrency($subTotal, 'Rp ') }}</th>
                          </tr>
                          <tr>
                            <th class="p-1 border-0" colspan="5"></th>
                            <th class="p-1 border-0 text-center">Diskon</th>
                            <th class="py-1 px-2 border-0 text-danger">{{ Helper::formatCurrency($merchant->DiscountPrice, 'Rp ') }}</th>
                          </tr>
                          <tr>
                            <th class="p-1 border-0" colspan="5"></th>
                            <th class="p-1 border-0 text-center">Biaya Layanan</th>
                            <th class="py-1 px-2 border-0">{{ Helper::formatCurrency($merchant->ServiceChargeNett, 'Rp ') }}</th>
                          </tr>
                          <tr>
                            <th class="p-1 border-0" colspan="5"></th>
                            <th class="p-1 border-0 text-center">Grand Total</th>
                            <th class="py-1 px-2 border-0">{{ Helper::formatCurrency($subTotal - $merchant->DiscountPrice + $merchant->ServiceChargeNett, 'Rp ') }}</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    </section>
  </div>
  
  <script>
    window.addEventListener("load", window.print());
  </script>
</body>
</html>