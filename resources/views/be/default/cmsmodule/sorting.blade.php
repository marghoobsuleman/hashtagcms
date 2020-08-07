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
            <div class="col-md-4">

                <menu-sorter
                        data-all-data="{{json_encode($allModules)}}"
                        data-controller-name="{!! htcms_get_module_name(request()->module_info) !!}"

                        data-show-groups="false"
                >
                </menu-sorter>

            </div>

        </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection

