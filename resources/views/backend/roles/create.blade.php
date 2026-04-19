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
                    <h2>Role Create</h2>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Role Create</li>
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
                            Add New Role
                        </h3>
                        {{ Form::open(['route' => 'admin.roles.store','id'=>'roles-form']) }}

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="mb-2">Role Name</label>
                                    {!! Form::text( 'name', old('name'), $attributes = ['class'=>'form-control','id'=>'name','placeholder'=>'Enter Role Name']) !!}
                                    @if($errors->has('name'))
                                        <strong style="color:red;">{{ $errors->first('name') }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="panel panel-default card-view">
                                    <div class="panel-heading">
                                        <div class="pull-left">
                                            <h6 class="panel-title txt-dark">Permission</h6>
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                @if($modules->count())
                                                    <div class="row">
                                                        @foreach($modules as $module)
                                                            <div class="card col-md-3 ">
                                                                <div class="panel panel-primary card-view">
                                                                    <div class="panel-heading">
                                                                        <div class="pull-left">
                                                                            <h6 class="panel-title txt-light">{{$module->title}}</h6>
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                    </div>
                                                                    <div class="panel panel-default card-view custom-view-result-panel">
                                                                        <div class="panel-body">
                                                                            @if(isset($module->permissions) && $module->permissions->count() > 0)
                                                                                @foreach($module->permissions as $permission)
                                                                                    <div class="form-group">
                                                                                        <div class="checkbox checkbox-success">
                                                                                            {{Form::checkbox('permission[]',$permission->id,false, ['id' =>'permission'. $permission->id])}}
                                                                                            <label for="{{'permission'.$permission->id}}">{{$permission->name}} </label>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="mb-2">Permission</label>
                                    <br>
                                    @foreach($permission as $value)
                                        <label>
                                            {!! Form::checkbox( 'permission[]',$value->id, false, $attributes = ['class'=>'']) !!}
                                            {{ $value->name }}
                                        </label>
                                        <br/>
                                    @endforeach
                                    @if($errors->has('permission'))
                                        <strong style="color:red;">{{ $errors->first('permission') }}</strong>
                                    @endif
                                </div>
                            </div>--}}
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
    </div>
@endsection
