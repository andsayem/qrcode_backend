@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Edit Lottery</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="body">

        <form action="{{ route('admin.lotteries.update', $lottery->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control"
                    value="{{ $lottery->title }}" required>
            </div>

            <div class="form-group">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control"
                    value="{{ $lottery->from_date->format('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control"
                    value="{{ $lottery->to_date->format('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>Required Points</label>
                <input type="number" name="required_points" class="form-control"
                    value="{{ $lottery->required_points }}" required>
            </div>

            <!-- NEW FIELD -->
            <div class="form-group">
                <label>Total Winners</label>
                <input type="number" name="total_winners" class="form-control"
                    value="{{ $lottery->total_winners }}" min="1" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="pending" {{ $lottery->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="running" {{ $lottery->status == 'running' ? 'selected' : '' }}>Running</option>
                    <option value="completed" {{ $lottery->status == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <button class="btn btn-primary">
                Update Lottery
            </button>

        </form>

    </div>
</div>

@endsection