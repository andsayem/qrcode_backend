@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')
        <!-- start page title -->
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>Users</h2>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                                    <label for="name" class="mb-2">Name</label>
                                    {!! Form::text('name', $request['name'] ?? '',['class'=>'form-control ', 'autocomplete'=>'off',  'placeholder'=>'Enter Name'])!!}
                                </div>
                            </div>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp</label>
                                    <div>
                                        <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i>Filter</button>
                                        <a type="button" class="btn btn-warning mr-2" href="{{ route('admin.users.index') }}"><i class="fa fa-refresh mr-1"></i> <span>Reset</span></a>
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
        <!--end row-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="header">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h2>
                                    User List
                                    <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                                </h2>
                            </div>
                            <div class="col-lg-6 text-right">
                                @can('product-create')
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="body pt-0">

                        <div class="table-responsive">
                            <table class="table table-hover table-striped m-b-0 c_list" >
                                <thead>
                                <tr>
                                    {{-- <th>Photo</th> --}}
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($tableData->count())
                                    @foreach($tableData as $key => $row)
                                        <tr>
                                            {{-- <td>
                                                @if($row->photo)
                                                    <img src="{{ asset('/storage/'.str_replace('user','user/thumbnails',$row->photo)) }}" alt="Image">
                                                @else
                                                    <img src="{{ asset('no-image.png') }}" alt="Image">
                                                @endif
                                            </td> --}}
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>
                                                @if(!empty($row->getRoleNames()))
                                                    @foreach($row->getRoleNames() as $v)
                                                        <label class="badge badge-success">{{ $v }}</label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td style='white-space: nowrap'>
                                              
                                                <a href="{{ route('admin.users.show',$row->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="View">
                                                    <i class="fa fa-eye font-12"></i>
                                                </a>
                                                @can('user-edit')
                                                    <a href="{{ route('admin.users.edit', $row->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>

                        </div>
                        <nav aria-label="Page navigation example" class="m-3">
                            <span>Showing {{ $tableData->appends($request)->firstItem() }} to {{ $tableData->appends($request)->lastItem() }} of {{ $tableData  ->appends($request)->total() }} entries</span>
                            <div>{{ $tableData->appends($request)->render() }}</div>
                        </nav>
                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

@endsection
