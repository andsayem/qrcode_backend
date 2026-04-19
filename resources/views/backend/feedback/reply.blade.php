@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Feedback Reply</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card"> 
            {!! Form::model($feedback, ['route' => ['admin.settings.update', $feedback->id], 'method' => 'patch']) !!} 
            <div class="table-responsive">
                <table class="table table-hover table-striped m-b-0 c_list">
                    <thead>
                        <tr>
                            <th>Technician Name</th> 
                            <th>{{$feedback->technician ? ($feedback->technician->user_info ? $feedback->technician->user_info->name : ''  ) : '' }} </th> 
                        </tr>
                        <tr>
                            <th>Complain</th> 
                            <th>{{$feedback->complain }}</th> 
                        </tr>
                        <tr>
                            <th>Picture</th> 
                            <th>@if($feedback->picture) 
                                <a href="{{ asset('uploads/feedback/'.$feedback->picture) }}" target="_blank"><img src="{{ asset('uploads/feedback/'.$feedback->picture) }}" alt="Complain image" width="80" height="50"></a> 
                                <img src="">
                                @endif </th> 
                        </tr>
                        <tr>
                            <th>Sku</th> 
                            <th>{{$feedback->sku }} </th> 
                        </tr>
                        <tr>
                            <th>Created At</th> 
                            <th>{{$feedback->created_at->format('d-M-Y') }}</th> 
                        </tr> 
                        <tr>
                            <th>Feedback Reply</th> 
                            <th><input type="text" name="feedback_reply" class="" value=""></th> 
                        </tr> 
                        <tr>
                            <th>Status</th> 
                            <th>
                                <select>
                                    <option value="0">Pending</option>
                                    <option value="1">approved</option>
                                    <option >Rejected</option>
                                </select>
                            </th> 
                        </tr> 
                    </thead> 
                </table>
            </div>
            
            <div class="card-footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('settings.index') }}" class="btn btn-default">Cancel</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
