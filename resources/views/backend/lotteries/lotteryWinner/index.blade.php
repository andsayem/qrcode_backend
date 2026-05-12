{{-- filepath: /c:/laragon/www/qrcode_backend/resources/views/backend/lotteries/winners/index.blade.php --}}
@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')


@section('content')
<div class="card">
    <div class="header">
        <h2>Lottery Winners</h2>
    </div>

    <div class="body">
        <form method="GET" class="mb-3">
            <div class="row">
                {{-- Search --}}
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or mobile number or User ID">
                </div>

                {{-- Lottery Filter --}}
                <div class="col-md-4">
                    <select name="lottery_id" class="form-control">
                        <option value="">All Lotteries</option>
                        @foreach($lotteries as $lottery)
                            <option value="{{ $lottery->id }}" {{ request('lottery_id') == $lottery->id ? 'selected' : '' }}>
                                {{ $lottery->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success mr-2"><i
                        class="fa fa-search mr-1"></i>Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>User ID</th>
                        <th>Winner Name</th>
                        <th>Mobile No</th>
                        <th>Lottery</th>
                        <th>Gift</th>
                        <th>Winning Position</th>
                        <th>Draw Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winners as $winner)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $winner->user_id }}</td>
                            <td>{{ $winner->winner_name }}</td>
                            <td>{{ $winner->user->email }}</td>
                            <td>{{ $winner->lottery->title ?? 'N/A' }}</td>
                            <td>{{ $winner->giftAssign->gift->gift_name ?? 'N/A' }}</td>
                            <td>{{ $winner->position }}</td>
                            <td>{{ $winner->draw_time ? $winner->draw_time->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.lottery-winners.show', $winner->id) }}" class="btn btn-info btn-sm">View</a>
                                {{-- <form action="{{ route('admin.lottery-winners.destroy', $winner->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No winners found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        {{ $winners->links() }}
    </div>
</div>
@endsection