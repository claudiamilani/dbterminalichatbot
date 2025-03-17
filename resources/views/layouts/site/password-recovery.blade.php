<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
@component('components.head',['title' => trans('passwords.recover_page_title')])
@endcomponent

<!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ @asset('/css/app.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ @asset('/vendor/adminlte/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ @asset('/vendor/adminlte/plugins/iCheck/square/orange.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ @asset('/css/custom.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo pull-left">
    </div>
    <div class="clearfix"></div>
    <!-- /.login-logo -->
    <div class="login-box-body" style="position:relative">
        <a href="{{ route('site::index') }}"><img src="{{ asset('/images/logo.jpg') }}" style="height: 50px;position:absolute;top:0;left:0;border-bottom-right-radius:50%"></a>
        <p class="login-box-msg" style="margin-top:50px">@lang('passwords.reset_password_msg')</p>
        <form action="{{ route('site::password.email') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="form-group has-feedback">
                <input name="user" type="text" class="form-control" placeholder="mario.rossi" @if(session('user')) value="{{session('user')}}" @endif>
                <span class="form-control-feedback"><i class="fas fa-fw fa-user"></i></span>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-xs-4 form-group col-xs-offset-8">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-fw fa-envelope"></i> @lang('common.form.send')</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        @include('components.s-message')

        <span class="pull-right login-text">@if(strtolower(config('app.env','local')) == 'local')Ambiente di PreProduzione - @endif{{ config('app.name') }}</span>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<!-- Bootstrap 3.3.7 -->
<script src="{{ @asset('/js/app.js') }}"></script>

<!-- iCheck -->
<script src="{{ @asset('/vendor/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-orange',
            radioClass: 'iradio_square-orange',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
