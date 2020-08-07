@extends(htcms_admin_config('theme').'.index')

@section('content')

    <div class="row border-bottom">
        <div class="col-md-6">
            <h3>

                {!! htcms_get_module_name(request()->module_info) !!}

            </h3>
        </div>
        <div class="pull-right back-link margin-bottom-05">

            <cms-module-dropdown
                    data-modules="{{json_encode(request()->module_info)}}"
            ></cms-module-dropdown>

        </div>
    </div>
    <div class="container">
        <div class="row" style="margin-top: 10px">

            @foreach($data as $row)
                <div class="col-lg-4">
                <info-box data-label="{{$row['label']}}"
                          data-total="{{$row['total']}}"
                          data-icon-css="{{$row['icon']}}"
                          data-link="{{htcms_admin_path($row['link'])}}"
                ></info-box>
                </div>
            @endforeach
        </div>

    </div>


@endsection
