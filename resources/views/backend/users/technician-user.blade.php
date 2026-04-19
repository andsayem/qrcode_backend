@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')
@section('content')

@php
    $get_status = request()->filled('status') ? request('status') : '';
    $get_name = request()->filled('name') ? request('name') : '';
    $get_country = request()->filled('country') ? request('country') : '';
    $get_division = request()->filled('division') ? request('division') : '';
    $get_district = request()->filled('district') ? request('district') : '';
    $get_thana = request()->filled('thana') ? request('thana') : '';
    $get_area = request()->filled('area') ? request('area') : '';
    $get_from_date = request()->filled('from_date') ? request('from_date') : '';
    $get_to_date = request()->filled('to_date') ? request('to_date') : ''; 
    $get_sap_code = request()->filled('sap_code') ? request('sap_code') : ''; 
@endphp

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            @if($_GET['status']=='1')
                <h2>Approved Technician</h2>
            @elseif($_GET['status']=='0')
            <h2>Pending Technician</h2>
            @endif
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Technician</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="header">
                <h2>Filter</h2>
            </div>
            <div class="body pt-0">
                {{Form::open(['method' => 'get'])}}
                <input type="hidden" name="status" value="{{ app('request')->input('status') }}">
                <div class="row">
                <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">Name Or Phone Number</label>
                            {!! Form::text('name', $request['name'] ?? '',['class'=>'form-control ',
                            'autocomplete'=>'off', 'placeholder'=>'Enter Name or Phone Number'])!!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">Country</label>
                            <select name="country" id="country-select" class="select2 form-control mb-3 custom-select" onchange="loadDivision(this)">
                                <option value="">Select One </option>
                                <?php foreach ($countries as $country) { ?>
                                    <option  value="{{ $country['id'] }}" {{ $country['id'] == $get_country ? 'selected' : '' }}>
                                        {{ $country['name'] }}
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">Division</label>
                            <select name="division" id="division-select" class="select2 form-control mb-3 custom-select" onchange="loadDistrict(this)">
                                <option value="">Select One </option> 
                                <?php foreach ($divisions as $division) { ?>
                                    <option  value="{{ $division['id'] }}" {{ $division['id'] == $get_division ? 'selected' : '' }}>
                                        {{ $division['name'] }}
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">District</label>
                            <select name="district" id="district-select" class="select2 form-control mb-3 custom-select">
                                <option value="">Select One </option>
                                <?php foreach ($district as $value) { ?>
                                    <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_district ? 'selected' : '' }}>
                                        {{ $value['district'] }}
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">Thana</label>
                            <select name="thana" id="thana-select" class="select2 form-control mb-3 custom-select">
                                <option value="">Select One </option>
                                @foreach ($thanas as $value)
                                    <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_thana ? 'selected' : '' }}>
                                        {{ $value['thana'] }}
                                    </option>
                                @endforeach
                                <!-- Options for areas will be loaded dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">Area</label>
                            <select name="area" id="area-select" class="select2 form-control mb-3 custom-select">
                                <option value="">Select One </option>
                                @foreach ($areas as $value)
                                    <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_area ? 'selected' : '' }}>
                                        {{ $value['area'] }}
                                    </option>
                                @endforeach
                                <!-- Options for areas will be loaded dynamically -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from_date" class="mb-2">From Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('from_date', $get_from_date, ['class'=>'form-control',
                                'autocomplete'=>'off', 'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker',
                                'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="to_date" class="mb-2">To Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('to_date', $get_to_date, ['class'=>'form-control',
                                'autocomplete'=>'off', 'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker',
                                'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i
                                        class="fa fa-search mr-1"></i>Filter</button>
                                <a type="button" class="btn btn-warning mr-2"
                                    href="{{ route('admin.users.technician_user') }}"><i class="fa fa-refresh mr-1"></i>
                                    <span>Reset</span></a>
                            </div>
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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-8 col-sm-12">
                        @if($_GET['status']=='1')
                            <h2>Approved Technician List</h2>
                            <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                        @elseif($_GET['status']=='0')
                        <h2>Pending Technician List</h2>
                        <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                        @elseif($_GET['status']=='2')
                        <h2>Hold Technician List</h2>
                        <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                        @endif
                    </div>
                    <!-- <div class="col-lg-6">
                        <h2>
                            Technician List
                            <span class="badge badge-info fill"> {{ $tableData->total() }}</span>
                        </h2>
                    </div> -->
                    <div class="col-lg-6 text-right">
                        <a href="{{ route('admin.users.new_technician') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                        @php  
                        $technician_download = 'status='.$get_status.'&name='.$get_name.'&district='.$get_district.'&thana='.$get_thana.'&area='.$get_area.'&from_date='.$get_from_date.'&to_date='.$get_to_date;
                        @endphp
                        <a href="{{ route('admin.users.technician_download', $technician_download) }}"
                        class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Download</span></a>
                    </div>
                </div>
            </div>

            <div class="body pt-0">
                <form action="{{url('admin/technicians-bulk-active')}}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped m-b-0 c_list">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-center">User Info</th>
                                    <th colspan="4" class="text-center">Verification</th>
                                    <th colspan="4" class="text-center">Point</th>
                                    <th class="text-center">Point</th>
                                    <th rowspan="1">Action</th>
                                </tr>
                                <tr>
                                    {{-- <th>Registration</th> --}}
                                    <th>Reg Date</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>IP</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Phone</th>
                                    <th>FO</th>
                                    <th>TSM</th>
                                    <th>Point</th>
                                    <th rowspan="2">Total </th>
                                    <th rowspan="2">Current </th>
                                    <th rowspan="2">Redeem <small>(Currency)</small></th>
                                    <th rowspan="2">Process </th>
                                    <th >
                                        @if($get_status == '0')
                                        <label><input type="checkbox" id="checkAll"> Check All</label> 
                                        <button type="submit" class="btn btn-sm px-3 btn-info">Bulk Approve</button> 
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @if($tableData->count())
                                @foreach($tableData as $key => $row)
                                <tr>
                                    <td>{{ $row->created_at->format('Y-m-d') }} </td>
                                    <td>
                                        @if($row->profile_image)
                                        <img src="{{ asset('storage/profile/'.$row->profile_image) }}" style="width: 200px;"
                                            alt="Image ss">
                                        @else
                                        <img src="{{ asset('assets/images/user.jpg') }}" alt="Image">
                                        @endif
                                    </td>
                                    <td>{{ $row->name }} </td>
                                    <td>{{ $row->ip_address }} </td>
                                    <td>{{ $row->email }}</td>
                                    <td>
                                        @if(!empty($row->getRoleNames()))
                                        @foreach($row->getRoleNames() as $v)
                                        <label class="badge badge-success">{{ $v }}</label>
                                        @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->phone_verification_status ==1)
                                        <label class="badge badge-success">Yes</label>
                                        @elseif($row->phone_verification_status ==0)
                                        <label class="badge badge-warning">No</label>
                                        @endif
                                    </td>
                                    <td>
                                        <i>{{$row->technician ? $row->technician->fo_name : ''}} <b>{{$row->technician?
                                                $row->technician->fo_code : ''}}</b> </i>
                                        @if($row->technician && $row->technician->fo_verify ==1)
                                        <label class="badge badge-success">Yes</label>
                                        @elseif($row->technician && $row->technician->fo_verify ==0)
                                        <label class="badge badge-warning">No</label>
                                        @endif
                                    </td>
                                    <td>
                                        <i>{{$row->technician ? $row->technician->tsm_name : ''}} <b>{{ $row->technician ?
                                                $row->technician->tsm_code :''}}</b> </i>
                                        @if($row->technician && $row->technician->tsm_verify ==1)
                                        <label class="badge badge-success">Yes</label>
                                        @elseif($row->technician && $row->technician->tsm_verify ==0)
                                        <label class="badge badge-warning">No</label>
                                        @endif
                                    </td>

                                    <td>
                                        <i>{{$row->technician ? $row->technician->point_name : ''}} <b>{{ $row->technician ?
                                                $row->technician->point_code :''}}</b> </i>
                                        @if($row->technician && $row->technician->point_verify ==1)
                                        <label class="badge badge-success">Yes</label>
                                        @elseif($row->technician && $row->technician->point_verify ==0)
                                        <label class="badge badge-warning">No</label>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $row->technician ? $row->technician->total_point : 0 }}
                                    </td>
                                    <td>
                                        {{ $row->technician ? $row->technician->current_point : 0 }}
                                    </td>
                                    <td>
                                        {{ $row->technician ? $row->technician->total_redeem_value : 0 }} 
                                    </td>
                                    <td>
                                        {{ $row->technician ? $row->technician->pending_point : 0 }}
                                    </td>

                                    <td style='white-space: nowrap'>

                                        @if(app('request')->input('status') ==1)
                                        @if(( $row->getRoleNames()->toArray()[0] == 'Super Admin' || 'Admin') &&
                                        ($row->phone_verification_status ==0))
                                        <a href="{{ route('admin.technicians.phone_verification',$row->id) }}" type="button"
                                            class="btn btn-outline-info btn-sm mr-2" title="Manual Verification">
                                            <i class="fa fa-check-square"></i>
                                        </a>
                                        @endif
                                        @endif
                                        @if(app('request')->input('status') ==0)
                                        @if($row->getRoleNames()->toArray()[0] == 'Technician'|| 'Super Admin')
                                        <input type="checkbox" name="approval_value[]" value="{{$row->id}}"  class="checkbox btn btn-outline-info btn-sm mr-2">
                                        <a href="{{ route('admin.technicians.active',$row->id) }}" type="button"
                                            class="btn btn-outline-info btn-sm mr-2" title="Approval">
                                            <i class="fa fa-check"></i>
                                        </a>
                                        @endif
                                        @endif

                                        @if($row->getRoleNames()->toArray()[0] == 'Admin'||'Super Admin')
                                        <a href="{{ route('admin.technicians.show',$row->id) }}" type="button"
                                            class="btn btn-outline-info btn-sm mr-2" title="View">
                                            <i class="fa fa-eye font-12"></i>
                                        </a>
                                        @endif
                                        @if($row->getRoleNames()->toArray()[0] == 'Technician' || 'Super Admin')
                                        <a href="{{ route('admin.technicians.edit', $row->id) }}" type="button"
                                            class="btn btn-outline-info btn-sm mr-2" title="Edit"><i
                                                class="fa fa-edit"></i></a>
                                        @endcan
                                        <a href="{{ route('admin.reports.index','mobile='. $row->email) }}" type="button"
                                            class="btn btn-outline-info btn-sm mr-2" title="Reports"><i
                                                class="fa fa-file"></i></a>

                                        <a target="_blank" href="{{ route('admin.redeem.index','user_id='. $row->id) }}"
                                            type="button" class="btn btn-outline-info btn-sm mr-2" title="Redeem"><i
                                                class="fa fa-trophy"></i>
                                        </a>

                                    </td>
                                </tr>
                                @endforeach
                                @endif 
                            </tbody>
                        </table>
                    </div>
                </form>
                <nav aria-label="Page navigation example" class="m-3">
                    <span>Showing {{ $tableData->appends($request)->firstItem() }} to {{
                        $tableData->appends($request)->lastItem() }} of {{ $tableData ->appends($request)->total() }}
                        entries</span>
                    <div>{{ $tableData->appends($request)->render() }}</div>
                </nav>
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->
<style>
    img {
        width: 30px !important;
        border-radius: 9px;
        height: 41px;
    }
