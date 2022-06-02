@extends('layouts.master')
@section('title', 'Dashboard - Edit Sales')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Ubah Sales')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
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
            <a href="{{ route('rtsales.saleslist') }}" class="btn btn-sm btn-light">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
          <div class="card-body">
            <form id="edit-sales" method="post" action="{{ route('rtsales.updateSales', ['salesCode' => $salesCode]) }}">
              @csrf
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label for="sales_code">Sales Code</label>
                    <input type="text" class="form-control" value="{{ $sales->SalesCode }}" readonly>
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="sales_name">Nama Sales</label>
                    <input type="text" name="sales_name" class="form-control 
                    @if($errors->has('sales_name')) is-invalid @endif" id="sales_name"
                        placeholder="Masukkan Nama Sales" value="{{ $sales->SalesName }}" required>
                    @if($errors->has('sales_name'))
                    <span class="error invalid-feedback">{{ $errors->first('sales_name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="sales_level">Sales Level</label>
                    <input type="number" name="sales_level" class="form-control 
                    @if($errors->has('sales_level')) is-invalid @endif" id="sales_level"
                        placeholder="Masukkan Level Sales" value="{{ $sales->SalesLevel }}">
                    @if($errors->has('sales_level'))
                    <span class="error invalid-feedback">{{ $errors->first('sales_level') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="team_by">Team By</label>
                    <input type="text" name="team_by" class="form-control 
                    @if($errors->has('team_by')) is-invalid @endif" id="team_by"
                        placeholder="Masukkan Level Sales" value="{{ $sales->TeamBy }}">
                    @if($errors->has('team_by'))
                    <span class="error invalid-feedback">{{ $errors->first('team_by') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="team">Team</label>
                    <select class="form-control selectpicker border
                      @if($errors->has('team')) is-invalid @endif" 
                      id="team" name="team" title="Pilih Team">
                      @foreach ($teams as $value)
                        <option value="{{ $value->TeamCode }}"
                          {{ ($sales->Team) == ($value->TeamCode) ? 'selected' : '' }}>
                          {{ $value->TeamCode }} ({{ $value->TeamName }})
                        </option>
                      @endforeach
                    </select>
                    @if($errors->has('team'))
                      <span class="error invalid-feedback">{{ $errors->first('team') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email"
                        class="form-control @if($errors->has('email')) is-invalid @endif" id="email"
                        placeholder="Masukkan Email" value="{{ $sales->Email }}">
                    @if($errors->has('email'))
                    <span class="error invalid-feedback">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="phone_number">Nomor Telepon</label>
                    <input type="number" name="phone_number" class="form-control 
                    @if($errors->has('phone_number')) is-invalid @endif" id="phone_number"
                        placeholder="Masukkan Nomor Telepon" value="{{ $sales->PhoneNumber }}">
                    @if($errors->has('phone_number'))
                    <span class="error invalid-feedback">{{ $errors->first('phone_number') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="work_status">Work Status</label>
                    <select class="form-control selectpicker border
                      @if($errors->has('work_status')) is-invalid @endif"
                      name="work_status" id="work_status" title="Pilih Work Status" required>
                      @foreach ($workStatus as $value)
                        <option value="{{ $value->SalesWorkStatusID }}"
                          {{ ($sales->SalesWorkStatus) == ($value->SalesWorkStatusID) ? 'selected' : '' }}>
                          {{ $value->SalesWorkStatusName }}
                        </option>
                      @endforeach
                    </select>
                    @if($errors->has('work_status'))
                    <span class="error invalid-feedback">{{ $errors->first('work_status') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="product_group">Product Group</label>
                    <select class="form-control selectpicker border
                      @if($errors->has('product_group')) is-invalid @endif" 
                      id="product_group" name="product_group[]" multiple title="Pilih Product Group">
                      @foreach ($productGroup as $value)
                        <option value="{{ $value->ProductGroupID }}"
                            @foreach ($salesProductGroup as $item)
                                {{ collect($item->ProductGroupID)->contains($value->ProductGroupID) ? 'selected' : '' }}
                            @endforeach>
                            {{ $value->ProductGroupName }}
                        </option>
                      @endforeach
                    </select>
                    @if($errors->has('product_group'))
                        <span class="error invalid-feedback">{{ $errors->first('product_group') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="text" name="password"
                        class="form-control @if($errors->has('password')) is-invalid @endif" id="password"
                        placeholder="Masukkan Password" value="{{ $sales->Password }}">
                    @if($errors->has('password'))
                    <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 col-12">
                  <div class="form-group">
                    <label for="is_active">Is Active</label>
                    <select name="is_active" id="is_active" class="form-control selectpicker border
                      @if($errors->has('product_group')) is-invalid @endif">
                      <option value="1" {{ $sales->IsActive == 1 ? 'selected' : ''}}>Ya</option>
                      <option value="0" {{ $sales->IsActive == 0 ? 'selected' : ''}}>Tidak</option>
                    </select>
                    @if($errors->has('is_active'))
                    <span class="error invalid-feedback">{{ $errors->first('is_active') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="form-group float-right">
                <button type="submit" class="btn btn-warning">Ubah</button>
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
<script src="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.js"></script>
@endsection