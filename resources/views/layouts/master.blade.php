<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{url('/')}}/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('/')}}/dist/css/adminlte.min.css">
    <!-- Jquery Confirm -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <!-- IziToast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- CSS Per-Pages -->
    @yield('css-pages')

    <style>
    .toolbar {
        float: left;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini sidebar-closed sidebar-collapse text-sm">
    <div class="wrapper" @if (session('success')) data-notif-success="{{session('success')}}" @else
        data-notif-success="" @endif @if (session('failed')) data-notif-failed="{{session('failed')}}" @else
        data-notif-failed="" @endif>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
            @include('layouts.header')
        </nav>
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-danger elevation-4">
            @include('layouts.sidebar')
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            @include('layouts.footer')
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{url('/')}}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{url('/')}}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{url('/')}}/dist/js/adminlte.min.js"></script>
    <!-- JQuery Confirm -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <!-- IziToast -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Thousand Separator -->
    <script src="{{url('/')}}/main/js/helper/thousands-separators.js"></script>
    <script>
    $(document).ready(function() {
        const messageNotifSuccess = $('body .wrapper').data("notif-success");
        const messageNotifFailed = $('body .wrapper').data("notif-failed");
        if (messageNotifSuccess != "") {
            iziToast.success({
                title: 'Berhasil',
                message: messageNotifSuccess,
                position: 'topRight',
            });
        }

        if (messageNotifFailed != "") {
            iziToast.error({
                title: 'Gagal',
                message: messageNotifFailed,
                position: 'topRight',
            });
        }
    });
    </script>
    <!-- JS Per-Pages -->
    @yield('js-pages')
</body>

</html>