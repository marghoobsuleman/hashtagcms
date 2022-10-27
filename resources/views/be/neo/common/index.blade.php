@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!} <cms-module-dropdown
                        data-modules="{{json_encode(request()->module_info)}}"
                ></cms-module-dropdown></h3>
        </div>
        <div class="pull-right back-link margin-bottom-05">

        </div>
    </div>
    <div class="container-fluid">
        <div class="row v-space">

            <info-boxes
                data-modules="{{json_encode(request()->module_info)}}"
                data-modules-allowed="{{json_encode($moduleAllowed)}}"
                data-is-admin="{{$isAdmin}}"
            >

            </info-boxes>

        </div>

    </div>


@endsection
