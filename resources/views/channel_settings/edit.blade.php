@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Channel Settings Update</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">ChannelSettings</a></li>
                    <li class="breadcrumb-item active">ChannelSettings Edit</li>
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
                            Edit ChannelSettings
                        </h3>
                        {{ Form::model($channel,['route' =>['admin.channel_settings.update',$channel->id ],'method' => 'PUT','files'=>'true','id'=>'thanas-form'])}}

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row required">
                                    <label class="col-sm-4 col-form-label control-label">Slab value</label>
                                    <div class="col-sm-8">
                                        {{Form::text('slab_value', $channel->slab_value, ['class' => 'form-control',  'placeholder'=>'slab value'])}}
                                        @include('/includes/validationmessages', ['field_name'=>'slab_value'])
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
