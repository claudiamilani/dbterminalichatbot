<nav class="navbar" style="margin-bottom:50px">
    <div class="container-fluid col-md-10 col-md-offset-1" style="position:relative">
        <a class="navbar-brand" style="margin:0 auto" href="{{ route('site::index') }}"><img
                    src="{{ @asset('/images/logo.png') }}"></a>
        <!-- Brand and toggle get grouped for better mobile display -->


        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav hide">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
            @auth
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">{{ Auth::user()->fullName }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('site::logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">Esci</a>
                                <form id="logout-form" action="{{ route('site::logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @else
                <ul class="nav navbar-nav navbar-right">
                    <li class="">
                        <a href="{{ route('site::login') }}" role="button" aria-haspopup="true"
                           aria-expanded="false">Accedi</a>
                    </li>
                </ul>
            @endauth
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>