<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Hashtag CMS</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <style>
            code{border: 1px solid #e6e6e6; display: block; padding:50px 5px; margin:30px 0 5px 0; background-color: #f2f2f2; text-align: center;}
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

    </head>
    <body>


    <div class="container" style="width: 50%; margin-top: 50px" id="app">
        @php
            $siteInfo->domain = request()->getSchemeAndHttpHost();
        @endphp
        <site-installer
            data-site-info="{{json_encode($siteInfo)}}"
            data-is-installed="{{$isInstalled}}"
        ></site-installer>
    </div>

    <script src="{{asset("assets/hashtagcms/installer/js/installer.js")}}"></script>

</body>
</html>
