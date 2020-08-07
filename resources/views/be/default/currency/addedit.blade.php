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
        $iso_code = old("iso_code");
        $iso_code_num = old("iso_code_num");
        $sign = old("sign");
        $blank = old("blank", 1);
        $format = old("format", 1);
        $decimals = old("decimals", 1);
        $conversion_rate = old("conversion_rate", 1);

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
                            {!!  FormHelper::label('iso_code', 'ISO Code') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'iso_code', $iso_code , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('iso_code_num', 'ISO Code Number') !!}
                            <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">Find ISO number</a>
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'iso_code_num', $iso_code_num, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('sign', 'Sign (Symbol)') !!}
                            <a href="https://en.wikipedia.org/wiki/Currency_symbol" target="_blank">Find Currency Symbolr</a>
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'sign', $sign, array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('blank', 'Blank?') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('blank', $blank) !!}
                        </div>
                    </div>


                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('format', 'Format') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('format', $format) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('decimals', 'Decimals?') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('decimals', $decimals) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('conversion_rate', 'Conversion Rate') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'conversion_rate', $conversion_rate, array('class'=>'form-control', 'required'=>'required')) !!}
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
