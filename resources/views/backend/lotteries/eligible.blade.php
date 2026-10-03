@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Eligible Technicians</h2>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.lotteries.index') }}">Lotteries</a>
                </li>
                <li class="breadcrumb-item active">Eligible Technicians</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">

    <div class="header">
        <div class="row align-items-center">

            <div class="col-lg-7">
                <h2>
                    {{ $lottery->title }}
                    <span class="badge badge-success fill">{{ $totalEligible }}</span>
                </h2>
                <small class="text-muted">
                    {{ $lottery->from_date->format('Y-m-d') }} to {{ $lottery->to_date->format('Y-m-d') }}
                    &middot; Required Points: {{ $lottery->required_points }}
                    @if($lottery->draw_date)
                        &middot; Draw Date: {{ $lottery->draw_date->format('Y-m-d') }}
                    @endif
                </small>
            </div>

            <div class="col-lg-5 text-right">
                <a href="{{ route('admin.lotteries.eligible.export', $lottery->id) }}" class="btn btn-sm px-3 btn-success">
                    <i class="fa fa-file-excel-o"></i> Download Excel
                </a>
                <a href="{{ route('admin.lotteries.index') }}" class="btn btn-sm px-3 btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

        </div>
    </div>

    <div class="body pt-0">

        <form method="GET" action="{{ route('admin.lotteries.eligible', $lottery->id) }}" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="search" class="form-control" placeholder="Search by name or phone"
                    value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">

                <thead>
                    <tr>
                        <th>SL</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Thana</th>
                        <th>Total Points</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone_number ?: $user->email }}</td>
                        <td>{{ $user->division_name ?? 'N/A' }}</td>
                        <td>{{ $user->district ?? 'N/A' }}</td>
                        <td>{{ $user->thana ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-primary">{{ number_format($user->total_points, 2) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No eligible technicians found</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @include('/includes/paginate', ['paginator' => $users])

    </div>
</div>

@endsection
