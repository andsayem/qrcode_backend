@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Scan History</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ssgcodes.index') }}">SSG Code</a></li>
                <li class="breadcrumb-item active">Scan History</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="card">
    <div class="header">
        <h2>
            {{ $ssgcode->product->sku ?? '' }} ({{ $ssgcode->product->product_name ?? '' }})
            &mdash; {{ $ssgcode->code }}
        </h2>
    </div>
    <div class="body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mobile</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Address</th>
                        <th>Lat - Long</th>
                        <th>IP</th>
                        <th>Checked At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->mobile_no }}</td>
                        <td>
                            @if($log->product_id)
                                {{ $log->product->sku ?? '' }} ({{ $log->product->product_name ?? '' }})
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'success')
                                <span class="badge badge-success">Success</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($log->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $log->address }}</td>
                        <td>
                            @if($log->lat && $log->long)
                                <a href="https://www.google.com/maps?q={{ $log->lat }},{{ $log->long }}" target="_blank" rel="noopener">
                                    {{ $log->lat }} - {{ $log->long }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $log->requested_ip }}</td>
                        <td>{{ optional($log->created_at)->format('d-m-Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            No scan history found for this code
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <nav class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted">
                Showing {{ $logs->firstItem() }} &ndash; {{ $logs->lastItem() }} of {{ $logs->total() }} entries
            </span>
            <div>{{ $logs->links() }}</div>
        </nav>
        @endif
    </div>
</div>

@endsection
