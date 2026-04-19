<!doctype html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="Super Star">
    <meta name="author" content="ThemeMakker, design by: ThemeMakker.com">

    <link rel="icon" href="{{ asset('/backend_assets/assets/images/SSG-favicon.png') }}" type="image/x-icon">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/animate-css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend_assets/assets/vendor/font-awesome/css/font-awesome.min.css') }}">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('/backend_assets/sass/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend_assets/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend_assets/assets/css/color_skins.css') }}">
</head>


<body class="theme-blue">
	<!-- WRAPPER -->
	<div id="wrapper">
		<div class="vertical-align-wrap">
			<div class="vertical-align-middle auth-main">
				<div class="auth-box">
                    <!-- <div class="mobile-logo"><a href="{{route('admin.dashboard')}}"><img src="../assets/images/logo-icon.svg" alt="Mplify"></a></div> -->
                    <div class="auth-left">
                        <!-- <div class="left-top">
                            <a href="{{route('admin.dashboard')}}">
                                <img src="../assets/images/SSG-paint-logo.png" alt="SSG">
                            </a>
                        </div> -->
                        <div class="left-slider">
                            <img src="{{ asset('backend_assets/assets/images/login/1.jpg') }}" class="img-fluid" alt="">
                        </div>
                    </div>
                    <div class="auth-right">

                        <div class="card">
                            <div class="header">
                                <a href="{{route('admin.dashboard')}}">
                                    <img src="{{ asset('backend_assets/assets/images/logo.png') }}" alt="SSG" width="100">
                                </a>
                                <p class="lead">Log in</p>
                            </div>
                            <div class="body">
                                <form class="form-auth-small" method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="signin-email" class="control-label sr-only">Email</label>
                                        {{-- <input type="email" class="form-control" id="signin-email" value="" placeholder="User Name"> --}}
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email"   placeholder="Email">
                                        @include('/includes/validationmessages', ['field_name'=>'email', 'session_field_name'=>session('fail')])
                                    </div>
                                    <div class="form-group">
                                        <label for="signin-password" class="control-label sr-only">Password</label>
                                        {{-- <input type="password" class="form-control" id="signin-password" value="" placeholder="Password"> --}}
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                        @include('/includes/validationmessages', ['field_name'=>'password'])
                                    </div>
                                    {{--<div class="form-group clearfix">
                                        <label class="fancy-checkbox element-left">
                                            <input type="checkbox">
                                            <span>Remember me</span>
                                        </label>
                                    </div>--}}
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
                                    {{--<div class="bottom">
                                        <span class="helper-text m-b-10"><i class="fa fa-lock"></i> <a href="page-forgot-password.html">Forgot password?</a></span>
                                    </div>--}}
                                </form>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	<!-- END WRAPPER -->
</body>
</html>
