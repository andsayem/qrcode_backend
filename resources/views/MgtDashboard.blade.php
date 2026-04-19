@extends('backend.layouts.app')

{{-- Topbar --}}
@section('topbar')
    @include('backend.layouts.topbar')
@endsection

{{-- Sidebar --}}
@section('sidebar')
    @include('backend.layouts.leftsidebar')
@endsection

@section('content')

    <style>
        .filter-card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #444;
        }

        .custom-control {
            height: 42px;
            border-radius: 6px;
            font-size: 14px;
        }

        .search-btn {
            height: 42px;
            border-radius: 6px;
            font-weight: 600;
            background: #A8237F;
            border: none;
        }

        .search-btn:hover {
            background: #8e1d6c;
        }
    </style>

    <div class="container-fluid">

        <h4 class="mb-3" style="font-weight:700;color:#A8237F;">
            Dashboard Report
        </h4>

        <div class="card filter-card">
            <div class="card-body">

                <form method="GET" action="{{ route('admin.mgtdashboard') }}">
                    <div class="row align-items-end">

                        {{-- From Date --}}
                        <div class="col-md-2">
                            <label class="filter-label">From Date</label>
                            <input type="date" name="from_date" class="form-control custom-control"
                                value="{{ old('from_date', $fromDate) }}">
                        </div>

                        {{-- To Date --}}
                        <div class="col-md-2">
                            <label class="filter-label">To Date</label>
                            <input type="date" name="to_date" class="form-control custom-control"
                                value="{{ old('to_date', $toDate) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="filter-label">Division</label>
                            <select name="division" class="form-select custom-control">
                                <option value="">Division</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ request('division') == $div->id ? 'selected' : '' }}>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label">Report Type</label>
                            <select name="report_type" class="form-select custom-control">
                                <option value="">Select Report</option>
                                @foreach([
                                        'registered' => 'Registered Electrician',
                                        'active' => 'Active',
                                        'inactive' => 'In-active User',
                                        'total_scan' => 'Total Scan Point'
                                    ] as $key => $label)
                                        <option value="{{ $key }}" {{ request('report_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        <!-- 'redeem' => 'Redeem Point', 
                                         , 
                                        'new_entry' => 'Month wise New Entry'-->
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Button --}}
                        <div class="col-md-2">
                            <button type="submit" class="btn search-btn w-100 text-white">
                                Search
                            </button>
                        </div>

                    </div>
                 </form>

                        </div>
            </div>

            {{-- Report Result --}}
            <div class="row mt-3">
               @if(!empty($reportType))
                @includeIf(
                    "backend.dashboard.reports.$reportType",
                    [
                        'reportData' => $reportData,
                        'fromDate' => $fromDate,
                        'toDate' => $toDate
                    ]
                )
            @else
            <div class="col-12">
            <p class="text-muted text-center">
                Select a report type and click Search to view data.
            </p>
        </div>
    @endif

    </div>

@endsection

{{-- Footer --}}
@section('footer')
    @include('backend.layouts.footer')
@endsection