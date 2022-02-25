@extends('layouts.master')
@section('title', 'Dashboard - Merchant Special Price')

@section('css-pages')
<meta name="csrf_token" content="{{ csrf_token() }}">
<meta name="role" content="{{ Auth::user()->RoleID }}">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('header-menu', 'Merchant Special Price')

@section('content')
<div class="content pt-3">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header pb-0">
                <a href="{{ route('distribution.merchant') }}" class="btn btn-sm btn-light mb-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="col-12 d-flex align-items-stretch flex-column">
                    <div class="card d-flex flex-fill">
                        <div class="card-body pt-3 pb-3">
                            <div class="row">
                                <div class="col-12 col-md-2 text-center">
                                    <img src="{{ config('app.base_image_url') . '/merchant/'. $merchant->StoreImage }}" alt="Store Image" class="rounded img-fluid pb-2 pb-md-0" style="object-fit: cover; width: 110px; height: 110px;">
                                </div>
                                <div class="col-12 col-md-10 align-self-center">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <h6><strong>Merchant ID : </strong>{{ $merchant->MerchantID }}</h6>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <h6><strong>Nama Toko : </strong>{{ $merchant->StoreName }}</h6>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <h6><strong>Nama Pemilik : </strong>{{ $merchant->OwnerFullName }}</h6>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <h6><strong>No. Telp : </strong><a href="tel:{{ $merchant->PhoneNumber }}">{{ $merchant->PhoneNumber }}</a></h6>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-pane active" id="special-price">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-datatables">
                                <thead>
                                    <tr>
                                        <th>Grade</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Special Price</th>
                                        @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" || Auth::user()->RoleID == "FI")
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-pages')
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

{{-- Main --}}
<script src="{{url('/')}}/main/js/distribution/merchant/special-price.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
    const storeName = `{{ $merchant->StoreName }}`;
    const merchantID = `{{ $merchant->MerchantID }}`;
    const gradeID = `{{ $grade->GradeID }}`;
</script>
@endsection