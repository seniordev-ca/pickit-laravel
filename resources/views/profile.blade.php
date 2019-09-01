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
                    <h2 class="content-heading pt-0">Account Info</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                Some vital information about your account
                            </p>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="form-group">
                                <label>
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="first-name" placeholder="First Name"
                                       value="{{$user->first_name}}">
                            </div>
                            <div class="form-group">
                                <label>
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="last-name" placeholder="Last Name"
                                       value="{{$user->last_name}}">
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8">
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
                        </div>
                    </div>

                    <h2 class="content-heading pt-0">Template Info (For App)</h2>
                    <div class="row push">
                        <div class="col-lg-4">
                            <p class="text-muted">
                                You can customize your mobile template here
                            </p>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            @if(Session::get('user-type')==3)
                                <div class="form-group">
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
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Template
                                </label>
                                <div class="d-flex">
                                    <select class="custom-select" name="template-no">
                                        <option value="0" disabled="disabled">Select a template</option>
                                        <option value="1" @if($user->template_no === 1) selected @endif>Template 1</option>
                                        <option value="2" @if($user->template_no === 2) selected @endif>Template 2</option>
                                    </select>
                                    <a href="javascript:showPreviewDialog();" class="btn btn-success" style="margin-left: 20px;" title="Preview"><i class="si si-eye"></i></a>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="example-colorpicker2">Banner Background Color</label>
                                <div class="js-colorpicker input-group" data-format="hex">
                                    <input type="text" class="form-control" name="banner-color" value="{{$user->banner_color}}">
                                    <div class="input-group-append">
                                    <span class="input-group-text colorpicker-input-addon">
                                        <i></i>
                                    </span>
                                    </div>
                                    <a href="javascript:resetBannerColor()" class="btn btn-success" style="margin-left: 20px;" title="Reset to default color"><i class="fa fa-undo"></i></a>
                                </div>
                            </div>
                            <div class="form-group" id="category-background-color-div">
                                <label for="example-colorpicker2">CategoryList Background Color</label>
                                <div class="js-colorpicker input-group" data-format="hex">
                                    <input type="text" class="form-control" name="category-background-color" value="{{$user->category_background_color}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon">
                                            <i></i>
                                        </span>
                                    </div>
                                    <a href="javascript:resetCategoryBackgroundColor();" class="btn btn-success" style="margin-left: 20px;" title="Reset to default color"><i class="fa fa-undo"></i></a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="example-colorpicker2">ProductList Background Color</label>
                                <div class="js-colorpicker input-group" data-format="hex">
                                    <input type="text" class="form-control" name="product-background-color" value="{{$user->product_background_color}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon">
                                            <i></i>
                                        </span>
                                    </div>
                                    <a href="javascript:resetProductBackgroundColor()" class="btn btn-success" style="margin-left: 20px;" title="Reset to default color"><i class="fa fa-undo"></i></a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="example-colorpicker2">Category Font Color</label>
                                <div class="js-colorpicker input-group" data-format="hex">
                                    <input type="text" class="form-control" name="font-color" value="{{$user->font_color}}">
                                    <div class="input-group-append">
                                <span class="input-group-text colorpicker-input-addon">
                                    <i></i>
                                </span>
                                    </div>
                                    <a href="javascript:resetFontColor()" class="btn btn-success" style="margin-left: 20px;" title="Reset to default color"><i class="fa fa-undo"></i></a>
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

    <div class="modal fade" id="modal-block-fadein" tabindex="-1" role="dialog" aria-labelledby="modal-block-fadein" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" >
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Modal Title</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" style="padding: 0px 0px 0px 0px;">
                        <img id="preview-image" src="" style="width: 100%;" >
                    </div>
                </div>
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

        function resetBannerColor() {
            $("[name='banner-color']").val("#161616");
            $("[name='banner-color']").trigger("change");
        }

        function resetCategoryBackgroundColor() {
            $("[name='category-background-color']").val("#161616");
            $("[name='category-background-color']").trigger("change");
        }

        function resetProductBackgroundColor() {
            $("[name='product-background-color']").val("#292929");
            $("[name='product-background-color']").trigger("change");
        }

        function resetFontColor() {
            if ($("[name='template-no']").val() == 1) {
                $("[name='font-color']").val("#ea2225");
            } else {
                $("[name='font-color']").val("#e72929");
            }
            $("[name='font-color']").trigger("change");
        }

        $("[name='template-no']").on("change", function () {
           if ($(this).val() == 2) {
               $("#category-background-color-div").hide();
           } else {
               $("#category-background-color-div").show();
           }
        });
        function showPreviewDialog() {
            if ($("[name='template-no']").val() == 1) {
                $("#modal-block-fadein .block-title").html("Template1 Preview");
                $("#preview-image").attr("src", '{{asset('/media/images/templates/template1.jpg')}}');
            } else {
                $("#modal-block-fadein .block-title").html("Template2 Preview");
                $("#preview-image").attr("src", '{{asset('/media/images/templates/template2.jpg')}}');
            }
            $("#modal-block-fadein").modal('show');
        }
    </script>
@endsection
