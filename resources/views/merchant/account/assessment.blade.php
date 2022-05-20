@extends('layouts.master')
@section('title', 'Dashboard - Assessment Merchant')

@section('css-pages')
@endsection

@section('header-menu', 'Assessment Merchant')

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
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <a href="{{ route('merchant.account') }}" class="btn btn-sm btn-light mb-2">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="row mt-3 text-center">
              <div class="col-md-6 col-12">
                <h6><strong>Merchant ID : </strong>{{ $assessment->MerchantID }}</h6>
              </div>
              <div class="col-md-6 col-12">
                <h6><strong>Nama Toko : </strong>{{ $assessment->StoreName }}</h6>
              </div>
              <div class="col-md-6 col-12">
                <h6><strong>Nama Pemilik : </strong>{{ $assessment->OwnerFullName }}
                </h6>
              </div>
              <div class="col-md-6 col-12">
                <h6><strong>No. Telp : </strong><a href="tel:{{ $assessment->PhoneNumber }}">{{
                    $assessment->PhoneNumber }}</a></h6>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-12 col-md-6 mb-3">
                <h6 class="font-weight-bold">1. Foto Toko</h6>
                <div class="row justify-content-center">
                  <div class="col-12 col-sm-6">
                    <div class="card w-100">
                      <img
                        src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoMerchantFront }}"
                        alt="Tampak Depan Toko" class="card-image-top" style="height: 220px; object-fit: cover;">
                      <div class="card-body p-2">
                        <p class="card-text mb-0">Tampak Depan</p>
                        <a href="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoMerchantFront }}"
                          target="_blank">(Lihat Gambar Full)</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-sm-6">
                    <div class="card w-100">
                      <img
                        src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoMerchantSide }}"
                        alt="Tampak Depan Toko" class="card-image-top" style="height: 220px; object-fit: cover;">
                      <div class="card-body p-2">
                        <p class="card-text mb-0">Tampak Samping</p>
                        <a href="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoMerchantSide }}"
                          target="_blank">(Lihat Gambar Full)</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 mb-3">
                <h6 class="font-weight-bold">2. Bukti Bon Distributor</h6>
                <div class="row justify-content-center">
                  <div class="col-12 col-md-8 col-lg-7">
                    <div class="card w-100">
                      <img
                        src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->StruckDistribution }}"
                        alt="Tampak Depan Toko" class="card-image-top" style="height: 220px; object-fit: cover;">
                      <div class="card-body p-2">
                        <p class="card-text mb-0">Omset Rata-Rata per Bulan :
                          {{Helper::formatCurrency($assessment->TurnoverAverage, 'Rp ')}}</p>
                        <a href="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->StruckDistribution }}"
                          target="_blank">(Lihat Gambar Full)</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 mb-3">
                <h6 class="font-weight-bold">3. Foto Stok Toko</h6>
                <div class="row justify-content-center">
                  <div class="col-12 col-md-8 col-lg-7">
                    <div class="card w-100">
                      <img
                        src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoStockProduct }}"
                        alt="Tampak Depan Toko" class="card-image-top" style="height: 220px; object-fit: cover;">
                      <div class="card-body p-2">
                        <a href="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoStockProduct }}"
                          target="_blank">(Lihat Gambar Full)</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 mb-3">
                <h6 class="font-weight-bold">4. Foto KTP</h6>
                <div class="row justify-content-center">
                  <div class="col-12 col-md-8 col-lg-7">
                    <div class="card w-100">
                      <img
                        src="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoIDCard }}"
                        alt="Tampak Depan Toko" class="card-image-top" style="height: 220px; object-fit: cover;">
                      <div class="card-body p-2">
                        <p class="card-text mb-0">No. KTP : {{ $assessment->NumberIDCard }}</p>
                        <a href="{{ config('app.base_image_url') . 'rtsales/merchantassessment/'. $assessment->PhotoIDCard }}"
                          target="_blank">(Lihat Gambar Full)</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 mb-3">
                <h6 class="font-weight-bold">5. Metode Transaksi yg Pernah Digunakan</h6>
                <div class="row justify-content-center">
                  <div class="col-12 col-md-8 col-lg-7">
                    @foreach ($assessment->AssessmentTransaction as $assessmentTrx)
                    <p class="my-1"><i class="fas fa-check"></i> {{ $assessmentTrx->TransactionName }}</p>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
          @if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" || Auth::user()->RoleID == "FI")
          <div class="card-footer py-4 text-center">
            <a data-assessment-id="{{ $assessment->MerchantAssessmentID }}" data-store-name="{{ $assessment->StoreName }}"
              class="btn btn-sm btn-danger btn-reset-assessment">
              Hapus Data Assessment
            </a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@section('js-pages')
<script>
  $('.btn-reset-assessment').on('click', function (e) {
    e.preventDefault();
    const assessmentID = $(this).data("assessment-id");
    const storeName = $(this).data("store-name");
    $.confirm({
      title: 'Hapus Data Assessment!',
      content: `Apakah yakin ingin menghapus data assessment dari <b>${storeName}</b>?`,
      closeIcon: true,
      type: 'red',
      typeAnimated: true,
      buttons: {
        ya: {
          btnClass: 'btn-danger',
          draggable: true,
          dragWindowGap: 0,
          action: function () {
            window.location = '/merchant/account/resetAssessment/' + assessmentID
          }
        },
        tidak: function () {
        }
      }
    });
  });
</script>
@endsection