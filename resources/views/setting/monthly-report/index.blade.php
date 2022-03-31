@extends('layouts.master')
@section('title', 'Dashboard - Monthly Report')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
<meta name="csrf_token" content="{{ csrf_token() }}">
@endsection

@section('header-menu', 'Monthly Report')

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
        <div class="card card-primary card-tabs">
          <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="add-data-tab" data-toggle="pill" href="#add-data" role="tab"
                  aria-controls="add-data" aria-selected="true">Tambah Data</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="edit-data-tab" data-toggle="pill" href="#edit-data" role="tab"
                  aria-controls="edit-data" aria-selected="false">Ubah Data</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="delete-data-tab" data-toggle="pill" href="#delete-data" role="tab"
                  aria-controls="delete-data" aria-selected="false">Hapus Data</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content" id="custom-tabsContent">
              <div class="tab-pane fade active show" id="add-data" role="tabpanel" aria-labelledby="add-data-tab">
                <form action="{{ route('monthlyReport.store') }}" method="post">
                  @csrf
                  <div class="row">
                    <div class="col-12 col-md-6">
                      <div class="form-group">
                        <label for="area">Area</label>
                        <select name="area" id="area" data-live-search="true" title="Pilih Area"
                          class="form-control selectpicker border @if($errors->has('area')) is-invalid @endif" required>
                          <option value="Cakung" {{ old('area')=='Cakung' ? 'selected' : '' }}>Cakung</option>
                          <option value="Bandung" {{ old('area')=='Bandung' ? 'selected' : '' }}>Bandung</option>
                          <option value="Ciracas" {{ old('area')=='Ciracas' ? 'selected' : '' }}>Ciracas</option>
                          <option value="Trading" {{ old('area')=='Trading' ? 'selected' : '' }}>Trading</option>
                          <option value="National" {{ old('area')=='National' ? 'selected' : '' }}>National</option>
                        </select>
                        @if($errors->has('area'))
                        <span class="error invalid-feedback">{{ $errors->first('area') }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="form-group">
                        <label for="periode">Periode</label>
                        <input type="month" name="periode" id="periode" required value="{{ old('periode') }}"
                          class="form-control @if($errors->has('periode')) is-invalid @endif">
                        @if($errors->has('periode'))
                        <span class="error invalid-feedback">{{ $errors->first('periode') }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <label for="sales" class="text-md-center col-sm-2 col-form-label">Sales</label>
                        <div class="col-sm-10">
                          <input type="text" name="sales" id="sales" required autocomplete="off"
                            value="{{ old('sales') }}"
                            class="form-control autonumeric @if($errors->has('sales')) is-invalid @endif">
                          @if($errors->has('sales'))
                          <span class="error invalid-feedback">{{ $errors->first('sales') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <label for="cogs" class="text-md-center col-sm-2 col-form-label">COGS</label>
                        <div class="col-sm-10">
                          <input type="text" name="cogs" id="cogs" required autocomplete="off" value="{{ old('cogs') }}"
                            class="form-control autonumeric @if($errors->has('cogs')) is-invalid @endif">
                          @if($errors->has('cogs'))
                          <span class="error invalid-feedback">{{ $errors->first('cogs') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <label for="gp_margin" class="text-md-center col-sm-2 col-form-label">GP Margin</label>
                        <div class="col-sm-10">
                          <input type="text" name="gp_margin" id="gp_margin" required autocomplete="off"
                            value="{{ old('gp_margin') }}"
                            class="form-control autonumeric @if($errors->has('gp_margin')) is-invalid @endif">
                          @if($errors->has('gp_margin'))
                          <span class="error invalid-feedback">{{ $errors->first('gp_margin') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <label for="gp_ratio" class="text-md-center col-sm-2 col-form-label">GP Ratio</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <input type="text" id="gp_ratio" name="gp_ratio" required autocomplete="off"
                              value="{{ old('gp_ratio') }}"
                              class="form-control autonumeric @if($errors->has('gp_ratio')) is-invalid @endif">
                            <div class="input-group-append">
                              <span class="input-group-text">%</span>
                            </div>
                          </div>
                          @if($errors->has('gp_ratio'))
                          <span class="error invalid-feedback">{{ $errors->first('gp_ratio') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <label for="ending_inventory" class="text-md-center col-sm-2 col-form-label">Ending
                          Inventory</label>
                        <div class="col-sm-10">
                          <input type="text" name="ending_inventory" id="ending_inventory" required autocomplete="off"
                            value="{{ old('ending_inventory') }}"
                            class="form-control autonumeric @if($errors->has('ending_inventory')) is-invalid @endif">
                          @if($errors->has('ending_inventory'))
                          <span class="error invalid-feedback">{{ $errors->first('ending_inventory') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group row">
                        <label for="inventory_ratio" class="text-md-center col-sm-2 col-form-label">Inventory
                          Ratio</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <input type="text" id="inventory_ratio" name="inventory_ratio" required autocomplete="off"
                              value="{{ old('inventory_ratio') }}"
                              class="form-control autonumeric @if($errors->has('inventory_ratio')) is-invalid @endif">
                            <div class="input-group-append">
                              <span class="input-group-text">%</span>
                            </div>
                          </div>
                          @if($errors->has('inventory_ratio'))
                          <span class="error invalid-feedback">{{ $errors->first('inventory_ratio') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-success mt-3 float-right">Simpan</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="tab-pane fade" id="edit-data" role="tabpanel" aria-labelledby="edit-data-tab">
                <div class="callout callout-info p-2">
                  <span>Pilih terlebih dahulu Area dan Periode yang ingin diubah datanya</span>
                </div>
                <form action="{{ route('monthlyReport.update') }}" method="post">
                  @csrf
                  <div class="row">
                    <div class="col-12 col-md-6">
                      <div class="form-group">
                        <label for="area-edit">Area</label>
                        <select name="area" id="area-edit" data-live-search="true" title="Pilih Area"
                          class="form-control selectpicker border @if($errors->has('area')) is-invalid @endif" required>
                          <option value="Cakung" {{ old('area')=='Cakung' ? 'selected' : '' }}>Cakung</option>
                          <option value="Bandung" {{ old('area')=='Bandung' ? 'selected' : '' }}>Bandung</option>
                          <option value="Ciracas" {{ old('area')=='Ciracas' ? 'selected' : '' }}>Ciracas</option>
                          <option value="Trading" {{ old('area')=='Trading' ? 'selected' : '' }}>Trading</option>
                          <option value="National" {{ old('area')=='National' ? 'selected' : '' }}>National</option>
                        </select>
                        @if($errors->has('area'))
                        <span class="error invalid-feedback">{{ $errors->first('area') }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="form-group">
                        <label for="periode-edit">Periode</label>
                        <input type="month" name="periode" id="periode-edit" required value="{{ old('periode') }}"
                          class="form-control @if($errors->has('periode')) is-invalid @endif">
                        @if($errors->has('periode'))
                        <span class="error invalid-feedback">{{ $errors->first('periode') }}</span>
                        @endif
                      </div>
                    </div>
                    <div id="result" class="col-12 p-0">

                    </div>
                  </div>
                </form>
              </div>
              <div class="tab-pane fade" id="delete-data" role="tabpanel" aria-labelledby="delete-data-tab">
                <div class="callout callout-info p-2">
                  <span>Pilih terlebih dahulu Area dan Periode yang ingin dihapus</span>
                </div>
                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for="area-delete">Area</label>
                      <select name="area" id="area-delete" data-live-search="true" title="Pilih Area"
                        class="form-control selectpicker border" required>
                        <option value="Cakung" {{ old('area')=='Cakung' ? 'selected' : '' }}>Cakung</option>
                        <option value="Bandung" {{ old('area')=='Bandung' ? 'selected' : '' }}>Bandung</option>
                        <option value="Ciracas" {{ old('area')=='Ciracas' ? 'selected' : '' }}>Ciracas</option>
                        <option value="Trading" {{ old('area')=='Trading' ? 'selected' : '' }}>Trading</option>
                        <option value="National" {{ old('area')=='National' ? 'selected' : '' }}>National</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for="periode-delete">Periode</label>
                      <input type="month" name="periode" id="periode-delete" required value="{{ old('periode') }}"
                        class="form-control">
                    </div>
                  </div>
                  <div id="result-delete" class="col-12 p-0">

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
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="https://unpkg.com/autonumeric"></script>
<script>
  // Set seperator '.' currency
  new AutoNumeric.multiple('.autonumeric', {
      allowDecimalPadding: false,
      decimalCharacter: ',',
      digitGroupSeparator: '.',
      unformatOnSubmit: true
  });

  let csrf = $('meta[name="csrf_token"]').attr("content");

  $('#area-edit, #periode-edit').on('change', function() {
    let area = $('#area-edit').val();
    let periode = $('#periode-edit').val();
    if (area && periode) {
        $.ajax({
            url: '/setting/monthly-report/getOneData',
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                "area": area,
                "periode": periode
            },
            type: 'post',
            success: function(result) {
                let div = '';
                if (result != "") {
                  div += `<div class="col-12">
                            <div class="form-group row">
                              <label for="sales" class="text-md-center col-sm-2 col-form-label">Sales</label>
                              <div class="col-sm-10">
                                <input type="text" name="sales" id="sales" required autocomplete="off" value="${result.Sales}"
                                  class="form-control autonumeric @if($errors->has('sales')) is-invalid @endif">
                                @if($errors->has('sales'))
                                <span class="error invalid-feedback">{{ $errors->first('sales') }}</span>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="cogs" class="text-md-center col-sm-2 col-form-label">COGS</label>
                              <div class="col-sm-10">
                                <input type="text" name="cogs" id="cogs" required autocomplete="off" value="${result.Cogs}"
                                  class="form-control autonumeric @if($errors->has('cogs')) is-invalid @endif">
                                @if($errors->has('cogs'))
                                <span class="error invalid-feedback">{{ $errors->first('cogs') }}</span>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="gp_margin" class="text-md-center col-sm-2 col-form-label">GP Margin</label>
                              <div class="col-sm-10">
                                <input type="text" name="gp_margin" id="gp_margin" required autocomplete="off" value="${result.GPMargin}"
                                  class="form-control autonumeric @if($errors->has('gp_margin')) is-invalid @endif">
                                @if($errors->has('gp_margin'))
                                <span class="error invalid-feedback">{{ $errors->first('gp_margin') }}</span>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="gp_ratio" class="text-md-center col-sm-2 col-form-label">GP Ratio</label>
                              <div class="col-sm-10">
                                <div class="input-group">
                                  <input type="text" id="gp_ratio" name="gp_ratio" required autocomplete="off" value="${result.GPRatio}"
                                    class="form-control autonumeric @if($errors->has('gp_ratio')) is-invalid @endif">
                                  <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                  </div>
                                </div>
                                @if($errors->has('gp_ratio'))
                                <span class="error invalid-feedback">{{ $errors->first('gp_ratio') }}</span>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="ending_inventory" class="text-md-center col-sm-2 col-form-label">Ending
                                Inventory</label>
                              <div class="col-sm-10">
                                <input type="text" name="ending_inventory" id="ending_inventory" required autocomplete="off"
                                  value="${result.EndingInventory}"
                                  class="form-control autonumeric @if($errors->has('ending_inventory')) is-invalid @endif">
                                @if($errors->has('ending_inventory'))
                                <span class="error invalid-feedback">{{ $errors->first('ending_inventory') }}</span>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="inventory_ratio" class="text-md-center col-sm-2 col-form-label">Inventory
                                Ratio</label>
                              <div class="col-sm-10">
                                <div class="input-group">
                                  <input type="text" id="inventory_ratio" name="inventory_ratio" required autocomplete="off"
                                    value="${result.InventoryRatio}"
                                    class="form-control autonumeric @if($errors->has('inventory_ratio')) is-invalid @endif">
                                  <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                  </div>
                                </div>
                                @if($errors->has('inventory_ratio'))
                                <span class="error invalid-feedback">{{ $errors->first('inventory_ratio') }}</span>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <button type="submit" class="btn btn-warning mt-3 float-right">Ubah</button>
                          </div>`;
                } else {
                  div += `<div class="callout callout-warning mt-3">
                            <h5 class="m-0">Data tidak ditemukan</h5>
                          </div>`;
                }
                $('#result').html(div);
                // Set seperator '.' currency
                new AutoNumeric.multiple('.autonumeric', {
                    allowDecimalPadding: false,
                    decimalCharacter: ',',
                    digitGroupSeparator: '.',
                    unformatOnSubmit: true
                });
            }
        });
    }
  });

  $('#area-delete, #periode-delete').on('change', function() {
    let area = $('#area-delete').val();
    let periode = $('#periode-delete').val();
    if (area && periode) {
        $.ajax({
            url: '/setting/monthly-report/getOneData',
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                "area": area,
                "periode": periode
            },
            type: 'post',
            success: function(result) {
                let div = '';
                if (result != "") {
                  div += `<div class="col-12">
                            <div class="form-group row">
                              <label for="sales" class="text-md-center col-sm-2 col-form-label">Sales</label>
                              <div class="col-sm-10">
                                <input type="text" name="sales" id="sales" readonly value="${result.Sales}" class="form-control autonumeric">
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="cogs" class="text-md-center col-sm-2 col-form-label">COGS</label>
                              <div class="col-sm-10">
                                <input type="text" name="cogs" id="cogs" readonly value="${result.Cogs}" class="form-control autonumeric">
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="gp_margin" class="text-md-center col-sm-2 col-form-label">GP Margin</label>
                              <div class="col-sm-10">
                                <input type="text" name="gp_margin" id="gp_margin" readonly value="${result.GPMargin}" class="form-control autonumeric">
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="gp_ratio" class="text-md-center col-sm-2 col-form-label">GP Ratio</label>
                              <div class="col-sm-10">
                                <div class="input-group">
                                  <input type="text" id="gp_ratio" name="gp_ratio" readonly value="${result.GPRatio}" class="form-control autonumeric">
                                  <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="ending_inventory" class="text-md-center col-sm-2 col-form-label">Ending
                                Inventory</label>
                              <div class="col-sm-10">
                                <input type="text" name="ending_inventory" id="ending_inventory" readonly
                                  value="${result.EndingInventory}" class="form-control autonumeric">
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group row">
                              <label for="inventory_ratio" class="text-md-center col-sm-2 col-form-label">Inventory
                                Ratio</label>
                              <div class="col-sm-10">
                                <div class="input-group">
                                  <input type="text" id="inventory_ratio" name="inventory_ratio" readonly
                                    value="${result.InventoryRatio}" class="form-control autonumeric">
                                  <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <a class="btn btn-danger btn-delete mt-3 float-right" data-area="${area}" data-periode="${periode}">Hapus</a>
                          </div>`;
                } else {
                  div += `<div class="callout callout-warning mt-3">
                            <h5 class="m-0">Data tidak ditemukan</h5>
                          </div>`;
                }
                $('#result-delete').html(div);
                new AutoNumeric.multiple('.autonumeric', {
                    allowDecimalPadding: false,
                    decimalCharacter: ',',
                    digitGroupSeparator: '.',
                    unformatOnSubmit: true
                });
                // Event listener saat tombol selesaikan order diklik
                $('.btn-delete').on('click', function (e) {
                    e.preventDefault();
                    const area = $(this).data("area");
                    const periode = $(this).data("periode");
                    $.confirm({
                        title: 'Hapus data report',
                        content: `Apakah yakin ingin menghapus data report <b>${area}</b> pada <b>${periode}</b>?`,
                        closeIcon: true,
                        type: 'red',
                        typeAnimated: true,
                        buttons: {
                            ya: {
                                btnClass: 'btn-danger',
                                draggable: true,
                                dragWindowGap: 0,
                                action: function () {
                                    window.location = '/setting/monthly-report/delete/' + area + '/' + periode
                                }
                            },
                            tidak: function () {
                            }
                        }
                    });
                });
            }
        });
    }
  });
</script>
@endsection