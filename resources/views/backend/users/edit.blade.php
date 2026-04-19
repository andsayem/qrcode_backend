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
                    <h2>User Edit </h2>
                </div> 
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">User Edit</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12 mx-auto">
                <div class="card">
                    <div class="card-body order-list">
                        <h3 class="header-title mt-0 mb-3">
                            {{-- <button type="button" class="btn btn-success waves-effect waves-light mr-2">
                                <i class="ti-map-alt"></i>
                            </button> --}}
                            Edit Users
                        </h3>
                        {{ Form::model($editModeData,['route' =>['admin.users.update',$editModeData->id ],'method' => 'PUT','files'=>'true','id'=>'users-form'])}}
                        
                        <div class="row">
                            
                        <div class="col-md-6">
                                <div class="form-group">
                                    <label for="roles" class="mb-2">Roles</label>
                                    {!! Form::select('roles[]', $roles,$userRole,['class'=>'select2 form-control mb-3 custom-select','placeholder' => 'SELECT ONE','id'=>'roles']);!!}
                                    @if($errors->has('roles'))
                                        <strong style="color:red;">{{ $errors->first('roles') }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="mb-2">Name</label>
                                    {!! Form::text( 'name', old('name'), $attributes = ['class'=>'form-control','id'=>'name','placeholder'=>'Enter Name']) !!}
                                    @if($errors->has('name'))
                                        <strong style="color:red;">{{ $errors->first('name') }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="mb-2">Email</label>
                                    {!! Form::text('email',  old('email'), ['class'=>'form-control','placeholder' => 'Enter Email','id'=>'email']);!!}
                                    @if($errors->has('email'))
                                        <strong style="color:red;">{{ $errors->first('email') }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="mb-2">Status </label>
                                    {!! Form::select('status', [1 => 'Active',0 => 'Inactive'],   old('status'), ['class'=>'select2 form-control mb-3 custom-select','placeholder' => 'SELECT ONE','id'=>'status']);!!}
                                    @if($errors->has('status'))
                                        <strong style="color:red;">{{ $errors->first('status') }}</strong>
                                    @endif
                                </div>
                            </div>  
                            <div class="form-group col-md-6">
                                <label for="password" class="mb-2">Password</label>
                                {!! Form::password('password', ['class'=>'form-control','placeholder' => 'Enter Password','id'=>'password']);!!}
                                @if($errors->has('password'))
                                <strong style="color:red;">{{ $errors->first('password') }}</strong>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="confirm_password" class="mb-2">Confirm Password</label>
                                {!! Form::password('confirm_password', ['class'=>'form-control','placeholder' => 'Enter Confirm Password','id'=>'confirm_password']);!!}
                                @if($errors->has('confirm_password'))
                                <strong style="color:red;">{{ $errors->first('confirm_password') }}</strong>
                                @endif
                            </div> 
                            
                            @if(auth()->user()->getRoleNames()[0] === 'Super Admin')
                            <div class="form-group col-md-6">
                                <label for="password" class="mb-2">Password</label>
                                {!! Form::password('password', ['class'=>'form-control','placeholder' => 'Enter Password','id'=>'password']);!!}
                                @if($errors->has('password'))
                                    <strong style="color:red;">{{ $errors->first('password') }}</strong>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="confirm_password" class="mb-2">Confirm Password</label>
                                {!! Form::password('confirm_password', ['class'=>'form-control','placeholder' => 'Enter Confirm Password','id'=>'confirm_password']);!!}
                                @if($errors->has('confirm_password'))
                                    <strong style="color:red;">{{ $errors->first('confirm_password') }}</strong>
                                @endif
                            </div>
                            @endif
                             
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Update</button>
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
    </div>
@endsection

@push('custom_script')

@endpush
