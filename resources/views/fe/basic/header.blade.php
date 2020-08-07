<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->
<header>
<!-- header-start -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="{{htcms_get_path('/')}}">{{htcms_get_site_info("name")}}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">

        </ul>
        <div class="navbar-text btn-group text-info">
            <ul class="navbar-nav mr-auto">
                @php
                    echo htcms_get_header_menu_html();
                @endphp
                <li class="nav-divider">&nbsp;</li>
                @auth
                    @if(strtolower(auth()->user()->user_type) == 'staff')
                        <li class="nav-item">
                            <a href="{{htcms_admin_path('dashboard')}}" class="nav-link" title="Dashboard"><i aria-hidden="true" class="fa fa-dashboard"></i> <span class="text">
                                    {{__('hashtagcms::links.dashboard')}}
                                </span></a>
                        @endif
                        </li>
                        <li class="nav-item">
                            <a href="{{htcms_get_path('profile')}}" class="nav-link" title="Profile"><i aria-hidden="true" class="fa fa-user"></i> <span class="text">
                                    {{__('hashtagcms::links.profile')}}
                                </span></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{htcms_get_path('logout')}}" class="nav-link" title="Logout"><i aria-hidden="true" class="fa fa-sign-out"></i> <span class="text">
                                    {{__('hashtagcms::links.logout')}}
                                </span></a>
                        </li>
                @endauth
                @guest
                    <li class="nav-item">
                    <a href="{{htcms_get_path('login')}}" class="nav-link"><span>{{__('hashtagcms::links.login')}}</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{htcms_get_path('register')}}" class="nav-link"><span>{{__('hashtagcms::links.register')}}</span></a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
</header> <!-- end navigation -->

