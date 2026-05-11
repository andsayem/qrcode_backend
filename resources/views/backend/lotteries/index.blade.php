@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Lotteries</h2>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Lotteries</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">

    <div class="header">
        <div class="row align-items-center">

            <div class="col-lg-6">
                <h2>
                    Lotteries
                    <span class="badge badge-info fill">
                        {{ $lotteries->total() ?? $lotteries->count() }}
                    </span>
                </h2>
            </div>

            <div class="col-lg-6 text-right">
                <a href="{{ route('admin.lotteries.create') }}" class="btn btn-sm px-3 btn-info">
                    <i class="fa fa-plus"></i> Create Lottery
                </a>
            </div>

        </div>
    </div>

    <div class="body pt-0">

        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">

                <thead>
                    <tr>
                        <th>Title</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Required Points</th>
                        <th>Total Winners</th>
                        <th>Status</th>
                        <th>Current Position</th>
                        <th>Started At</th>
                        <th>Completed At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($lotteries as $lottery)
                    <tr>

                        <td>{{ $lottery->title }}</td>

                        <td>{{ $lottery->from_date }}</td>

                        <td>{{ $lottery->to_date }}</td>

                        <td>{{ $lottery->required_points }}</td>

                        <!-- NEW FIELD -->
                        <td>
                            <span class="badge badge-primary">
                                {{ $lottery->total_winners }}
                            </span>
                        </td>

                        <td>
                            @if($lottery->status == 'pending')
                            <span class="badge badge-secondary">Pending</span>
                            @elseif($lottery->status == 'running')
                            <span class="badge badge-success">Running</span>
                            @else
                            <span class="badge badge-dark">Completed</span>
                            @endif
                        </td>

                        <td>{{ $lottery->current_position ?? 0 }}</td>

                        <td>
                            {{ $lottery->started_at ? $lottery->started_at->format('Y-m-d H:i') : 'N/A' }}
                        </td>

                        <td>
                            {{ $lottery->completed_at ? $lottery->completed_at->format('Y-m-d H:i') : 'N/A' }}
                        </td>

                        <td>
                            <!-- 🎯 Draw Page -->
                            <a href="{{ route('admin.lotteries.draw', $lottery->id) }}"
                                class="btn btn-outline-warning btn-sm mr-2"
                                title="Draw Lottery">

                                <i class="fa fa-play"></i> Draw
                            </a>
                            <!-- Edit -->
                            <a href="{{ route('admin.lotteries.edit', $lottery->id) }}"
                                class="btn btn-outline-info btn-sm mr-2">
                                <i class="fa fa-edit"></i>
                            </a>

                            <!-- Gift Assign -->
                            <a href="{{ route('admin.lottery-gift-assign.index', ['lottery' => $lottery->id]) }}"
                                class="btn btn-outline-success btn-sm mr-2"
                                title="Gift Assign">
                                <i class="fa fa-gift"></i>
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('admin.lotteries.destroy', $lottery->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">No data found</td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        @include('/includes/paginate', ['paginator' => $lotteries])

    </div>
</div>

@endsection