@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!}</h3>
        </div>
        <div class="pull-right back-link margin-bottom-05">


            <cms-module-dropdown
                    data-modules="{{json_encode(request()->module_info)}}"
            ></cms-module-dropdown>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row v-space">

            <passport-clients></passport-clients>

            <passport-personal-access-tokens></passport-personal-access-tokens>

            <passport-authorized-clients></passport-authorized-clients>

        </div>

    </div>


@endsection
