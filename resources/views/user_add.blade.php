@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/bootstrap-imageupload/css/bootstrap-imageupload.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Add Accounts</h1>
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

                <form action="/categories/add" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row push">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="Last Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" name="category-name" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label>
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" name="category-name" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label>
                                    Birthday
                                </label>
                                <input type="text" class="form-control" name="category-name" placeholder="09/01/1988">
                            </div>
                            <div class="form-group">
                                <label class="d-block">Gender</label>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline1" name="example-radio-custom-inline" checked="">
                                    <label class="custom-control-label" for="example-radio-custom-inline1">Male</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline custom-control-primary">
                                    <input type="radio" class="custom-control-input" id="example-radio-custom-inline2" name="example-radio-custom-inline">
                                    <label class="custom-control-label" for="example-radio-custom-inline2">Female</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-8">
                                    <label for="dm-project-new-category">
                                        Avatar
                                    </label>
                                    <!-- bootstrap-imageupload. -->
                                    <div class="imageupload panel panel-default">
                                        <div class="file-tab panel-body">
                                            <label class="btn btn-primary btn-file" style="margin-bottom: 0px;">
                                                <span>Browse</span>
                                                <!-- The file is stored here. -->
                                                <input type="file" name="image">
                                            </label>
                                            <button type="button" class="btn btn-danger">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>
                                        Start Date
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="Start Date">
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        Expire Date
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="Expire Date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dm-project-new-name">
                                    Address
                                </label>
                                <input type="text" class="form-control" name="category-name" placeholder="">
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        City
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="City">
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        State
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="State">
                                </div>
                                <div class="col-md-4">
                                    <label for="dm-project-new-name">
                                        Zip Code
                                    </label>
                                    <input type="text" class="form-control" name="category-name" placeholder="Zip Code">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row push">
                        <div class="col-lg-8 col-xl-5 offset-lg-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle mr-1"></i> Submit
                                </button>
                                <a class="btn btn-danger" href="{{url('/users')}}">
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

    <!-- Page JS Code -->
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();

    </script>
@endsection
