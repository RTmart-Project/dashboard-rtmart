@extends('layouts.master')
@section('title', 'Dashboard - New Users')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Tambah Pengguna Baru')

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
                        <a href="{{ route('setting.users') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="add-user" method="post" action="{{ route('setting.createNewUser') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control 
                                        @if($errors->has('email')) is-invalid @endif" id="email"
                                            placeholder="Masukkan Email Pengguna" value="{{ old('email') }}" required>
                                        @if($errors->has('email'))
                                        <span class="error invalid-feedback">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" name="name"
                                            class="form-control @if($errors->has('name')) is-invalid @endif" id="name"
                                            placeholder="Masukkan Nama Pengguna" value="{{ old('name') }}" required>
                                        @if($errors->has('name'))
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phonenumber">Nomor Telepon</label>
                                        <input type="number" name="phonenumber"
                                            class="form-control @if($errors->has('phonenumber')) is-invalid @endif"
                                            id="phonenumber" placeholder="Masukkan Nomor Telepon Pengguna" value="{{ old('phonenumber') }}">
                                        @if($errors->has('phonenumber'))
                                        <span class="error invalid-feedback">{{ $errors->first('phonenumber') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role-id">Role</label>
                                        <select class="form-control selectpicker border @if($errors->has('role_id')) is-invalid @endif"
                                            name="role_id" id="role-id" data-live-search="true" title="Pilih Role" required>
                                            @foreach ($roleUser as $value)
                                                @if (old('role_id') == $value->RoleID)
                                                    <option value="{{ $value->RoleID }}" selected>{{ $value->RoleName }}</option>
                                                @else
                                                    <option value="{{ $value->RoleID }}">{{ $value->RoleName }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if($errors->has('role_id'))
                                        <span class="error invalid-feedback">{{ $errors->first('role_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="depo">Depo</label>
                                        <select class="form-control selectpicker border @if($errors->has('depo')) is-invalid @endif"
                                            name="depo" id="depo" data-live-search="true" title="Pilih Depo" required>
                                            <option value="ALL" {{ (old('depo') == "ALL") ? 'selected' : '' }}>ALL</option>
                                            @foreach ($depo as $item)
                                            <option value="{{ $item->Depo }}" {{ (old('depo') == $item->Depo) ? 'selected' : '' }}>
                                                {{ $item->Depo }} - {{ $item->DistributorName }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('depo'))
                                        <span class="error invalid-feedback">{{ $errors->first('depo') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label>Akses</label>
                                    <div class="form-group">                                        
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="access[]" id="rtmart" value="IsDashboardRTMart">
                                            <label class="form-check-label" for="rtmart">Dashboard RTMart</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="access[]" id="rtrabat" value="IsDashboardRTRabat">
                                            <label class="form-check-label" for="rtrabat">Dashboard RTRabat</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="access[]" id="rtsales" value="IsDashboardRTSales">
                                            <label class="form-check-label" for="rtsales">Dashboard RTSales</label>
                                        </div>
                                        @if($errors->has('access'))
                                            <span class="error invalid-feedback" style="display: block;">{{ $errors->first('access') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password"
                                            class="form-control @if($errors->has('password')) is-invalid @endif"
                                            id="password" placeholder="Masukkan Password Pengguna" required>
                                        @if($errors->has('password'))
                                        <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-success">Tambah</button>
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