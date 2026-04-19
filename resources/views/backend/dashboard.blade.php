@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')


@section('content')
    @php
        $get_status = request()->filled('status') ? request('status') : '';
        $get_name = request()->filled('name') ? request('name') : '';
        $get_country = request()->filled('country') ? request('country') : '';
        $get_channels = request()->filled('channel') ? request('channel') : '';
        $get_district = request()->filled('district') ? request('district') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : ''; 
        $get_year = request()->filled('year') ? request('year') : '';
    @endphp
    <div class="content">

        <!-- Start Content-->

        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-sm-12">
                        <h2>Dashboard</h2>
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
                                            <label for="name" class="mb-2">Country</label>
                                            <select name="country" id="country-select"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php foreach ($country as $value) { ?>
                                                <option value="{{ $value['id'] }}" >
                                                    {{ $value['name'] }}
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Division</label>
                                            <select name="division" id="division-select"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php  
                                                foreach ($divisions as $value) { ?>
                                                <option value="{{ $value['id'] }}" >
                                                    {{ $value['name'] }}
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">District</label>
                                            <select name="district" id="district-select"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php  
                                                foreach ($district as $value) { ?>
                                                <option value="{{ $value['id'] }}" >
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
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                @foreach ($thanas as $value)
                                                    <option value="{{ $value['id'] }}" >
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
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                @foreach ($areas as $value)
                                                    <option value="{{ $value['id'] }}" >
                                                        {{ $value['area'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="channel" class="mb-2">Channel</label>
                                            <select name="channel" id="channel-select"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php foreach ($channels as $value) { ?>
                                                <option value="{{ $value['id'] }}" >
                                                    {{ $value['name'] }}
                                                </option>
                                                <?php } ?>
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
                            <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Scanned code </h5>
                            <h3 class="mt-3 mb-3"><span id="qrcodeCount">0</span></h3>
                           
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
                            
                        </div>
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
                            <form method="GET" id="chartFilterFrom"  onsubmit="formSubmitChart(event)" action="" accept-charset="UTF-8"> 
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Country</label>
                                            <select name="country" id="chart-country-select" onchange="chartCountrySelect()"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php foreach ($country as $value) { ?>
                                                <option value="{{ $value['id'] }}"
                                                    {{ $value['id'] == $get_country ? 'selected' : '' }}>
                                                    {{ $value['name'] }}
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Division</label>
                                            <select name="country" id="chart-division-select" onchange="chartDivisionSelect()"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">District</label>
                                            <select name="district" id="chart-district-select" onchange="chartDistrictSelect()"
                                                class=" form-control mb-3 custom-select">
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

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Thana</label>
                                            <select name="thana" id="chart-thana-select"
                                                class="  form-control mb-3 custom-select" onchange="chartThanaSelect()">
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

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Area</label>
                                            <select name="area" id="chart-area-select"
                                                class=" form-control mb-3 custom-select">
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
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name" class="mb-2">Year</label>
                                            <select name="year" id="yearFilter"
                                                class=" form-control mb-3 custom-select">
                                                <option value="">Select One </option>
                                                <?php 
                                                    $currentYear = date('Y'); 
                                                    for ($year = 2022; $year <= $currentYear; $year++) {
                                                    ?>
                                                <option value="{{ $year }}" {{ $year == $get_year ? 'selected' : '' }}>
                                                    {{ $year . PHP_EOL }}
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp</label>
                                            <div>
                                                <button type="submit"  class="btn btn-success mr-2"> Filter</button> 
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

                <div class="col-sm-7" style="display: none">
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

                <div class="col-sm-6">
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
                </div> 
                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0" title="Monthly Redeem">Verified Product</h5>
                            <div id="productPieChartContainer" class="apex-charts mb-0 mt-4" dir="ltr"></div> 
                            <div class="text-center">
                                <ul class="list-inline chart-detail-list mb-0">
                                    <li class="list-inline-item">
                                        <h6 class="text-info"><i class="fa fa-circle me-1"></i>Redeem Amount</h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div> 
                </div> 
                <div class="col-sm-8">
                    <div class="card widget-flat">
                        <div class="card-body"> 
                            <h5 class="text-muted fw-normal mt-0" title="Month-wise Earn vs Settlement Report">Month-wise Earn vs Settlement Report</h5>
                            <div id="lineEarnVSsettlement" class="apex-charts mb-0 mt-4" dir="ltr"></div> 
                        </div>
                    </div> 
                </div>  
            </div> 
            <!-- container -->
        </div>
    </div>
@endsection
@extends('backend.layouts.footer')

@push('custom_scripts') 
    <script>
        
        // Function to initialize and render the line chart
        function initializeLineChart(chartData, chartLabels) {
            let options = {
                series: [{
                    name: 'Redeem Amount',
                    data: chartData
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
                    categories: chartLabels,
                    labels: {
                        style: {
                            colors: '#8e8da4'
                        },
                        formatter: function(value) {
                            // Convert month number to month name
                            let monthNames = ["January", "February", "March", "April",
                                "May", "June", "July", "August", "September",
                                "October", "November", "December"
                            ];
                            return monthNames[value - 1];
                        }
                    }
                },
                // Other options...
            };

            // Initialize and render the line chart
            var chart = new ApexCharts(document.querySelector('#chartContainer'), options);
            chart.render();
        }

        // Function to initialize and render the pie chart
        function monthlyRedeemPieChart(chartData, chartLabels) {
            let monthNames = ["January", "February", "March", "April", "May", "June", "July",
                "August", "September", "October", "November", "December"
            ];

            let options = {
                series: chartData,
                chart: {
                    type: 'pie',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                labels: chartLabels.map(function(value) {
                    return monthNames[value - 1];
                }),
                colors: ['#36a2eb', '#ff6384', '#ffcd56', '#4bc0c0', '#9966ff'],
                // Other options...
            };

            // Initialize and render the pie chart
            let pieChart = new ApexCharts(document.querySelector('#pieChartContainer'), options);
            pieChart.render();
        } 

        function verifiedProductPieChart(chartData, chartLabels) {
            let monthNames = ["January", "February", "March", "April", "May", "June", "July",
                "August", "September", "October", "November", "December"
            ];

            let options = {
                series: chartData,
                chart: {
                    type: 'pie',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                labels: chartLabels.map(function(value) {
                    return monthNames[value - 1];
                }),
                colors: ['#36a2eb', '#ff6384', '#ffcd56', '#4bc0c0', '#9966ff'],
                // Other options...
            };

            // Initialize and render the pie chart
            let pieChart = new ApexCharts(document.querySelector('#productPieChartContainer'), options);
            pieChart.render();
        }

        function earnVsSettlement(earnings, settlements, labels){
            // Sample data for the chart
                // const chartData = {
                //     earnings: [5000, 8000, 12000, 9000, 15000, 11000, 16000, 20000, 18000, 25000, 22000, 28000],
                //     settlements: [4000, 7500, 10000, 8000, 13000, 10000, 15000, 18000, 16000, 21000, 19000, 24000],
                //     months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                // };
                // console.log('labels', labels);
                let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
                // Create the chart
                const options = {
                chart: {
                    type: 'line'
                },
                series: [
                    {
                    name: 'Earnings',
                    data: earnings
                    },
                    {
                    name: 'Settlements',
                    data: settlements
                    }
                ],
                xaxis: {
                    categories: months
                },
                title: {
                    text: 'Month-wise Earn vs Settlement Report'
                }
                };
            
                // Render the chart
                const chart = new ApexCharts(document.getElementById('lineEarnVSsettlement'), options);
                chart.render();
        }
        monthlyRedeemPieChart({{ json_encode($monthly_redeem['chartData']) }},  {{ json_encode($monthly_redeem['chartLabels']) }});
        verifiedProductPieChart({{ json_encode($verified_product['chartData']) }},  {{ json_encode($verified_product['chartLabels']) }});
        earnVsSettlement({{ json_encode($earn_settlement['earnings']) }}, {{ json_encode($earn_settlement['settlements']) }}, `{{ json_encode($earn_settlement['labels']) }}`);
        function chartCountrySelect(){
            let countryId = $('#chart-country-select').val(); 
            loadDivisions(countryId, 'chart-division-select');
        }
        function chartDivisionSelect(){
            let divisionId = $('#chart-division-select').val(); 
            loadDistrict(divisionId, 'chart-district-select' );
        }
        function chartDistrictSelect(){
            let districtid = $('#chart-district-select').val(); 
            loadThana(districtid, 'chart-thana-select');
        }
        function chartThanaSelect(){
            let selectedThana = $('#chart-thana-select').val();   
            loadArea(selectedThana, 'chart-thana-select','chart-area-select');
        }
        $(document).ready(function() {  
    
            $('#country-select').change(function() {
                let countryId = $('#country-select').val(); 
                loadDivisions( countryId, 'division-select') 
            });
            $('#division-select').change(function() {
                let divisionId = $('#division-select').val(); 
                loadDistrict( divisionId, 'district-select' ) 
            });
            $('#district-select').change(function() {
                let districtid = $('#district-select').val();
                loadThana(districtid, 'thana-select' );
            });

            $('#thana-select').change(function() {
                // let selectedThana = $(this).val();
                let selectedThana = $('#thana-select').val(); 
                loadArea(selectedThana, 'thana-select','area-select');
            }); 

            technicians();
            scannedCode();
            points();
            redeem();
        });

        function redeem() {
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let channel = $('#channel-select').val();
            $.ajax({
                url: '{{ route('admin.getRedeemData') }}',
                type: 'GET',
                data: {
                    district,
                    thana,
                    area,
                    from_date,
                    to_date,
                    channel
                },
                success: function(data) {
                    $('#redeemCount').text(data.redeemCount);
                    
 

                }
            });
        }

        function scannedCode() {
            let country = $('#country-select').val();
            let division = $('#division-select').val();
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let channel = $('#channel-select').val();
            $.ajax({
                url: '{{ route('admin.getQrcodeData') }}',
                type: 'GET',
                data: {
                    country,
                    division,
                    district,
                    thana,
                    area,
                    from_date,
                    to_date,
                    channel
                },
                success: function(data) {
                    $('#qrcodeCount').text(data.qrcodeCount);

                 
                }
            });
        }

        function technicians() {
            let country = $('#country-select').val();
            let division = $('#division-select').val();
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            $.ajax({
                url: '{{ route('admin.getTechniciansData') }}',
                type: 'GET',
                data: {
                    country,
                    division,
                    district,
                    thana,
                    area,
                    from_date,
                    to_date
                },
                success: function(data) {
                    $('#techniciansCount').text(data.techniciansCount);
                    $('#techniciansCountPending').text('(' + data.pendingCount + ' Pending)');
                 
                }
            });
        }

        function points() {
            let country = $('#country-select').val();
            let division = $('#division-select').val();
            let district = $('#district-select').val();
            let thana = $('#thana-select').val();
            let area = $('#area-select').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let channel = $('#channel-select').val();
            $.ajax({
                url: '{{ route('admin.getQrPointData') }}',
                type: 'GET',
                data: {
                    country,
                    division,
                    district,
                    thana,
                    area,
                    from_date,
                    to_date,
                    channel
                },
                dataType: 'json',
                success: function(response) { 

                    console.log();
                    $('#totalPoints').text(response.value);
 
                },
                error: function() {
                    // alert('Error occurred while fetching the summary.');
                }
            });
        }

        function loadDivisions(countryId, divisionID) { 
            $('#'+divisionID).empty();
            $('#'+divisionID).append('<option value="">Select One</option>');
            $('#'+divisionID).trigger('change'); 
            $.ajax({
                url: '{{ route('admin.users.getSsforceDivisions') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    country_id: countryId
                },
                success: function(response) { 
                    // Add the new options for areas
                    response.forEach(function(district) {
                        $('#'+divisionID).append('<option value="' + district['id'] + '">' + district['name'] + '</option>');
                    });
                },
                error: function() {
                    // alert('Error occurred while fetching areas.');
                }
            });
        }

        function loadDistrict(divisionID, districtID ) { 
            $('#'+districtID).empty();
            $('#'+districtID).append('<option value="">Select One</option>');
            $('#'+districtID).trigger('change'); 
            if(divisionID){
                $.ajax({
                    url: '{{ route('admin.users.getSsforceDistrict') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        division_id: divisionID
                    },
                    success: function(response) {
                        response.forEach(function(district) {
                            $('#'+districtID).append('<option value="' + district['id'] + '">' + district['district'] + '</option>');
                        });
                    },
                    error: function() {
                        // alert('Error occurred while fetching areas.');
                    }
                });
            }
        }

        function loadThana(districtId, thanaID ) {  

            $('#'+thanaID).empty();
            $('#'+thanaID).append('<option value="">Select One</option>');
            $('#'+thanaID).trigger('change'); 
            if(districtId){
                $.ajax({
                    url: '{{ route('admin.users.getSsforcethana') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        district_id: districtId
                    },
                    success: function(response) { 
                        response.forEach(function(area) {
                            $('#'+thanaID).append('<option value="' + area['id'] + '">' + area['thana'] + '</option>');
                        });
                    },
                    error: function() {
                        // alert('Error occurred while fetching areas.');
                    }
                });
            }
        }

        function loadArea(thanaId = null, thanaID, areaID) {
            let selectedThana = thanaId ?? $('#'+thanaID).val(); 
            $('#'+areaID).empty(); 
            $('#'+areaID).trigger('change'); 
            if (selectedThana) {
                $.ajax({
                    url: '{{ route('admin.users.getSsforcearea') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        thana_id: selectedThana, 
                    },
                    success: function(response) {
                        // Clear the current options in the area select box
                        $('#'+areaID).empty();
                        $('#'+areaID).append('<option value="">Select One</option>');
                        // Add the new options for areas
                        response.forEach(function(area) {
                            $('#'+areaID).append('<option value="' + area['id'] + '">' + area['area'] + '</option>');
                        });
                    },
                    error: function() {
                        // alert('Error occurred while fetching areas.');
                    }
                });
            }
        } 
        
        function formSubmitChart(event){
            event.preventDefault();
            let countryOption = document.getElementById('chart-country-select').options[document.getElementById('chart-country-select').selectedIndex];
            let divisionOption = document.getElementById('chart-division-select').options[document.getElementById('chart-division-select').selectedIndex];
            let districtOption = document.getElementById('chart-district-select').options[document.getElementById('chart-district-select').selectedIndex];
            let thanaOption = document.getElementById('chart-thana-select').options[document.getElementById('chart-thana-select').selectedIndex];
            let areaOption = document.getElementById('chart-area-select').options[document.getElementById('chart-area-select').selectedIndex];
            let yearOption = document.getElementById('yearFilter').options[document.getElementById('yearFilter').selectedIndex];
            let country = countryOption.value;
            let division = divisionOption.value;
            let districtid = districtOption.value;
            let thanaid = thanaOption.value;
            let areaid = areaOption.value;
            let year = yearOption.value; 
            // console.log(districtid, thanaid, areaid, year ); 
            
            $.ajax({
                url: '{{ route('admin.getChartData') }}',
                type: 'GET',
                data: { country, division, districtid, thanaid, areaid, year },
                success: function(data) {
                    // console.log('ChartData:', data);
                    $('#pieChartContainer').selectpicker('refresh');
                    monthlyRedeemPieChart(data.chartData, data.chartLabels)
                }
            }); 

            // console.log('ChartData:', data1);

            $.ajax({
                url: '{{ route('admin.getVerifiedProduct') }}',
                type: 'GET',
                data: { country, division, districtid, thanaid, areaid, year },
                success: function(data) {
                    // console.log('VerifiedProduct:', data);
                    verifiedProductPieChart(data.chartData, data.chartLabels);
                }
            });



            $.ajax({
                url: '{{ url('admin/earn-vs-settlement-report') }}',
                type: 'GET',
                data: { country, division, districtid, thanaid, areaid, year },
                success: function(data) {
                    // console.log('earn-vs-settlement-report:', data);
                    earnVsSettlement(data.earnings, data.settlements, data.labels)
                }
            });



            // alert(districtValue)
            // console.log(districtid, thanaid, areaid, year);
            

            
        }

        let filter = document.getElementById('filterFrom');
        filter.addEventListener('submit', function(event) {
            event.preventDefault();
            points();
            technicians();
            scannedCode();
            // redeem();
            return false;

        }, false); 
        // document.getElementById("filter").addEventListener("click", function(){ alert("Hello World!"); }); 
        // filter.lisen()
    </script> 
@endpush
