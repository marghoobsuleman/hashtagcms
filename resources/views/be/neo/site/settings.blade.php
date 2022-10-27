@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!} - <small>Settings for <span class="text-danger">{{$siteInfo->name}}</span></small></h3>
        </div>
        <div class="pull-right back-link">
            <a href="{{$backURL}}">Back</a>
        </div>

    </div>

    @php
    //print_r($siteInfo);
    @endphp



    <div class="row">
        @include(htcms_admin_get_view_path('site.tabs'))
    </div>

    <div class="row">
       <site-wise
        data-site-data="{{json_encode($selectedData)}}"
        data-all-data="{{json_encode($allData)}}"
        data-message="{{isset($allData['message']) ? $allData['message'] : ''}}"
        data-current-key="{{$activeTab}}"
        data-site-id="{{$siteInfo->id}}"


       >

       </site-wise>
    </div>


@endsection
