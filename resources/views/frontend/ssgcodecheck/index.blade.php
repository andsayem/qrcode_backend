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
        font-size: .8rem;
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

</header>

<!-- Begin page content -->
<main class="container">

    @include('includes.alertmessages')

    <div class="row ssg-height">
      <div class="col-xl-4 col-lg-5 col-md-6 m-auto pb-5">
        <div class="text-center">
          <img src="{{ asset('frontend_assets/assets/images/logo.png') }}" width="120px" class="mb-4 logo" />
        </div>

        <div class="card ssg">
          <div class="card-body">
            <h5 class="card-title">Product Verification</h5>
            <p class="card-text text-gray">Please enter your mobile number and product code
              which is just under the barcode</p>

                {{ Form::open(['route' => 'checkCodeURLValidate','id'=>'roles-form']) }}
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        {{Form::text('mobile', '', ['class' => 'form-control numberOnlyInput', 'placeholder' => 'Enter Mobile Number',  'aria-describedby'=>'mobile', 'min' => 2, 'autocomplete'=>'off','pattern'=> '(^(\+88|0088|88)?(01){1}[3456789]{1}(\d){8})$', 'oninvalid' => 'this.setCustomValidity("Enter valid mobile no")', 'oninput' => 'this.setCustomValidity("")'])}}
                        @include('/includes/validationmessages', ['field_name'=>'mobile'])
                    </div>

                    <div class="mb-4" style="display: {{ request('unique_code')?'none':'block' }}">
                        <label for="code" class="form-label">Product Code</label>
                        {{Form::text('code', request('unique_code'), ['class' => 'form-control','placeholder' => 'Enter Product Code', 'id'=>'code',  'aria-describedby'=>'code'])}}
                        @include('/includes/validationmessages', ['field_name'=>'code'])
                    </div>

                    <div class="d-grid gap-2 pt-1">
                        <button type="submit" class="btn btn-primary text-uppercase font-700">Verify Now</button>
                    </div>

                    <div class="text-center text-between text-gray mt-1">

                    <p class="ms-auto me-auto mb-3">
                        <span> OR </span>
                    </p>
                </div>
                <p class="text-gray text-center mb-0 font-13"> You can scan QR code with your mobile phone. Just open your mobile QR scanner and scan the product QR code. </p>
              {{ Form::close() }}

          </div> <!-- end card body -->
        </div>  <!-- end card -->
      </div> <!-- end col -->
    </div>   <!-- end row -->

  <!-- end container -->
</main>

<footer class="footer mt-auto py-3 bg-dark">
  <div class="container text-center">
    <span class="text-white">© {{ getCurrentYear() }} SSGESHOP All Rights Reserved. | Tech Support by SSL Wireless</span>
  </div>
</footer> <!-- end footer -->


<script src="{{ asset('frontend_assets/assets/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/assets/libs/jquery/jquery.min.js') }}"></script>

<script>
    jQuery('.numberOnlyInput').keyup(function () {
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });


    $(document).on('keypress','.numberOnlyInput',function(e){
        if(!(e.keyCode >=48 && e.keyCode <=57) ){
            e.preventDefault();
        }
    });
</script>

  </body>
</html>
