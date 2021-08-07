<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! htcms_get_all_meta_tags() !!}
    {!! htcms_get_all_link_tags() !!}
    {!! htcms_get_header_content() !!}
    <title>{!! htcms_get_header_title() !!}</title>
    <script>
        let _siteProps_ = <?php echo htcms_get_site_props(true); ?>;
    </script>
</head>
<body>
{!! htcms_get_body_content() !!}
{!! htcms_get_footer_content() !!}
<form id="logout-form" action="/login/doLogout" method="POST" style="display: none;">
    @csrf
    <input type="submit" value="Logout">
</form>
<div class="small" style="padding:10px; text-align: center;">
    Page rendered in  - {{ (microtime(true) - LARAVEL_START) }}
<a title="#CMS" href="http://www.hashtagcms.org/">Powered by HashtagCms.org</a>
</div>
@if(env('GOOGLE_TAG_MANAGER_KEY') != '')
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer',"<?php echo env('GOOGLE_TAG_MANAGER_KEY'); ?>");
    </script>
@endif
<script>
    (function () {
        HashtagCms.Analytics.init(_siteProps_);

        //HashtagCms.Analytics.trackPageView(_siteProps_.categoryName + ""+_siteProps_.pageName)
    })()
</script>
</body>
</html>
