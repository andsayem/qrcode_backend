@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="container-fluid mt-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">SMS Management</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">SMS Management</li>
            </ol>
        </nav>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="fa fa-exclamation-circle me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- SMS Form Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-envelope me-2"></i> Send SMS</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sms.send') }}" method="post" onsubmit="return confirm('Do you really want to send SMS?');">
                @csrf

                <div class="mb-3">
                    <label for="employee_type" class="form-label fw-semibold">Send To</label>
                    <select name="employee_type" id="employee_type" class="form-select form-select-lg">
                        <option value="all">All Technician</option>
                        <!-- <option value="individual">Individual Employee</option> -->
                    </select>
                </div>

                <div class="mb-3" id="individual_users_div" style="display:none;">
                    <label class="form-label fw-semibold">Select Employee(s)</label>
                    <select name="users[]" class="form-select select2-multiple" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->mobile ?? $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Message</label>
                    <textarea name="message" class="form-control form-control-lg" rows="4" placeholder="Type your message here..." required></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                    <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-paper-plane me-1"></i> Send SMS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.getElementById('employee_type').addEventListener('change', function() {
    document.getElementById('individual_users_div').style.display = this.value === 'individual' ? 'block' : 'none';
});

$(document).ready(function() {
    $('.select2-multiple').select2({
        placeholder: "Select employee(s)",
        width: '100%',
        theme: 'bootstrap-5'
    });
});
</script>

@endsection
