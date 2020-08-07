@extends(htcms_admin_config('theme').'.index')

@section('content')
<h1>{{request()->module_info->name}}</h1>


<div class="panel panel-default" role="alert">
    @if(is_array($isSaved) && isset($isSaved['status']) && $isSaved['status']>200)
        <div class="panel-heading">Error! </div>
        <div class="panel-body alert-danger">
            <p> {{$isSaved['message']}}</p>
        </div>
    @else
        <div class="panel-heading">Success! </div>
        <div class="panel-body alert-success">
            <p> Great! Data has been saved successfully.  <timer-button data-timeout="3" data-back-url="{{$backURL}}">Going Back in... </timer-button>
            </p>

        </div>
    @endif

    <div class="panel-footer">

        <a class="btn btn-primary" href="{{htcms_admin_path(request()->module_info->controller_name.'/add')}}">Add New</a>
        <a class="btn btn-secondary" href="{{$backURL}}">Back</a>


    </div>

</div>

@endsection
