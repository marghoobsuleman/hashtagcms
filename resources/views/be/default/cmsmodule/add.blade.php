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

    @php

        if(isset($results)) {
            extract($results);
        }


    @endphp


        <div class="row">
            <div class="admin-form">

                <module-creator
                        data-cms-modules="{{json_encode($cmsModules)}}"
                        data-database-tables="{{json_encode($allTables)}}"
                        data-controller-name="{{request()->module_info->controller_name}}"
                        data-back-url="{{$backURL}}"
                >
                </module-creator>
            </div>
        </div>


@endsection
