<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @component('components.head',['title' => $page_title])
    @endcomponent
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ @asset('/css/app.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ @asset('/vendor/adminlte/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="{{ @asset('/vendor/adminlte/css/skins/skin-yellow.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ @asset('/vendor/adminlte/plugins/iCheck/square/orange.css') }}">
    <!-- Bootstrap Toggle -->
    <link rel="stylesheet" href="{{ @asset('/vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ @asset('/css/custom.css') }}">
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-yellow sidebar-mini @if(session('collapseSidebar')) sidebar-collapse @endif">
<div class="wrapper">
@include('components.toastr')
    <!-- Main Header -->
@include('layouts.adminlte.header')

<!-- Left side column. contains the logo and sidebar -->
@include('layouts.adminlte.main-sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @if(!empty($fa_icon_class))<i class="fa {{ $fa_icon_class }}"></i>
                @else <i class="fa fa-chevron-right"></i>
                @endif
                {{ $page_title ?? 'Page title'}}

                <small>{{ $page_description ?? '' }}</small>
            </h1>
            @yield('page_nav_default')
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

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

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
@include('layouts.adminlte.footer')

<!-- Control Sidebar -->
{{--@include('layouts.adminlte.control-sidebar')--}}
<!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
    immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<!-- Bootstrap 3.3.7 -->
<script src="{{ @asset('/js/app.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ @asset('/vendor/adminlte/js/adminlte.min.js') }}"></script>
<!-- Bootstrap Toggle -->
<script src="{{ @asset('/vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') }}"></script>
<script>
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
    toastr.options.positionClass = "toast-top-right";
</script>
<script>
    $('#sidebarToggle').on('click', function (e) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '{{ route('admin::toggleSidebar') }}',
            success: function( data ) {
                //console.log(data)
            }
        });
    });
</script>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
@stack('scripts')
</body>
</html>