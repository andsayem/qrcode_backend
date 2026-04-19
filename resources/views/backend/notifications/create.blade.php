@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Notifications Create</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Notifications</a></li>
                    <li class="breadcrumb-item active">Notifications Create</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->


<div class="row">
    <div class="col-12 mx-auto">
        <div class="card">
            <div class="card-body order-list">
                <h4 class="header-title mt-0 mb-3">
                    Add New Notifications
                </h4>
                {{ Form::open(['route' => 'notification.store','id'=>'roles-form']) }}

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row ">
                            <label class="col-sm-4 col-form-label control-label">Message</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('messages', '', ['id' =>'desc','class' => 'form-control', 'rows' => 2, 'placeholder'=>'Message']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'desc'])
                            </div>
                        </div>
                    </div> 

                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Type</label>
                            <div class="col-sm-8">
                                {!! Form::select('type',['all'=>'All','single'=>'Single'], 'All', ['class' => 'form-control','id'=>'notification_type']) !!}
                                @include('/includes/validationmessages', ['field_name'=>'type'])
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-6" id="anotherDiv" style="display: none;">
                        <div class="form-group row required">
                            <label for="role" class="col-sm-4 col-form-label control-label">Technician</label>
                            <div class="col-sm-8">
                                <select class="form-control select2" name="user_id"> 
                                    <option value="0">Select User</option>
                                    @foreach ( $technician as $techn)                                        
                                    <option value="{{$techn->id}}">{{$techn->name}}</option>
                                    @endforeach
                                </select>                                
                            </div> 
                        </div>
                    </div>                    

                    <div class="col-md-12 text-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Save Now</button>
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
<script>
    let notificationTypeSelect = document.getElementById('notification_type');

    notificationTypeSelect.addEventListener('change', function() {
        // Get the selected value
        let selectedValue = notificationTypeSelect.value; 
        
        // Get the div to show/hide
        let anotherDiv = document.getElementById('anotherDiv');
        
        // // Check the selected value and show/hide the div accordingly
        if (selectedValue === 'single') {
            anotherDiv.style.display = 'block'; // Show the div
        } else {
            anotherDiv.style.display = 'none'; // Hide the div
        }
    });
</script>


@endsection
