@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3> {!! htcms_get_module_name(request()->module_info) !!} </h3>

        </div>
        <div class="pull-right back-link">
            <a href="{{$backURL}}">Back</a>
        </div>
    </div>

    @php


        $id = 0;

        if(isset($results)) {
            extract($results);
        }


    @endphp
        <div class="row">
            <div class="admin-form">
                <module-permission

                        data-back-url="{{$backURL}}"
                        data-cms-modules="{{json_encode($allModules)}}"
                        data-user-modules="{{json_encode($userModules)}}"
                        data-controller-name="{{request()->module_info->controller_name}}"
                        data-is-super-admin="{{$isSuperAdmin}}"
                ></module-permission>

            </div>
        </div>
@endsection
