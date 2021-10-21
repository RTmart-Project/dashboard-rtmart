@extends('layouts.master')
@section('title', 'Dashboard - Edit User')

@section('css-pages')
<link rel="stylesheet" href="{{ url('/') }}/plugins/bootstrap-select/bootstrap-select.min.css">
@endsection

@section('header-menu', 'Edit Pengguna')

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
                        <form id="edit-user" method="post" action="{{ route('setting.updateUser', ['user' => $userById->UserID]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control 
                                        @if($errors->has('email')) is-invalid @endif" id="email"
                                            placeholder="Masukkan email pengguna" value="{{ $userById->Email }}">
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
                                            placeholder="Masukkan nama pengguna" value="{{ $userById->Name }}">
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
                                            id="phonenumber" placeholder="Masukkan nomor telepon pengguna" value="{{ $userById->PhoneNumber }}">
                                        @if($errors->has('phonenumber'))
                                        <span class="error invalid-feedback">{{ $errors->first('phonenumber') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role-id">Role</label>
                                        <select class="form-control selectpicker border @if($errors->has('role_id')) is-invalid @endif"
                                            name="role_id" id="role-id" data-live-search="true" required>
                                        @foreach ($roleUser as $value)
                                            <option value="{{ $value->RoleID }}" {{ ($userById->RoleID) == ($value->RoleID) ? 'selected' : '' }}>{{ $value->RoleName }}</option>
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
                                            name="depo" id="depo" data-live-search="true" required>
                                            <option value="ALL" {{ ($userById->Depo == "ALL" ? 'selected' : '') }}>ALL</option>
                                            <option value="CRS" {{ ($userById->Depo == "CRS" ? 'selected' : '') }}>Ciracas</option>
                                            <option value="CKG" {{ ($userById->Depo == "CKG" ? 'selected' : '') }}>Cakung</option>
                                            <option value="BDG" {{ ($userById->Depo == "BDG" ? 'selected' : '') }}>Bandung</option>
                                        </select>
                                        @if($errors->has('depo'))
                                        <span class="error invalid-feedback">{{ $errors->first('depo') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group float-right">
                                <button type="submit" class="btn btn-warning">Simpan</button>
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