@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!}</h3>
        </div>
        <div class="pull-right back-link">
            <copy-paste
                data-form="addEditFrm"
                class="margin-bottom-05"
                data-back-url="{{$backURL}}"
            ></copy-paste>
        </div>
    </div>

    <div class="row">
        <div class="admin-form">
            <front-module-creator
                    data-form-action="{{htcms_get_save_path(request()->module_info->controller_name)}}"
                    data-results="{{json_encode($results)}}"
                    data-site="{{json_encode($sites)}}"
                    data-controller-name="{{request()->module_info->controller_name}}"
                    data-back-url="{{$backURL}}"
                    data-action-performed="{{$actionPerformed}}"
                    data-data-types="{{json_encode($dataTypes)}}"
                    data-data-types-info="{{json_encode($dataTypesInfo)}}"

            >
          </front-module-creator>
        </div>
    </div>

@endsection
