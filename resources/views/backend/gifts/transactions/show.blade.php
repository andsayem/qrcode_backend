@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<style>
.page-center {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px 10px;
}

.tracking-card {
    width: 100%;
    max-width: 750px;
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    text-align: center;
}

.title {
    font-weight: 600;
    margin-bottom: 20px;
}

.sub-info {
    margin-bottom: 20px;
    color: #555;
    line-height: 1.8;
}

.badge-id {
    background: #343a40;
    color: #fff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.timeline {
    margin-top: 25px;
}

.timeline-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

.timeline-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    color: #fff;
    margin-bottom: 8px;
}

.timeline-line {
    width: 3px;
    height: 30px;
    background: #eee;
    margin: auto;
}

.done { background: #28a745 !important; }
.rejected { background: #dc3545 !important; }
.pending { background: #c7c7c7 !important; }
.active { background: #17a2b8 !important; }

.step-text h6 {
    margin: 0;
    font-weight: 600;
}

.step-text small {
    color: #777;
}

.info-box {
    text-align: left;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 14px;
}
</style>

<div class="page-center">

    <div class="tracking-card">

        <h4 class="title">🎁 Gift Tracking Details</h4>

        {{-- TRANSACTION ID --}}
        <div class="mb-3">
            <span class="badge-id">
                Transaction ID: #{{ $transaction->id }}
            </span>
        </div>

        {{-- USER + GIFT INFO --}}
        <div class="info-box">
            <div><b>User Name:</b> {{ $transaction->user->name ?? '-' }}</div>
            <div><b>Email:</b> {{ $transaction->user->email ?? '-' }}</div>
            <div><b>Phone:</b> {{ $transaction->user->phone ?? '-' }}</div>
            <hr>
            <div><b>Gift:</b> {{ $transaction->gift->gift_name ?? '-' }}</div>
            <div><b>Policy:</b> {{ $transaction->policy->program_name ?? '-' }}</div>
        </div>

        <hr>

        {{-- TIMELINE --}}
        <div class="timeline">

            {{-- REQUESTED --}}
            <div class="timeline-step">
                <div class="timeline-icon {{ $transaction->requested_at ? 'done' : 'pending' }}">
                    <i class="fa fa-paper-plane"></i>
                </div>
                <div class="step-text">
                    <h6>Requested</h6>
                    <small>
                        {{ $transaction->requested_at
                            ? \Carbon\Carbon::parse($transaction->requested_at)->format('d M Y, h:i A')
                            : 'Pending' }}
                    </small>
                </div>
            </div>

            <div class="timeline-line"></div>

            {{-- APPROVED --}}
            <div class="timeline-step">
                <div class="timeline-icon {{ $transaction->request_status == 1 ? 'done' : ($transaction->request_status == 2 ? 'rejected' : 'pending') }}">
                    <i class="fa {{ $transaction->request_status == 2 ? 'fa-times' : 'fa-check' }}"></i>
                </div>
                <div class="step-text">
                    <h6>{{ $transaction->request_status == 2 ? 'Rejected' : 'Approved' }}</h6>
                    <small>
                        {{ $transaction->approved_at
                            ? \Carbon\Carbon::parse($transaction->approved_at)->format('d M Y, h:i A')
                            : 'Pending' }}
                    </small>
                </div>
            </div>

            <div class="timeline-line"></div>

            {{-- SENT --}}
            <div class="timeline-step">
                <div class="timeline-icon {{ $transaction->delivery_status != 'not_sent' ? 'done' : 'pending' }}">
                    <i class="fa fa-truck"></i>
                </div>
                <div class="step-text">
                    <h6>Sent</h6>
                    <small>
                        {{ $transaction->sent_at
                            ? \Carbon\Carbon::parse($transaction->sent_at)->format('d M Y, h:i A')
                            : 'Not Sent Yet' }}
                    </small>
                </div>
            </div>

            <div class="timeline-line"></div>

            {{-- RECEIVED --}}
            <div class="timeline-step">
                <div class="timeline-icon {{ $transaction->delivery_status == 'received' ? 'active' : 'pending' }}">
                    <i class="fa fa-gift"></i>
                </div>
                <div class="step-text">
                    <h6>Received</h6>
                    <small>
                        {{ $transaction->received_at
                            ? \Carbon\Carbon::parse($transaction->received_at)->format('d M Y, h:i A')
                            : 'Not Received Yet' }}
                    </small>
                </div>
            </div>

        </div>

        <hr>

        <a href="{{ route('admin.gift.transactions.index') }}" class="btn btn-secondary">
            ← Back
        </a>

    </div>

</div>

@endsection