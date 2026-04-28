<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
<title>{{ config('app.name', 'Super Star Group') }}</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="SSG">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="icon" href="{{ asset('/backend_assets/assets/images/logo.png')}}" type="image/x-icon">
<!-- VENDOR CSS -->
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/animate-css/animate.min.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}" />
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/multi-select/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/libs/nouislider/nouislider.min.css') }}" />

<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/chartist/css/chartist.min.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/c3/c3.min.css')}}">

<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/select2/select2.css')}}" />

<!-- MAIN CSS -->
<link rel="stylesheet" href="{{ asset('/backend_assets/sass/custom.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/css/main.css')}}">
<link rel="stylesheet" href="{{ asset('/backend_assets/assets/css/color_skins.css')}}">

<link rel="stylesheet" href="{{ asset('/css/app.css')}}">

@stack('custom_styles')

</head>
<body class="theme-blue">

    @include('includes.alertmessages')
    @include('includes.loader')


    <!-- Overlay For Sidebars -->
    <div class="overlay" style="display: none;"></div>

    <div id="wrapper">

        @yield('topbar_content')
        @yield('leftsidebar_content')

        <div id="main-content" class="main_wrap_custom position-relative">
            <div class="container-fluid">
                @yield('content')
            </div>
            @yield('footer_content')
        </div>

    </div>

    <div class="modal fade bs-example-modal-center" id="user-password-change" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="exampleModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" action="{{route('password.update')}}" method="POST" id="password_update">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label class="mb-2">Old Password</label>
                            <input type="password" class="form-control" name="old_password" required minlength="8" placeholder="Enter Old Password">
                        </div>
                        <div class="form-group">
                            <label class="mb-2">New Password</label>
                            <input type="password" name="new_password" id="new_password" required minlength="8"  class="form-control" placeholder="Enter New Password">
                        </div>
                        <div class="form-group">
                            <label class="mb-2">Repeat Password</label>
                            <input type="password" name="password_confirmation" required class="form-control" placeholder="Repeat New Password">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Update Now</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Javascript -->
    <script src="{{ asset('/backend_assets/assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/bundles/vendorscripts.bundle.js') }}"></script>

    <script src="{{ asset('/backend_assets/assets/vendor/c3/d3.v5.min.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/vendor/c3/c3.min.js') }}"></script>

    <script src="{{ asset('/backend_assets/assets/bundles/chartist.bundle.js') }}"></script>

    <script src="{{ asset('/backend_assets/assets/bundles/flotscripts.bundle.js') }}"></script> <!-- flot charts Plugin Js -->
    <script src="{{ asset('/backend_assets/assets/vendor/flot-charts/jquery.flot.time.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/vendor/flot-charts/jquery.flot.selection.js') }}"></script>

    <script src="{{ asset('/backend_assets/assets/bundles/knob.bundle.js') }}"></script> <!-- Jquery Knob-->

    <script src="{{ asset('/backend_assets/assets/bundles/mainscripts.bundle.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/bundles/morrisscripts.bundle.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/js/pages/charts/flot.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/js/pages/chart/c3.js') }}"></script>

    <script src="{{ asset('/backend_assets/assets/vendor/select2/select2.min.js')}}"></script> <!-- Select2 Js -->

    <script src="{{ asset('/backend_assets/assets/js/index.js') }}"></script>

    <script src="{{ asset('/backend_assets/assets/vendor/multi-select/js/jquery.multi-select.js') }}"></script> <!-- Multi Select Plugin Js -->
    <script src="{{ asset('/backend_assets/assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}"></script> <!-- Bootstrap Colorpicker Js -->
    <script src="{{ asset('/backend_assets/assets/vendor/jquery-inputmask/jquery.inputmask.bundle.js') }}"></script> <!-- Input Mask Plugin Js -->
    <script src="{{ asset('/backend_assets/assets/vendor/jquery.maskedinput/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script> <!-- Bootstrap Tags Input Plugin Js -->
    <script src="{{ asset('/backend_assets/assets/vendor/nouislider/nouislider.js') }}"></script> <!-- noUISlider Plugin Js -->
    <script src="{{ asset('/backend_assets/assets/js/pages/forms/advanced-form-elements.js') }}"></script>
    <script src="{{ asset('/backend_assets/assets/vendor/apexcharts/apexcharts.js') }}"></script> <!-- ApexCharts Plugin Js -->

    @stack('custom_scripts')

</body>
</html>
