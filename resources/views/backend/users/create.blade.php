@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

        <!-- start page title -->
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>User Create</h2>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">User Create</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h2>Add New User</h2>
                    </div>

                    <div class="body">
                        {{ Form::open(['route' => 'admin.users.store', 'files'=>'true', 'id'=> 'basic-form', 'class'=> 'form-row']) }}
                        
                        <div class="form-group col-md-6">

                                <label for="roles" class="mb-2">Roles</label>
                                {!! Form::select('roles', $roleList,   old('roles'), ['class'=>'select2 form-control mb-3 custom-select','id'=>'roles']);!!}
                                @if($errors->has('roles'))
                                    <strong style="color:red;">{{ $errors->first('roles') }}</strong>
                                @endif
                        </div>

                        <div class="form-group col-md-6">

                                <label for="name" class="mb-2">Name</label>
                                {!! Form::text( 'name', old('name'), $attributes = ['class'=>'form-control','id'=>'name','placeholder'=>'Enter Name']) !!}
                                @if($errors->has('name'))
                                    <strong style="color:red;">{{ $errors->first('name') }}</strong>
                                @endif
                        </div>
                        <div class="form-group col-md-6">

                                <label for="email" class="mb-2">Email</label>
                                {!! Form::text('email',  old('email'), ['class'=>'form-control','placeholder' => 'Enter Email','id'=>'email']);!!}
                                @if($errors->has('email'))
                                    <strong style="color:red;">{{ $errors->first('email') }}</strong>
                                @endif
                        </div>
                        {{-- <div class="form-group col-md-6">

                                <label for="contact" class="mb-2">Contact</label>
                                {!! Form::tel('contact',  old('contact'), ['class'=>'form-control','placeholder' => 'Enter Contact','id'=>'contact']);!!}
                                @if($errors->has('contact'))
                                    <strong style="color:red;">{{ $errors->first('contact') }}</strong>
                                @endif
                        </div>
                        <div class="form-group col-md-6">

                                <label for="photo" class="mb-2">Photo</label>
                                <input type="file" id="photo" class="form-control mb-3" name="photo" accept="image/*"/>
                                @if($errors->has('photo'))
                                    <strong style="color:red;">{{ $errors->first('photo') }}</strong>
                                @endif
                        </div> --}}
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
                        <!-- <div class="form-group col-md-6">

                                <label for="roles" class="mb-2">Roles</label>
                                {!! Form::select('roles', $roleList,   old('roles'), ['class'=>'select2 form-control mb-3 custom-select','id'=>'roles']);!!}
                                @if($errors->has('roles'))
                                    <strong style="color:red;">{{ $errors->first('roles') }}</strong>
                                @endif
                        </div> -->

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
@endsection
@push('custom_script')

@endpush
