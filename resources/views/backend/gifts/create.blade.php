@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-6">
            <h2>Create Gift</h2>
        </div>

        <div class="col-lg-6 text-right">
            <a href="{{ route('admin.gifts.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="card">

    <div class="body">

        <form action="{{ route('admin.gifts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Policy ID -->
            <div class="form-group">
                <label>Gift Policy</label>
                <select name="policy_id" class="form-control" required>
                    <option value="">Select Policy</option>
                    @foreach($policies as $policy)
                    <option value="{{ $policy->id }}">
                        {{ $policy->program_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Point Slab -->
            <div class="form-group">
                <label>Point Slab</label>
                <input type="number" name="point_slab" class="form-control" required>
            </div>

            <!-- Gift Name -->
            <div class="form-group">
                <label>Gift Name</label>
                <input type="text" name="gift_name" class="form-control" required>
            </div>

            <!-- Policy Type -->
            <div class="form-group">
                <label>Policy Type</label>
                <select name="policy_type" class="form-control" required>
                    <option value="instant">Instant</option>
                    <option value="year_end">Year End</option>
                </select>
            </div>

            <!-- Gift Type -->
            <div class="form-group">
                <label>Gift Type</label>
                <select name="gift_type" class="form-control" required>
                    <option value="payment_gateway">Payment Gateway</option>
                    <option value="physical_gift">Physical Gift</option>
                </select>
            </div>

            <!-- Is Point Cut -->
            <div class="form-group">
                <label>Is Point Cut?</label>
                <select name="is_point_cut" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <!-- Image -->
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                <small class="text-info">Required image size: 720x400 pixels.</small>
                @error('image')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <!-- Max Redeem Limit -->
            <div class="form-group">
                <label>Max Redeem Limit</label>
                <input type="number" name="max_redeem_limit" class="form-control">
                <small class="text-info">Leave blank for unlimited.</small>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Gift
                </button>

        </form>

    </div>
</div>

@endsection