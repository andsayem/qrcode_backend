@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')
@section('content')
@php
    $get_country = request()->filled('country') ? request('country') : '';
    $get_division = request()->filled('division') ? request('division') : '';
    $get_district = request()->filled('district') ? request('district') : '';
    $get_thana = request()->filled('thana') ? request('thana') : '';
    $get_area = request()->filled('area') ? request('area') : '';
    $get_from_date = request()->filled('from_date') ? request('from_date') : '';
    $get_db_pay_status = request()->filled('db_pay_status') ? request('db_pay_status') : '';
    $get_to_date = request()->filled('to_date') ? request('to_date') : ''; 
    $get_sap_code = request()->filled('sap_code') ? request('sap_code') : ''; 
@endphp
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Redeem</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Redeem</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="header">
                <h2>Filter </h2>
            </div>
            <div class="body pt-0">
                {{Form::open(['method' => 'get'])}}
                <div class="row">

                   <div class="col-md-2">
                        <div class="form-group">
                            <label for="name" class="mb-2">Country</label>
                            <select name="country" id="country-select" class="select2 form-control mb-3 custom-select" onchange="loadDivision(this)">
                                <option value="">Select One </option>
                                <?php foreach ($countries as $value) { ?>
                                <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_country ? 'selected' : '' }}>
                                    {{ $value['name'] }}
                                </option>
                                <?php } ?>
                            </select>                             
                        </div>
                    </div>
                   <div class="col-md-2">
                        <div class="form-group">
                            <label for="name" class="mb-2">Division</label>
                            <select name="division" id="division-select" class="select2 form-control mb-3 custom-select" onchange="loadDistrict(this)">
                                <option value="">Select One </option>
                                <?php foreach ($divisions as $value) { ?>
                                <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_division ? 'selected' : '' }} >
                                    {{ $value['name'] }}
                                </option>
                                <?php } ?>
                            </select>                             
                        </div>
                    </div>
                   <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="name" class="mb-2">Thana</label>
                            <select name="thana" id="thana-select" class="select2 form-control mb-3 custom-select">
                                <option value="">Select One </option>
                                @foreach ($thanas as $value)
                                    <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_thana ? 'selected' : '' }}>
                                        {{ $value['thana'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-2">

                        <div class="form-group">
                            <label for="name" class="mb-2">Area</label>
                            <select name="area" id="area-select" class="select2 form-control mb-3 custom-select">
                                <option value="">Select One </option>
                                @foreach ($areas as $value)
                                    <option  value="{{ $value['id'] }}" {{ $value['id'] == $get_area ? 'selected' : '' }}>
                                        {{ $value['area'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from_date" class="mb-2">SAP</label>
                            <div class="input-group mb-3">
                                <select class="input-group mb-3 form-control select2" name="sap_code" id="sap_code">
                                    <option value="">Select SAP Code</option>
                                    @foreach($sap_code as $sap)
                                    <option value="{{$sap->sender_sap_code}}" {{ $sap->sender_sap_code == $get_sap_code ? 'selected' : '' }}>
                                        {{$sap->sender_sap_code}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="name" class="mb-2">Paid Status (DB)</label>
                            <select name="db_pay_status" id="db_pay_status-select" class="select2 form-control mb-3 custom-select" onchange="loadDivision(this)">
                                <option value=""  >All</option>        
                                <option value="0"  {{ $get_db_pay_status  == 0 ? 'selected' : '' }} >Unpaid</option>    
                                <option value="1"  {{ $get_db_pay_status  == 1 ? 'selected' : '' }} >Paid</option> 
                            </select>                             
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from_date" class="mb-2">From Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('from_date', $get_from_date,['class'=>'form-control', 'id'=>'from_date', 'autocomplete'=>'off',  'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="to_date" class="mb-2">To Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('to_date', $get_to_date,['class'=>'form-control', 'id'=>'to_date', 'autocomplete'=>'off',  'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i></button>
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
<!-- Summary Section -->
<!-- <div class="row">
    <div class="col-md-6">
        <div class="card premium-card">
            <div class="header">
                <h2>Total Points</h2>
            </div>
            <div class="body">
                <h3 id="total-points" class="premium-text"></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card premium-card">
            <div class="header">
                <h2>Total Amount</h2>
            </div>
            <div class="body">
                <h3 id="total-amount" class="premium-text"></h3>
            </div>
        </div>
    </div>
</div> -->


<!-- End Summary Section -->

<div class="row">
    <div class="col-12">
        <div class="card">

            <!--end card-body-->
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2>
                            Redeem List
                            <span class="badge badge-info fill"> {{ count($items) }}</span>
                        </h2>
                    </div>
                    <div class="col-lg-6 text-right">
                   
                    <a role="button" href="#" data-toggle="modal" data-target="#upload_modal" class="btn btn-sm btn-info px-3"><i class="fa fa-upload mr-2"></i> <span>Upload</span></a>
                    <!-- <a href="{{ route('admin.redeem.redeem_paid_download') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Download (DB Unpaid)</span></a>   -->

                    <a href="{{ url('admin/redeem_paid_download') . '?country=' . request('country') . '&division=' . request('division') . '&district=' . request('district') . '&thana=' . request('thana') . '&area=' . request('area') . '&sap_code=' . request('sap_code') . '&db_pay_status=' . request('db_pay_status') . '&from_date=' . request('from_date') . '&to_date=' . request('to_date')  }}" class="btn btn-sm px-3 btn-info">
                        <i class="fa fa-plus"></i> <span>Download (DB Unpaid)</span>
                    </a>
                    </div>
                </div>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>Technician</th>
                                <th>Redeem Point</th>
                                <th class="text-right">Amount (BDT)</th>
                                <th > SAP Code</th>
                                <!-- <th>Sender Name</th> -->
                                <th>Status</th>
                                <th>DB Status</th>
                                <th>Paid Date</th>
                                <th>Redeem Date</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                            @foreach ($items as $i => $item)
                            <?php 
                            $jsonDecode = json_decode($item->sender_info);
                            ?>
                            <tr>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->point }} </td>
                                <td class="text-right" >{{ number_format(floatval($item->amount), 2) }} Tk.</td>
                                <td>{{ $item->sender_sap_code }}</td>
                                <!-- <td>{{ $jsonDecode ? $jsonDecode->display_name : '' }}</td> -->
                                <td>
                                    @include('includes.status', ['status' => [['key' => 'Paid', 'value' => 1, 'class'=> 'badge-success'], ['key' => 'Unpaid', 'value' => 0, 'class'=> 'badge-danger']], 'selected'=> $item->status])
                                </td>
                                <td>  
                                    @include('includes.status', ['status' => [['key' => 'Paid', 'value' => 1, 'class'=> 'badge-success'], ['key' => 'Unpaid', 'value' => 0, 'class'=> 'badge-danger']], 'selected'=> $item->db_pay_status])
                                </td>
                                <td> {{ $item->paid_at}}</td>
                                <td> {{ $item->created_at->format('d M Y')}}</td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->

    <div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="upload_modal" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="upload_modal">Payment Paid List Upload</h4>
            </div>
            <form action="{{ route('admin.redeem.redeem_db_paid_list') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');"  enctype="multipart/form-data" >
                @csrf

                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Payment Paid List (DB) </label>
                            <div class="col-sm-8">
                                {!! Form::file('csv_file',['class' => 'form-control','required' => true ]) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button> 
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
@push('custom_scripts')
<script> 
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
    function loadDivisions(selectElement) {
        var selectedValue = selectElement.value;   
        $('#division-select').empty();
        $('#division-select').trigger('change');
        $.ajax({
            url: '{{ url('api/divisions') }}',
            type: 'GET',
            dataType: 'json',
            data: {
                country_id: selectedValue
            },
            success: function(response) {                
                // Add the new options for divisions
                $('#division-select').append($('<option>', {
                        value: '',
                        text: 'Select Division'
                }));
                $.each(response.data, function(index, division) { 
                    $('#division-select').append($('<option>', {
                        value: division.id,
                        text: division.name
                    }));
                }); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error:", textStatus, errorThrown); // Log error if occurred
                // alert('Error occurred while fetching divisions.');
            }
        });
    }

    function loadDistricts(selectElement) {
        var selectedValue = selectElement.value;
        $('#district-select').empty();
        $('#district-select').trigger('change');
        $.ajax({
            url: '{{ url('api/district') }}',
            type: 'GET',
            dataType: 'json',
            data: {
                division_id: selectedValue
            },
            success: function(response) {                
                // Add the new options for district
                $('#district-select').append($('<option>', {
                        value: '',
                        text: 'Select District'
                }));
                $.each(response.data, function(index, division) { 
                    $('#district-select').append($('<option>', {
                        value: division.id,
                        text: division.district
                    }));
                }); 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error:", textStatus, errorThrown); // Log error if occurred
                // alert('Error occurred while fetching divisions.');
            }
        });
    }
    $(document).ready(function() {
        // AJAX request to fetch the total points and total amount
        let country = $('#country-select').val();
        let division = $('#division-select').val();
        let district = $('#district-select').val();
        let thana = $('#thana-select').val();
        let area = $('#area-select').val();
        let db_pay_status = $('#db_pay_status-select').val();
        
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let sap_code = $('#sap_code').val();
       // redeemSummary(country, division, district, thana, area, db_pay_status , from_date, to_date, sap_code);

    }); 
    // function redeemSummary(country_id, division_id, district_id,thana_id,area_id, db_pay_status ,start_date,end_date,sapcode) {
    //     $.ajax({
    //         url: '{{ route('admin.redeem.getRedeemSummary') }}',
    //         type: 'GET',
    //         dataType: 'json',
    //         data: {
    //             country: country_id,
    //             division: division_id,
    //             district: district_id,
    //             thana: thana_id,
    //             area: area_id,
    //             db_pay_status : db_pay_status ,
    //             from_date: start_date,
    //             to_date: end_date,
    //             sap_code: sapcode,
    //            // user_id: $('#user_id').val()
    //         },
    //         success: function(response) {
    //             $('#total-points').text(response.totalPoints);
    //             $('#total-amount').text(response.totalAmount + ' TK');
    //         },
    //         error: function() {
    //             alert('Error occurred while fetching the summary.');
    //         }
    //     });
    // }

    function loadThana(districtId) {
        // AJAX request to fetch areas based on the selected district
        // Clear the current options in the area select box
        $('#thana-select').empty();
        $('#thana-select').append('<option value="">Select One</option>');
        $('#thana-select').trigger('change');
        $('#area-select').empty(); 
        $('#area-select').append('<option value="">Select One</option>');
        $('#area-select').trigger('change');
        $.ajax({
            url: '{{ route('admin.users.getSsforcethana') }}',
            type: 'GET',
            dataType: 'json',
            data: {
                district_id: districtId
            },
            success: function (response) {
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

    // Event listener for the district select box
    $(document).ready(function () {
        $('#district-select').change(function () {
            var selectedDistrict = $(this).val();
            loadThana(selectedDistrict);
        });
    });


    function loadArea(thanaId, districtId) {
        // AJAX request to fetch areas based on the selected "thana" and "district" 

        $('#area-select').empty(); 
        $('#area-select').append('<option value="">Select One</option>');
        $('#area-select').trigger('change');
        $.ajax({
            url: '{{ route('admin.users.getSsforcearea') }}',
            type: 'GET',
            dataType: 'json',
            data: {
                thana_id: thanaId,
                district_id: districtId
            },
            success: function (response) { 
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
    // Event listener for the district select box
    $(document).ready(function () {
        $('#thana-select').change(function () {
            var selectedThana = $(this).val();
            var selectedDistrict = $('#district-select').val();
            loadArea(selectedThana, selectedDistrict);
        });
    });


</script>
@endpush