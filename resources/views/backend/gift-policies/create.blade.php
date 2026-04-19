@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-6">
            <h2>Create Gift Policy</h2>
        </div>

        <div class="col-lg-6 text-right">
            <a href="{{ route('admin.gift-policies.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="card">

    <div class="body">

        <form action="{{ route('admin.gift-policies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Program Name -->
            <div class="form-group">
                <label>Program Name</label>
                <input type="text" name="program_name" class="form-control" required>
            </div>

            <!-- Start Date -->
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <!-- End Date -->
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            

            <!-- Submit -->
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save
            </button>

        </form>

    </div>
</div>

@endsection