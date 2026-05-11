@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-6">
            <h2>🎯 Lottery Draw - {{ $lottery->title }}</h2>
        </div>

        <div class="col-lg-6 text-right">

            <span class="badge badge-info">
                {{ $lottery->current_position ?? 0 }} / {{ $lottery->total_winners }}
            </span>

            @if(($lottery->current_position ?? 0) >= $lottery->total_winners)
            <span class="badge badge-success">Completed</span>
            @else
            <span class="badge badge-warning">Running</span>
            @endif

        </div>
    </div>
</div>

<div class="card">
    <div class="body">

        {{-- SUCCESS / ERROR --}}
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- DRAW BUTTON --}}
        <form action="{{ route('admin.lotteries.draw-next', $lottery->id) }}" method="POST">
            @csrf

            <button type="submit"
                class="btn btn-danger btn-lg"
                {{ ($lottery->current_position ?? 0) >= $lottery->total_winners ? 'disabled' : '' }}>

                🎯 Draw Next Winner
            </button>
        </form>

        <hr>

        {{-- WINNERS TABLE --}}
        <h5>🏆 Winners List</h5>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Gift</th>
                    <th>Time</th>
                </tr>
            </thead>

            <tbody>
                @forelse($lottery->winners as $winner)
                <tr>
                    <td>{{ $winner->position }}</td>
                    <td>{{ $winner->winner_name }}</td>
                    <td>{{ $winner->mobile_no }}</td>
                    <td>{{ $winner->giftAssign->gift->gift_name ?? 'N/A' }}</td>
                    <td>{{ $winner->draw_time ? $winner->draw_time->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No winners yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection