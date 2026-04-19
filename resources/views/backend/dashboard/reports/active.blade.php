<div class="card shadow-sm border-0">

    {{-- Generate Dynamic Months --}}
    @php
        use Carbon\Carbon;

        $months = [];
        $start = Carbon::parse($fromDate)->startOfMonth();
        $end = Carbon::parse($toDate)->endOfMonth();
        $current = $start->copy();

        while ($current <= $end) {
            $months[] = [
                'alias' => strtolower($current->format('M')) . '_' . $current->year,
                'label' => $current->format('M-Y')
            ];
            $current->addMonth();
        }
    @endphp

    {{-- Header --}}
    <div class="card-header bg-light">
        <h4 class="mb-0 fw-bold" style="color:#1d3557;">
            Division Wise - <span style="color:#000;">(Active User)</span>
        </h4>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead style="background:#f1e0a6;font-weight:600;">
                    <tr>
                        <th>SL</th>
                        <th>Division Name</th>
                        <th>Active User Qty</th>
                        <th>Total Point</th>

                        {{-- Dynamic Month Headers --}}
                        @foreach($months as $month)
                            <th>{{ $month['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->division }}</td>
                            <td>{{ $row->active_users }}</td>
                            <td>{{ $row->total_points }}</td>

                            {{-- Dynamic Month Data --}}
                            @foreach($months as $month)
                                <td>{{ $row->{$month['alias']} ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + count($months) }}">No Data Found</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>