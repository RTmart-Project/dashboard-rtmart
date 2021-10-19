@extends('layouts.master')
@section('title', 'Dashboard - New Role')

@section('css-pages')

@endsection

@section('header-menu', 'Tambah Role Baru')

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
                        <a href="/setting/role" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="add-role" method="post" action="/setting/role/create">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="role_id">Role ID</label>
                                        <input type="text" name="role_id" class="form-control 
                                        @if($errors->has('role_id')) is-invalid @endif" id="role_id"
                                            placeholder="Masukkan Role ID" value="{{ old('role_id') }}" maxlength="2"
                                            oninput="this.value = this.value.toUpperCase()" required>
                                        @if($errors->has('role_id'))
                                        <span class="error invalid-feedback">{{ $errors->first('role_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="role_name">Nama Role</label>
                                        <input type="text" name="role_name"
                                            class="form-control @if($errors->has('role_name')) is-invalid @endif" id="role_name"
                                            placeholder="Masukkan Nama Role" value="{{ old('role_name') }}" required>
                                        @if($errors->has('role_name'))
                                        <span class="error invalid-feedback">{{ $errors->first('role_name') }}</span>
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

@endsection