@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Notifications</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active" href="{{ route('admin.categories.index') }}">Notifications</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title --> 
<div class="card">

    <div class="header">
        <div class="row align-items-center">
            <div class="col-lg-10">
                <h2>
                    Notifications List
                    <span class="badge badge-info fill"> {{$notifications->total()}} </span>
                </h2>
            </div> 
            <div class="col-lg-2">
                <a  href="{{url('admin/notification/create')}}" ><span class="badge badge-info fill"> Create New</span></a>
            </div> 
        </div>
    </div>

    <div class="body pt-0">

        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>User Name</th> 
                        <th>Messages</th>  
                        <th>Type</th> 
                        <th>Created At</th>  
                    </tr>
                </thead>
                <tbody>
                    @if ($notifications->count()>0)
                        @foreach ($notifications as $data)
                            <tr>
                                <th> {{$data->user ? ($data->user ? $data->user->name : ''  ) : '' }} </th>  
                                <th> {{$data->messages }} </th>  
                                <th> {{$data->type }} </th>   
                                <th> {{$data->created_at->format('d-M-Y') }} </th>  
                            </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        @include('/includes/paginate', ['paginator' => $notifications])
    </div>
</div>
@endsection 