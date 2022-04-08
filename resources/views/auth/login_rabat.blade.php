<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RTRabat Dashboard - Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('/')}}/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{url('/')}}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('/')}}/dist/css/adminlte.min.css">
    <!-- BG Login -->
    <link rel="stylesheet" href="{{url('/')}}/main/css/custom/login.css">
    <!-- IziToast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="wrapper hold-transition login-page bg_login_rabat" @if (session('success'))
        data-notif-success="{{session('success')}}" @else data-notif-success="" @endif @if (session('failed'))
        data-notif-failed="{{session('failed')}}" @else data-notif-failed="" @endif>
        <div class="login-box">
            <!-- /.login-logo -->
            <div class="card card-outline card-danger">
                <div class="card-header text-center">
                    <a href="{{route('auth.login.rabat')}}" class="h1"><img src="{{ url('/') }}/dist/img/rtrabat.png"
                            alt="RTmart" height="50"></a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Masuk ke dashboard RTRabat</p>

                    <form action="{{route('auth.validateLoginRabat')}}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">

                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.login-box -->
    </div>

    <!-- jQuery -->
    <script src="{{url('/')}}/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{url('/')}}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{url('/')}}/dist/js/adminlte.min.js"></script>
    <!-- IziToast -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
</body>

</html>