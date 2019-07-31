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
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Customers</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active" aria-current="page">Customers</li>
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
                <h3 class="block-title">Customer List</h3>
            </div>
            <div class="block-content block-content-full">
                <div style="margin-bottom: 10px;">
                    <a class="btn btn-primary" href="{{url('/customers/add')}}"><i class="si si-user-follow"></i> Add Customer</a>
                </div>
                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th class="d-none d-sm-table-cell">Name</th>
                        <th class="d-none d-sm-table-cell">StartDate</th>
                        <th class="d-none d-sm-table-cell">ExpireDate</th>
                        <th class="d-none d-sm-table-cell">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="font-w600">
                                {{$customer->name}}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                24 June 2020, 09:30AM
                            </td>
                            <td class="d-none d-sm-table-cell">
                                24 June 2021, 09:30AM
                            </td>
                            <td>
                                <div class="custom-control custom-switch custom-control-lg custom-control-inline mb-2">
                                    <input type="checkbox" class="custom-control-input" id="example-sw-custom-inline-lg1" name="example-sw-custom-inline-lg1" checked="">
                                    <label class="custom-control-label" for="example-sw-custom-inline-lg1"></label>
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

@endsection
