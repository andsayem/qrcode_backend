@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Gift Transactions</h2>
        </div>
    </div>
</div>

<div class="card">

    <div class="header">
        <h2>
            Transaction List
            <span class="badge badge-dark">
                Total: {{ $transactions->total() }}
            </span>
        </h2>
    </div>

    <div class="body pt-0">

        {{-- ================= FILTER ================= --}}
        <form method="GET" class="mb-3">
            <div class="row">

                {{-- USER SEARCH --}}
                <div class="col-md-3">
                    <input type="text"
                           name="user"
                           value="{{ request('user') }}"
                           class="form-control"
                           placeholder="Name / Phone">
                </div>

                {{-- GIFT --}}
                <div class="col-md-2">
                    <select name="gift_id" class="form-control">
                        <option value="">All Gifts</option>
                        @foreach(\App\Models\Gift::select('id','gift_name')->get() as $gift)
                            <option value="{{ $gift->id }}"
                                {{ request('gift_id') == $gift->id ? 'selected' : '' }}>
                                {{ $gift->gift_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- POLICY --}}
                <div class="col-md-2">
                    <select name="policy_id" class="form-control">
                        <option value="">All Policy</option>
                        @foreach(\App\Models\GiftPolicy::select('id','program_name')->get() as $policy)
                            <option value="{{ $policy->id }}"
                                {{ request('policy_id') == $policy->id ? 'selected' : '' }}>
                                {{ $policy->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FROM DATE --}}
                <div class="col-md-2">
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date') }}"
                           class="form-control">
                </div>

                {{-- TO DATE --}}
                <div class="col-md-2">
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date') }}"
                           class="form-control">
                </div>

                {{-- BUTTON --}}
                <div class="col-md-1">
                    <button class="btn btn-primary btn-block">
                        Filter
                    </button>
                </div>

            </div>
        </form>

        {{-- ================= TABLE ================= --}}
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Gift</th>
                        <th>Policy</th>
                        <th>Request Date</th>
                        <th>Request Status</th>
                        <th>Delivery Status</th>
                        <th width="240">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($transactions as $item)
                        <tr>

                            {{-- ID --}}
                            <td>
                                <span class="badge badge-dark">#{{ $item->id }}</span>
                            </td>

                            {{-- USER --}}
                            <td>
                                <b>{{ $item->user->name ?? '-' }}</b><br>
                                <small>{{ $item->user->email ?? '-' }}</small><br>
                                <small>{{ $item->user->phone ?? '-' }}</small>
                            </td>

                            {{-- GIFT --}}
                            <td>
                                <span class="badge badge-info">
                                    {{ $item->gift->gift_name ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- POLICY --}}
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $item->policy->program_name ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- REQUEST DATE --}}
                            <td>
                                {{ $item->requested_at
                                    ? \Carbon\Carbon::parse($item->requested_at)->format('d M Y')
                                    : '-' }}
                            </td>

                            {{-- REQUEST STATUS --}}
                            <td>
                                @if($item->request_status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->request_status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($item->request_status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-secondary">{{ $item->request_status }}</span>
                                @endif
                            </td>

                            {{-- DELIVERY STATUS --}}
                            <td>
                                @if($item->delivery_status == 'not_sent')
                                    <span class="badge badge-light">Not Sent</span>
                                @elseif($item->delivery_status == 'sent')
                                    <span class="badge badge-primary">Sent</span>
                                @elseif($item->delivery_status == 'received')
                                    <span class="badge badge-success">Received</span>
                                @else
                                    <span class="badge badge-dark">{{ $item->delivery_status }}</span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td>

                                <div class="btn-group btn-group-sm">

                                    {{-- VIEW --}}
                                    <a href="{{ route('admin.gift.transactions.show', $item->id) }}"
                                       class="btn btn-info">
                                        View
                                    </a>

                                    {{-- APPROVE / REJECT --}}
                                    @if($item->request_status == 'pending')

                                        <form action="{{ route('admin.gift.transactions.approve', $item->id) }}"
                                              method="POST">
                                            @csrf
                                            <button class="btn btn-success"
                                                    onclick="return confirm('Approve?')">
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.gift.transactions.reject', $item->id) }}"
                                              method="POST">
                                            @csrf
                                            <button class="btn btn-danger"
                                                    onclick="return confirm('Reject?')">
                                                Reject
                                            </button>
                                        </form>

                                    @endif

                                    {{-- SEND --}}
                                    @if($item->request_status == 'approved' && $item->delivery_status == 'not_sent')

                                        <form action="{{ route('admin.gift.transactions.send', $item->id) }}"
                                              method="POST">
                                            @csrf
                                            <button class="btn btn-primary"
                                                    onclick="return confirm('Send gift?')">
                                                Send
                                            </button>
                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        @include('/includes/paginate', ['paginator' => $transactions])

    </div>
</div>

@endsection