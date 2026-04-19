@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Channel</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Channel</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

            <!--end card-body-->
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2>
                            Channel List
                            <span class="badge badge-info fill"> {{ count($channels) }}</span>
                        </h2>
                    </div>
                    <div class="col-lg-6 text-right">
                        @can('category-create')
                        <a href="{{ route('admin.channels.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($channels as $i => $channel)
                            <tr>
                                <td>{{ $channel->name }}</td>
                                <td>
                                    @include('includes.status', ['status' => [['key' => 'Active', 'value' => 1, 'class'=> 'badge-success'], ['key' => 'Inactive', 'value' => 0, 'class'=> 'badge-danger']], 'selected'=> $channel->status])
                                </td>
                                <td> 
                                        <a href="{{ route('admin.channel_settings.update', $channel->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a> 
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>


</div>
@endsection