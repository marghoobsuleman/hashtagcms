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
        $group_name = old('group_name', '');
        $name = old('name');
        $value = old('value');
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $tenant_id = old('tenant_id');
        $is_public = old('is_public', 0);

        //print_r($results);

        if(isset($results)) {
            extract($results);
        }



        //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
            $lang["name"] = "";
        }

    @endphp


        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                    @if(sizeof($siteGroups) > 0)
                        <div class="form-group">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('siteGroups', 'Choose Existing Group') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::select('siteGroups', $siteGroups, array('class'=>'form-control', 'onChange'=>'group_name.value = this.value'), '', "plain_array") !!}

                            </div>

                        </div>
                    @endif

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('group_name', 'Site Group') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'group_name', $group_name , array('class'=>'form-control', 'required'=>'required')) !!}

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
                            {!! FormHelper::textarea('value', $value, array('rows'=>8, 'class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>


                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('site_id', 'Site') !!}
                        </div>

                        <div class="col-sm-10">
                            <site-button data-sites="{{json_encode($sites)}}" data-selected="{{$site_id}}"></site-button>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('tenant_id', 'Tenant') !!}
                        </div>

                        <div class="col-sm-10">

                            <tenant-button
                                    data-id="tenant_id"

                                    data-name="{{$id > 0 ? 'tenant_id' : 'tenant_id[]'}}"

                                    data-site-id="{{$site_id}}"

                                   @if($id > 0) data-selected="{{$tenant_id}}" @endif

                                    data-fetch-on-init="true"
                                    @if($id === 0) data-multiple="true" @endif
                            ></tenant-button>

                        </div>
                    </div>

                    <fieldset class="fieldset">
                        <legend>Visibility</legend>
                        <div class="form-group">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('is_public', 'Is Public?') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('checkbox', 'is_public', $is_public) !!}
                            </div>

                        </div>
                    </fieldset>


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

