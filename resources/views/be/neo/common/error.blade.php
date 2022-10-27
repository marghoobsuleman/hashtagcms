@extends(htcms_admin_config('theme').'.index')

@section('content')
<h1>{{request()->module_info->name}}</h1>


<div class="panel panel-default" role="alert">
    <div class="panel-heading">{{$title ?? "Whoops!"}}</div>
    <div class="panel-body alert-danger">
        {{$message ?? "Don't know, but found some error."}}
    </div>
</div>

@endsection

