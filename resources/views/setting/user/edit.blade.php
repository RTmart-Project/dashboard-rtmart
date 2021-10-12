@extends('layouts.master')
@section('title', 'Dashboard - Edit Users')

@section('css-pages')
<!-- daterange picker -->
<link rel="stylesheet" href="{{url('/')}}/plugins/daterangepicker/daterangepicker.css">
<!-- Datatables -->
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{url('/')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                        <a href="/setting/users" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body">
                        <form id="edit-user" method="post" action="/setting/users/update/{{ $userById->UserID }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control 
                                        @if($errors->has('email')) is-invalid @endif" id="email"
                                            placeholder="Masukan email pengguna" value="{{ $userById->Email }}">
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
                                            placeholder="Masukan nama pengguna" value="{{ $userById->Name }}">
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
                                            id="phonenumber" placeholder="Masukan nomor telepon pengguna" value="{{ $userById->PhoneNumber }}">
                                        @if($errors->has('phonenumber'))
                                        <span class="error invalid-feedback">{{ $errors->first('phonenumber') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role-id">Role</label>
                                        <select class="form-control @if($errors->has('role_id')) is-invalid @endif"
                                            name="role_id" id="role-id">
                                            <option value="IT" {{ ($userById->RoleID) == "IT" ? 'selected' : '' }} >IT</option>
                                            <option value="FI" {{ ($userById->RoleID) == "FI" ? 'selected' : '' }}>Finance</option>
                                            <option value="BM" {{ ($userById->RoleID) == "BM" ? 'selected' : '' }}>Business</option>
                                            <option value="HR" {{ ($userById->RoleID) == "HR" ? 'selected' : '' }}>Human Resource</option>
                                            <option value="AH" {{ ($userById->RoleID) == "AH" ? 'selected' : '' }}>Admin HO</option>
                                            <option value="AD" {{ ($userById->RoleID) == "AD" ? 'selected' : '' }}>Admin Depo</option>
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
                                        <select class="form-control @if($errors->has('depo')) is-invalid @endif"
                                            name="depo" id="depo">
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
<!-- Main JS -->
<!-- <script src="{{url('/')}}/main/js/setting/users/users.js"></script>
<script src="{{url('/')}}/main/js/helper/export-datatable.js"></script> -->
<script>
</script>
@endsection