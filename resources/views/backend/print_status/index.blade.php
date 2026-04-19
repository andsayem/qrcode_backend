@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')
@section('content')
<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Code Print Status List</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Code Print Status List</li>
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
                            <label class="mb-2">Status</label>
                            {!! Form::select('is_print', ['' => 'Select Status','1' => 'Print','0' => 'Not Print'], request('is_print'), ['class' => 'form-control select2']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i>Filter</button>
                                <a type="button" class="btn btn-warning mr-2" href="{{ route('admin.code-print-status-list.index') }}"><i class="fa fa-refresh mr-1"></i> <span>Reset</span></a>
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
                    Code Print Status List
                    <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                </h2>
            </div>
            <div class="col-lg-6 text-right">
                @can('code-print-status-list-download')
                    <a href="{{ $download_url }}" class="btn btn-sm btn-success px-3"><i class="fa fa-download mr-2"></i> Download</a>
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
                        <th>Serial</th>
                        <th>Code</th>
                        <th>Print Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($tableData->count())
                        @foreach ($tableData as $key =>  $row)
                            <tr>
                                <td>
                                    {{ ($row->product->sku ?? ''). ' ('.($row->product->product_name ?? '').')' }}
                                </td>
                                <td>{{ $row->serial }}</td>
                                <td>{{ substr_replace($row->final_unique_code,'********',3,8) }}</td>
                                <td>{{ $row->is_print === 1 ? 'Printed' : 'Not Print' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        @include('/includes/paginate', ['paginator' => $tableData])
    </div>
</div>
@endsection

