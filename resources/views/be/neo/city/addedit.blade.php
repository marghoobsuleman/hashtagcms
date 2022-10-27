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
        $country_id = old('country_id');
        $iso_code = old('iso_code');
        $tax_behavior = old('tax_behavior');
        $airport_name = old('airport_name');
        $airport_code = old('airport_code');
        $latitude = old('latitude');
        $longitude = old('longitude');

        if(isset($results)) {
            extract($results);
        }

        //dd($countries[0]);


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
                            {!!  FormHelper::label('name', 'City name') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control', 'required'=>'required')) !!}
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('country_id', 'Country') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::select('country_id', $countries,
                                                    array('required'=>'required'),
                                                    $country_id,
                                                    array("value"=>"id", "label"=>"lang.name")) !!}
                        </div>

                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('iso_code', 'ISO Code') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'iso_code', $iso_code , array('class'=>'form-control')) !!}
                        </div>

                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('airport_code', 'Airport Code') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'airport_code', $airport_code, array('class'=>'form-control')) !!}

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('airport_name', 'Airport Name') !!}
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'airport_name', $airport_name, array('class'=>'form-control')) !!}

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('tax_behavior', 'Tax Behavior') !!}
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::checkbox('tax_behavior', $tax_behavior) !!}
                        </div>


                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('latitude', 'Latitude') !!}
                            <i class="fa fa-refresh hand text-info" onclick="LatLng.udpateLatLngFields()" aria-hidden="true"></i>
                        </div>

                        <div class="col-sm-10">

                            {!! FormHelper::input('text', 'latitude', $latitude, array('class'=>'form-control')) !!}

                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-2">
                            {!!  FormHelper::label('longitude', 'Longitude') !!}
                            <i class="fa fa-refresh hand text-info" onclick="LatLng.udpateLatLngFields()" aria-hidden="true"></i>
                        </div>

                        <div class="col-sm-10">
                            {!! FormHelper::input('text', 'longitude', $longitude, array('class'=>'form-control')) !!}

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

@push("scripts")
<script src="{{htcms_admin_asset('js/map.js')}}"></script>

<script>
    var LatLng = {
        get: function (city, country="") {
            country = (country=="") ? "" : ", "+country;
            return new Promise(function(resolve, reject) {
                MapApi.getLatLong(city+country).then(function(res) {
                    resolve(res);
                }).catch(function(e) {
                    reject(res)
                });
            });
        },
        udpateLatLngFields: function () {
            let city = document.getElementById("name").value;
            let country = document.getElementById("country_id").options[document.getElementById("country_id").selectedIndex].text;

            LatLng.get(city, country).then(function(res) {
                //console.log(res)
                let data = format(res);

                if(data.length>0) {

                    let latitude = data[0].latLng.lat;
                    let longitude = data[0].latLng.lng;

                    document.getElementById("latitude").value = latitude;
                    document.getElementById("longitude").value = longitude;
                }


            });


            function format(res) {
                let data = [];

                let countryKey = {key:"adminArea1", label:"country"};
                let stateKey = {key:"adminArea3", label:"state"};
                let cityKey = {key:"adminArea5", label:"city"};

                let results = (res.data.results.length == 0 ) ? null : res.data.results[0];

                //console.log("results");
                //console.log(results);


                if(results != null) {
                    let providedLocation = results.providedLocation.location;
                    let allLocations = results.locations;

                    let keysToStore = ['displayLatLng', 'latLng', 'p    ostalCode', 'sideOfStreet', 'street'];

                    allLocations.forEach(function(location, index) {
                        let locationObj = {};
                        locationObj[countryKey.label] = location[countryKey.key];
                        locationObj[stateKey.label] = location[stateKey.key];
                        locationObj[cityKey.label] = location[cityKey.key];

                        keysToStore.forEach(function(key) {
                            locationObj[key] = location[key];
                        });
                        data.push(locationObj);
                    });

                }
                return data;

            }

        }
    }

</script>
@endpush("scripts")
