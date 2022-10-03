@extends('layouts.master')
@section('title', 'Dashboard - Edit Invoice Purchase Stock')

@section('css-pages')
@endsection

@section('header-menu', 'Ubah Invoice Purchase Stock')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row">
      <!-- left -->
      <div class="col-sm-6">
      </div>
      <!-- Right -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item">

          </li>
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
            <a href="{{ route('stock.purchase') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <div class="callout callout-warning">
              <h5>Peringatan!</h5>
              <h6>Data Invoice hanya dapat diubah sekali saja. Pastikan input data dengan benar.</h6>
            </div>
            <form id="edit-purchase" method="post" enctype="multipart/form-data"
              action="{{ route('stock.updateInvoicePurchase', ['purchaseID' => $purchase->PurchaseID]) }}">
              @csrf
              <div class="row">
                <div class="col-12 col-md-6">
                  <div class="form-group">
                    <label>Purchase ID</label>
                    <input type="text" class="form-control" readonly value="{{ $purchase->PurchaseID }}">
                  </div>
                </div>
                {{-- <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="distributor">Distributor</label>
                    <input type="text" class="form-control" readonly value="{{ $purchase->DistributorName }}">
                  </div>
                </div> --}}
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="investor">Investor</label>
                    <input type="text" class="form-control" readonly value="{{ $purchase->InvestorName }}">
                  </div>
                </div>
                {{-- <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <input type="text" class="form-control" readonly value="{{ $purchase->SupplierName }}">
                  </div>
                </div> --}}
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="invoice_number">No. Invoice</label>
                    <input type="text" name="invoice_number" id="invoice_number" placeholder="Masukkan Nomor Invoice" 
                      value="{{ old('invoice_number') ? old('invoice_number') : $purchase->InvoiceNumber }}"
                      class="form-control @if($errors->has('invoice_number')) is-invalid @endif">
                    @if($errors->has('invoice_number'))
                    <span class="error invalid-feedback">{{ $errors->first('invoice_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="invoice_image">Gambar Nota / Invoice</label>
                    <input type="file" name="invoice_image" id="invoice_image"
                      class="form-control mb-2 @if($errors->has('invoice_image')) is-invalid @endif">
                    @if($errors->has('invoice_image'))
                    <span class="error invalid-feedback">{{ $errors->first('invoice_image') }}</span>
                    @endif
                    @if ($purchase->InvoiceFile != NULL)
                    <a href="{{ config('app.base_image_url').'stock_invoice/'.$purchase->InvoiceFile }}"
                      target="_blank">{{ $purchase->InvoiceFile }}</a>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group float-right mt-4">
                <button type="submit" class="btn btn-success">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js-pages')

@endsection