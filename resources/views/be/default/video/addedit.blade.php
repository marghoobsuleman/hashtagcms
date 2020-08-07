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
        $lang_name = old('lang_name', '');
        $site_id = old('site_id', htcms_get_siteId_for_admin());

        $video_url = old('video_url', '');
        $publish_status = old('publish_status', 1);

        $image = old('image');


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

                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('name', 'Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'lang_name', $lang["name"] , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('video_url', 'Url') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'video_url', $video_url , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('image', 'Image') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::file('image', $image, array('accept'=>'image/*'), TRUE) !!}
                        </div>

                    </div>

                    <fieldset class="fieldset">
                        <legend>Publishing Options</legend>
                        <div class="form-group">

                            <div class="col-sm-2">
                                {!!  FormHelper::label('publish_status', 'Published') !!}
                            </div>

                            <div class="col-sm-10">
                                {!! FormHelper::input('checkbox', 'publish_status', $publish_status) !!}
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

