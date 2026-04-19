@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Technician Create</h2>
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
                <h2>User Information</h2>
            </div>

            <div class="body">
                {{ Form::open(['route' => 'admin.technician.store_technician', 'files'=>'true', 'id'=> 'basic-form', 'class'=> 'form-row']) }}

                {!! Form::hidden('roles','13', ['class'=>'form-control','placeholder' => 'roles','id'=>'roles']);!!} 
                <div class="form-group col-md-6">

                    <label for="name" class="mb-2">Name</label>
                    {!! Form::text( 'name', old('name'), $attributes = ['class'=>'form-control','id'=>'name','placeholder'=>'Enter Name']) !!}
                    @if($errors->has('name'))
                    <strong style="color:red;">{{ $errors->first('name') }}</strong>
                    @endif
                </div>
                <div class="form-group col-md-6">

                    <label for="email" class="mb-2">Email</label>
                    {!! Form::text('email', old('email'), ['class'=>'form-control','placeholder' => 'Enter Email','id'=>'email']);!!}
                    @if($errors->has('email'))
                    <strong style="color:red;">{{ $errors->first('email') }}</strong>
                    @endif
                </div>

                <div class="form-group col-md-6">

                    <label for="photo" class="mb-2">Photo</label>
                    <input type="file" id="photo" class="form-control mb-3" name="photo" accept="image/*" />
                    @if($errors->has('photo'))
                    <strong style="color:red;">{{ $errors->first('photo') }}</strong>
                    @endif
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
                <div class="header">
                    <h2>Technician Information</h2>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name" class="mb-2">Father's Name</label>
                        {!! Form::text( 'father_name', old('father_name'), $attributes = ['class'=>'form-control','id'=>'father_name','placeholder'=>'Father Name']) !!}
                        @if($errors->has('father_name'))
                        <strong style="color:red;">{{ $errors->first('father_name') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="mb-2">Permanent Address</label>
                        {!! Form::text( 'permanent_address', old('permanent_address'), $attributes = ['class'=>'form-control','id'=>'permanent_address','placeholder'=>'Write Your Permanent Address']) !!}
                        @if($errors->has('permanent_address'))
                        <strong style="color:red;">{{ $errors->first('permanent_address') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="mb-2">Current Address</label>
                        {!! Form::text( 'current_address', old('current_address'), $attributes = ['class'=>'form-control','id'=>'current_address','placeholder'=>'Write Your Current Address']) !!}
                        @if($errors->has('current_address'))
                        <strong style="color:red;">{{ $errors->first('current_address') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="mb-2">Date of birth</label>
                        {!! Form::text( 'birthday', old('birthday'), $attributes = ['class'=>'form-control','id'=>'birthday','placeholder'=>'Birth Date']) !!}
                        @if($errors->has('birthday'))
                        <strong style="color:red;">{{ $errors->first('birthday') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="mb-2">Blood group</label>
                        {!! Form::text( 'blood_group', old('blood_group'), $attributes = ['class'=>'form-control','id'=>'blood_group','placeholder'=>'Blood Group']) !!}
                        @if($errors->has('blood_group'))
                        <strong style="color:red;">{{ $errors->first('blood_group') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="mb-2">NID Number</label>
                        {!! Form::text( 'nid_number', old('nid_number'), $attributes = ['class'=>'form-control','id'=>'nid_number','placeholder'=>'*** *** ****']) !!}
                        @if($errors->has('nid_number'))
                        <strong style="color:red;">{{ $errors->first('nid_number') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="mb-2">Occupation</label>
                        {!! Form::text( 'occupation', old('occupation'), $attributes = ['class'=>'form-control','id'=>'occupation','placeholder'=>'Occupation']) !!}
                        @if($errors->has('occupation'))
                        <strong style="color:red;">{{ $errors->first('occupation') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="mb-2">Job Experience</label>
                        {!! Form::text( 'experience', old('experience'), $attributes = ['class'=>'form-control','id'=>'experience','placeholder'=>'Job Experience']) !!}
                        @if($errors->has('experience'))
                        <strong style="color:red;">{{ $errors->first('experience') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" class="mb-2">Education</label>
                        {!! Form::text( 'education', old('education'), $attributes = ['class'=>'form-control','id'=>'education','placeholder'=>'Education']) !!}
                        @if($errors->has('education'))
                        <strong style="color:red;">{{ $errors->first('education') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="mb-2">Reference/Dealer Name</label>
                        {!! Form::text( 'dealer_name', old('dealer_name'), $attributes = ['class'=>'form-control','id'=>'dealer_name','placeholder'=>'Dealer']) !!}
                        @if($errors->has('dealer_name'))
                        <strong style="color:red;">{{ $errors->first('dealer_name') }}</strong>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="mb-2">Reference/Dealer Code</label>
                        {!! Form::text( 'dealer_code', old('dealer_code'), $attributes = ['class'=>'form-control','id'=>'dealer_code','placeholder'=>'Dealer Code']) !!}
                        @if($errors->has('dealer_code'))
                        <strong style="color:red;">{{ $errors->first('dealer_code') }}</strong>
                        @endif
                    </div>
                </div>

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