@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<style>
    body {
        background: #f5f7fb;
    }

    .card-white {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid #eef0f5;
    }

    .title {
        font-size: 26px;
        font-weight: 700;
        color: #1f2937;
    }

    .sub-text {
        color: #6b7280;
    }

    .counter {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 30px;
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 600;
    }

    .status-running {
        background: #fff7ed;
        color: #f97316;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .status-done {
        background: #ecfdf5;
        color: #10b981;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .draw-btn {
        padding: 14px 40px;
        font-size: 18px;
        border-radius: 50px;
        border: none;
        background: linear-gradient(135deg, #4f46e5, #3b82f6);
        color: #fff;
        font-weight: 600;
        transition: 0.3s;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
    }

    .draw-btn:hover {
        transform: scale(1.05);
    }

    .winner-card {
        background: #ffffff;
        border: 1px solid #eef0f5;
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 10px;
        transition: 0.2s;
    }

    .winner-card:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .pos-badge {
        background: #e0e7ff;
        color: #4338ca;
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: 600;
    }

    .loading {
        display: none;
        margin-top: 15px;
        color: #4f46e5;
        font-weight: 600;
    }

    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="block-header">
    <h2 class="title">🎯 Lottery Draw - {{ $lottery->title }}</h2>
    <p class="sub-text">Select winners one by one in real-time draw system</p>
</div>

<div class="container-fluid">

    {{-- TOP INFO --}}
    <div class="card-white p-4 mb-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <span class="counter">
                    {{ $lottery->current_position ?? 0 }} / {{ $lottery->total_winners }}
                </span>
            </div>

            <div>
                @if(($lottery->current_position ?? 0) >= $lottery->total_winners)
                <span class="status-done">Completed</span>
                @else
                <span class="status-running">Running</span>
                @endif
            </div>

        </div>

    </div>

    {{-- DRAW BOX --}}
    <div class="card-white p-5 text-center mb-4">

        <form id="drawForm"
            action="{{ route('admin.lotteries.draw-next', $lottery->id) }}"
            method="POST">
            @csrf

            <button type="submit"
                class="draw-btn"
                {{ ($lottery->current_position ?? 0) >= $lottery->total_winners ? 'disabled' : '' }}>

                🎯 Draw Next Winner
            </button>
        </form>

        <div class="loading" id="loading">
            ⏳ Processing draw...
            <i class="fa fa-spinner spin"></i>
        </div>

    </div>

    {{-- WINNERS LIST --}}
    <div class="card-white p-4">

        <h5 class="mb-3">🏆 Winners List</h5>

        @forelse($lottery->winners as $winner)
        <div class="winner-card d-flex justify-content-between align-items-center">

            <div>
                <span class="pos-badge">#{{ $winner->position }}</span>
            </div>

            <div>
                <strong>{{ $winner->winner_name }}</strong><br>
                <small>{{ $winner->mobile_no }}</small>
            </div>

            <div>
                <span class="badge badge-primary">
                    {{ $winner->giftAssign->gift->gift_name ?? 'N/A' }}
                </span>
            </div>

        </div>
        @empty
        <p class="text-muted text-center">No winners yet</p>
        @endforelse

    </div>

</div>

{{-- SCRIPT --}}
<script>
    document.getElementById('drawForm').addEventListener('submit', function() {
        document.querySelector('.draw-btn').style.display = 'none';
        document.getElementById('loading').style.display = 'block';
    });
</script>

@endsection