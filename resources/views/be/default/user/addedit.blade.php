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

        $name = old('name');
        $email = old('email');
        $father_name = old('father_name');
        $mother_name = old('mother_name');
        $mobile = old('mobile');
        $gender = old('gender');
        $cateogry = old('category');
        $religion = old('religion');
        $date_of_birth = old('date_of_birth');
        $aadhaar_card = old('aadhaar_card');
        $previous_college_roll_number = old('previous_college_roll_number');
        $institute = old('institute');
        $session = old('session');
        $student_type = old('student_type');
        $semester = old('semester');
        $admission_code = old('admission_code');
        $course_id = old('course_id');
        $course_level = "";


        if(isset($results)) {
            extract($results);

            dd($profile, $registration, $courses, $institutes, $religions, $religionCategories, $genders, $studentsTypes);

        }



        //work around if no lang
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
            $lang["name"] = "";
        }

    @endphp
    <div class="container" style="margin-top: 10px; display: none">
        <div class="row">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Personal Info</h3>
                </div>
                <div class="panel-body hook_modules">
                    <p><strong>Name: </strong>{{$name}} {{$middle_name}} {{$last_name}}</p>
                    <p><strong>Father's Name: </strong>{{$profile['father_name']}}</p>
                    <p><strong>Mother's Name: </strong>{{$profile['mother_name']}}</p>
                </div>
                <div class="panel-footer pb-4">&nbsp;</div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="container">

            <form method="POST" action="/registration/store">
                @csrf

                <fieldset style="border: 1px solid #f3f3f3; padding: 10px; margin-bottom: 40px">
                    <legend>College Information</legend>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="institute">College</label>
                                {{$institute}}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="session">Session</label>
                                {{$session}}
                            </div>
                        </div>
                    </div>
                </fieldset>


                <fieldset style="border: 1px solid #f3f3f3; padding: 10px; margin-bottom: 40px">
                    <legend>Course Information</legend>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="course">Course</label>
                                {{$course_id}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="semester">Semester</label>

                                {{$semester}}

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="student_type">Student Type</label>
                                {{$student_type}}
                            </div>
                        </div>
                    </div>

                </fieldset>

                <fieldset style="border: 1px solid #f3f3f3; padding: 10px; margin-bottom: 40px">
                    <legend>Personal Information</legend>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" required class="form-control" id="name" name="name" aria-describedby="name" placeholder="Please enter your name" value="{{$name}}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="father_name">Father's Name</label>
                                <input type="text" required class="form-control" id="father_name" name="father_name" aria-describedby="father_name" placeholder="Please enter your father's name" value="{{$father_name}}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="mother_name">Mother's Name</label>
                                <input type="text" required class="form-control" id="mother_name" name="mother_name" aria-describedby="mother_name" placeholder="Please enter your mother's name" value="{{$mother_name}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" required class="form-control" id="email" name="email" aria-describedby="email" placeholder="Please enter your email" value="{{$email}}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="mobile">Mobile</label>
                                <input type="text" required class="form-control" id="mobile" name="mobile" aria-describedby="name" placeholder="Please enter your mobile number" value="{{$mobile}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" required class="form-control" id="date_of_birth" name="date_of_birth" aria-describedby="date_of_birth" placeholder="Please enter your date of birth" value="{{$date_of_birth}}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="aadhaar_card">Aadhaar Card Number</label>
                                <input type="text" required class="form-control" id="aadhaar_card" name="aadhaar_card"  aria-describedby="aadhaar_card" placeholder="Please enter your Aadhaar Card Number" value="{{$aadhaar_card}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                {!! FormHelper::select('gender', $genders, array('class'=>'form-control', 'required'=>'required'), $gender, 'plain_array') !!}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="religion">Religion</label>
                                {!! FormHelper::select('religion', $religions, array('class'=>'form-control', 'required'=>'required'), $religion, 'plain_array') !!}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="category">Category</label>
                                {{$cateogry}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="previous_college_roll_number">Previous Roll Number</label>
                                <input type="previous_college_roll_number" class="form-control" id="previous_college_roll_number" name="previous_college_roll_number" placeholder="Please enter your previous college roll number" value="{{$previous_college_roll_number}}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="admission_code">Admission Code</label>
                                {{$admission_code}}
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-12 text-lg-center mt-5">
                        <input type="submit" class="thm-btn become-teacher__form-btn" value="Submit" />
                    </div>
                </div>
            </form>
        </div><!-- /.container -->
    </div>


    <div class="row">
        <div class="admin-form">
            <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
                  class="form-horizontal" role="form" enctype="multipart/form-data">

                {{csrf_field()}}

                {!! FormHelper::input('hidden', 'id', $id) !!}

                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}

                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <div class="form-group">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('id_card_number', 'Aadhar Card') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::input('text', 'id_card_number', $profile['id_card_number'] , array('class'=>'form-control', 'required'=>'required')) !!}
                    </div>
                </div>
                <div class="form-group">

                    <div class="col-sm-2">
                        {!!  FormHelper::label('gender', 'Gender') !!}
                    </div>

                    <div class="col-sm-10">
                        {!! FormHelper::select('gender', $genders, array('class'=>'form-control', 'required'=>'required'), $profile['gender'], "plain_array") !!}
                    </div>
                </div>


                <div class="row">
                    <div class="form-group center-align">
                        <input type="submit" name="submit" value="Save" class="btn btn-success"/>
                        <a href="{{$backURL ?? request()->headers->get('referer')}}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection

