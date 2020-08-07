@extends(htcms_admin_config('theme').'.index')

@section('content')

    @php
        //default settings
        //dd($categoryModules);
        $site_id = (isset($site_id)) ? $site_id : $siteInfo->id;
        $microsite_id = (isset($microsite_id)) ? $microsite_id : 0;
        $category_id = (isset($category_id)) ? $category_id : $siteInfo->category_id;
        $tenant_id = (isset($tenant_id)) ? $tenant_id : 1;

        $categories = isset($siteInfo->category) ? $siteInfo->category : [];
        $microsites = isset($siteInfo->microsite) ? $siteInfo->microsite : [];
        $tenants = isset($siteInfo->tenant) ? $siteInfo->tenant : [];

    @endphp


    <div class="row border-bottom">
        <div class="col-md-6">
            <h3><small> <left-menu-toggle data-icon-css="fa fa-bars hand" data-icon-css-off="fa fa-bars hand"></left-menu-toggle></small> {!! htcms_get_module_name(request()->module_info) !!}</h3>
        </div>
    </div>
    <admin-homepage
            data-site-id="{{$site_id}}"
            data-microsite-id="{{$microsite_id}}"
            data-tenant-id="{{$tenant_id}}"
            data-category-id="{{$category_id}}"

            data-categories="{{$categories}}"
            data-microsites="{{$microsites}}"
            data-tenants="{{$tenants}}"

            data-site-info="{{json_encode($siteInfo)}}"
            data-hook-info="{{json_encode($siteInfo->hook)}}"
            data-all-modules="{{json_encode($allModules)}}"
            data-category-modules="{{json_encode($categoryModules)}}"
            data-category-info="{{json_encode($categoryInfo)}}"
            data-theme-info="{{json_encode($themeInfo)}}"
            data-user-rights="{{json_encode($user_rights)}}"
            data-is-module-readonly="{{$isModuleReadonly}}"
            data-all-sites="{{json_encode($allSites)}}"
    >
    </admin-homepage>
    allSites:(typeof this.dataAllSites == "undefined" || this.dataAllSites == "") ? [] : JSON.parse(this.dataAllSites),
@endsection

