@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Product Edit</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Product</a></li>
                    <li class="breadcrumb-item active">Product Edit</li>
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
                    Edit Product
                </h3>
                {{ Form::model($editModeData,['route' =>['admin.products.update',$editModeData->id ],'method' => 'PUT','files'=>'true','id'=>'thanas-form'])}}

                <div class="row">


                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Product</label>
                            <div class="col-sm-8">
                                {{Form::text('product_name', $editModeData->product_name, ['class' => 'form-control',  'placeholder'=>'Product name'])}}
                                @include('/includes/validationmessages', ['field_name'=>'product_name'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">SKU</label>
                            <div class="col-sm-8">
                                {{Form::text('sku', $editModeData->sku, ['class' => 'form-control', 'placeholder'=>'SKU', 'readonly'])}}
                                @include('/includes/validationmessages', ['field_name'=>'sku'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label for="role" class="col-sm-4 col-form-label control-label">Category</label>
                            <div class="col-sm-8">
                                {!! Form::select('category_id', ['' => 'Select Category']+ $parentcategories, $editModeData->category_id, ['id' =>'category_id','class' => 'form-control select2']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'category_id'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label for="role" class="col-sm-4 col-form-label control-label">Channels</label>
                            <div class="col-sm-8">
                                {!! Form::select('channel_id', ['' => 'Select Channel']+ $channels, $editModeData->channel_id, ['id' =>'channel_id','class' => 'form-control select2']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'channel_id'])
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Status</label>
                            <div class="col-sm-8">
                                {!! Form::select('status', App\Utilities\Enum\StatusEnum::getKeysValues(), $editModeData->status, ['class' => 'form-control']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'status'])
                            </div>
                        </div>
                    </div> 

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Point Slab</label>
                            <div class="col-sm-8">
                                {{Form::text('point_slab', $editModeData->point_slab, ['class' => 'form-control', 'placeholder'=>'Point Slab'])}}
                                @include('/includes/validationmessages', ['field_name'=>'point_slab'])
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="form-group row ">
                            <label class="col-sm-4 col-form-label control-label">Description</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('desc', $editModeData->desc, ['id' =>'desc','class' => 'form-control', 'rows' => 5, 'placeholder'=>'Description']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'desc'])
                            </div>
                        </div>
                    </div>




                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Update</button>
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

@push('custom_script')

@endpush
