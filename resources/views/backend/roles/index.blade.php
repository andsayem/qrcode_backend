@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>Roles</h2>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="header">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h2>
                                    Role List
                                    <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                                </h2>
                            </div>
                            <div class="col-lg-6 text-right">
                                @can('role-create')
                                    <a href="{{ route('admin.roles.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped m-b-0 c_list" >
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($tableData->count())
                                    @foreach($tableData as $key => $row)
                                        <tr>
                                            <td>{{ $row->name }}</td>
                                            <td>

                                                <a href="{{ route('admin.roles.show',$row->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="View">
                                                    <i class="fa fa-eye text-success font-16"></i>
                                                </a>
                                                @can('role-edit')
                                                    <a href="{{ route('admin.roles.edit', $row->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a>
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
    </div>

@endsection
