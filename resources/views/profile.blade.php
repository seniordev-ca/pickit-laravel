@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-imageupload/css/bootstrap-imageupload.min.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Profile</h1>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-content">

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/profile/edit" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row push">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="first-name" placeholder="First Name"
                                           value="{{$user->first_name}}">
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="last-name" placeholder="Last Name"
                                           value="{{$user->last_name}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label for="dm-project-new-category">
                                        Avatar <span class="text-danger">*</span>
                                    </label>
                                    <!-- bootstrap-imageupload. -->
                                    <div class="imageupload panel panel-default">
                                        <img id="preview"
                                             src="{{asset('media/avatars').'/'.$user->avatar}}"
                                             class="avatar-preview-edit"/>
                                        <input type="file" id="image" name="image" style="display: none;"/>
                                        <!--<input type="hidden" style="display: none" value="0" name="remove" id="remove">-->
                                        <a href="javascript:changeProfile()" class="btn btn-primary">Change</a>
                                        <a href="javascript:removeImage()" class="btn btn-danger">Original</a>
                                    </div>
                                </div>
                                @if(Session::get('user-type')==3)
                                    <div class="col-lg-6">
                                        <label for="dm-project-new-category">
                                            Company logo
                                        </label>
                                        <!-- bootstrap-imageupload. -->
                                        <div class="imageupload panel panel-default">
                                            <img id="preview-company-logo"
                                                 src="{{asset('media/company_logos').'/'.$user->company_logo}}"
                                                 class="avatar-preview-edit"/>
                                            <input type="file" id="company_logo" name="company_logo" style="display: none;"/>
                                            <!--<input type="hidden" style="display: none" value="0" name="remove" id="remove">-->
                                            <a href="javascript:changeProfile1()" class="btn btn-primary">Change</a>
                                            <a href="javascript:removeImage1()" class="btn btn-danger">Original</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" name="email" placeholder="Email"
                                       value="{{$user->email}}">
                            </div>
                            <div class="form-group">
                                <label>
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" name="password" placeholder="Password">
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label for="example-colorpicker2">Template Color</label>
                                    <div class="js-colorpicker input-group" data-format="hex">
                                        <input type="text" class="form-control" name="theme-color" value="{{$user->template_color}}">
                                        <div class="input-group-append">
                                            <span class="input-group-text colorpicker-input-addon">
                                                <i></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" value="{{$user->id}}" name="id">
                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle mr-1"></i> Submit
                                </button>
                                <a class="btn btn-danger" href="{{url('/dashboard')}}">
                                    <i class="fa fa-times-circle mr-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- END Submit -->
                </form>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{asset('js/plugins/bootstrap-imageupload/js/bootstrap-imageupload.min.js')}}"></script>
    <script src="{{asset('js/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script>jQuery(function(){ Dashmix.helpers(['colorpicker']); });</script>
    <!-- Page JS Code -->
    <script>
        function changeProfile() {
            $('#image').click();
        }
        $('#image').change(function () {
            var imgPath = this.value;
            var ext = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            if (ext == "gif" || ext == "png" || ext == "jpg" || ext == "jpeg")
                readURL(this);
            else
                alert("Please select image file (jpg, jpeg, png).")
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $('#preview').attr('src', e.target.result);
//              $("#remove").val(0);
                };
            }
        }
        function removeImage() {
            $('#preview').attr('src', "{{asset('media/avatars').'/'.$user->avatar}}");
//      $("#remove").val(1);
        }


        function changeProfile1() {
            $('#company_logo').click();
        }
        $('#company_logo').change(function () {
            var imgPath = this.value;
            var ext = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            if (ext == "gif" || ext == "png" || ext == "jpg" || ext == "jpeg")
                readURL1(this);
            else
                alert("Please select image file (jpg, jpeg, png).")
        });
        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $('#preview-company-logo').attr('src', e.target.result);
//              $("#remove").val(0);
                };
            }
        }
        function removeImage1() {
            $('#preview-company-logo').attr('src', "{{asset('media/company_logos').'/'.$user->company_logo}}");
//      $("#remove").val(1);
        }
    </script>
@endsection
