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
@if(env('FIREBASE_API_KEY') != '')
<script src="https://www.gstatic.com/firebasejs/7.14.4/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.4/firebase-analytics.js"></script>
<script>
    // Your web app's Firebase configuration
    var firebaseConfig = {
        apiKey: "<?php echo env('FIREBASE_API_KEY') ?>",
        authDomain: "<?php echo env('FIREBASE_AUTH_DOMAIN') ?>",
        databaseURL: "<?php echo env('FIREBASE_DATABASE_URL') ?>",
        projectId: "<?php echo env('FIREBASE_PROJECT_ID') ?>",
        storageBucket:"<?php echo env('FIREBASE_STORAGE_BUCKET') ?>",
        messagingSenderId: "<?php echo env('FIREBASE_MESSAGING_SENDER_ID') ?>",
        appId: "<?php echo env('FIREBASE_APP_ID') ?>",
        measurementId: "<?php echo env('FIREBASE_MEASUREMENT_ID') ?>"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    firebase.analytics();
</script>
@endif

</body>
</html>
