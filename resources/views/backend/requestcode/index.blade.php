@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<?php
$cg_keys =  App\Utilities\Enum\RequestCodeStatusEnum::getKeys();
$cg_values =  App\Utilities\Enum\RequestCodeStatusEnum::getValues();
$cg_keys_values =  App\Utilities\Enum\RequestCodeStatusEnum::getKeysValues();
?>
<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Request Codes</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Request Codes</li>
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
                            <label for="product_id" class="mb-2">Product SKU</label>
                            {!! Form::select('product_id', ['' => 'Select Product SKU']+ $parentproducts, request('product_id'), ['id' =>'product_id','class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    @if(auth()->user()->getRoleNames()[0] === 'Super Admin')
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vendor_id" class="mb-2">Vendor</label>
                            {!! Form::select('vendor_id', ['' => 'Select Vendor']+ $parentvendors, request('vendor_id'), ['id' =>'vendor_id','class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2">Status</label>
                            {!! Form::select('status', ['' => 'Select Status'] + $cg_keys_values, null, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i>Filter</button>
                                <a type="button" class="btn btn-warning mr-2" href="{{ route('admin.requestcodes.index') }}"><i class="fa fa-refresh mr-1"></i> <span>Reset</span></a>
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
                    Request Code List
                    <span class="badge badge-info fill"> {{ $requestcodes->total() }}</span>
                </h2>
            </div>
            <div class="col-lg-6 text-right">
                @can('request-code-create')
                <a href="{{ route('admin.requestcodes.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                @endcan
                @can('product-edit')
                <!-- <a href="{{ route('requestcodes.code_generator') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-code"></i> <span>Code Generator</span></a> -->
                <a href="{{ route('requestcodes.code_generator_v3') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-code"></i> <span>Code Generator</span></a>
              
                <!-- <a href="{{ route('requestcodes.code_generator_v2') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-code"></i> <span>Code Generator V2</span></a> -->
              
                
                  @endcan
            </div>


        </div>
    </div>
    <div class="body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product SKU</th>
                        <th>Code Length</th>
                        <th>Total No. of Code</th>
                        <th>Total Complete</th>
                        <!-- <th>Printed</th> -->
                        <th>Vendor Name</th>
                        <th>Vendor Mobile No.</th>
                        <th>Request By</th>
                        <th>Status</th>
                        <th>Comments</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($requestcodes->count()>0)
                    @foreach ($requestcodes as $i => $requestcode)
                    <tr>
                        <td>{{dateConvertDBtoForm($requestcode->created_at)}}</td>
                        <td>
                            {{
                                        ($requestcode->product->sku ?? '').
                                        ' ('.($requestcode->product->product_name ?? '').')'
                                    }}
                        </td>
                        <td>{{ $requestcode->code_length }}</td>
                        <td>{{ $requestcode->total_no_of_code }}</td>
                        <td>{{ $requestcode->total_complete }}</td>

                        <td>{{ $requestcode->vendor->vendor_name ?? '' }}</td>
                        <td>{{ $requestcode->vendor->mobile ?? '' }}</td>
                        <td>{{ $requestcode->creator->name ?? '' }}</td>
                        <td>
                            <span class="badge {{ requestCodeStatusBdage($requestcode->status) }}">{{ camelCaseToWords(\App\Utilities\Enum\RequestCodeStatusEnum::getKey($requestcode->status)) }}</span>


                            @if ($requestcode->status === 3)
                            @if ($requestcode->print_status == 0)
                            <a role="button" class="badge badge badge-warning" title="Waiting for Print">
                                Waiting for Print
                            </a>
                            @elseif($requestcode->print_status == 1)
                            <a role="button" class="badge badge badge-warning" title="Waiting for Print">
                                Send to SCM
                            </a>
                            @elseif($requestcode->print_status == 2)
                            <a role="button" class="badge badge badge-success" title="Waiting for Print">
                                Print Completed
                            </a>
                            @endif
                            @endif



                        </td>
                        <td>{{ $requestcode->comments }}</td>
                        <td>
                            @isset($requestcode->file_path)
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-action py-0">
                                    {{-- <a href="{{ asset($requestcode->file_path) }}" download class="btn btn-link">Download</a> --}}
                                    <a href="{{ route('download_codes', $requestcode->id) }}" target="_blank" class="btn btn-link">Download</a>
                                </li>
                                <li class="list-group-item list-group-item-action py-0">
                                    Pass: {{ $requestcode->file_password }}
                                </li>
                            </ul>
                            @endisset
                        </td>
                        <td>
                            @if ($requestcode->status === 1)
                            @can('product-edit')
                            <a role="button" href="#" data-toggle="modal" data-target="#approval_modal" data-id='{{ $requestcode->id }}' data-status='{{ $requestcode->status }}' type="button" class="btn btn-outline-info btn-sm mr-2" title="Aprrove">
                                <i class="fa fa-shield"></i>
                            </a>
                            @endcan
                            @endif
                            @if ($requestcode->status === 1)
                            @can('product-edit')
                            <a role="button" href="#" data-toggle="modal" data-target="#vendor_modal" data-id='{{ $requestcode->id }}' data-vendor_id='{{ $requestcode->vendor_id }}' type="button" class="btn btn-outline-info btn-sm mr-2" title="Vendor">
                                <i class="fa fa-edit"></i>
                            </a>
                            @endcan
                            @endif

                            @if ($requestcode->status === 3)

                            @if ($requestcode->print_status === 0 && $requestcode->status === 3)
                            @can('product-edit')
                            <a role="button" href="#" data-toggle="modal" data-target="#print_modal" data-id='{{ $requestcode->id }}' data-status='{{ $requestcode->status }}' type="button" class="btn btn-outline-info btn-sm mr-2" title="Print">
                                <i class="fa fa-shield"></i>
                            </a>
                            @endcan
                            @endif

                            @endif
                            @can('product-edit')
                            @if($requestcode->print_status == 1)
                            <a role="button" href="{{ route('admin.ssgcodes.printed',$requestcode->id) }}" class="btn btn-outline-info btn-sm mr-2" title="Aprrove"> Printing Process
                                <i class="fa fa-print"></i>
                            </a>
                            @endif

                            @if($requestcode->print_status == 0 && $requestcode->status === 3)
                            <a role="button" href="{{ route('requestcodes.code_generator_delete',$requestcode->id) }}" class="btn btn-outline-info btn-sm mr-2" title="Aprrove">Delete
                                <i class="fa fa-trash"></i>
                            </a>

                            @endif
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

        </div>

        @include('/includes/paginate', ['paginator' => $requestcodes])
    </div>
</div>



{{-- approval modal --}}
<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="approval_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="approval_modal">Approval</h4>
            </div>
            <form action="{{ route('requestcodes.approval') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');">
                @csrf

                <div class="modal-body">
                    {!! Form::number('id', null, ['class' => 'form-control id', 'hidden'=>'hidden']) !!}
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Status</label>
                            <div class="col-sm-8">
                                {!! Form::select('status', ['' => 'Select Status'] + collect($cg_keys_values)->except(['1','3'])->toArray(), null, ['id'=> 'status','required'=> 'true','class' => 'form-control status']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="comment-section">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label control-label">Comments</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('comments', null, ['id'=> 'comments','class' => 'form-control comments','rows' => '4']) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button>
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- approval_modal -->

<div class="modal fade" id="print_modal" tabindex="-1" role="dialog" aria-labelledby="print_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="print_modal">Print Status</h4>
            </div>
            <form action="{{ route('requestcodes.print') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');">
                @csrf

                <div class="modal-body">
                    {!! Form::number('id', null, ['class' => 'form-control id', 'hidden'=>'hidden']) !!}
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Status</label>
                            <div class="col-sm-8">
                                {!! Form::select('print_status', ['' => 'Select One', '0' => 'Waiting for Print','1' => 'Send to SCM','2' => 'Print Completed'] , null , ['id'=> 'print_status','required'=> 'true','class' => 'form-control status']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="comment-section">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label control-label">Comments</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('comments', null, ['id'=> 'comments','class' => 'form-control comments','rows' => '4']) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button>
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="vendor_modal" tabindex="-1" role="dialog" aria-labelledby="vendor_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="approval_modal">Vendor</h4>
            </div>
            <form action="{{ route('requestcodes.vendor') }}" method="post" onsubmit="return confirm('Do you really change to vendor?');">
                @csrf

                <div class="modal-body">
                    {!! Form::number('id', null, ['class' => 'form-control id', 'hidden'=>'hidden']) !!}
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Vendor</label>
                            <div class="col-sm-8">
                                {!! Form::select('status', ['' => 'Select vendor'] + $parentvendors , null, ['id'=> 'status','required'=> 'true','class' => 'form-control vendor_id']) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button>
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- <div class="modal fade" id="vendor_modal" tabindex="-1" role="dialog" aria-labelledby="vendor_modal" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="vendor_modal">Vendor</h4>
            </div>
            <form action="{{ route('requestcodes.vendor') }}" method="post" >
                @csrf

                <div class="modal-body">
                    {!! Form::number('id', null, ['class' => 'form-control id' ]) !!}
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Vendor</label>
                            <div class="col-sm-8"> 
                                {!! Form::select('vendor_id', ['' => 'Select Vendor']+ $parentvendors, null, ['id' =>'vendor_id','class' => 'form-control vendor_id']) !!}
                                 
                            </div>
                        </div>
                    </div> 

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button>
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div> -->
@endsection
@push('custom_scripts')
<script type="text/javascript">
    $(function() {
        $('#approval_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var status = button.data('status');
            var modal = $(this);
            modal.find('.modal-body .id').val(id);
            //modal.find('.modal-body .status').val(status);
        });
    });

    $('#comment-section').hide();
    $('#status').on('change', function() {
        if (this.value == 4) {
            $('#comment-section').show(500);
            $("#comments").attr("required", true);

        } else {
            $('#comment-section').hide(500);
            $("#comments").attr("required", false);
        }
    });



    $(function() {
        $('#vendor_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var vendor_id = button.data('vendor_id');
            var modal = $(this);
            modal.find('.modal-body .id').val(id);
            modal.find('.modal-body .vendor_id').val(vendor_id);
        });
    });


    $(function() {
        $('#print_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var print_status = button.data('print_status');
            var modal = $(this);
            modal.find('.modal-body .id').val(id);
            modal.find('.modal-body .print_status').val(print_status);
        });
    });
</script>
</script>
@endpush