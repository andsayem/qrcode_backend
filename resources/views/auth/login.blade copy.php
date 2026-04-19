@extends('layouts.app_simple')

@section('content')

<div class="auth-page">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-xxl-3 col-lg-4 col-md-5">
                <div class="auth-full-page-content d-flex p-sm-5 p-4">
                    <div class="w-100">
                        <div class="d-flex flex-column h-100">
                            {{-- <div class="mb-4 mb-md-5 text-center">
                                <a href="{{route('admin.dashboard')}}" class="d-block auth-logo">
                                    <img src="assets/images/logo.png" alt="" height="28"> <span class="logo-txt">Marks Dessert Queen</span>
                                </a>
                            </div> --}}
                            <div class="auth-content my-auto">
                                <div class="text-center">
                                    <h5 class="mb-0">Welcome Back !</h5>
                                    <p class="text-muted mt-2">log in to continue.</p>
                                </div>
                                <form class="mt-4 pt-2"  method="POST" action="{{ route('loginPost') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus  placeholder="Enter email">
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <label class="form-label">Password</label>
                                            </div>
                                            {{-- <div class="flex-shrink-0">
                                                <div class="">
                                                    <a href="auth-recoverpw.html" class="text-muted">Forgot password?</a>
                                                </div>
                                            </div> --}}
                                        </div>

                                        <div class="input-group auth-pass-inputgroup">
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">

                                            {{-- <input type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon"> --}}
                                            <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <button class="btn btn-success w-100 waves-effect waves-light" type="submit">Log In</button>
                                    </div>
                                </form>




                            </div>
                            <div class="mt-4 mt-md-5 text-center">
                                {{-- <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> Minia   . Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand</p> --}}
                                Copyright © <script>document.write(new Date().getFullYear())</script> | Software Shop Limited | All Rights Reserved.
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end auth full page content -->
            </div>
            <!-- end col -->
            <div class="col-xxl-9 col-lg-8 col-md-7 bg-theme">
                <div class="auth-bg pt-md-5 p-4 d-flex">
                    <div class="bg-overlay bg-theme"></div>
                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <!-- end bubble effect -->
                    <div class="row justify-content-center align-items-center">


                        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                {{-- <div class="carousel-item active">
                                    <img class="d-block w-100" src="{{ asset('assets/images/banner/banner-1.png') }}" alt="First slide">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100" src="{{ asset('assets/images/banner/banner-2.jpeg') }}" alt="Second slide">
                                </div> --}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container fluid -->
</div>

@endsection
