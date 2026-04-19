@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="content">

    <!-- Start Content-->

    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>Dashboard V.4 </h2>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <ul class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="icon-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard </li>
                    </ul>
                </div>
            </div>
        </div>

     
    <div class="row">
        <!-- <div class="col-lg-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="fa fa-users widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Technicians</h5>
                    <h3 class="mt-3 mb-3">{{ $approvedTechnician }}<span class="sub-head-line">({{ $pendingTechnician }} Pending)</span></h3>

                    <p class="mb-0 text-muted">
                        @if ($percentageDifference > 0)
                            <span class="text-success me-2"><i class="fa fa-arrow-up"></i> {{$percentageDifference}}%</span>
                        @elseif ($percentageDifference < 0)
                            <span class="text-danger me-2"><i class="fa fa-arrow-down"></i> {{$percentageDifference}}%</span>
                        @else
                            <span>{{$percentageDifference}}%</span>
                        @endif
                        <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="fa fa-qrcode widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Scanned code</h5>
                    <h3 class="mt-3 mb-3">{{$totalVerify}}</h3>

                    <p class="mb-0 text-muted">
                        @if ($percentageDifferenceUseCode > 0)
                            <span class="text-success me-2"><i class="fa fa-arrow-up"></i> {{$percentageDifferenceUseCode}}%</span>
                        @elseif ($percentageDifferenceUseCode < 0)
                            <span class="text-danger me-2"><i class="fa fa-arrow-down"></i> {{$percentageDifferenceUseCode}}%</span>
                        @else
                            <span>{{$percentageDifferenceUseCode}}%</span>
                        @endif
                        <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <a href="{{ route('admin.redeem.index') }}" style="text-decoration: none; color: inherit;">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="fa fa-registered widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Redeem</h5>
                        <h3 class="mt-3 mb-3">{{$totalAmount}}Tk</h3>

                        <p class="mb-0 text-muted">
                            @if ($amountDifference > 0)
                                <span class="text-success me-2"><i class="fa fa-arrow-up"></i> {{$percentageDifferenceUseCode}}%</span>
                            @elseif ($amountDifference < 0)
                                <span class="text-danger me-2"><i class="fa fa-arrow-down"></i> {{$percentageDifferenceUseCode}}%</span>
                            @else
                                <span>{{$amountDifference}}%</span>
                            @endif
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div>
                </div>
            </a>
        </div> -->

        <div class="col-sm-3">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="fa fa-cubes widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Growth">Points</h5>
                    <h3 class="mt-3 mb-3">0</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2"><i class="fa fa-arrow-up"></i> 0%</span>
                        <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
 

<div class="row">

            <div class="col-xl-7 col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <h5 class="text-muted fw-normal mt-0" title="Monthly Redeem">Monthly Redeem</h5>
                        <div id="redeem-chart" class="apex-charts mb-0 mt-4" dir="ltr"></div>

                        <div class="text-center">
                            <ul class="list-inline chart-detail-list mb-0">
                                <li class="list-inline-item">
                                    <h6 class="text-info"><i class="fa fa-circle me-1"></i>Redeem Amount</h6>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>

        
 
    <!-- container -->

</div>

@endsection

@push('custom_scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> 
<script>
    // Monthly redeem chart
    // var options = {
    //     series: [{
    //         name: 'Redeem Amount',
    //         data: @json($chartData)
    //     }],
    //     chart: {
    //         type: 'line',
    //         height: 250,
    //         toolbar: {
    //             show: false
    //         }
    //     },
    //     colors: ['#36a2eb'],
    //     stroke: {
    //         width: 3,
    //         curve: 'smooth'
    //     },
    //     xaxis: {
    //         categories: @json($chartLabels),
    //         labels: {
    //             style: {
    //                 colors: '#8e8da4'
    //             },
    //             formatter: function(value) {
    //                 // Convert month number to month name
    //                 var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    //                 return monthNames[value - 1];
    //             }
    //         }
    //     },
    //     // Other options...
    // };

    // var chart = new ApexCharts(document.querySelector("#redeem-chart"), options);
    // chart.render();
</script>

@endpush
