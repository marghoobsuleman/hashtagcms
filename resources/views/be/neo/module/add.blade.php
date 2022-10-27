@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!}</h3>
        </div>
        <div class="pull-right back-link">
            <copy-paste
                data-form="addEditForm"
                class="margin-bottom-05"
                data-back-url="{{$backURL}}"
            ></copy-paste>
        </div>

    </div>

    @php

    //not in used

        $id = 0;
        $lang = array();
        $lang["lang_id"] = "";
        $lang["name"] = "";


        $name = "";

        //print_r($results);

        if(isset($results)) {
            extract($results);
        }



        //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang[0]["lang_id"] = session("lang_id");
            $lang[0]["name"] = "";
        }

    @endphp

    <div class="row">
        <div class="admin-form">

            <front-module-creator
                    data-form-action="{{htcms_get_save_path(request()->module_info->controller_name)}}"
                    data-results="{{json_encode($results)}}"
                    data-site="{{json_encode($sites)}}"
                    data-controller-name="{{request()->module_info->controller_name}}"
                    data-back-url="{{$backURL}}"
                    data-action-performed="{{$actionPerformed}}"
            >
          </front-module-creator>
        </div>
    </div>

@endsection
