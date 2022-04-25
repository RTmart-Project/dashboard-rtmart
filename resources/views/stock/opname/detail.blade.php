@extends('layouts.master')
@section('title', 'Dashboard - Detail Stock Opname')

@section('css-pages')
@endsection

@section('header-menu', 'Detail Stock Opname')

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
            <a href="{{ route('stock.opname') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
              Kembali</a>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="stock-opname-detail">
                <div class="row">
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-file-invoice mr-1"></i> Stock Opname ID</strong>
                    <p class=" m-0">{{ $opnameByID->StockOpnameID }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Distributor</strong>
                    <p>{{ $opnameByID->DistributorName }}</p>
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-calendar-day mr-1"></i> Tanggal Stock Opname</strong>
                    <p>{{ date('d F Y\, H:i', strtotime($opnameByID->OpnameDate)) }}</p>
                  </div>
                  <div class="col-12 col-md-3">
                    <strong><i class="fas fa-users mr-1 mb-2"></i> Petugas Stock Opname</strong>
                    <table class="table table-sm">
                      <thead>
                        <tr><th>Nama</th><th>Role</th></tr>
                      </thead>
                      <tbody>
                        @foreach ($opnameByID->Officer as $officer)
                        <tr>
                          <td>{{ $officer->Name }}</td>
                          <td>{{ $officer->RoleID }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-1"></div>
                  <div class="col-12 col-md-4 mb-3">
                    <strong><i class="fas fa-sticky-note mr-1"></i> Catatan</strong>
                    <p>{{ $opnameByID->Notes }}</p>
                  </div>
                  <div class="col-12">
                    <strong><i class="fas fa-cubes mr-1"></i> Detail Produk</strong><br>
                    <div class="table-responsive">
                      <table class="table table-bordered text-nowrap">
                        <thead>
                          <tr>
                            <th>Produk ID</th>
                            <th>Nama Produk</th>
                            <th>Qty Lama</th>
                            <th>Qty Baru</th>
                            <th>Harga Beli</th>
                            <th>Kondisi Barang</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($opnameByID->Detail as $detail)
                          <tr>
                            <td>{{ $detail->ProductID }}</td>
                            <td>{{ $detail->ProductName }}</td>
                            <td>{{ $detail->OldQty }}</td>
                            <td>{{ $detail->NewQty }}</td>
                            <td>{{ Helper::formatCurrency($detail->PurchasePrice, 'Rp ') }}</td>
                            <td>{{ $detail->ConditionStock }}</td>
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
</div>
@endsection

@section('js-pages')

@endsection