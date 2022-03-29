@extends('layouts.master')
@section('title', 'Dashboard - Monthly Report')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
        <div class="card">
          @if (Auth::user()->RoleID == "IT" || (Auth::user()->RoleID == "FI"))
          <div class="card-header">
            <form action="{{ route('monthlyReport.post') }}" method="POST">
              <div class="row">
                @csrf
                <div class="col-12 col-md-3">
                  <input type="month" class="form-control form-control-sm" name="start_date"
                    value="{{ request('start_date') }}">
                </div>
                <div class="col-2 col-md-1 d-none d-md-flex align-items-center justify-content-center">
                  <span>sampai</span>
                </div>
                <div class="col-12 col-md-3 py-2 py-md-0">
                  <input type="month" class="form-control form-control-sm" name="end_date"
                    value="{{ request('end_date') }}">
                </div>
                <div class="col-3 col-md-1 text-md-center pt-md-0 w-100">
                  <button type="submit" class="btn btn-primary btn-block btn-sm">Filter</button>
                </div>
                <div class="col-3 col-md-1 text-md-center pt-md-0">
                  <a href="{{ route('monthlyReport') }}" class="btn btn-warning btn-sm">Refresh</a>
                </div>
                <div class="col-4 mt-md-2">
                  <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm">Action</button>
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-icon"
                      data-toggle="dropdown" aria-expanded="false">
                    </button>
                    <div class="dropdown-menu" role="menu" style="">
                      <a class="dropdown-item" href="{{ route('monthlyReport.create') }}">Tambah</a>
                      <a class="dropdown-item" href="{{ route('monthlyReport.edit') }}">Ubah</a>
                      {{-- <a class="dropdown-item" href="{{ route('monthlyReport.edit') }}">Hapus</a> --}}
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          @endif
          <div class="card-body mt-2">
            <div class="tab-content">

              <div class="tab-pane active" id="monthly-report">
                <div class="row">
                  <div class="col-12">
                    <div class="card-body monthly-report-table table-responsive p-0">
                      <table class="table table-hover table-bordered text-nowrap table-sm">
                        <thead class="bg-lightblue">
                          <tr class="text-center">
                            <th colspan="2">Area</th>
                            @foreach ($data->groupBy('Periode') as $item)
                            <th>{{ $item[0]->Periode }}</th>
                            @endforeach
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($data->groupBy('AreaName') as $item)
                          <tr>
                            <th class="text-center align-middle" rowspan="7">{{ $item[0]->AreaName }}</th>
                          </tr>
                          <tr>
                            @foreach ($item as $value)
                            @if ($loop->first)
                            <th>Sales</th>
                            @endif
                            <td class="text-right align-middle p-2">
                              {{ $value->Sales != "-" ? Helper::formatCurrency($value->Sales, '') : $value->Sales}}
                            </td>
                            @endforeach
                          </tr>
                          <tr>
                            @foreach ($item as $value)
                            @if ($loop->first)
                            <th>COGS</th>
                            @endif
                            <td class="text-right align-middle p-2">
                              {{ $value->Cogs != "-" ? Helper::formatCurrency($value->Cogs, '') : $value->Cogs}}
                            </td>
                            @endforeach
                          </tr>
                          <tr>
                            @foreach ($item as $value)
                            @if ($loop->first)
                            <th>GP Margin</th>
                            @endif
                            <td class="text-right align-middle p-2">
                              {{ $value->GPMargin != "-" ? Helper::formatCurrency($value->GPMargin, '') :
                              $value->GPMargin}}
                            </td>
                            @endforeach
                          </tr>
                          <tr>
                            @foreach ($item as $value)
                            @if ($loop->first)
                            <th>GP Ratio</th>
                            @endif
                            <td class="text-right align-middle p-2">
                              {{ $value->GPRatio != "-" ? number_format($value->GPRatio, "2", ",", ".").'%' :
                              $value->GPRatio }}
                            </td>
                            @endforeach
                          </tr>
                          <tr>
                            @foreach ($item as $value)
                            @if ($loop->first)
                            <th>Ending Inventory</th>
                            @endif
                            <td class="text-right align-middle p-2">
                              {{ $value->EndingInventory != "-" ? Helper::formatCurrency($value->EndingInventory, '') :
                              $value->EndingInventory}}
                            </td>
                            @endforeach
                          </tr>
                          <tr>
                            @foreach ($item as $value)
                            @if ($loop->first)
                            <th>Inventory Ratio</th>
                            @endif
                            <td class="text-right align-middle p-2">
                              {{ $value->InventoryRatio != "-" ? number_format($value->InventoryRatio, "2", ",",
                              ".").'%' : $value->InventoryRatio }}
                            </td>
                            @endforeach
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
<script src="{{url('/')}}/plugins/freeze-table/freeze-table.js"></script>
<!-- Main JS -->
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script>
<script>
  $(".monthly-report-table").freezeTable();
</script>
@endsection