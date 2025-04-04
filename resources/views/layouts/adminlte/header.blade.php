<header class="main-header">

    <!-- Logo -->
    <a href="{{ route('admin::dashboard') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{{ config('app.shortcode') }}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>{{ config('app.name') }}</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu hide">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">4</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 4 messages</li>
                        <li>
                            <!-- inner menu: contains the messages -->
                            <ul class="menu">
                                <li><!-- start message -->
                                    <a href="#">
                                        <div class="pull-left">
                                            <!-- User Image -->
                                            <img src="{{ @asset('/images/logo_WTB_orange160x160.png') }}" class="img-circle" alt="User Image">
                                        </div>
                                        <!-- Message title and timestamp -->
                                        <h4>
                                            Support Team
                                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                        </h4>
                                        <!-- The message -->
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <!-- end message -->
                            </ul>
                            <!-- /.menu -->
                        </li>
                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>
                <!-- /.messages-menu -->

                <!-- Notifications Menu -->
                <li class="dropdown notifications-menu hide">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">10</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 10 notifications</li>
                        <li>
                            <!-- Inner Menu: contains the notifications -->
                            <ul class="menu">
                                <li><!-- start notification -->
                                    <a href="#">
                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                    </a>
                                </li>
                                <!-- end notification -->
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                </li>
                <!-- Tasks Menu -->
                <li class="dropdown tasks-menu">
                    <!-- Menu Toggle Button -->
                    {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="fa fa-flag-o"></i>--}}
                        {{--<span class="label label-danger">9</span>--}}
                    {{--</a>--}}
                    <a href="{{ route('admin::viewManual') }}" class="dropdown-toggle">
                        <i class="far fa-question-circle"></i>
                    </a>
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li class="header">You have 9 tasks</li>--}}
                        {{--<li>--}}
                            {{--<!-- Inner menu: contains the tasks -->--}}
                            {{--<ul class="menu">--}}
                                {{--<li><!-- Task item -->--}}
                                    {{--<a href="#">--}}
                                        {{--<!-- Task title and progress text -->--}}
                                        {{--<h3>--}}
                                            {{--Design some buttons--}}
                                            {{--<small class="pull-right">20%</small>--}}
                                        {{--</h3>--}}
                                        {{--<!-- The progress bar -->--}}
                                        {{--<div class="progress xs">--}}
                                            {{--<!-- Change the css width attribute to simulate progress -->--}}
                                            {{--<div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"--}}
                                                 {{--aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">--}}
                                                {{--<span class="sr-only">20% Complete</span>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</a>--}}
                                {{--</li>--}}
                                {{--<!-- end task item -->--}}
                            {{--</ul>--}}
                        {{--</li>--}}
                        {{--<li class="footer">--}}
                            {{--<a href="#">View all tasks</a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                </li>
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ @asset('/images/logo_WTB_orange160x160.png') }}" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ Auth::User()->fullname }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ @asset('/images/logo_WTB_orange160x160.png') }}" class="img-circle" alt="User Image">

                            <p>
                                {{ Auth::User()->name }}
                                <small>{{ Auth::User()->email }}</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body hide">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Sales</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('admin::users.edit',Auth::user()->id) }}" class="btn btn-default"><i class="fas fa-fw fa-user"></i> @lang('users.profile')</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('admin::logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                   class="btn btn-default"><i class="fas fa-fw fa-right-from-bracket"></i> @lang('common.logout')</a>
                                <form id="logout-form" action="{{ route('admin::logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li class="hide">
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>