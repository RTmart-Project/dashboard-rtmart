@extends('layouts.master')
@section('title', 'Dashboard - Add Purchase Stock')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
@endsection

@section('header-menu', 'Tambah Purchase Stock')

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
            <form id="add-purchase" method="post" action="{{ route('stock.storePurchase') }}"
              enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label for="purchase_plan">Sumber Purchase Plan</label>
                    <select name="purchase_plan" id="purchase_plan" data-live-search="true" title="Pilih Purchase Plan"
                      class="form-control selectpicker border @if($errors->has('purchase_plan')) is-invalid @endif"
                      required>
                      @foreach ($purchasePlan as $item)
                      <option value="{{ $item->PurchasePlanID }}" 
                        {{ old('purchase_plan') == $item->PurchasePlanID ? 'selected' : '' }}>
                        {{ $item->PurchasePlanID }} - {{ $item->InvestorName }} - (Tgl Plan {{ date('d F Y', strtotime($item->PlanDate)) }})
                      </option>
                      @endforeach
                    </select>
                    @if($errors->has('purchase_plan'))
                    <span class="error invalid-feedback">{{ $errors->first('purchase_plan') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="investor">Investor</label>
                    <input type="text" class="form-control" name="investor" id="investor" readonly required>
                    <input type="hidden" class="form-control" name="investor_id" id="investor_id" readonly required>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="purchase_date">Tanggal Purchase</label>
                    <input type="datetime-local" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}"
                      class="form-control @if($errors->has('purchase_date')) is-invalid @endif" required>
                    @if($errors->has('purchase_date'))
                    <span class="error invalid-feedback">{{ $errors->first('purchase_date') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="estimation_arrive">Tanggal Estimasi Tiba</label>
                    <input type="datetime-local" name="estimation_arrive" id="estimation_arrive" value="{{ old('estimation_arrive') }}"
                      class="form-control @if($errors->has('estimation_arrive')) is-invalid @endif" required>
                    @if($errors->has('estimation_arrive'))
                    <span class="error invalid-feedback">{{ $errors->first('estimation_arrive') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <hr>
              <h4>Detail Produk</h4>
              <div class="table-responsive">
                <table class="table table-datatables">
                  <thead>
                    <tr>
                      <th>Distributor</th>
                      <th>Supplier</th>
                      <th>Keterangan</th>
                      <th>Produk ID</th>
                      <th>Produk</th>
                      <th>Produk Label</th>
                      <th>Qty</th>
                      <th>Qty PO</th>
                      <th>% PO</th>
                      <th>Harga Beli</th>
                      <th>Value Beli</th>
                      <th>Bunga</th>
                      <th>Harga Jual</th>
                      <th>Value Jual</th>
                      <th>Gross Margin</th>
                      <th>Margin /ctn</th>
                      <th>Nett Margin</th>
                      <th>% Margin</th>
                      <th>Stock</th>
                    </tr>
                  </thead>
                  <tbody id="details">
                    
                  </tbody>
                </table>
              </div>

              <div class="form-group float-right mt-4">
                <button type="button" class="btn btn-success" id="btn-save">Simpan</button>
              </div>
              
              <!-- Modal -->
              <div class="modal fade" id="konfirmasi" data-backdrop="static" tabindex="-1" role="dialog"
              aria-labelledby="konfirmasiLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title" id="konfirmasiLabel"><i class="fas fa-info"></i> Konfirmasi</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah produk yang di-input sudah benar?</h5>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-dismiss="modal">Kembali</button>
                      <button type="button" class="btn btn-sm btn-success" data-target="#konfirmasi2"
                        data-toggle="modal" data-dismiss="modal">Benar</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="konfirmasi2" aria-hidden="true" data-backdrop="static"
                tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h6 class="modal-title"><i class="far fa-question-circle"></i> Buat Purchase Stock</h6>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <h5>Apakah yakin ingin membuat Purchase Stock?</h5>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                        data-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-sm btn-success btn-create-purchase-stock" data-toggle="modal">
                        Yakin <i class="fas fa-circle-notch fa-spin d-none loader"></i>
                      </button>
                    </div>
                  </div>
                </div>
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
<script src="{{url('/')}}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{url('/')}}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
  let Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 4000,
  });

  $("#purchase_plan").on("change", function () {
    const purchasePlanID = $(this).val();
    
    $.ajax({
      type: "get",
      url: `/stock/purchase/by-purchase-plan/${purchasePlanID}`,
      success: function (response) {
        $("#investor").val(response.InvestorName);
        $("#investor_id").val(response.InvestorID);
        $("#purchase_date").val(response.PlanDate.replace(" ", "T").substring(0,16));

        let data = '';
        $.each(response.Detail, function(index, value){
          data += `<tr>
                    <td>${value.DistributorName}</td>
                    <td>${value.SupplierName}</td>
                    <td>${value.Note}</td>
                    <td>${value.ProductID}</td>
                    <td>${value.ProductName}</td>
                    <td>${value.ProductLabel}</td>
                    <td>${value.Qty}</td>
                    <td>${value.QtyPO}</td>
                    <td>${value.PercentagePO}</td>
                    <td>${thousands_separators(value.PurchasePrice)}</td>
                    <td>${thousands_separators(value.PurchaseValue)}</td>
                    <td>${thousands_separators(value.Interest)}</td>
                    <td>${thousands_separators(value.SellingPrice)}</td>
                    <td>${thousands_separators(value.SellingValue)}</td>
                    <td>${thousands_separators(value.GrossMargin)}</td>
                    <td>${thousands_separators(value.MarginCtn)}</td>
                    <td>${thousands_separators(value.NettMargin)}</td>
                    <td>${value.PercentageMargin}</td>
                    <td>${value.LastStock}</td>
                  </tr>`;
        });
        $('#details').html(data);
      }
    });
  });

  $("#btn-save").on("click", function () {
    const planID = $("#purchase_plan").val();
    const estimationArrive = $("#estimation_arrive").val();

    if (!planID) {
      Toast.fire({
        icon: "error",
        title: `Harap Pilih Purchase Plan!`,
      });    
    } else if (!estimationArrive) {
      Toast.fire({
        icon: "error",
        title: `Harap Isi Tanggal Estimasi Tiba!`,
      });    
    } else {
      $('#konfirmasi').modal('show');
    }
  });

  $("#add-purchase").on("submit", function (e) {
    $('.btn-create-purchase-stock').prop("disabled", true);
    $('.btn-create-purchase-stock').children().removeClass("d-none");
  })
</script>
@endsection