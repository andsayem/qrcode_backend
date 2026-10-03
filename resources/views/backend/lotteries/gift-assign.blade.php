@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@push('custom_styles')
<style>
    .ga-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        align-items: center;
    }

    .ga-summary .ga-stat {
        min-width: 120px;
    }

    .ga-summary .ga-stat small {
        display: block;
        color: #8a94a6;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .ga-summary .ga-stat strong {
        font-size: 18px;
        color: #2d3748;
    }

    .ga-progress {
        flex: 1;
        min-width: 220px;
    }

    .ga-progress .progress {
        height: 8px;
        border-radius: 4px;
    }

    .ga-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .ga-card {
        border: 1px solid #e6e9ef;
        border-radius: 8px;
        padding: 16px;
        background: #fff;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: border-color .2s, box-shadow .2s;
    }

    .ga-card:hover {
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
    }

    .ga-card.is-assigned {
        border-color: #28a745;
    }

    .ga-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ga-rank {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #2d3748;
    }

    .ga-rank-num {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        background: #eef1f6;
        color: #4a5568;
    }

    .ga-rank-num.rank-1 { background: #ffd700; color: #5c4400; }
    .ga-rank-num.rank-2 { background: #d8dde3; color: #3c4650; }
    .ga-rank-num.rank-3 { background: #e0a066; color: #4a2a0c; }

    .ga-status {
        font-size: 11px;
    }

    .ga-preview {
        height: 120px;
        border-radius: 6px;
        background: #f6f8fb;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #b0b8c4;
    }

    .ga-preview img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .ga-preview .fa {
        font-size: 36px;
    }

    .ga-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid #e6e9ef;
        padding: 12px 0;
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 5;
    }
</style>
@endpush

@section('content')

@php
    $assignedCount = $assignedGifts->count();
    $totalPositions = $lottery->total_winners;
    $percent = $totalPositions > 0 ? round(min($assignedCount, $totalPositions) / $totalPositions * 100) : 0;
    $isLocked = $lottery->status === 'completed';
@endphp

<div class="block-header">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <h2>Gift Assign</h2>
        </div>
        <div class="col-lg-6 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.lotteries.index') }}">Lotteries</a>
                </li>
                <li class="breadcrumb-item active">Gift Assign</li>
            </ul>
        </div>
    </div>
</div>

<!-- Summary -->
<div class="card">
    <div class="body">
        <div class="ga-summary">
            <div class="ga-stat">
                <small>Lottery</small>
                <strong>{{ $lottery->title }}</strong>
            </div>
            <div class="ga-stat">
                <small>Period</small>
                <strong>{{ $lottery->from_date->format('d M') }} - {{ $lottery->to_date->format('d M Y') }}</strong>
            </div>
            @if($lottery->draw_date)
            <div class="ga-stat">
                <small>Draw Date</small>
                <strong>{{ $lottery->draw_date->format('d M Y') }}</strong>
            </div>
            @endif
            <div class="ga-progress">
                <small class="d-flex justify-content-between text-muted mb-1">
                    <span>GIFTS ASSIGNED</span>
                    <span id="ga-progress-text">{{ $assignedCount }} / {{ $totalPositions }}</span>
                </small>
                <div class="progress">
                    <div class="progress-bar {{ $percent == 100 ? 'bg-success' : 'bg-info' }}" id="ga-progress-bar"
                        style="width: {{ $percent }}%"></div>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.lotteries.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="body">

        @if($isLocked)
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i> This lottery is completed. Gift assignments can no longer be modified.
            </div>
        @endif

        @if($gifts->isEmpty())
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No gifts found.
                <a href="{{ route('admin.lottery-gifts.create') }}">Create a gift</a> first.
            </div>
        @endif

        <form action="{{ route('admin.lottery-gift-assign.store', $lottery->id) }}" method="POST" id="ga-form">
            @csrf

            <div class="ga-grid">
                @for($i = 1; $i <= $totalPositions; $i++)
                    @php
                        $existing = $assignedGifts->where('position', $i)->first();
                        $existingImage = $existing && $existing->gift && $existing->gift->gift_image
                            ? asset('uploads/lottery_gifts/' . $existing->gift->gift_image)
                            : null;
                    @endphp

                    <div class="ga-card {{ $existing ? 'is-assigned' : '' }}">

                        <div class="ga-card-head">
                            <span class="ga-rank">
                                <span class="ga-rank-num {{ $i <= 3 ? 'rank-' . $i : '' }}">{{ $i }}</span>
                                Position {{ $i }}
                            </span>

                            <span class="ga-status badge {{ $existing ? 'badge-success' : 'badge-secondary' }}">
                                {{ $existing ? 'Assigned' : 'Not assigned' }}
                            </span>
                        </div>

                        <div class="ga-preview">
                            @if($existingImage)
                                <img src="{{ $existingImage }}" alt="Gift">
                            @else
                                <i class="fa fa-gift"></i>
                            @endif
                        </div>

                        <select name="gifts[{{ $i }}][gift_id]" class="form-control ga-select" required
                            {{ $isLocked ? 'disabled' : '' }}>
                            <option value="">-- Select Gift --</option>
                            @foreach($gifts as $gift)
                                <option value="{{ $gift->id }}"
                                    data-image="{{ $gift->gift_image ? asset('uploads/lottery_gifts/' . $gift->gift_image) : '' }}"
                                    {{ $existing && $existing->gift_id == $gift->id ? 'selected' : '' }}>
                                    {{ $gift->gift_name }}
                                </option>
                            @endforeach
                        </select>

                        <input type="hidden" name="gifts[{{ $i }}][position]" value="{{ $i }}">

                        @if($existing && !$isLocked)
                            <button type="submit" form="ga-delete-{{ $existing->id }}"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remove gift from position {{ $i }}?')">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        @endif

                    </div>
                @endfor
            </div>

            @unless($isLocked)
            <div class="ga-footer">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> Select a gift for every position, then save.
                </small>
                <button type="submit" class="btn btn-primary px-4" {{ $gifts->isEmpty() ? 'disabled' : '' }}>
                    <i class="fa fa-save"></i> Save Gift Assign
                </button>
            </div>
            @endunless
        </form>

        {{-- Delete forms live outside the main form (nested forms are not allowed) --}}
        @unless($isLocked)
            @foreach($assignedGifts as $item)
                <form id="ga-delete-{{ $item->id }}" action="{{ route('admin.lottery-gift-assign.destroy', $item->id) }}"
                    method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endunless

    </div>
</div>

@endsection

@push('custom_scripts')
<script>
    (function () {
        var total = {{ $totalPositions }};
        var selects = document.querySelectorAll('.ga-select');

        function updatePreview(select) {
            var card = select.closest('.ga-card');
            var preview = card.querySelector('.ga-preview');
            var option = select.options[select.selectedIndex];
            var image = option ? option.getAttribute('data-image') : '';

            preview.innerHTML = image
                ? '<img src="' + image + '" alt="Gift">'
                : '<i class="fa fa-gift"></i>';

            card.classList.toggle('is-assigned', !!select.value);
        }

        function updateProgress() {
            var selected = 0;
            selects.forEach(function (s) {
                if (s.value) selected++;
            });

            var percent = total > 0 ? Math.round(selected / total * 100) : 0;
            var bar = document.getElementById('ga-progress-bar');

            bar.style.width = percent + '%';
            bar.classList.toggle('bg-success', percent === 100);
            bar.classList.toggle('bg-info', percent !== 100);
            document.getElementById('ga-progress-text').textContent = selected + ' / ' + total;
        }

        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                updatePreview(select);
                updateProgress();
            });
        });
    })();
</script>
@endpush
