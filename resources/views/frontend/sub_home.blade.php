@extends('layouts.frontend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{asset('js/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css')}}">
@endsection

@section('css_after')
    <style>
        div.block.block-link-pop:hover {
            box-shadow: 0 0.5rem 1rem {{$theme->font_color}};
            -webkit-transform: translateY(-1px);
            transform: translateY(-1px);
            opacity: 1;
        }
        div.block {
            background-color: transparent;
            box-shadow: 0 2px 4px {{$theme->font_color}};
        }
        div.block p {
            font-size: 18px;
            color: {{$theme->font_color}};
        }
        div.block a {
            color: {{$theme->font_color}};
        }
        #text-search {
            background: {{$theme->product_background_color}};
            color: {{$theme->font_color}};
        }
    </style>
@endsection
@section('content')
    <!-- Hero -->
    <div class="bg-body-light" style="background-color: {{$theme->product_background_color}} !important;">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                @foreach($category_array as $category)
                    @if($category->id == $category_id)
                        <a href="javascript:onCategoryChange({{$category->id}});" style="margin-right: 50px;"><h1 class="flex-sm-fill font-size-h2 mt-2 mb-0 mb-sm-2" style="color: {{$theme->font_color}}; font-weight: bold;">{{$lang == 'en' ? $category->name : $category->name_second}}</h1></a>
                    @else
                        <a href="javascript:onCategoryChange({{$category->id}});" style="margin-right: 50px;"><h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2" style="color: {{$theme->font_color}};">{{$lang == 'en' ? $category->name : $category->name_second}}</h1></a>
                    @endif

                @endforeach
                <div class="flex-sm-fill"></div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <input type="text" placeholder="Search..." id="text-search" value="{{$search}}"/>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        @if($category_id == 0)
            <p style="color: {{$theme->font_color}};">Coming soon...</p>
        @endif
        @foreach($product_array as $product)
            @if($loop->index % 4 == 0)
                <div class="row">
                    @endif
                    <div class="col-md-6 col-xl-3">
                        <div class="block block-rounded block-link-pop text-center" onclick="onProductSelect({{$product->id}});" >
                            <div class="bg-image" style="width: 100%; position: relative; padding-top:75%; background-image: url('{{asset('media/images/products/thumbnail').'/'.$product->picture}}');">
                                <div class="block-content block-content-full d-flex justify-content-between" style="background-color: {{$theme->product_background_color.'90'}};">
                                    <p class="mb-0">{{ $lang == 'en' ? $product->name : $product->name_second}}</p>
                                    @if(isset($product->video_id))
                                    <a href="javascript:;" onclick="event.stopPropagation(); onVideoButtonClicked('{{$product->video_id}}', '{{$lang == 'en' ? $product->name : $product->name_second}}'); " data-id="{{$product->id}}"><i class="far fa-play-circle" style="vertical-align: middle;"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($loop->index % 4 == 3 || $loop->index == count($product_array) - 1)
                </div>
                @endif
        @endforeach
    </div>

    <div class="modal fade" id="modal-block-fadein" tabindex="-1" role="dialog" aria-labelledby="modal-block-fadein" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px;">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" style="background-color: {{$theme->product_background_color}};" >
                    <div class="block-header bg-primary-dark" style="background-color: {{$theme->banner_color}} !important;" >
                        <h3 class="block-title">Modal Title</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" style="padding: 8px 8px 0px 8px;" id="modal-content">
                        <iframe src="" frameborder="0" allowfullscreen class="iframe-video" width="100%" height="600" ></iframe>
                    </div>
                </div>
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
    <script src="{{asset('js/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('js/pages/be_tables_datatables.min.js')}}"></script>

    <!-- Page JS Helpers (Select2 plugin) -->
    <script>jQuery(function(){ Dashmix.helpers('select2'); });</script>

    <script>
        function onCategoryChange(id) {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('category')) {
                urlParams.set('category', id);
            } else {
                urlParams.append('category', id);
            }
            window.location.search = urlParams;
        }

        function onLanguageChange(lang) {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('lang')) {
                urlParams.set('lang', lang);
            } else {
                urlParams.append('lang', lang);
            }
            window.location.search = urlParams;
        }

        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        };

        function onProductSelect(id) {
            $.ajax({
                url: baseUrl + "/product/get",
                type: "POST",
                data: {
                    "_token": Laravel.csrfToken,
                    "id": id
                },
                error: function() {

                },
                success: function (data) {
                    if (data.message.length == 0 && data.data.product != undefined) {
                        var product = data.data.product;
                        $("#modal-block-fadein .block-title").html(product.name);
                        var html_str = '<div style="margin-bottom: 8px;">';
                        html_str += '<img src="' + '{{asset('/media/images/products/original/')}}' + '/' + product.picture + '" style="width:100%;">';
                        html_str += '<div><p>' + product.description + '</p><p>' + product.price + ' ' + product.currency.name +  '</p></div>'
                        html_str += '</div>';
                        $("#modal-content").html(html_str);
                        $("#modal-block-fadein").modal('show');

                    }
                }
            });

        }

        function onVideoButtonClicked(videoId, productName) {
            $("#modal-block-fadein .block-title").html(productName);
            $("#modal-block-fadein iframe").attr("src", "https://www.youtube.com/embed/" + videoId);
            $("#modal-block-fadein").modal('show');
        }

        $('#modal-block-fadein').on('hidden.bs.modal', function () {
            $("#modal-content").html('');
        });

        $("#text-search").on("change", function () {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) {
                urlParams.set('search', $(this).val());
            } else {
                urlParams.append('search', $(this).val());
            }
            window.location.search = urlParams;
        });
    </script>
@endsection
