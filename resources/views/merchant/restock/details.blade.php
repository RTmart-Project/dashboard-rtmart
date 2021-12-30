@extends('layouts.master')
@section('title', 'Dashboard - ' . $merchant->StoreName . ' Restock Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Detail Restock ' . $merchant->StoreName)

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
                        <a href="{{ route('merchant.restock') }}" class="btn btn-sm btn-light mb-2"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <a href="" class="btn btn-sm btn-info float-right mb-2">Cetak Invoice</a>
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
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12 col-12 mt-1">
                                <div class="card card-info card-outline collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title">Order History</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!-- The time line -->
                                                <div class="timeline">
                                                    @foreach ($merchantOrderHistory as $value)
                                                    <div>
                                                        <i class="far fa-clock @if ($value->StatusOrderId == "S009")
                                                            bg-secondary
                                                        @elseif ($value->StatusOrderId == "S010")
                                                            bg-primary
                                                        @elseif ($value->StatusOrderId == "S023")
                                                            bg-warning
                                                        @elseif ($value->StatusOrderId == "S012")
                                                            bg-info
                                                        @elseif ($value->StatusOrderId == "S018")
                                                            bg-success
                                                        @elseif ($value->StatusOrderId == "S011")
                                                            bg-danger
                                                        @endif"></i>
                                                        <div class="timeline-item">
                                                            <h3 class="timeline-header">{{ date('d F Y H:i:s', strtotime($value->ProcessTime)) }}</h3>
                                                            <div class="timeline-body pl-3">
                                                                <strong>{{ $value->StatusOrder }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    <!-- END timeline item -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                    <th class="py-1 px-2 border-0 text-danger">{{ Helper::formatCurrency($merchant->DiscountPrice, '- Rp ') }}</th>
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
    </div>
</div>
@endsection

@section('js-pages')
<!-- InputMask -->
<script src="{{url('/')}}/plugins/moment/moment.min.js"></script>
<script src="{{url('/')}}/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="{{url('/')}}/plugins/daterangepicker/daterangepicker.js"></script>
<!-- DataTables  & Plugins -->
<script src="{{url('/')}}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="{{url('/')}}/plugins/jszip/jszip.min.js"></script>
<script src="{{url('/')}}/plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{url('/')}}/plugins/pdfmake/vfs_fonts.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="{{url('/')}}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Main JS -->
<script src="{{url('/')}}/main/js/merchant/restock/details.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
</script>
@endsection