@php
$user = auth()->user();
$userName = $user->name;

@endphp

<top-nav data-username="{{$userName}}"
         data-sitename="{{htcms_admin_config('site_label')}}"
         data-current-site="{{htcms_get_siteId_for_admin()}}"
         data-site-combo="true"
        ></top-nav>
