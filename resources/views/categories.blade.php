@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Categories</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active" aria-current="page">Categories</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">Category List</h3>
            </div>
            <div class="block-content block-content-full">
                <div style="margin-bottom: 10px;">
                    <a class="btn btn-primary" href="{{url('/categories/add')}}"><i class="si si-plus"></i> Add Category</a>
                </div>
                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">#</th>
                        <th class="d-none d-sm-table-cell" style="width: 100px;">Picture</th>
                        <th class="d-none d-sm-table-cell">Category Name</th>
                        <th class="d-none d-sm-table-cell">Tags</th>
                        <th class="d-none d-sm-table-cell" style="width: 80px;">Show</th>
                        <th class="d-none d-sm-table-cell" style="width: 80px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="font-w600">
                                <div align="center">
                                    <img src="{{asset('/media/images/categories/thumbnail/').'/'.$category->picture}}"
                                         style="width: 80px;">
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{$category->name}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="badge badge-primary">{{$category->tags}}</span>
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-switch custom-control custom-control-inline mb-2"
                                     align="center">
                                    <input type="checkbox" class="custom-control-input"
                                           id="show-toggle-{{$category->id}}" name="show-toggle-{{$category->id}}"
                                           @if($category->show_flag == 1) checked @endif >
                                    <label class="custom-control-label" for="show-toggle-{{$category->id}}"></label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{url('/categories/edit').'/'.$category->id}}"
                                       class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <a href="javascript:delCategory({{$category->id}})" class="btn btn-sm btn-primary"
                                       data-toggle="tooltip" title="Delete">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{asset('js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Page JS Code -->
    <script src="{{asset('js/pages/be_tables_datatables.min.js')}}"></script>

    <script>
        function delCategory(id) {
            if (confirm("Do you want delete this category?\nThe data related to this category (Products) will be also deleted.")) {
                $.ajax({
                    url: '{{url('/categories/del')}}',
                    type: "POST",
                    data: {
                        "_token": Laravel.csrfToken,
                        "id": id,
                    },
                    error: function () {
                    },
                    success: function (data) {
                        if (data.status == true) {
                            window.location.reload();
                        }
                    }
                });
            }
        }

        $(document).ready(function () {
            $("[name^='show-toggle-']").on('change', function () {
                var id = this.name.split("show-toggle-")[1];
                $.ajax({
                    url: '{{url('/categories/toggle_visible')}}',
                    type: "POST",
                    data: {
                        "_token": Laravel.csrfToken,
                        "id": id,
                    },
                    error: function () {
                    },
                    success: function (data) {
                        if (data.status == true) {
                            //window.location.reload();
                        }
                    }
                });
            });
        });
    </script>
@endsection
