@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Vendor</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active" href="{{ route('admin.vendors.index') }}">Vendor</li>
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
                            <label for="name" class="mb-2">Vendor</label>
                            {!! Form::select('vendor_name', ['' => 'Select Vendor']+ $parentvendors, request('vendor_name'), ['id' =>'vendor_name','class' => 'form-control select2']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i>Filter</button>
                                <a type="button" class="btn btn-warning mr-2" href="{{ route('admin.vendors.index') }}"><i class="fa fa-refresh mr-1"></i> <span>Reset</span></a>
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
                    Vendor List
                    <span class="badge badge-info fill"> {{ $vendors->total() }}</span>
                </h2>
            </div>
            <div class="col-lg-6 text-right">
                @can('vendor-create')
                    <a href="{{ route('admin.vendors.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                @endcan
            </div>
        </div>
    </div>

    <div class="body pt-0">

        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Contact Person</th>
                        <th>Mobile</th>
                        <th>address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($vendors->count()>0)
                        @foreach ($vendors as $i =>  $vendor)
                            <tr>
                                <td>{{$vendor->vendor_name}}</td>
                                <td>{{  $vendor->contact_person}}</td>
                                <td>{{$vendor->mobile}}</td>
                                <td>{{$vendor->address}}</td>
                                <td>
                                    @can('vendor-edit')
                                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>

        @include('/includes/paginate', ['paginator' => $vendors])
    </div>
</div>
@endsection
