<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


@component('components.head',['title' => trans('passwords.reset_page_title')])
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
        <a href="{{ route('admin::dashboard') }}"><img src="{{ asset('/images/logo_WTB_orange.png') }}" style="height: 50px;position:absolute;top:-57px;left:0"></a>
        <p class="login-box-msg" style="margin-top:50px">@lang('passwords.reset_msg')</p>
        <form action="{{ route('admin::password.request') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token ?? old('token') }}">
            <div class="form-group has-feedback">
                <input name="user" type="text" class="form-control" value="{{ $email ?? old('user') }}" placeholder="mario.rossi">
                <span class="form-control-feedback"><i class="fas fa-fw fa-user"></i></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="@lang('passwords.new_password')">
                <span class="form-control-feedback"><i class="fas fa-fw fa-lock"></i></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('passwords.confirm_new_password')">
                <span class="form-control-feedback"><i class="fas fa-fw fa-lock"></i></span>
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
        <span class="pull-right login-text">
            @switch(strtolower(config('app.env','local')))
                @case('local')
                    Ambiente di Sviluppo
                    @break
                @case('test')
                @case('staging')
                    Ambiente di Test
            @endswitch {{ config('app.name') }}
        </span>
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
