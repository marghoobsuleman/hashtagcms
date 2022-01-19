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


        $id = 0;
        $module_id = old('module_id',);
        $name = old('name');
        $group = old('group');
        $site_id = old('site_id', htcms_get_siteId_for_admin());

        $update_in_all_language = old('update_in_all_language', 1);

         $lang = array();

        $lang["value"] = old('value');


       $moduleComboName = "module_id";
            $tenantComboName = "tenant_id";
        if ($actionPerformed === 'add') {
             $moduleComboName = "module_id[]";
            $tenantComboName = "tenant_id[]";
        }

        $module_id = old($moduleComboName, array());
        $tenant_id = old($tenantComboName, array());

        //dd($module_id, $moduleComboName, $actionPerformed);


        //print_r($results);
        //dd($modules);
        if(isset($results)) {
            extract($results);
        }

        //work around if no lang
        if(empty($lang)) {
            $lang = array();
        }

    @endphp


    <div class="row">
        <div class="admin-form">
            <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                {{csrf_field()}}

                {!! FormHelper::input('hidden', 'id', $id) !!}

                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                <div class="form-group">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('module_id', 'Choose Modules') !!}
                    </div>

                    <div class="col-sm-4">
                        {!! FormHelper::select($moduleComboName, $modules, array('id'=>'module_id', 'class'=>'form-control select-big', 'size'=>10), $module_id, array("value"=>"id", "label"=>"alias")) !!}
                    </div>

                </div>

                <div class="form-group">
                    <div class="col-sm-2">
                        {!!  FormHelper::label('tenant_id', 'Choose Tenants') !!}
                    </div>
                    <div class="col-sm-4">
                        {!! FormHelper::select($tenantComboName, $tenants, array('id'=>'tenant_id', 'class'=>'form-control select-big'), $tenant_id, array("value"=>"id", "label"=>"name")) !!}
                    </div>
                </div>

                <div class="form-group">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('group', 'Group') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'group', $group, array('class'=>'form-control')) !!}
                    </div>
                </div>

                <div class="form-group">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('name', 'Name') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>

                <div class="form-group">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('value', 'Value') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('value', $lang["value"], array('rows'=>8, 'class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>
                @if($actionPerformed === "edit")
                    <fieldset class="fieldset">
                        <legend>Additional Options</legend>
                        <div class="form-group">
                            <div class="col-sm-2">
                                {!!  FormHelper::label('update_in_all_language', 'Update in all languages') !!}
                            </div>
                            <div class="col-sm-10">
                                {!! FormHelper::checkbox('update_in_all_language', $update_in_all_language) !!}
                            </div>
                        </div>
                    </fieldset>
                @endif

                <div class="row">
                    <div class="form-group center-align">
                        <input type="submit" name="submit" value="Save" class="btn btn-success" />
                        <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

