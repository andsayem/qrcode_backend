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
                <h2>Technician Data</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Technician</a></li>
                    <li class="breadcrumb-item active">Technician Data</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body order-list">
                    <h3 class="header-title mt-0 mb-4">
                        {{-- <button type="button" class="btn btn-success waves-effect waves-light mr-2">
                                <i class="ti-agenda"></i>
                            </button> --}}
                        Technician Details 
                    </h3>

                    <table class="table table-striped valign-top mt-3">
                        <tbody>
                            <tr>
                                <td class="text-dark font-weight-bold">Name</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Father's name</td>
                                <td>{{ $technician->father_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Permanent address</td>
                                <td>{{ $technician->permanent_address }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Current address</td>
                                <td>{{ $technician->current_address }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Phone</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Date of birth</td>
                                <td>{{ $technician->birthday }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Blood group</td>
                                <td>{{ $technician->blood_group }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">NID Number</td>
                                <td>{{ $technician->nid_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Occupation</td>
                                <td>{{ $technician->occupation }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Job Experience</td>
                                <td>{{ $technician->experience }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Education</td>
                                <td>{{ $technician->education }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Reference/Dealer Name</td>
                                <td>{{ $technician->dealer_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-dark font-weight-bold">Reference/Dealer Code</td>
                                <td>{{ $technician->dealer_code }}</td>
                            </tr>

                        </tbody>
                    </table>
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