@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!}</h3>
        </div>
        <div class="pull-right back-link">
            <a href="{{$backURL}}">Back</a>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="margin-top-20">
                <site-cloner
                        data-all-sites="{{json_encode($siteInfo)}}"
                >

                </site-cloner>

            </div>

        </div>
    </div>


@endsection
