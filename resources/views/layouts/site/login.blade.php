<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    @component('components.head',['title' => trans('auth.login')])
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
    <span class="pull-right login-text">@if(strtolower(config('app.env','local')) == 'local')
            SITE TEST
            -
        @endif{{ config('app.name') }}</span>
    <div class="clearfix"></div>
    <!-- /.login-logo -->
    <div class="login-box-body" style="position:relative">
        <a href="{{ route(\App\LftRouting\RoutingManager::home()) }}"><img src="{{ asset('/images/logo.jpg') }}"
                                                                           style="height: 50px;position:absolute;top:0;left:0;border-bottom-right-radius:50%"></a>
        @if(!$enabled_auth_types->count())
            <b><p class="login-box-msg" style="margin-top:50px">@lang('auth.no_auth_types') </p></b>

        @else
            @switch($requested_auth_type->id)
                @case(\App\Auth\AuthType::LOCAL)
                @case(\App\Auth\AuthType::LDAP)
                    <b><p class="login-box-msg" style="margin-top:50px">
                        @if($requested_auth_type->id == \App\Auth\AuthType::LOCAL)
                            @lang('auth.local_login_message')
                            <div class="text-center">
                                <i style="color: darkblue; font-size: 24px;"
                                   class="fa fa-database"></i>
                            </div>
                        @else
                            @lang('auth.ldap_login_message')
                            <div class="text-center">
                                <img src="{{asset('images/ldap_logo.svg')}}" style="width: 100%; height:30px">
                            </div>
                            @endif
                            </p></b>
                    <form action="{{ route(\App\LftRouting\RoutingManager::loginRoute()) }}" method="post"
                          autocomplete="off">
                        {{ csrf_field() }}
                        <input type="hidden" name="auth_type" value="{{request('auth_type')}}">
                        <div class="form-group has-feedback">
                            <input name="user" type="text" class="form-control" placeholder="mario.rossi">
                            <span class="form-control-feedback"><i class="fas fa-fw fa-user"></i></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" name="password" class="form-control" placeholder="password">
                            <span class="form-control-feedback"><i class="fas fa-fw fa-lock"></i></span>
                        </div>
                        <div class="row form-group">
                            <div class="col-xs-8">
                                <div class="checkbox icheck">
                                    <label>
                                        <input id="remember" name="remember" type="checkbox"> @lang('auth.remember_me')
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <button type="submit"
                                        class="btn btn-primary btn-block"><i class="fas fa-right-to-bracket"></i> @lang('auth.login')</button>
                            </div>
                            @if($driver->canResetPwd())
                                <!-- /.col -->
                                <div class="col-xs-12">
                                    <p>
                                        <small><a href="{{ route('site::password.request') }}">@lang('passwords.reset_password')</a></small>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </form>
                    @break
                @case(\App\Auth\AuthType::AZURE)
                    <b><p class="login-box-msg"
                          style="margin-top:50px">@lang('auth.azure_login_message') </p>
                    </b>
                    <div class="row">
                        <div class="col-xs-12">
                            <a href="{{route('admin::loginAzure', ['auth_type'=>\App\Auth\AuthType::AZURE])}}">
                                <button type="button"
                                        class="btn auth-type-button btn-bitbucket auth-type-button">
                                    <div class="col-xs-9">
                                        @lang('Accedi con Microsoft Azure')
                                    </div>
                                    <div class="col-xs-3">
                                        <img style="width:100%  "
                                             src="{{asset('/images/azure_logo.svg')}}">
                                    </div>
                                </button>
                            </a>
                        </div>
                    </div>
                    @break
                @default
                    <b><p class="login-box-msg" style="margin-top:50px">@lang('Scegliere la modalità di accesso.') </p>
                    </b>
            @endswitch
            @include('components.s-message')
            @if($enabled_auth_types->count() > 1)
                <hr>
            @endif
            <div style="">
                @foreach($enabled_auth_types->where('id','<>',$requested_auth_type->id) as $enabledAuthType)
                    @switch($enabledAuthType->id)
                        @case(\App\Auth\AuthType::LOCAL)
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="{{route(\App\LftRouting\RoutingManager::loginRoute(), ['auth_type'=>\App\Auth\AuthType::LOCAL])}}">
                                        <button type="button"
                                                class="btn btn-default btn auth-type-button">
                                            <div class="col-xs-9">
                                                @lang('Accedi Localmente')
                                            </div>
                                            <div class="col-xs-3">
                                                <i style="color: darkblue; font-size: 20px;"
                                                   class="fa fa-database"></i>
                                            </div>
                                        </button>
                                    </a>
                                </div>
                            </div>
                            @break
                        @case(\App\Auth\AuthType::LDAP)
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="{{route(\App\LftRouting\RoutingManager::loginRoute(), ['auth_type'=>\App\Auth\AuthType::LDAP])}}">
                                        <button type="button"
                                                class="btn bg-orange-active auth-type-button">
                                            <div class="col-xs-9">
                                                @lang('Accedi con LDAP')
                                            </div>
                                            <div class="col-xs-3">
                                                <img src="{{asset('images/ldap_logo.svg')}}"
                                                     style="width: 100%; height:20px">
                                            </div>
                                        </button>
                                    </a>
                                </div>
                            </div>
                            @break
                        @case(\App\Auth\AuthType::AZURE)
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="{{route('site::loginAzure', ['auth_type'=>\App\Auth\AuthType::AZURE])}}">
                                        <button type="button"
                                                class="btn auth-type-button btn-bitbucket auth-type-button">
                                            <div class="col-xs-9">
                                                @lang('Accedi con Microsoft Azure')
                                            </div>
                                            <div class="col-xs-3">
                                                <img style="width:100%  "
                                                     src="{{asset('/images/azure_logo.svg')}}">
                                            </div>
                                        </button>
                                    </a>
                                </div>
                            </div>
                    @endswitch
                @endforeach
            </div>
            <!-- /.login-box-body -->
        @endif
    </div>
    <!-- /.login-box -->

</div>
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
