<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE; chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' href='{{htcms_admin_asset("img/favicon.png")}}'>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (Auth::guest()==false)
    <title> Admin - {{request()->module_info->name}}</title>
    @else
    <title>HashtagCMS</title>
    @endif

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    @vite(['resources/assets/hashtagcms/be/neo/sass/app.scss'])
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token()
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
          <div class="row mb-5">
              <div class="col col-lg-2">
                  @include(htcms_admin_get_view_path('common.sidebar'))
              </div>
              <div class="col">
                  @yield('content')
              </div>
          </div>
          <div class="row">
              @include(htcms_admin_get_view_path('common.components'))
          </div>
      </div>
    </div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>
@vite(['resources/assets/hashtagcms/be/neo/js/app.js'])
@stack('scripts')
</body>
</html>
