
@php
$name = old('name');
$email = old('email');
$phone = old('phone');
$comment = old('comment');

@endphp
<section style="margin-top:30px">
    <div class="container mt-30">
        @if (session('success'))
            <div class="alert alert-success" role="alert" id="success" style="margin-bottom: 0;">
                {{session('success')}} <span title="Close" class="fa fa-close" style="float:right; cursor: pointer" onclick="document.getElementById('success').style.display = 'none'">&nbsp;</span>
            </div>
        @endif
            <div class="card">
                <div class="card-header">Contact Us</div>
                <div class="card-body">
                    <form action="/common/contact" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text"  required placeholder="Please enter your full name" id="name" name="name" class="form-control" value="{{$name}}">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" required  placeholder="Please enter your email" id="email" name="email" class="form-control" value="{{$email}}">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" placeholder="Please enter your phone number" id="phone" name="phone" class="form-control" value="{{$phone}}">
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <textarea type="text" required placeholder="Please tell us your query" id="comment" name="comment" class="form-control">{{$comment}}</textarea>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Submit') }}
                                </button>

                            </div>
                        </div>
                    </form>


                </div>
            </div>
    </div>
</section>


@if ($errors->any())
    <script>window.error_messages = JSON.parse('<?php echo json_encode($errors->messages()); ?>');</script>
    <script src="{{asset('/assets/be/js/error-handler.js?version='.htcms_admin_config('version'))}}"></script>
@endif
