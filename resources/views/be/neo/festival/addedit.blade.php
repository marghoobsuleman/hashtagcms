@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"></title-bar>
    @php


        $id = 0;
        $site_id = htcms_get_siteId_for_admin();
        $image = old('image', '');
        $lottie = old('lottie', '');
        $header_css = old('header_css', '');
        $footer_css = old('footer_css', '');
        $body_css = old('body_css', '');
        $start_date = old('start_date', date('Y-m-d'));
        $end_date = old('end_date', date('Y-m-d', strtotime("next month")));
        $publish_status = old('publish_status', 1);


        //print_r($results);

        if(isset($results)) {
            extract($results);
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
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
            <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
                  class="form-horizontal" role="form" enctype="multipart/form-data">

                {{csrf_field()}}

                {!! FormHelper::input('hidden', 'id', $id) !!}

                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('body_css', 'Body Css') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'body_css', $body_css , array('class'=>'form-control')) !!}
                    </div>
                </div>

                <!-- create all fields here -->
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('image', 'Image') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::file('image', $image, array('accept'=>'image/*'), TRUE, 100) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('lottie', 'Lottie') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::textarea('lottie', $lottie , array('class'=>'form-control')) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('header_css', 'Header Css') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text','header_css', $header_css , array('class'=>'form-control')) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('footer_css', 'Footer Css') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'footer_css', $footer_css , array('class'=>'form-control')) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('start_date', 'Start Date') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('date', 'start_date', $start_date , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>
                <div class="form-group row">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('end_date', 'End Date') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('date', 'end_date', $end_date, array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>
                <fieldset class="fieldset">
                    <legend>Publishing Options</legend>

                    <div class="form-group row">

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
                        <input type="submit" name="submit" value="Save" class="btn btn-success"/> <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

