@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')

@section('content')

<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <h2>Dashboard</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Dashboard </li>
                </ul>
            </div>
        </div>
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
                    <h3 class="mt-3 mb-3"><span>{{ $total_technician }}</span><span class="sub-head-line">({{ $total_pending_technician }} Pending)</span></h3>
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
                    <h3 class="mt-3 mb-3"><span>{{ $total_redeem_request_amount }}</span></h3>
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
                    <h3 class="mt-3 mb-3"><span>{{ $total_scanned_code }}</span></h3>
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
                    <h3 class="mt-3 mb-3"><span>{{ $total_user_point }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <h5 class="text-muted fw-normal mt-0" title="Monthly Redeem">Monthly Redeem ({{ $current_year }})</h5> 
                    <div id="monthlyRedeemPieChart" class="apex-charts mb-0 mt-4" dir="ltr"></div> 
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
                    <h5 class="text-muted fw-normal mt-0" title="Monthly Redeem">Verified Product ({{ $current_year }})</h5>
                    <div id="monthlyVerifiedProductPieChart" class="apex-charts mb-0 mt-4" dir="ltr"></div> 
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
    </div>
    <div class="card widget-flat">
        <div class="card-body"> 
            <h5 class="text-muted fw-normal mt-0" title="Monthwise Earn vs Settlement Report">Monthwise Earn vs Settlement Report ({{ $current_year }})</h5>
            <div id="monthWiseEarningsSettlement" class="apex-charts mb-0 mt-4" dir="ltr"></div> 
        </div>
    </div> 

</div>
@endsection
@extends('backend.layouts.footer')


@push('custom_scripts') 
    <script>
        // Function to initialize and render the line chart
        function monthlyRedeemPieChart() {
            let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            
            $.ajax({
                url: "{{ route('reports.monthly-redeem-pie-chart') }}",
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#monthlyRedeemPieChart').html('<div class="text-center">Loading chart...</div>');
                },
                success: function(response) {
                    let values = Object.values(response).map(value => parseFloat(value)); // Ensure values are numbers
                    let keys = Object.keys(response).map(value => months[value - 1]);
                    
                    // Chart options
                    let options = {
                        series: values,
                        chart: {
                            type: 'pie',
                            height: 250,  // Increased height for better visibility
                            toolbar: {
                                show: false
                            }
                        },
                        labels: keys,
                        // Other options...
                    };

                    // Initialize and render the chart
                    let pieChart = new ApexCharts(document.querySelector('#monthlyRedeemPieChart'), options);
                    pieChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                    $('#monthlyRedeemPieChart').html('<div class="alert alert-danger">Failed to load chart data</div>');
                }
            });
        }

        function monthlyVerifiedProductPieChart() {
            let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            
            $.ajax({
                url: "{{ route('reports.monthly-verified-product-pie-chart') }}",
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#monthlyVerifiedProductPieChart').html('<div class="text-center">Loading chart...</div>');
                },
                success: function(response) {
                    let values = Object.values(response).map(value => parseFloat(value)); // Ensure values are numbers
                    let keys = Object.keys(response).map(value => months[value - 1]);
                    
                    // Chart options
                    let options = {
                        series: values,
                        chart: {
                            type: 'pie',
                            height: 250,  // Increased height for better visibility
                            toolbar: {
                                show: false
                            }
                        },
                        labels: keys,
                        // Other options...
                    };

                    // Initialize and render the chart
                    let pieChart = new ApexCharts(document.querySelector('#monthlyVerifiedProductPieChart'), options);
                    pieChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                    $('#monthlyVerifiedProductPieChart').html('<div class="alert alert-danger">Failed to load chart data</div>');
                }
            });
        }

        function monthWiseEarningsSettlement() {
            let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            $.ajax({
                url: "{{ route('reports.month-wise-earnings-settlement') }}",
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#monthWiseEarningsSettlement').html('<div class="text-center">Loading chart...</div>');
                },
                success: function(response) {
                    let values = Object.values(response).map(value => parseFloat(value)); // Ensure values are numbers
                    let keys = Object.keys(response).map(value => months[value - 1]);
                    // Chart options
                    let options = {
                        chart: {
                            type: 'line'
                        },
                        series: [
                            {
                                name: 'Earnings',
                                data: values
                            },
                            {
                                name: 'Settlements',
                                data: keys
                            }
                        ],
                        xaxis: {
                            categories: months
                        },
                        title: {
                            text: 'Month-wise Earn vs Settlement Report'
                        }
                    };

                    // Initialize and render the chart
                    let pieChart = new ApexCharts(document.querySelector('#monthWiseEarningsSettlement'), options);
                    pieChart.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading chart data:', error);
                    $('#monthWiseEarningsSettlement').html('<div class="alert alert-danger">Failed to load chart data</div>');
                }
            });
        }

        // Call the function when needed
        $(document).ready(function() {
            monthlyRedeemPieChart();
            monthlyVerifiedProductPieChart();
            monthWiseEarningsSettlement();
        });
    </script> 
@endpush
