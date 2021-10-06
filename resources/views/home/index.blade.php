@extends('layouts.master')

@section('title', 'Dashboard - Home')

@section('css-pages')

@endsection

@section('header-menu', 'Home')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
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
        <div class="row">
            <div class="col-12">
                <h3>Selamat Datang, {{$userName->Name}}</h3>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-pages')
@endsection