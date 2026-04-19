@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Downlaod</h5>
        <p class="card-text"><a href="{{ route('report_download_generate') }}" target="_blank">Report Download</a></p>
        <p class="card-text"><a href="{{ route('report_download_lock') }}" target="_blank">Report Lock</a></p>
    </div>
</div>

@endsection

@push('custom_scripts')

@endpush
