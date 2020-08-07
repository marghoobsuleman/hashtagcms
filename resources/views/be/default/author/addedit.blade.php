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


        $name = old('name');
        $password = "";
        $email = old('email');
        $roles = old('roles') ?? [];


        if(isset($results)) {
            extract($results);
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
                            {!!  FormHelper::label('name', 'Name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!!  FormHelper::input('text', 'name', $name, array('class'=>'form-control', 'required'=>'required')) !!}

                        </div>

                    </div>

                    <div class="form-group">

                        <div  class="col-sm-2">
                            {!! FormHelper::label('email', 'Email') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'email', $email, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>

                    </div>

                    <div class="form-group">

                        <div  class="col-sm-2">
                            {!! FormHelper::label('roles', 'Choose Roles') !!}
                        </div>

                        <div class="col-sm-5">
                            <input type="hidden" value="0" name="updateRoles" id="updateRoles" />
                            {!! FormHelper::select('roles[]', $allRoles , array('class'=>'form-control', 'required'=>'required', 'onChange'=>'document.getElementById("updateRoles").value = 1'), $roles) !!}

                        </div>
                    </div>


                    <div class="form-group">
                        <label for="lang.name" class="col-sm-2">Password</label>
                        <div class="col-sm-10">
                            @if($id == 0)
                                {!! FormHelper::input('text', 'password', $password, array('class'=>'form-control', 'required'=>'required')) !!}
                            @else
                                {!! FormHelper::input('text', 'password', $password, array('class'=>'form-control')) !!}
                            @endif
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

