<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ @asset('/images/logo_WTB_orange160x160.png') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><a style="color: #ffffff"
                      href="{{route('admin::users.edit', Auth::user()->id)}}">{{ Auth::user()->fullname }}</a></p>
                <!-- Status -->
                {{--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>--}}
            </div>
        </div>

        <!-- search form (Optional) -->
        <form action="{{--{{ route('admin::global_search') }}--}}" id="global_search_form" method="get"
              class="sidebar-form hide">
            <div class="input-group">
                <input id="global_search" type="text" name="search" value="{{ request('search') }}" class="form-control"
                       placeholder="@lang('common.search.placeholder')">
                <span class="input-group-btn">
              <button type="submit" id="search-btn" class="btn"><i class="fa fa-search"></i>
              </button>
            </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header hide">HEADER</li>
            <!-- Optionally, you can add icons to the links -->
            @switch(strtolower(config('app.env','local')))
                @case('local')
                    <li class="bg-primary">
                        <a href="{{ route('admin::dashboard') }}">
                            <i class="fas  fa-exclamation-triangle"></i> <span
                                    class="text-bold">Ambiente di Sviluppo</span>
                        </a>
                    @break
                @case('test')
                @case('staging')
                    <li class="bg-primary">
                        <a href="{{ route('admin::dashboard') }}">

                            <i class="fas  fa-exclamation-triangle"></i> <span
                                    class="text-bold">Ambiente di Test</span>
                        </a>
                    </li>
            @endswitch
            @include('dbt.menu.main')
        @if (Auth::user()->can('list', 'App\Auth\User') || Auth::user()->can('list', 'App\Auth\Role') || Auth::user()->can('managePermissions','App\Auth\Role') || Auth::user()->can('list','App\Auth\AuthType'))
                <li class="treeview {{ isActiveRoute('admin::users*','admin::roles*','admin::permissions*','admin::user_sessions*','admin::pending_pwd_resets*', 'admin::auth_types*','admin::external_roles*') ? 'active':'' }}">
                    <a href="#"><i class="fa fa-key"></i> <span>@lang('common.account_security')</span>
                        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('list','App\Auth\User')
                            <li class="{{ isActiveRoute('admin::users*') ? 'active':'' }}"><a
                                        href="{{ route('admin::users.index') }}"><span>@lang('users.menu_title')</span></a>
                            </li>
                        @endcan
                        @can('list','App\Auth\Role')
                            <li class="{{ isActiveRoute('admin::roles*') ? 'active':'' }}"><a
                                        href="{{ route('admin::roles.index') }}"><span>@lang('roles.menu_title')</span></a>
                            </li>
                        @endcan
                        @can('list','App\Auth\ExternalRole')
                            <li class="{{ isActiveRoute('admin::external_roles*') ? 'active':'' }}"><a
                                        href="{{ route('admin::external_roles.index') }}"><span>@lang('external_roles.menu_title')</span></a>
                            </li>
                        @endcan
                        @can('managePermissions','App\Auth\Role')
                            <li class="{{ isActiveRoute('admin::permissions*') ? 'active':'' }}"><a
                                        href="{{ route('admin::permissions.index') }}"><span>@lang('permissions.menu_title')</span></a>
                            </li>
                        @endcan
                        @can('list','App\Auth\AuthType')
                            <li class="{{ isActiveRoute('admin::auth_types*') ? 'active':'' }}"><a
                                        href="{{ route('admin::auth_types.index') }}"><span>@lang('auth_types.menu_title')</span></a>
                            </li>
                        @endcan
                        @can('list','App\Auth\PasswordRecovery')
                            <li class="{{ isActiveRoute('admin::pending_pwd_resets*') ? 'active':'' }}"><a
                                        href="{{ route('admin::pending_pwd_resets.index') }}"><span>@lang('pending_pwd_resets.menu_title')</span></a>
                            </li>
                        @endcan
                        @can('list','App\Auth\UserSession')
                            @if(config('session.driver') == 'custom-database')
                                <li class="{{ isActiveRoute('admin::user_sessions.index*') ? 'active':'' }}"><a
                                            href="{{ route('admin::user_sessions.index') }}"><span>@lang('user_sessions.menu_title')</span></a>
                                </li>
                            @endif
                        @endcan
                    </ul>
                </li>
            @endif
            @if(class_exists('Medialogic\Ticketing\TicketingServiceProvider'))
                @include('ticketing::menu.admin')
            @endif
            @can('view','App\AppConfiguration')

                <li class="{{ isActiveRoute('admin::app_configuration.show*') ? 'active':'' }}"><a
                            href="{{ route('admin::app_configuration.show') }}"><i class="fa fa-gears"></i>
                        <span>@lang('app_configuration.menu_title')</span></a></li>

            @endcan
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>