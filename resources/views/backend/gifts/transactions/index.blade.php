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

        <div class="header">
            <h2>
                @if ($status == 0)
                    Pending Requests
                @elseif($status == 1)
                    Approved Requests
                @elseif($status == 2)
                    Rejected Requests
                @elseif($status == 'sent')
                    Sent Requests
                @endif
            </h2>
        </div>

        <div class="body pt-0">

            {{-- ================= FILTER ================= --}}
            <form method="GET" class="mb-3">
                <div class="row">

                    {{-- USER SEARCH --}}
                    <div class="col-md-3 mb-3">
                        <input type="text" name="user" value="{{ request('user') }}" class="form-control"
                            placeholder="Name / Phone">
                    </div>

                    {{-- GIFT --}}
                    <div class="col-md-3 mb-3">
                        <select name="gift_id" class="form-control">
                            <option value="">All Gifts</option>
                            @foreach (\App\Models\Gift::select('id', 'gift_name')->get() as $gift)
                                <option value="{{ $gift->id }}" {{ request('gift_id') == $gift->id ? 'selected' : '' }}>
                                    {{ $gift->gift_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    {{-- POLICY --}}
                    <div class="col-md-3 mb-3">
                        <select name="policy_id" class="form-control">
                            <option value="">All Policy</option>
                            @foreach (\App\Models\GiftPolicy::select('id', 'program_name')->get() as $policy)
                                <option value="{{ $policy->id }}"
                                    {{ request('policy_id') == $policy->id ? 'selected' : '' }}>
                                    {{ $policy->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- REQUEST STATUS --}}
                    <div class="col-md-3 mb-3">
                        <select name="request_status" id="request_status" class="form-control">
                            <option value="0" {{ request('request_status') == '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ request('request_status') == '1' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="2" {{ request('request_status') == '2' ? 'selected' : '' }}>Rejected
                            </option>
                            <option value="sent" {{ request('request_status') == 'sent' ? 'selected' : '' }}>Sent
                            </option>
                        </select>
                    </div>

                    {{-- FROM DATE --}}
                    <div class="col-md-3 mb-3">
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                    </div>

                    {{-- TO DATE --}}
                    <div class="col-md-3 mb-3">
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                    </div>

                    {{-- BUTTON --}}
                    <div class="col-md-1 mb-3">
                        <button class="btn btn-primary btn-block">
                            Filter
                        </button>
                    </div>
                    <div class="col-md-1.5 mb-3">
                        @php
                            $gift_transaction_download =
                                'user=' .
                                request('user') .
                                '&gift_id=' .
                                request('gift_id') .
                                '&policy_id=' .
                                request('policy_id') .
                                '&request_status=' .
                                request('request_status') .
                                '&from_date=' .
                                request('from_date') .
                                '&to_date=' .
                                request('to_date');
                        @endphp
                        <a href="{{ route('admin.gift.transactions.export', request()->all()) }}"
                            class="btn btn-block btn-info">
                            <i class="fa fa-download"></i> Download Excel
                        </a>
                    </div>

                </div>
            </form>

            {{-- ================= TABLE ================= --}}
            @php
                $hasBulkAction = ($status == 0 && $transactions->count() > 0) || 
                                 ($status == 1 && $transactions->contains('delivery_status', 'not_sent'));
            @endphp
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
                            <th width="240">
                                Actions

                            </th>
                            @if ($hasBulkAction)
                            <th> 
                                @if ($status !=2 && $status != 'sent')
                                <label><input type="checkbox" id="checkAll">Check All</label> {{-- Check All Checkbox --}}
                                <button type="button" id="bulk-action-btn" onclick="bulkAction()"
                                    class="btn btn-sm px-3 btn-info">
                                    @if ($status == 0)
                                        Bulk Approve
                                    @elseif($status == 1)
                                        Bulk Send
                                    @endif
                                </button>
                                @endif
                            </th>
                            @endif
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
                                    {{ $item->requested_at ? \Carbon\Carbon::parse($item->requested_at)->format('d M Y') : '-' }}
                                </td>

                                {{-- REQUEST STATUS --}}
                                <td>
                                    @if ($item->request_status == 0)
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($item->request_status == 1)
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($item->request_status == 2)
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>

                                {{-- DELIVERY STATUS --}}
                                <td>
                                    @if ($item->delivery_status == 'not_sent')
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
                                            class="btn btn-info mr-1">
                                            View
                                        </a>

                                        {{-- APPROVE / REJECT --}}
                                        @if ($item->request_status == 0)
                                            <form action="{{ route('admin.gift.transactions.approve', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                <button class="btn btn-success mr-1" onclick="return confirm('Approve?')">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.gift.transactions.reject', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                <button class="btn btn-danger" onclick="return confirm('Reject?')">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif

                                        {{-- SEND --}}
                                        @if ($item->request_status == 1 && $item->delivery_status == 'not_sent')
                                            <form action="{{ route('admin.gift.transactions.send', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                <button class="btn btn-primary" onclick="return confirm('Send gift?')">
                                                    Send
                                                </button>
                                            </form>
                                        @endif

                                    </div>

                                </td>
                                @if ($hasBulkAction)
                                <td>
                                    @if ($status !=2)
                                    @if ($item->delivery_status == 'not_sent')
                                    <input type="checkbox" name="transaction_ids[]" value="{{ $item->id }}"
                                        class="checkbox">
                                    @endif
                                    @endif
                                    
                                </td>
                                @endif

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

    @push('custom_scripts')
        <script>
            $(document).ready(function() {

                let status = @json($status);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');

                // ================= CHECK ALL =================
                $('#checkAll').on('change', function() {
                    $('.checkbox').prop('checked', $(this).prop('checked'));
                });

                // ================= INDIVIDUAL CHECK =================
                $(document).on('change', '.checkbox', function() {

                    if (!$(this).prop('checked')) {
                        $('#checkAll').prop('checked', false);
                    }

                    if ($('.checkbox:checked').length === $('.checkbox').length) {
                        $('#checkAll').prop('checked', true);
                    }
                });

                // ================= BULK ACTION =================
                window.bulkAction = function() {

                    let selected = [];

                    $('.checkbox:checked').each(function() {
                        selected.push($(this).val());
                    });

                    if (selected.length === 0) {
                        alert('Please select at least one item');
                        return;
                    }

                    if (status == 0 && !confirm('Approve selected gifts?')) return;
                    if (status == 1 && !confirm('Send selected gifts?')) return;

                    let url = '';

                    if (status == 0) {
                        url = "{{ route('admin.gift.transactions.bulk_approve') }}";
                    } else if (status == 1) {
                        url = "{{ route('admin.gift.transactions.bulk_send') }}";
                    } else {
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            transaction_ids: selected,
                            _token: csrfToken
                        },
                        success: function(response) {
                            alert(response.message || 'Success');
                            location.reload();
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert('Something went wrong');
                        }
                    });
                };


            });
            // ================= AUTO FILTER ON REQUEST STATUS =================
            $('#request_status').on('change', function() {


                let form = $(this).closest('form');

                // submit form automatically
                form.submit();

            });
        </script>
    @endpush
@endsection
