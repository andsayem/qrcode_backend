@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')


@section('content')
    @php
        $get_status = request()->filled('status') ? request('status') : '';
        $get_name = request()->filled('name') ? request('name') : '';
        $get_district = request()->filled('district') ? request('district') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : '';
        $get_from_date = request()->filled('from_date') ? request('from_date') : '';
        $get_to_date = request()->filled('to_date') ? request('to_date') : '';
        $get_sap_code = request()->filled('sap_code') ? request('sap_code') : '';
    @endphp
    <div class="content">

        <!-- Start Content-->

        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-sm-12">
                        <h2>Dashboard V.7 </h2>
                    </div>
                    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                        <ul class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item active">Dashboard </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="header">
                            <h2>Filter</h2>
                        </div>
                        <div class="body pt-0">
                            <form method="GET" id="filterFrom" action="" accept-charset="UTF-8">
                                <input type="hidden" name="status" value="{{ app('request')->input('status') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">District</label>
                                            <select name="district" id="district-select"
                                                class="select2 form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php foreach ($district as $value) { ?>
                                                <option value="{{ $value['id'] }}"
                                                    {{ $value['id'] == $get_district ? 'selected' : '' }}>
                                                    {{ $value['district'] }}
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Thana</label>
                                            <select name="thana" id="thana-select"
                                                class="select2 form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                @foreach ($thanas as $value)
                                                    <option value="{{ $value['id'] }}"
                                                        {{ $value['id'] == $get_thana ? 'selected' : '' }}>
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
                                            <select name="area" id="area-select"
                                                class="select2 form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                @foreach ($areas as $value)
                                                    <option value="{{ $value['id'] }}"
                                                        {{ $value['id'] == $get_area ? 'selected' : '' }}>
                                                        {{ $value['area'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp</label>
                                            <div>
                                                <button type="submit" id="filter" class="btn btn-success mr-2"><i
                                                        class="fa fa-search mr-1"></i>Filter</button>
                                                {{-- <a type="button" class="btn btn-warning mr-2"
                                            href="{{ route('admin.users.technician_user') }}"><i class="fa fa-refresh mr-1"></i>
                                            <span>Reset</span></a> --}}
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


            <div class="row">

                <div class="col-lg-3">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">

                                <a href="{{ route('admin.users.technician_user', 'status=1') }}" type="button"
                                    class="btn btn-outline-info btn-sm mr-2" title="View">
                                    <i class="fa fa-users widget-icon"></i>
                                </a>
                            </div>
                            <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Technicians</h5>
                            <h3 class="mt-3 mb-3"><span id="techniciansCount">0</span><span class="sub-head-line"
                                    id="techniciansCountPending">(Pending)</span></h3>
                            <p class="mb-0 text-muted" id="techniciansCount_Since">
                                <span class="text-success me-2"><i class="fa fa-arrow-up"></i> <span
                                        id="percentageSpan">12%</span></span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <a href="{{ route('admin.redeem.index') }}" type="button"
                                    class="btn btn-outline-info btn-sm mr-2" title="View">
                                    <i class="fa fa-registered widget-icon"></i>
                                </a>
                            </div>
                            <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Redeem</h5>
                            <h3 class="mt-3 mb-3"><span id="redeemCount">0</span></h3>
                            <p class="mb-0 text-muted" id="redeemCount_Since">
                                <span class="text-success me-2"><i class="fa fa-arrow-up"></i> <span
                                        id="percentageredeem">0%</span></span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>
                </div>



                <div class="col-lg-3">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">


                                <a href="{{ url('admin/ssgcodes?code=&serial=&mobile=&status=1') }}" type="button"
                                    class="btn btn-outline-info btn-sm mr-2" title="View">
                                    <i class="fa fa-qrcode widget-icon"></i>
                                </a>
                            </div>
                            <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Scanned code</h5>
                            <h3 class="mt-3 mb-3"><span id="qrcodeCount">0</span></h3>
                            <p class="mb-0 text-muted" id="qrcodeCount_Since">
                                <span class="text-success me-2"><i class="fa fa-arrow-up"></i> <span
                                        id="percentageqrcode">0%</span></span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>
                </div>



                <div class="col-sm-3">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">

                                <a href="{{ route('admin.redeem.index') }}" type="button"
                                    class="btn btn-outline-info btn-sm mr-2" title="View">
                                    <i class="fa fa-cubes widget-icon"></i>
                                </a>
                            </div>
                            <h5 class="text-muted fw-normal mt-0" title="Growth">Points</h5>
                            <h3 class="mt-3 mb-3"><span id="totalPoints">0</span></h3>
                            <p class="mb-0 text-muted"  >
                                <span class="text-success me-2"><i class="fa fa-arrow-up"></i> <span
                                        id="percentagepoint">0%</span></span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div>
                    </div>
                </div>



            </div>

            

            <div class="row">
                

                <div class="col-sm-7">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0" title="Monthly Redeem">Monthly Redeem</h5>
                            <div id="chartContainer" class="apex-charts mb-0 mt-4" dir="ltr"></div>

                            <div class="text-center">
                                <ul class="list-inline chart-detail-list mb-0">
                                    <li class="list-inline-item">
                                        <h6 class="text-info"><i class="fa fa-circle me-1"></i>Redeem Amount</h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- end card-->
                </div>

                <div class="col-sm-5">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0" title="Monthly Redeem">Monthly Redeem</h5>
                            <div id="pieChartContainer" class="apex-charts mb-0 mt-4" dir="ltr"></div>

                            <div class="text-center">
                                <ul class="list-inline chart-detail-list mb-0">
                                    <li class="list-inline-item">
                                        <h6 class="text-info"><i class="fa fa-circle me-1"></i>Redeem Amount</h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- end card-->
                </div><!-- end col -->
            </div> 
            <!-- container -->
        </div>
    </div>
@endsection
@extends('backend.layouts.footer')

@push('custom_scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {

            $.ajax({
                url: '{{ route('admin.getChartData') }}',
                type: 'GET',
                success: function(data) {
                    var monthNames = ["January", "February", "March", "April", "May", "June", "July",
                        "August", "September", "October", "November", "December"
                    ];

                    var options = {
                        series: data.chartData,
                        chart: {
                            type: 'pie',
                            height: 250,
                            toolbar: {
                                show: false
                            }
                        },
                        labels: data.chartLabels.map(function(value) {
                            return monthNames[value - 1];
                        }),
                        colors: ['#36a2eb', '#ff6384', '#ffcd56', '#4bc0c0', '#9966ff'],
                        // Other options...
                    };

                    // Initialize and render the pie chart
                    var pieChart = new ApexCharts(document.querySelector('#pieChartContainer'),
                    options);
                    pieChart.render();
                }
            });


            $.ajax({
                url: '{{ route('admin.getChartData') }}',
                type: 'GET',
                success: function(data) {
                    var options = {
                        series: [{
                            name: 'Redeem Amount',
                            data: data.chartData
                        }],
                        chart: {
                            type: 'line',
                            height: 250,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#36a2eb'],
                        stroke: {
                            width: 3,
                            curve: 'smooth'
                        },
                        xaxis: {
                            categories: data.chartLabels,
                            labels: {
                                style: {
                                    colors: '#8e8da4'
                                },
                                formatter: function(value) {
                                    // Convert month number to month name
                                    var monthNames = ["January", "February", "March", "April",
                                        "May", "June", "July", "August", "September",
                                        "October", "November", "December"
                                    ];
                                    return monthNames[value - 1];
                                }
                            }
                        },
                        // Other options...
                    };

                    // Initialize and render the chart
                    var chart = new ApexCharts(document.querySelector('#chartContainer'), options);
                    chart.render();
                }
            });



        });
    </script>
    <script>
        $(document).ready(function() {
            $('#district-select').change(function() {
                let districtid = $('#district-select').val();
                loadThana(districtid);
            });

            $('#thana-select').change(function() {
                let selectedThana = $(this).val();
                let selectedDistrict = $('#district-select').val();
                loadArea(selectedThana, selectedDistrict);
            });
            technicians();
            redeem();
            scannedCode();
            points();
        });

        function redeem() {
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            $.ajax({
                url: '{{ route('admin.getRedeemData') }}',
                type: 'GET',
                data: {
                    district,
                    thana,
                    area,
                    from_date,
                    to_date
                },
                success: function(data) {
                    $('#redeemCount').text(data.redeemCount);
                    $('#percentageredeem').text(data.percentage + '%');

                    if (data.percentage > 0) {
                        $('#percentageredeem').siblings('i').removeClass('fa-arrow-down').addClass(
                            'fa-arrow-up');
                        $('.text-success i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                    } else if (data.percentage < 0) {
                        $('#percentageredeem').siblings('i').removeClass('fa-arrow-up').addClass(
                            'fa-arrow-down');
                        $('.text-success i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                    }

                }
            });
        }

        function scannedCode() {
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            $.ajax({
                url: '{{ route('admin.getQrcodeData') }}',
                type: 'GET',
                data: {
                    district,
                    thana,
                    area,
                    from_date,
                    to_date
                },
                success: function(data) {
                    $('#qrcodeCount').text(data.qrcodeCount);

                    var percentageSpan = $('#percentageqrcode');
                    percentageSpan.text(data.percentage + '%');

                    var iconElement = $('#qrcodeCount_Since .text-success i');
                    if (data.percentage > 0) {
                        percentageSpan.removeClass('text-danger').addClass('text-success');
                        iconElement.removeClass('fa-arrow-down').addClass('fa-arrow-up');
                        iconElement.css('color', 'green'); // Change icon color to green
                    } else if (data.percentage < 0) {
                        percentageSpan.removeClass('text-success').addClass('text-danger');
                        iconElement.removeClass('fa-arrow-up').addClass('fa-arrow-down');
                        iconElement.css('color', 'red'); // Change icon color to red
                    }
                }
            });
        }

        function technicians() {
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            $.ajax({
                url: '{{ route('admin.getTechniciansData') }}',
                type: 'GET',
                data: {
                    district,
                    thana,
                    area,
                    from_date,
                    to_date
                },
                success: function(data) {
                    $('#techniciansCount').text(data.techniciansCount);
                    $('#techniciansCountPending').text('(' + data.pendingCount + ' Pending)');
                    $('#percentageSpan').text(data.percentage + '%');

                    var percentageSpan = $('.card-body span.text-success');
                    if (data.percentage > 0) {
                        percentageSpan.removeClass('text-danger').addClass('text-success');
                        $('.text-success i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                    } else if (data.percentage < 0) {
                        percentageSpan.removeClass('text-success').addClass('text-danger');
                        $('.text-success i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                    }
                }
            });
        }

        function points() {
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            $.ajax({
                url: '{{ route('admin.getRedeemData') }}',
                type: 'GET',
                data: {
                    district,
                    thana,
                    area,
                    from_date,
                    to_date
                },
                dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    $('#totalPoints').text(response.redeemPoint);
                    // alert(response.redeemPoint)
                    var percentagepoint = $('#percentagepoint');
                    percentagepoint.text(response.percentage + '%');

                    if (response.percentage > 0) {
                        $('#percentagepoint').siblings('i').removeClass('fa-arrow-down').addClass(
                        'fa-arrow-up');
                        $('.text-success i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                    } else if (response.percentage < 0) {
                        $('#percentagepoint').siblings('i').removeClass('fa-arrow-up').addClass(
                        'fa-arrow-down');
                        $('.text-success i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                    }
                },
                error: function() {
                    alert('Error occurred while fetching the summary.');
                }
            });
        }

        function loadThana(districtId) {
            // alert(districtId)
            // AJAX request to fetch areas based on the selected district
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
                success: function(response) {
                    // Clear the current options in the area select box
                    $('#thana-select').empty();
                    // Add the new options for areas
                    response.forEach(function(area) {
                        $('#thana-select').append('<option value="' + area['id'] + '">' + area[
                            'thana'] + '</option>');
                    });
                },
                error: function() {
                    // alert('Error occurred while fetching areas.');
                }
            });
        }

        function loadArea(thanaId = null, districtId = null) {
            let selectedThana = thanaId ?? $('#thana-select').val();
            let selectedDistrict = districtId ?? $('#district-select').val();
            $('#area-select').empty();
            $('#area-select').append('<option value="">Select One</option>');
            $('#area-select').trigger('change');
            // alert(selectedThana);
            // alert(selectedDistrict);
            if (selectedThana && selectedDistrict) {
                $.ajax({
                    url: '{{ route('admin.users.getSsforcearea') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        thana_id: selectedThana,
                        district_id: selectedDistrict
                    },
                    success: function(response) {
                        // Clear the current options in the area select box
                        $('#area-select').empty();

                        // Add the new options for areas
                        response.forEach(function(area) {
                            $('#area-select').append('<option value="' + area['id'] + '">' + area[
                                'area'] + '</option>');
                        });
                    },
                    error: function() {
                        // alert('Error occurred while fetching areas.');
                    }
                });
            }
        } 

        let filter = document.getElementById('filterFrom');
        filter.addEventListener('submit', function(event) {
            event.preventDefault();
            points();
            technicians();
            redeem();
            scannedCode();
            return false;

        }, false);
        // document.getElementById("filter").addEventListener("click", function(){ alert("Hello World!"); }); 
        // filter.lisen()
    </script>
@endpush
