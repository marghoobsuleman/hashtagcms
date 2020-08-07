@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>{!! htcms_get_module_name(request()->module_info) !!} - Content Copier</h3>
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

    @endphp
    <div class="row">
        <div class="admin-form">
            <language-copier
                    data-languages="{{json_encode($languages)}}"
                    data-language-tables="{{json_encode($langTables)}}"
            ></language-copier>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

