@extends('layouts.master')
@section('title', 'Dashboard - Summary')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('header-menu', 'Summary')

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
          {{-- @if (Auth::user()->RoleID == "IT" || (Auth::user()->RoleID == "FI"))
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
              </div>
            </form>
          </div>
          @endif --}}
          <div class="card-body mt-2">
            <div class="tab-content">

              <div class="tab-pane active" id="summary">
                <div class="row">
                  <div class="col-12">
                    <div class="card-body summary-table table-responsive p-0">
                      <table class="table table-hover table-bordered text-nowrap table-sm">
                        <thead class="bg-lightblue">
                          <tr class="text-center">
                            <th colspan="2">Area</th>
                            @foreach ($tanggal as $item)
                            <th>{{ date('j M \'y', strtotime($item->DateSummary)) }}</th>
                            @endforeach
                          </tr>
                        </thead>
                        <tbody>
                          <div>
                            @foreach ($data->groupBy('DistributorName') as $item)
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              <th class="text-center align-middle" rowspan="8">{{ $item[0]->DistributorName }}</th>
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Purchase Order</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->PurchaseOrder != "-" ? Helper::formatCurrency($value->PurchaseOrder, '') : $value->PurchaseOrder}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Purchasing</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->Purchasing != "-" ? Helper::formatCurrency($value->Purchasing, '') : $value->Purchasing}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Voucher</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->Voucher != "-" ? Helper::formatCurrency($value->Voucher, '') : $value->Voucher}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Delivery Order</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->DeliveryOrder != "-" ? Helper::formatCurrency($value->DeliveryOrder, '') : $value->DeliveryOrder }}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Bill Real</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->BillReal != "-" ? Helper::formatCurrency($value->BillReal, '') : $value->BillReal}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Bill Target</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->BillTarget != "-" ? Helper::formatCurrency($value->BillTarget, '') : $value->BillTarget }}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Ending Inventory</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->EndingInventory != "-" ? Helper::formatCurrency($value->EndingInventory, '') : $value->EndingInventory }}
                              </td>
                              @endforeach
                            </tr>
                            @endforeach
                          </div>
                          <div>
                            @foreach ($data2->groupBy('DistributorName') as $item)
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              <th class="text-center align-middle" rowspan="8">{{ $item[0]->DistributorName }}</th>
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Purchase Order</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->PurchaseOrder != "-" ? Helper::formatCurrency($value->PurchaseOrder, '') : $value->PurchaseOrder}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Purchasing</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->Purchasing != "-" ? Helper::formatCurrency($value->Purchasing, '') : $value->Purchasing}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Voucher</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->Voucher != "-" ? Helper::formatCurrency($value->Voucher, '') : $value->Voucher}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Delivery Order</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->DeliveryOrder != "-" ? Helper::formatCurrency($value->DeliveryOrder, '') : $value->DeliveryOrder }}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Bill Real</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->BillReal != "-" ? Helper::formatCurrency($value->BillReal, '') : $value->BillReal}}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Bill Target</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->BillTarget != "-" ? Helper::formatCurrency($value->BillTarget, '') : $value->BillTarget }}
                              </td>
                              @endforeach
                            </tr>
                            <tr style="background-color: {{ $item[0]->BGcolor }}">
                              @foreach ($item as $value)
                              @if ($loop->first)
                              <th>Ending Inventory</th>
                              @endif
                              <td class="text-right align-middle p-2">
                                {{ $value->EndingInventory != "-" ? Helper::formatCurrency($value->EndingInventory, '') : $value->EndingInventory }}
                              </td>
                              @endforeach
                            </tr>
                            @endforeach
                          </div>
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
<script src="{{url('/')}}/plugins/freeze-table/freeze-table.js"></script>
<script>
  $(".summary-table").freezeTable();
</script>
@endsection