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
                <h2>Technician Edit</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Technician</a></li>
                    <li class="breadcrumb-item active">Edit Technician</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body order-list">

                    {{ Form::model($technician, ['route' => ['admin.technicians.update', $technician->id], 'files' => true , 'method' => 'patch']) }}
                    <h3 class="header-title mt-0 mb-3">
                        User Information
                    </h3>
                    <div class="row">
                        {!! Form::hidden('user_id', $userinfo->id, ['class'=>'form-control','placeholder' => 'Enter Email','id'=>'email']);!!}

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="mb-2">Name</label>
                                {!! Form::text( 'name', $userinfo->name, $attributes = ['class'=>'form-control','id'=>'name','placeholder'=>'Enter Name']) !!}
                                @if($errors->has('name'))
                                <strong style="color:red;">{{ $errors->first('name') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="mb-2">Email/Phone</label>
                                {!! Form::text('email', $userinfo->email, ['class'=>'form-control','placeholder' => 'Enter Email','id'=>'email']);!!}
                                @if($errors->has('email'))
                                <strong style="color:red;">{{ $errors->first('email') }}</strong>
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="mb-2">Status </label>
                                {!! Form::select('status', [1 => 'Active', 0 => 'Inactive', 2 => 'Hold'], $userinfo->status, ['class'=>'select2 form-control mb-3 custom-select','placeholder' => 'SELECT ONE','id'=>'status']);!!}
                                @if($errors->has('status'))
                                <strong style="color:red;">{{ $errors->first('status') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6"> 
                            <label for="photo" class="mb-2">Photo</label>
                            <input type="file" id="photo" class="form-control mb-3" name="photo" accept="image/*" />
                            @if($errors->has('photo'))
                            <strong style="color:red;">{{ $errors->first('photo') }}</strong>
                            @endif
                        </div>

                    </div>
                    <h3 class="header-title mt-0 mb-3">
                        Edit Technician
                    </h3>
                    <div class="row">
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
                                {!! Form::text( 'experience', old('experience'), $attributes = ['class'=>'form-control','id'=>'experience','placeholder'=>'job experience']) !!}
                                @if($errors->has('experience'))
                                <strong style="color:red;">{{ $errors->first('experience') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name" class="mb-2">Education</label>
                                {!! Form::text( 'education', old('education'), $attributes = ['class'=>'form-control','id'=>'education','placeholder'=>'education']) !!}
                                @if($errors->has('education'))
                                <strong style="color:red;">{{ $errors->first('education') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="mb-2">Reference/Dealer Name</label>
                                {!! Form::text( 'dealer_name', old('dealer_name'), $attributes = ['class'=>'form-control','id'=>'dealer_name','placeholder'=>'dealer']) !!}
                                @if($errors->has('dealer_name'))
                                <strong style="color:red;">{{ $errors->first('dealer_name') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="mb-2">Reference/Dealer Code</label>
                                {!! Form::text( 'dealer_code', old('dealer_code'), $attributes = ['class'=>'form-control','id'=>'dealer_code','placeholder'=>'dealer code']) !!}
                                @if($errors->has('dealer_code'))
                                <strong style="color:red;">{{ $errors->first('dealer_code') }}</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="mb-2">FO Code</label>
                                {!! Form::text( 'fo_code', old('fo_code'), $attributes = ['class'=>'form-control','id'=>'fo_code','placeholder'=>'FO code']) !!}
                                @if($errors->has('fo_code'))
                                <strong style="color:red;">{{ $errors->first('fo_code') }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Update</button>
                            </div>
                        </div>
                    </div>

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