</style>
@endsection
@push('custom_scripts')

<script>
    // Get the "Check All" checkbox
    let checkAllCheckbox = document.getElementById('checkAll');

    // Get all checkboxes with class 'checkbox'
    let checkboxes = document.querySelectorAll('.checkbox');

    // Function to check or uncheck all checkboxes
    function toggleCheckboxes() {
      checkboxes.forEach(function(checkbox) {
        checkbox.checked = checkAllCheckbox.checked;
      });
    }

    // Attach the function to the "Check All" checkbox change event
    checkAllCheckbox.addEventListener('change', toggleCheckboxes);
  </script>
<script> 

    // $(document).ready(function() {
    //     // AJAX request to fetch the total points and total amount
    //     let district = $('#district-select').val();
    //     let thana = $('#thana-select').val();
    //     let area = $('#area-select').val();
    //     let from_date = $('#from_date').val();
    //     let to_date = $('#to_date').val();
    //     let sap_code = $('#sap_code').val();
    //     loadThana(district);
    //     //redeemSummary(district, thana, area, from_date, to_date, sap_code);

    // }); 

    // Event listener for the district select box
    $(document).ready(function () {
        // let district = $('#district-select').val();
        // let thana = $('#thana-select').val();
        // let area = $('#area-select').val();
        // let from_date = $('#from_date').val();
        // let to_date = $('#to_date').val();
        // let sap_code = $('#sap_code').val();
        $('#district-select').change(function () { 
            let districtid = $('#district-select').val(); 
            loadThana(districtid);
        });

        $('#thana-select').change(function () {
            let selectedThana = $(this).val();
            let selectedDistrict = $('#district-select').val(); 
            loadArea(selectedThana, selectedDistrict);
        });
    });


    function loadDivision(selectElement) {
        var selectedValue = selectElement.value;  
        $('#division-select').empty(); 
        $('#division-select').append('<option value="">Select One</option>');
        $('#division-select').trigger('change');
        if(selectedValue){
            $.ajax({
                url: '{{ route('admin.users.getSsforceDivisions') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    country_id: selectedValue
                },
                success: function (response) {
                    // Clear the current options in the area select box
                    $('#division-select').empty();
                    $('#division-select').append('<option value="">Select One</option>');
                    // Add the new options for areas
                    response.forEach(function (area) {
                        $('#division-select').append('<option value="' + area['id'] + '">' + area['name'] + '</option>');
                    });
                },
                error: function () {
                    // alert('Error occurred while fetching areas.');
                }
            });
        }
    }
    function loadDistrict(selectElement) {
        var selectedValue = selectElement.value;  
        $('#district-select').empty(); 
        $('#district-select').append('<option value="">Select One</option>');
        $('#district-select').trigger('change');

        $('#thana-select').empty(); 
        $('#thana-select').append('<option value="">Select One</option>');
        $('#thana-select').trigger('change');
        if(selectedValue){
            $.ajax({
                url: '{{ route('admin.users.getSsforceDistrict') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    division_id: selectedValue
                },
                success: function (response) {
                    // Clear the current options in the area select box
                    $('#district-select').empty();
                    $('#district-select').append('<option value="">Select One</option>');
                    $('#district-select').trigger('change');
                    // Add the new options for areas
                    response.forEach(function (area) {
                        $('#district-select').append('<option value="' + area['id'] + '">' + area['district'] + '</option>');
                    });
                },
                error: function () {
                    // alert('Error occurred while fetching areas.');
                }
            });
        }
    }
    function loadThana(districtId) {
        // AJAX request to fetch areas based on the selected district
        $('#thana-select').empty(); 
        $('#thana-select').trigger('change');
        $('#area-select').empty();  
        $('#area-select').trigger('change');
        if(districtId){
            $.ajax({
                url: '{{ route('admin.users.getSsforcethana') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    district_id: districtId
                },
                success: function (response) {
                    // Clear the current options in the area select box
                    $('#thana-select').empty();
                    $('#thana-select').append('<option value="">Select One</option>');
                    // Add the new options for areas
                    response.forEach(function (area) {
                        $('#thana-select').append('<option value="' + area['id'] + '">' + area['thana'] + '</option>');
                    });
                },
                error: function () {
                    // alert('Error occurred while fetching areas.');
                }
            });
        }
    }


    function loadArea(thanaId=null, districtId=null) {
        let selectedThana = thanaId ?? $('#thana-select').val();
        let selectedDistrict = districtId ?? $('#district-select').val();
        $('#area-select').empty(); 
        $('#area-select').append('<option value="">Select One</option>');
        $('#area-select').trigger('change');
        if(thanaId && districtId){
            $.ajax({
                url: '{{ route('admin.users.getSsforcearea') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    thana_id: thanaId,
                    district_id: districtId
                },
                success: function (response) {
                    // Clear the current options in the area select box
                    $('#area-select').empty();
                    $('#area-select').append('<option value="">Select One</option>');
                    // Add the new options for areas
                    response.forEach(function (area) {
                        $('#area-select').append('<option value="' + area['id'] + '">' + area['area'] + '</option>');
                    });
                },
                error: function () {
                    // alert('Error occurred while fetching areas.');
                }
            });
        }
    } 
</script>
@endpush