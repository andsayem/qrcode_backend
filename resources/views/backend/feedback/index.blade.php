@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Feedback</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active" href="{{ route('admin.categories.index') }}">Feedback</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title --> 
<div class="card">

    <div class="header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>
                    Feedback List
                    <span class="badge badge-info fill"> {{$feedback->total()}} </span>
                </h2>
            </div> 
        </div>
    </div>

    <div class="body pt-0">

        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>Technician Name</th> 
                        <th>Complain</th> 
                        <th>Picture</th> 
                        <th>Sku</th> 
                        <th>Created At</th> 
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    @if ($feedback->count()>0)
                        @foreach ($feedback as $data)
                            <tr>
                                <th> {{$data->technician ? ($data->technician->user_info ? $data->technician->user_info->name : ''  ) : '' }} </th>  
                                <th> {{$data->complain }} </th> 
                                <th> @if($data->picture) 
                                    <a href="{{ asset('uploads/feedback/'.$data->picture) }}" target="_blank"><img src="{{ asset('uploads/feedback/'.$data->picture) }}" alt="Complain image" width="80" height="50"></a> 
                                    <img src="">
                                    @endif 
                                </th>  
                                <th> {{$data->sku }} </th>   
                                <th> {{$data->created_at->format('d-M-Y') }} </th>   
                                <td>
                                    @can('category-edit')
                                        <a href="{{url('admin/feedback-reply',$data->id)}}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Reply"><i class="fa fa-reply"></i></a>
                                    @endcan
                                </td>
                            </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        @include('/includes/paginate', ['paginator' => $feedback])
    </div>
</div>
@endsection 