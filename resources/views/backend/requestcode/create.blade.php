@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Request Code Create</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.requestcodes.index') }}">Request Code</a></li>
                <li class="breadcrumb-item active">Request Code Create</li>
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
                    Add New Request Code
                </h3>
                {{ Form::open(['route' => 'admin.requestcodes.store','id'=>'roles-form']) }}

                <div class="row">


                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Product SKU</label>
                            <div class="col-sm-8">
                                {!! Form::select('product_id', $parentproducts, null, ['id' =>'product_id','class' => 'form-control select2', 'placeholder'=>'Select Product SKU']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'product_id'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Code Length</label>
                            <div class="col-sm-8">
                                {{Form::number('code_length', 14, ['class' => 'form-control',  'placeholder'=>'Code Length', 'readonly'])}}
                                @include('/includes/validationmessages', ['field_name'=>'code_length'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Total no of code</label>
                            <div class="col-sm-8">
                                {{Form::number('total_no_of_code', '', ['class' => 'form-control',  'placeholder'=>'Total no of code', 'max'=>'1000000', 'min'=>'1'])}}
                                @include('/includes/validationmessages', ['field_name'=>'total_no_of_code'])
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->getRoleNames()[0] === 'Super Admin')
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Vendor</label>
                            <div class="col-sm-8">
                                {!! Form::select('vendor_id', ['' => 'Select Vendor']+ $parentvendors, null, ['id' =>'vendor_id','class' => 'form-control select2']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'vendor_id'])
                            </div>
                        </div>
                    </div>
                    @endif


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