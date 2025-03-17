<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
@component('components.head',['title' => $page_title])
@endcomponent
<!-- Place here favicon -->
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- APP CSS -->
    <link rel="stylesheet" href="{{ @asset('/css/app.css') }}">
    <!-- Bootstrap Toggle -->
    <link rel="stylesheet" href="{{ @asset('/vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">

    <link rel="stylesheet" href="{{ @asset('/vendor/adminlte/plugins/iCheck/square/orange.css') }}">

    <!-- Stack CSS  -->
@stack('styles')
<!-- Custom -->
    <link rel="stylesheet" href="{{ @asset('/css/custom.css') }}">
</head>
<body>
@include('layouts.site.header')
<div id="wrap">
    <div id="main" class="container-fluid col-md-10 col-md-offset-1">
    @yield('content')
    <!-- Modal -->
        <div id="myModalLg" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content"></div>
            </div>
        </div>
        <div id="myModalSm" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content"></div>
            </div>
        </div>
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content"></div>
            </div>
        </div>
    </div>
</div>
@include('layouts.site.footer')


<!-- jQuery 3 -->
<!-- Bootstrap 3.3.7 -->
<script src="{{ @asset('/js/app.js') }}"></script>

@stack('scripts')
</body>
</html>
