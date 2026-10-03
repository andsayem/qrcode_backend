@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@php
    $total = $lottery->total_winners;
    $drawn = $lottery->current_position ?? 0;
    $percent = $total > 0 ? round($drawn / $total * 100) : 0;
    $status = $lottery->status;
    $giftByPosition = $lottery->giftAssignments->keyBy('position');
    $winnerByPosition = $lottery->winners->keyBy('position');
    $newWinnerId = session('drawn_winner_id');
    $newWinner = $newWinnerId ? $lottery->winners->firstWhere('id', $newWinnerId) : null;
    $giftImage = function ($giftAssign) {
        return $giftAssign && $giftAssign->gift && $giftAssign->gift->gift_image
            ? asset('uploads/lottery_gifts/' . $giftAssign->gift->gift_image)
            : null;
    };
    $currentStep = $status === 'pending' ? 1 : ($status === 'running' ? 2 : 3);
@endphp

@push('custom_styles')
<style>
    .ld-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .ld-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .ld-meta {
        color: #6b7280;
        font-size: 13px;
        margin-top: 4px;
    }

    .ld-meta span + span::before {
        content: "\2022";
        margin: 0 8px;
        color: #cbd5e1;
    }

    .ld-pill {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .ld-pill.pending { background: #f3f4f6; color: #6b7280; }
    .ld-pill.running { background: #fff7ed; color: #ea580c; }
    .ld-pill.completed { background: #ecfdf5; color: #059669; }

    .ld-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef0f5;
        box-shadow: 0 6px 20px rgba(15, 23, 42, .05);
        padding: 22px;
        margin-bottom: 20px;
    }

    /* Stepper */
    .ld-steps {
        display: flex;
        align-items: center;
    }

    .ld-step {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #9ca3af;
        font-weight: 600;
        white-space: nowrap;
    }

    .ld-step-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f3f4f6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .ld-step.active { color: #4f46e5; }
    .ld-step.active .ld-step-dot { background: #4f46e5; color: #fff; box-shadow: 0 0 0 5px #e0e7ff; }
    .ld-step.done { color: #059669; }
    .ld-step.done .ld-step-dot { background: #10b981; color: #fff; }

    .ld-step-line {
        flex: 1;
        height: 2px;
        background: #e5e7eb;
        margin: 0 14px;
        min-width: 20px;
    }

    .ld-step-line.done { background: #10b981; }

    /* Checklist */
    .ld-check {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 10px;
        background: #f9fafb;
        margin-bottom: 8px;
    }

    .ld-check .fa { font-size: 18px; width: 20px; text-align: center; }
    .ld-check.ok .fa { color: #10b981; }
    .ld-check.fail .fa { color: #ef4444; }
    .ld-check.warn .fa { color: #f59e0b; }
    .ld-check .ld-check-label { flex: 1; color: #374151; }

    /* Draw stage */
    .ld-stage {
        background: linear-gradient(135deg, #1e1b4b, #312e81 60%, #4338ca);
        color: #fff;
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .ld-stage-label {
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-size: 12px;
        color: #c7d2fe;
    }

    .ld-next-gift {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .15);
        border-radius: 14px;
        padding: 10px 18px 10px 10px;
        margin: 12px 0 20px;
    }

    .ld-next-gift img,
    .ld-next-gift .ld-gift-ph {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        background: #fff;
        object-fit: contain;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-size: 24px;
    }

    .ld-next-gift .ld-pos {
        font-size: 13px;
        color: #fde68a;
        font-weight: 700;
        text-align: left;
    }

    .ld-next-gift .ld-gift-name {
        font-size: 18px;
        font-weight: 700;
        text-align: left;
    }

    .ld-reel {
        background: rgba(0, 0, 0, .25);
        border: 2px dashed rgba(255, 255, 255, .25);
        border-radius: 14px;
        height: 84px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: .5px;
        margin: 0 auto 22px;
        max-width: 520px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 0 16px;
    }

    .ld-reel.rolling {
        border-color: #fbbf24;
        color: #fde68a;
    }

    .ld-btn {
        padding: 14px 42px;
        font-size: 18px;
        border-radius: 50px;
        border: none;
        font-weight: 700;
        color: #1e1b4b;
        background: linear-gradient(135deg, #fde68a, #f59e0b);
        box-shadow: 0 10px 25px rgba(245, 158, 11, .35);
        transition: transform .2s, box-shadow .2s;
        cursor: pointer;
    }

    .ld-btn:hover:not(:disabled) { transform: translateY(-2px); }
    .ld-btn:disabled { opacity: .6; cursor: not-allowed; }

    .ld-btn.start {
        color: #fff;
        background: linear-gradient(135deg, #4f46e5, #3b82f6);
        box-shadow: 0 10px 25px rgba(79, 70, 229, .3);
    }

    .ld-progress {
        height: 8px;
        background: rgba(255, 255, 255, .15);
        border-radius: 4px;
        overflow: hidden;
        margin-top: 24px;
    }

    .ld-progress > div {
        height: 100%;
        background: #fbbf24;
    }

    /* New winner reveal */
    .ld-reveal {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #a7f3d0;
        border-radius: 16px;
        padding: 18px 22px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        animation: ld-pop .5s ease-out;
    }

    .ld-reveal .ld-trophy {
        font-size: 40px;
    }

    .ld-reveal h4 {
        margin: 0;
        font-weight: 800;
        color: #065f46;
    }

    @keyframes ld-pop {
        0% { transform: scale(.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Prize board */
    .ld-board-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 12px;
        border: 1px solid #eef0f5;
        margin-bottom: 8px;
        transition: background .2s;
    }

    .ld-board-item.is-next {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .ld-board-item.is-won {
        background: #f9fafb;
        cursor: pointer;
    }

    .ld-board-item.is-won:hover {
        border-color: #a5b4fc;
        box-shadow: 0 4px 14px rgba(79, 70, 229, .12);
    }

    /* Winner details popup */
    .wd-head {
        display: flex;
        align-items: center;
        gap: 14px;
        text-align: left;
        padding-bottom: 14px;
        border-bottom: 1px solid #eef0f5;
        margin-bottom: 12px;
    }

    .wd-head img,
    .wd-head .wd-ph {
        width: 72px;
        height: 72px;
        border-radius: 12px;
        object-fit: contain;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-size: 28px;
        flex-shrink: 0;
    }

    .wd-pos {
        font-size: 12px;
        font-weight: 700;
        color: #d97706;
        letter-spacing: .5px;
    }

    .wd-name {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
    }

    .wd-gift {
        color: #4b5563;
        font-size: 14px;
    }

    .wd-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        text-align: left;
    }

    .wd-item {
        background: #f9fafb;
        border-radius: 10px;
        padding: 10px 12px;
    }

    .wd-item small {
        display: block;
        color: #9ca3af;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .wd-item span {
        color: #1f2937;
        font-weight: 600;
        word-break: break-word;
    }

    .wd-item.full {
        grid-column: 1 / -1;
    }

    .wd-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e0e7ff;
        flex-shrink: 0;
    }

    .wd-tags {
        margin-top: 6px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .wd-tag {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .wd-tag.ok { background: #ecfdf5; color: #059669; }
    .wd-tag.warn { background: #fff7ed; color: #c2410c; }

    .wd-body {
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 4px;
    }

    .wd-section-title {
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #4f46e5;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin: 14px 0 8px;
    }

    .wd-section-title:first-child {
        margin-top: 0;
    }

    @media (max-width: 576px) {
        .wd-grid { grid-template-columns: 1fr; }
    }

    .ld-board-item.is-new {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .ld-rank {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4338ca;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .ld-rank.r1 { background: #fde68a; color: #78350f; }
    .ld-rank.r2 { background: #e5e7eb; color: #374151; }
    .ld-rank.r3 { background: #fed7aa; color: #7c2d12; }

    .ld-board-img {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: contain;
        background: #f3f4f6;
        flex-shrink: 0;
    }

    .ld-board-body {
        flex: 1;
        min-width: 0;
    }

    .ld-board-body .ld-board-gift {
        font-weight: 600;
        color: #1f2937;
        font-size: 13px;
    }

    .ld-board-body .ld-board-winner {
        font-size: 12px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ld-board-body .ld-board-winner strong {
        color: #059669;
    }
</style>
@endpush

@section('content')

<div class="block-header">
    <div class="ld-head">
        <div>
            <h2 class="ld-title">🎯 {{ $lottery->title }}</h2>
            <div class="ld-meta">
                <span>{{ $lottery->from_date->format('d M Y') }} - {{ $lottery->to_date->format('d M Y') }}</span>
                <span>Min {{ $lottery->required_points }} points</span>
                @if($lottery->draw_date)
                    <span>Draw date {{ $lottery->draw_date->format('d M Y') }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 10px;">
            <span class="ld-pill {{ $status }}">{{ ucfirst($status) }}</span>
            <a href="{{ route('admin.lotteries.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- STEPPER --}}
<div class="ld-card">
    <div class="ld-steps">
        @foreach(['Prepare', 'Draw Winners', 'Completed'] as $index => $stepName)
            @php $step = $index + 1; @endphp
            @if($index > 0)
                <div class="ld-step-line {{ $currentStep > $index ? 'done' : '' }}"></div>
            @endif
            <div class="ld-step {{ $currentStep > $step || $currentStep == 3 ? 'done' : ($currentStep == $step ? 'active' : '') }}">
                <span class="ld-step-dot">
                    @if($currentStep > $step || $currentStep == 3)
                        <i class="fa fa-check"></i>
                    @else
                        {{ $step }}
                    @endif
                </span>
                {{ $stepName }}
            </div>
        @endforeach
    </div>
</div>

<div class="row">

    {{-- LEFT: STAGE --}}
    <div class="col-lg-8">

        {{-- STEP 1: PREPARE --}}
        @if($status === 'pending')
            <div class="ld-card">
                <h5 class="mb-3">Pre-draw checklist</h5>

                @foreach($checklist as $item)
                    @php
                        $state = $item['ok'] ? 'ok' : ($item['required'] ? 'fail' : 'warn');
                        $icon = $item['ok'] ? 'fa-check-circle' : ($item['required'] ? 'fa-times-circle' : 'fa-exclamation-circle');
                    @endphp
                    <div class="ld-check {{ $state }}">
                        <i class="fa {{ $icon }}"></i>
                        <span class="ld-check-label">{{ $item['label'] }}</span>
                        @if($item['link'])
                            <a href="{{ $item['link'] }}" class="btn btn-sm btn-link">
                                {{ $item['ok'] ? 'View' : 'Fix' }} <i class="fa fa-angle-right"></i>
                            </a>
                        @endif
                    </div>
                @endforeach

                <div class="text-center mt-4">
                    <form id="startForm" action="{{ route('admin.lotteries.start', $lottery->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="ld-btn start" {{ $canStart ? '' : 'disabled' }}>
                            <i class="fa fa-play"></i> Start Lottery
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">
                        @if($canStart)
                            Starting moves the lottery to Running. Winners are drawn one by one after that.
                        @else
                            Fix the red items above to enable the start.
                        @endif
                    </small>
                </div>
            </div>
        @endif

        {{-- NEW WINNER REVEAL --}}
        @if($newWinner)
            <div class="ld-reveal">
                <span class="ld-trophy">🏆</span>
                <div>
                    <small class="text-success font-weight-bold">
                        POSITION {{ $newWinner->position }} WINNER
                    </small>
                    <h4>{{ $newWinner->winner_name }}</h4>
                    <div class="text-muted">
                        {{ optional($newWinner->user)->phone_number ?: $newWinner->mobile_no }}
                        &middot; wins <strong>{{ $newWinner->giftAssign->gift->gift_name ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        @endif

        {{-- STEP 2: DRAW --}}
        @if($status === 'running' && $nextPosition)
            <div class="ld-stage">
                <div class="ld-stage-label">Next draw</div>

                <div class="ld-next-gift">
                    @if($giftImage($nextGift))
                        <img src="{{ $giftImage($nextGift) }}" alt="Gift">
                    @else
                        <span class="ld-gift-ph"><i class="fa fa-gift"></i></span>
                    @endif
                    <div>
                        <div class="ld-pos">POSITION {{ $nextPosition }}</div>
                        <div class="ld-gift-name">{{ $nextGift->gift->gift_name ?? 'No gift assigned' }}</div>
                    </div>
                </div>

                <div class="ld-reel" id="reel">? ? ?</div>

                <form id="drawForm" action="{{ route('admin.lotteries.draw-next', $lottery->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="ld-btn" id="drawBtn" {{ $nextGift ? '' : 'disabled' }}>
                        🎯 Draw Position {{ $nextPosition }}
                    </button>
                </form>

                <div class="ld-progress">
                    <div style="width: {{ $percent }}%"></div>
                </div>
                <small class="d-block mt-2" style="color: #c7d2fe;">
                    {{ $drawn }} of {{ $total }} winners drawn
                </small>
            </div>
        @endif

        {{-- STEP 3: COMPLETED --}}
        @if($status === 'completed')
            <div class="ld-card text-center">
                <div style="font-size: 48px;">🎉</div>
                <h4 class="font-weight-bold mb-1">Lottery completed</h4>
                <p class="text-muted mb-0">
                    All {{ $total }} winners were drawn
                    @if($lottery->completed_at)
                        on {{ $lottery->completed_at->format('d M Y, h:i A') }}
                    @endif
                </p>
                <a href="{{ route('admin.lottery-winners.index') }}" class="btn btn-outline-success mt-3">
                    <i class="fa fa-trophy"></i> View all winners
                </a>
            </div>
        @endif

    </div>

    {{-- RIGHT: PRIZE BOARD --}}
    <div class="col-lg-4">
        <div class="ld-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">🏆 Prize Board</h5>
                <span class="badge badge-light">{{ $drawn }} / {{ $total }}</span>
            </div>

            @for($position = 1; $position <= $total; $position++)
                @php
                    $assign = $giftByPosition->get($position);
                    $winner = $winnerByPosition->get($position);
                    $classes = [];
                    if ($winner) $classes[] = 'is-won';
                    if ($winner && $winner->id == $newWinnerId) $classes[] = 'is-new';
                    if (!$winner && $status === 'running' && $position == $nextPosition) $classes[] = 'is-next';
                @endphp
                <div class="ld-board-item {{ implode(' ', $classes) }}"
                    @if($winner) data-position="{{ $position }}" role="button" title="View winner details" @endif>
                    <span class="ld-rank {{ $position <= 3 ? 'r' . $position : '' }}">{{ $position }}</span>

                    @if($giftImage($assign))
                        <img src="{{ $giftImage($assign) }}" class="ld-board-img" alt="Gift">
                    @else
                        <span class="ld-board-img d-inline-flex align-items-center justify-content-center text-muted">
                            <i class="fa fa-gift"></i>
                        </span>
                    @endif

                    <div class="ld-board-body">
                        <div class="ld-board-gift">{{ $assign->gift->gift_name ?? 'No gift assigned' }}</div>
                        <div class="ld-board-winner">
                            @if($winner)
                                <strong><i class="fa fa-check-circle"></i> {{ $winner->winner_name }}</strong>
                            @elseif($status === 'running' && $position == $nextPosition)
                                <span class="text-warning">Up next</span>
                            @else
                                Waiting
                            @endif
                        </div>
                    </div>
                </div>
            @endfor

            <small class="text-muted d-block mt-2">
                <i class="fa fa-info-circle"></i> Draw order: position {{ $total }} first, position 1 last.
            </small>
        </div>
    </div>

</div>

@endsection

@push('custom_scripts')
<script>
    (function () {
        var startForm = document.getElementById('startForm');
        if (startForm) {
            startForm.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Start this lottery?',
                    text: 'The status will change to Running and winner draws will be enabled.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, start',
                }).then(function (result) {
                    if (result.isConfirmed || result.value) {
                        startForm.submit();
                    }
                });
            });
        }

        // Winner details popup on the prize board
        var winners = @json($winnerDetails);

        function esc(value) {
            var div = document.createElement('div');
            div.textContent = value == null || value === '' ? 'N/A' : value;
            return div.innerHTML;
        }

        function item(label, value, full) {
            return '<div class="wd-item' + (full ? ' full' : '') + '"><small>' + label + '</small><span>' + esc(value) + '</span></div>';
        }

        function section(title, items) {
            return '<div class="wd-section-title">' + title + '</div><div class="wd-grid">' + items.join('') + '</div>';
        }

        function tag(text, type) {
            return '<span class="wd-tag ' + type + '">' + esc(text) + '</span>';
        }

        document.querySelectorAll('.ld-board-item[data-position]').forEach(function (el) {
            el.addEventListener('click', function () {
                var w = winners[el.getAttribute('data-position')];
                if (!w) return;

                var image = w.gift_image
                    ? '<img src="' + esc(w.gift_image) + '" alt="Gift">'
                    : '<span class="wd-ph"><i class="fa fa-gift"></i></span>';

                var location = [w.thana, w.district, w.division].filter(Boolean).join(', ');

                var avatar = w.profile_image
                    ? '<img class="wd-avatar" src="' + esc(w.profile_image) + '" alt="" onerror="this.style.display=\'none\'">'
                    : '';

                var tags =
                    tag(w.phone_verified ? 'Phone verified' : 'Phone not verified', w.phone_verified ? 'ok' : 'warn') +
                    tag(w.account_active ? 'Active account' : 'Inactive account', w.account_active ? 'ok' : 'warn') +
                    (w.other_wins > 0 ? tag('Won ' + w.other_wins + 'x in other lotteries', 'warn') : '');

                Swal.fire({
                    width: 680,
                    showCloseButton: true,
                    showConfirmButton: false,
                    html:
                        '<div class="wd-head">' + image +
                            '<div style="flex:1">' +
                                '<div class="wd-pos">POSITION ' + esc(w.position) + ' WINNER</div>' +
                                '<div class="wd-name">' + esc(w.name) + '</div>' +
                                '<div class="wd-gift"><i class="fa fa-gift"></i> ' + esc(w.gift_name) +
                                    ' &middot; drawn ' + esc(w.draw_time) + '</div>' +
                                '<div class="wd-tags">' + tags + '</div>' +
                            '</div>' + avatar +
                        '</div>' +
                        '<div class="wd-body">' +
                            section('Points', [
                                item('In lottery period', Number(w.total_points).toLocaleString()),
                                item('Point entries', w.entries),
                                item('Lifetime points', Number(w.lifetime_points).toLocaleString()),
                                item('Member since', w.member_since),
                            ]) +
                            section('Contact', [
                                item('Phone', w.phone),
                                item('User ID', w.user_id),
                                item('Payment number', w.payment_number),
                                item('Location', location),
                                item('Current address', w.current_address, true),
                                item('Permanent address', w.permanent_address, true),
                            ]) +
                            section('Personal', [
                                item('Father name', w.father_name),
                                item('Date of birth', w.birthday),
                                item('NID number', w.nid_number),
                                item('Blood group', w.blood_group),
                                item('Education', w.education),
                                item('Occupation', w.occupation),
                                item('Experience', w.experience),
                                item('Organization', w.organization),
                            ]) +
                            section('Sales network', [
                                item('FO', w.fo),
                                item('TSM', w.tsm),
                                item('Point', w.point),
                                item('Dealer', w.dealer),
                            ]) +
                        '</div>'
                });
            });
        });

        var drawForm = document.getElementById('drawForm');
        if (!drawForm) return;

        var names = @json($shuffleNames->values());
        var reel = document.getElementById('reel');
        var button = document.getElementById('drawBtn');
        var submitting = false;

        drawForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (submitting) return;
            submitting = true;

            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Drawing...';
            reel.classList.add('rolling');

            // Shuffle names with a slowing rhythm, then submit for the real (server side) pick
            var delay = 50;
            var elapsed = 0;

            function tick() {
                if (names.length) {
                    reel.textContent = names[Math.floor(Math.random() * names.length)];
                }
                elapsed += delay;
                if (elapsed < 3000) {
                    delay = Math.min(delay * 1.08, 250);
                    setTimeout(tick, delay);
                } else {
                    reel.textContent = '...';
                    drawForm.submit();
                }
            }

            tick();
        });
    })();
</script>
@endpush
