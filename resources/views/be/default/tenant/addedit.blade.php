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
        $name = old("name");
        $link_rewrite = old("link_rewrite");

        $site_id = old('site_id', session("site_id", 1));

        if(isset($results)) {
            extract($results);
        }

    @endphp

        <div class="row">
            <div class="admin-form">
                <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" class="form-horizontal" role="form">

                    {{csrf_field()}}

                    {!! FormHelper::input('hidden', 'id', $id) !!}

                    {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                    {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                    {!! FormHelper::input('hidden', 'site_id', $site_id) !!}


                    <div class="form-group">
                        <label for="name" class="col-sm-2">Name</label>
                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="link_rewrite" class="col-sm-2">Link Rewrite</label>
                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'link_rewrite', $link_rewrite , array('class'=>'form-control', 'required'=>'required')) !!}
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
