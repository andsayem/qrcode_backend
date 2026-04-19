@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Category Create</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Category</a></li>
                    <li class="breadcrumb-item active">Category Create</li>
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
                    Add New Category
                </h3>
                {{ Form::open(['route' => 'admin.categories.store','id'=>'roles-form']) }}

                <div class="row">


                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Category</label>
                            <div class="col-sm-8">
                                {{Form::text('category_name', '', ['class' => 'form-control',  'placeholder'=>'Category name'])}}
                                @include('/includes/validationmessages', ['field_name'=>'category_name'])
                            </div>
                        </div>
                    </div>

                    {{--<div class="col-md-6">
                        <div class="form-group row ">
                            <label for="role" class="col-sm-4 col-form-label control-label">Parent Category</label>
                            <div class="col-sm-8">
                                {!! Form::select('parent_id', ['' => 'Select Parent Category']+ $parentcategories, null, ['id' =>'parent_id','class' => 'form-control select2']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'parent_id'])
                            </div>
                        </div>
                    </div>--}}

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Status</label>
                            <div class="col-sm-8">
                                {!! Form::select('status', App\Utilities\Enum\StatusEnum::getKeysValues(), App\Utilities\Enum\StatusEnum::Active, ['class' => 'form-control']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'status'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row ">
                            <label class="col-sm-4 col-form-label control-label">Description</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('desc', '', ['id' =>'desc','class' => 'form-control', 'rows' => 5, 'placeholder'=>'Description']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'desc'])
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
