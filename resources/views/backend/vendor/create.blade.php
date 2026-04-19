@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Vendor Create</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendors.index') }}">Vendor</a></li>
                    <li class="breadcrumb-item active">Vendor Create</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->


<div class="row">
    <div class="col-12 mx-auto">
        <div class="card">
            <div class="card-body order-list">
                <h3 class="header-title mt-0 mb-3">
                    Add New Vendor
                </h3>
                {{ Form::open(['route' => 'admin.vendors.store','id'=>'roles-form']) }}

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Vendor</label>
                            <div class="col-sm-8">
                                {{Form::text('vendor_name', '', ['class' => 'form-control',  'placeholder'=>'Vendor name'])}}
                                @include('/includes/validationmessages', ['field_name'=>'vendor_name'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Contact Person</label>
                            <div class="col-sm-8">
                                {{Form::text('contact_person', '', ['class' => 'form-control', 'placeholder'=>'Contact Person'])}}
                                @include('/includes/validationmessages', ['field_name'=>'contact_person'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Mobile</label>
                            <div class="col-sm-8">
                                {{Form::text('mobile', '', ['class' => 'form-control', 'placeholder'=>'Mobile'])}}
                                @include('/includes/validationmessages', ['field_name'=>'mobile'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row ">
                            <label class="col-sm-4 col-form-label control-label">Address</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('address', '', ['id' =>'address','class' => 'form-control', 'rows' => 5, 'placeholder'=>'Address']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'address'])
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12 text-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Save Now</button>
                        </div>
                    </div>
                </div>
                <!--end /div-->
                {{ Form::close() }}
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->



@endsection
