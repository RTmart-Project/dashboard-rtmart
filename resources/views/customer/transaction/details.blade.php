@extends('layouts.master')
@section('title', 'Dashboard - ' . $customer->FullName . ' Transaction Details')

@section('css-pages')
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Main -->
<link rel="stylesheet" href="{{url('/')}}/main/css/custom/select-filter.css">
@endsection

@section('header-menu', 'Detail Transaksi ' . $customer->FullName)

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
                        <a href="{{ route('customer.transaction') }}" class="btn btn-sm btn-light mb-2"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <h6><strong>Order ID : </strong>{{ $orderId }}</h6>
                        <h6><strong>Nama Customer : </strong>{{ $customer->FullName }}</h6>
                        <h6><strong>No. Telp : </strong><a href="tel:{{ $customer->PhoneNumber }}">{{ $customer->PhoneNumber }}</a></h6>
                        <h6><strong>Alamat : </strong>{{ $customer->Address }}</h6>
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
                                                    @foreach ($customerOrderHistory as $value)
                                                    <div>
                                                        <i class="far fa-clock @if ($value->StatusOrderId == "S013")
                                                            bg-secondary
                                                        @elseif ($value->StatusOrderId == "S014")
                                                            bg-primary
                                                        @elseif ($value->StatusOrderId == "S019")
                                                            bg-warning
                                                        @elseif ($value->StatusOrderId == "S015")
                                                            bg-info
                                                        @elseif ($value->StatusOrderId == "S016")
                                                            bg-success
                                                        @elseif ($value->StatusOrderId == "S017")
                                                            bg-danger
                                                        @else
                                                            
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
                            <div class="tab-pane active" id="customer-transaction-details">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-datatables">
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
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5"></th>
                                                    <th>Grand Total</th>
                                                    <th>Grand Total</th>
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
<script src="{{url('/')}}/main/js/customer/transaction/details.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
</script>
@endsection