<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE; chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' href='{{htcms_admin_asset("img/favicon.png")}}'>
    <script>
    function isIE() {
        var ua = window.navigator.userAgent;

        var msie = ua.indexOf('MSIE ');
        if (msie > 0) {
            // IE 10 or older => return version number
            return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
        }

        var trident = ua.indexOf('Trident/');
        if (trident > 0) {
            // IE 11 => return version number
            var rv = ua.indexOf('rv:');
            return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
        }

        var edge = ua.indexOf('Edge/');
        if (edge > 0) {
            // Edge (IE 12+) => return version number
            return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
        }

        // other browser
        return false;
    }
</script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (Auth::guest()==false)
    <title> Admin - {{request()->module_info->name}}</title>
    @else
    <title>Laravel</title>
    @endif

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ htcms_admin_asset('css/app.css') }}" />

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <script>
        window.Laravel.htcmsAdminConfig = function(key) {
          return window.Laravel.adminConfig[key];
        };
        window.Laravel.adminConfig = @php echo htmlspecialchars_decode(htcms_admin_config(), ENT_QUOTES) @endphp
    </script>
@stack('links')
</head>
<body>

    <div id="app">
      @include(htcms_admin_get_view_path('common.topbar'))
      <div class="container-fluid">
        <div class="row">
          @if (Auth::guest()==false)
            <div class="col-md-2 noLRpadding hidden-md hidden-xs" id="mainLeftContent">
              @include(htcms_admin_get_view_path('common.sidebar'))
            </div>
          @endif
          <?php
          $css = (Auth::guest()) ? "" : "col-md-10";
          ?>
          <div class="{{$css}}" id="mainRightContent">
            @yield('content')
          </div>

        </div>
      </div>
        @include(htcms_admin_get_view_path('common.components'))
    </div>



<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>

    <script id="ieappend">
        if(isIE() > 0) {
           var script = document.createElement("script");
           var host = window.location.protocol + "//"+window.location.host;
            script.src = host+"{{htcms_admin_asset('js/ie-polyfills.js')}}";
            document.getElementById("ieappend").parentNode.appendChild(script);
        }
    </script>
    <!--[if IE]><script src="{{htcms_admin_asset('js/ie-polyfills.js')}}"></script><![endif]-->
    <!-- Scripts -->

    <script src="{{htcms_admin_asset('js/app.js')}}"></script>

@stack('scripts')
</body>
</html>
