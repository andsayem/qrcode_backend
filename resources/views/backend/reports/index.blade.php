@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<?php
    $scs_keys =  App\Utilities\Enum\SSGCodeStatusEnum::getKeys();
    $scs_values =  App\Utilities\Enum\SSGCodeStatusEnum::getValues();
    $scs_keys_values =  App\Utilities\Enum\SSGCodeStatusEnum::getKeysValues();
    $cg_keys =  App\Utilities\Enum\RequestCodeStatusEnum::getKeys();
    $cg_values =  App\Utilities\Enum\RequestCodeStatusEnum::getValues();
    $cg_keys_values =  App\Utilities\Enum\RequestCodeStatusEnum::getKeysValues();
?>
<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Reports</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="header">
                <h2>Filter</h2>
            </div>
            <div class="body pt-0">
                {{Form::open(['method' => 'get'])}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from_date" class="mb-2">From Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('from_date', $request['from_date'] ?? '',['class'=>'form-control', 'autocomplete'=>'off',  'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="to_date" class="mb-2">To Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('to_date', $request['to_date'] ?? '',['class'=>'form-control', 'autocomplete'=>'off',  'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="product_id" class="mb-2">Product SKU</label>
                        {!! Form::select('product_id', ['' => 'Select Product SKU']+ $parentproducts, request('product_id'), ['id' =>'product_id','class' => 'form-control select2']) !!}
                    </div>

                    <!-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="code" class="mb-2">Code</label>
                            {!! Form::text('code', request('code') ?? '',['class'=>'form-control ',  'placeholder'=>'Code'])!!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="serial" class="mb-2">Serial</label>
                            {!! Form::text('serial', request('serial') ?? '',['class'=>'form-control ',  'placeholder'=>'Serial'])!!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mobile" class="mb-2">Mobile</label>
                            {!! Form::text('mobile', request('mobile') ?? '',['class'=>'form-control',  'placeholder'=>'Mobile'])!!}
                        </div>
                    </div> -->


                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2">Status</label>
                            {!! Form::select('status', ['' => 'Select Status'] + $scs_keys_values, request('status'), ['class' => 'form-control select2']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i>Filter</button>
                                <a type="button" class="btn btn-warning mr-2" href="{{ route('admin.ssgcodes.index') }}"><i class="fa fa-refresh mr-1"></i> <span>Reset</span></a>
                            </div>
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

<div class="card">
    <div class="header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>
                    Report
                    <span class="badge badge-info fill"> {{ $ssgcodes->total() }}</span>
                </h2>
            </div>
            <!-- <div class="col-lg-6 text-right">
                @can('ssg-code-upload')
                    <a role="button" href="#"   data-toggle="modal" data-target="#upload_modal" class="btn btn-sm btn-info px-3"><i class="fa fa-upload mr-2"></i> <span>Upload</span></a>
                @endcan
            </div> -->
        </div>
    </div>
    <div class="body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>Product SKU</th>
                        <th>Serial</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Total Used</th>
                        <th>Uploaded By</th>
                        <th>Uploaded IP</th>
                        <th>Mobile</th>
                        <th>User Id</th>
                        <th>User Name</th>
                        <!-- <th>Address</th> -->
                        <!-- <th>Lat - Long</th> -->
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($ssgcodes->count()>0)
                        @foreach ($ssgcodes as $i =>  $ssgcode)
                            <tr>
                                <td>
                                    {{ ($ssgcode->product->sku ?? ''). ' ('.($ssgcode->product->product_name ?? '').')' }}
                                </td>
                                <td>{{ $ssgcode->serial }}</td>
                                <td>{{ substr_replace($ssgcode->code,'********',3,8) }}</td>
                                <td>
                                    @include('includes.status', ['status' => [['key' => $scs_keys[0], 'value' => $scs_values[0], 'class'=> 'badge-danger'], ['key' => $scs_keys[1], 'value' => $scs_values[1], 'class'=> 'badge-success']], 'selected'=> $ssgcode->status])
                                </td>
                                <td>{{ $ssgcode->total_used }}</td>
                                <td>{{ $ssgcode->uploader->name ?? '' }}</td>
                                <td>{{ $ssgcode->uploaded_ip }}</td>
                                <td>{{ $ssgcode->mobile }}</td>
                                <td>
                                    
                                     @if($ssgcode->technicians )
                                     <a target="_blank" href="{{ route('admin.technicians.show',$ssgcode->technicians->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="View"> SSG{{$ssgcode->technicians->id }} </a>
                                     @endcan 
                                </td>
                                <td>
                                    @if($ssgcode->technicians )
                                     <a target="_blank" href="{{ route('admin.technicians.show',$ssgcode->technicians->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="View"> {{$ssgcode->technicians->name }} </a>
                                     @endcan 
                                </td>
                                <!-- <td>{{ $ssgcode->address }}</td>  -->
                                <!-- <td>{{ $ssgcode->lat }} - {{ $ssgcode->long }}</td> -->
                                <td>{{ $ssgcode->code_used_time }}</td> 

                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>

        @include('/includes/paginate', ['paginator' => $ssgcodes])
    </div>
</div>



{{-- upload modal --}}
<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="upload_modal" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="upload_modal">Printed Code Upload</h4>
            </div>
            <form action="{{ route('admin.ssgcodes.upload') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');"  enctype="multipart/form-data" >
                @csrf

                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">CSV File</label>
                            <div class="col-sm-8">
                                {!! Form::file('csv_file',['class' => 'form-control','required' => true,'accept'=>'.csv']) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button>
                    <a href="{{ route('code-sample-file.download') }}" class="btn btn-warning mr-2 float-right"><i class="fa fa-download mr-2"></i>Demo Download</a>
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('custom_scripts')

@endpush
