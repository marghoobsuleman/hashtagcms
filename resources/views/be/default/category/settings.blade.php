@extends(htcms_admin_config('theme').'.index')

@section('content')

    <action-bar
            data-controller-title="{{request()->module_info->name}}"
            data-controller-name="{{request()->module_info->controller_name}}"
            data-show-add="false"
            data-show-back="true"
    >
    </action-bar>


    <admin-category-settings
            data-site-id="{{htcms_get_siteId_for_admin()}}"
            data-platform-id="{{$platform_id}}"
            data-microsite-id="{{$microsite_id}}"
            data-site-microsites="{{json_encode($siteMicrosites)}}"
            data-site-categories="{{json_encode($siteCategories)}}"
            data-site-themes="{{json_encode($siteThemes)}}"
            data-site-platforms="{{json_encode($sitePlatforms)}}"
            data-categories="{{json_encode($categories)}}"
    ></admin-category-settings>
@endsection
