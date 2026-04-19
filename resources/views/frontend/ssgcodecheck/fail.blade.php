<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="generator" content="">
    <title>SSG</title>
    <link rel="icon" href="{{ asset('/backend_assets/assets/images/logo.png')}}" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" referrerpolicy="no-referrer" />

  <!-- Bootstrap core CSS -->
  <link href="{{ asset('frontend_assets/assets/dist/css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="{{ asset('frontend_assets/sass/style.css') }}" rel="stylesheet">
  </head>
  <body class="d-flex flex-column h-100">

    <header>
        <!-- Fixed navbar -->
        <nav class="navbar navbar-expand-md bg-primary">
            <div class="container justify-content-between">
                <a class="nav-link text-white font-14" aria-current="page" href="mailto:info@ssgbd.com"> <i class="fa fa-envelope me-2"> </i>info@ssgbd.com</a>
                <a class="nav-link text-white font-14" aria-current="page" href="tel:+8809610774774"> <i class="fa fa-phone me-2"> </i>+88-09610-774774</a>
            </div>
        </nav>
        <nav class="navbar navbar-expand-md bg-white header-shadow py-3">
            <div class="container justify-content-between align-items-center">
                <a href="{{ route('checkCodeURL') }}" class="text-decoration-none">
                    <img src="{{ asset('frontend_assets/assets/images/logo.png') }}" width="90px" class="" />
                </a>
                <a href="{{ route('checkCodeURL') }}" class="text-decoration-none btn btn-outline-primary px-3">
                   Verfiy Another Product
                </a>
            </div>
        </nav>
    </header>

    <!-- Begin page content -->
    <main class="container">
        <div class="row align-items-center ssg-height-inner">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 ssg-left">
                <h2 class="mb-4 text-danger">This code is invalid. Please enter the right code or contact with seller.</h2>
                {{--<p class="mb-4 font-13 text-gray"> This product code <span class="text-black">"{{ $code ?? null }}"</span>  is not authorized based on our product list. Please contact with your seller for further query.</p>--}}
            </div> <!-- end col -->
        </div>   <!-- end row -->
    <!-- end container -->
    </main>
    <footer class="footer mt-auto py-3 bg-dark">
    <div class="container text-center">
        <span class="text-white">© 2021 SSGESHOP All Rights Reserved. | Tech Support by SSL Wireless</span>
    </div>
    </footer> <!-- end footer -->
    <script src="{{ asset('frontend_assets/assets/dist/js/bootstrap.bundle.min.js') }}"></script>
  </body>
</html>
