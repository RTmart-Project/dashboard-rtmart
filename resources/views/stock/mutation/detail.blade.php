@extends('layouts.master')
@section('title', 'Dashboard - Detail Mutation Stock')

@section('css-pages')
@endsection

@section('header-menu', 'Detail Stok Mutasi')

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
            <a href="{{ route('stock.mutation') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="mutation-stock">
                <div class="row">
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-invoice mr-1"></i> Mutasi ID</strong>
                    <p class=" m-0">{{ $mutation->StockMutationID }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-calendar-day mr-1"></i> Tanggal Mutasi</strong>
                    <p>{{ date('d F Y\, H:i', strtotime($mutation->MutationDate)) }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-contract mr-1"></i> Sumber Purchase</strong>
                    <p>{{ $mutation->PurchaseID }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-truck mr-1"></i> Dari Distributor</strong>
                    <p>{{ $mutation->FromDistributorName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-truck-loading mr-1"></i> Ke Distributor</strong>
                    <p>{{ $mutation->ToDistributorName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-user-edit mr-1"></i> Dibuat oleh</strong>
                    <p class="m-0">{{ $mutation->CreatedBy }}</p>
                    <small>pada : {{ date('d F Y\, H:i', strtotime($mutation->CreatedDate)) }}</small>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-comment-alt mr-1"></i> Catatan</strong>
                    <p class="m-0">{{ $mutation->Notes }}</p>
                  </div>
                  
                  <div class="col-12">
                    <strong><i class="fas fa-cubes mr-1"></i> Detail Produk</strong><br>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Produk ID</th>
                          <th>Nama Produk</th>
                          <th>Label Produk</th>
                          <th>Qty</th>
                          <th>Harga Beli</th>
                          <th>Total Harga</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($mutation->Detail as $detail)
                        <tr>
                          <td>{{ $detail->ProductID }}</td>
                          <td>{{ $detail->ProductName }}</td>
                          <td>{{ $detail->ProductLabel }}</td>
                          <td>{{ $detail->Qty }}</td>
                          <td>{{ Helper::formatCurrency($detail->PurchasePrice, 'Rp ') }}</td>
                          <td>{{ Helper::formatCurrency($detail->Qty * $detail->PurchasePrice, 'Rp ') }}</td>
                        </tr>
                        @endforeach
                      </tbody>
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
@endsection