@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-6">
            <h2>Edit Gift</h2>
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

        <form action="{{ route('admin.gifts.update', $gift->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Policy -->
            <div class="form-group">
                <label>Gift Policy</label>
                <select name="policy_id" class="form-control" required>
                    <option value="">Select Policy</option>
                    @foreach($policies as $policy)
                    <option value="{{ $policy->id }}"
                        {{ $gift->policy_id == $policy->id ? 'selected' : '' }}>
                        {{ $policy->program_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Point Slab -->
            <div class="form-group">
                <label>Point Slab</label>
                <input type="number" name="point_slab" class="form-control"
                    value="{{ $gift->point_slab }}" required>
            </div>

            <!-- Gift Name -->
            <div class="form-group">
                <label>Gift Name</label>
                <input type="text" name="gift_name" class="form-control"
                    value="{{ $gift->gift_name }}" required>
            </div>

            <!-- Policy Type -->
            <div class="form-group">
                <label>Policy Type</label>
                <select name="policy_type" class="form-control" required>
                    <option value="instant" {{ $gift->policy_type == 'instant' ? 'selected' : '' }}>Instant</option>
                    <option value="year_end" {{ $gift->policy_type == 'year_end' ? 'selected' : '' }}>Year End</option>
                </select>
            </div>

            <!-- Gift Type -->
            <div class="form-group">
                <label>Gift Type</label>
                <select name="gift_type" class="form-control" required>
                    <option value="payment_gateway" {{ $gift->gift_type == 'payment_gateway' ? 'selected' : '' }}>
                        Payment Gateway
                    </option>
                    <option value="physical_gift" {{ $gift->gift_type == 'physical_gift' ? 'selected' : '' }}>
                        Physical Gift
                    </option>
                </select>
            </div>

            <!-- Is Point Cut -->
            <div class="form-group">
                <label>Is Point Cut?</label>
                <select name="is_point_cut" class="form-control">
                    <option value="1" {{ $gift->is_point_cut == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ $gift->is_point_cut == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <!-- Max Redeem Limit -->
            <div class="form-group">
                <label>Max Redeem Limit</label>
                <input type="number" name="max_redeem_limit"
                    class="form-control"
                    value="{{ $gift->max_redeem_limit }}">
                <small class="text-muted">Leave empty for unlimited</small>
            </div>

            <!-- Image -->
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control">

                @if($gift->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/'.$gift->image) }}"
                        width="80" style="border-radius:8px;">
                </div>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Update Gift
            </button>

        </form>

    </div>
</div>

@endsection