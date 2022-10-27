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
        $name = old('name', 'Create your form here...');

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

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('migration', 'Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'migration', $migration , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">
                         <div class="col-sm-2">

                          </div>
                           <div class="col-sm-10">
                                    <p class="alert alert-warning"> <i class="fa fa-info-circle" area-hidden="true"></i> Please create your form here</p>
                           </div>

                    </div>

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

