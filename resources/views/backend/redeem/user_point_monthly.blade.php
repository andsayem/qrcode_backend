@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>User Point Monthly</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">User Points</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="header">
                {{-- Filter Form --}}
                <form method="GET" class="row mb-3">
                    <div class="col-md-3">
                        <input type="month" name="start_month" value="{{ $startMonth }}" class="form-control" placeholder="Start Month">
                    </div>
                    <div class="col-md-3">
                        <input type="month" name="end_month" value="{{ $endMonth }}" class="form-control" placeholder="End Month">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('admin.user_point_monthly.download', request()->all()) }}"
                           class="btn btn-success">
                            <i class="fa fa-download"></i> Download Excel
                        </a>
                    </div>
                </form>

                <h2>User Point List
                    <span class="badge badge-info">{{ $items->total() }}</span>
                </h2>
            </div>

            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>

                                @foreach ($months as $month)
                                    <th>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $i => $item)
                                <tr>
                                    <td>{{ $items->firstItem() + $i }}</td>
                                    <td>{{ $item->user_name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->division_name }}</td>
                                    <td>{{ $item->district }}</td>
                                    <td>{{ $item->thana }}</td>

                                    @foreach ($months as $month)
                                        @php
                                            $field = 'points_' . str_replace('-', '_', $month);
                                        @endphp
                                        <td>{{ $item->$field ?? 0 }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-3 me-2">
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom_scripts')
<script>
    // Optional: You can add JS here for future enhancements
</script>
@endpush
