@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<?php
$scs_keys =  App\Utilities\Enum\SSGCodeStatusEnum::getKeys();
$scs_values =  App\Utilities\Enum\SSGCodeStatusEnum::getValues();
$scs_keys_values =  App\Utilities\Enum\SSGCodeStatusEnum::getKeysValues();
// dd($scs_keys_values);
?>
<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>SSG Code</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">SSG Code</li>
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
                            <label for="code" class="mb-2">Code</label>
                            {!! Form::text('code', request('code') ?? '',['class'=>'form-control ', 'placeholder'=>'Code'])!!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="serial" class="mb-2">Serial</label>
                            {!! Form::text('serial', request('serial') ?? '',['class'=>'form-control ', 'placeholder'=>'Serial'])!!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mobile" class="mb-2">Mobile</label>
                            {!! Form::text('mobile', request('mobile') ?? '',['class'=>'form-control', 'placeholder'=>'Mobile'])!!}
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
                    SSG Code List </span>
                </h2>
            </div>
            <div class="col-lg-6 text-right">
                @can('ssg-code-upload')
                <a role="button" href="#" data-toggle="modal" data-target="#upload_modal" class="btn btn-sm btn-info px-3"><i class="fa fa-upload mr-2"></i> <span>Upload</span></a>
                @endcan
            </div>
        </div>
    </div>
    <div class="body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>Product SKU</th>
                        <th>Name</th>
                        <th>Serial</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Total Used</th>
                        <th>Uploaded By</th>
                        <th>Uploaded IP</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Updated At</th>
                        <th>Lat - Long</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($ssgcodes->count()>0)
                    @foreach ($ssgcodes as $i => $ssgcode)
                    <tr>
                        <td>
                            {{ ($ssgcode->product->sku ?? ''). ' ('.($ssgcode->product->product_name ?? '').')' }}
                        </td>
                        <td>
                            {{ $ssgcode->user->name ?? '' }}
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
                        <td>{{ $ssgcode->address }}</td>
                        <td>{{ $ssgcode->updated_at ? $ssgcode->updated_at->format('d-m-Y') : '' }}</td>
                        <td>{{ $ssgcode->lat }} - {{ $ssgcode->long }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

        </div>

    </div>
</div>


@endsection


@push('custom_scripts')

@endpush