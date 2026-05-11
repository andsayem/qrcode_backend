@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Create Lottery</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="body">

        <form action="{{ route('admin.lotteries.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Required Points</label>
                <input type="number" name="required_points" class="form-control" required>
            </div>

            <!-- NEW FIELD -->
            <div class="form-group">
                <label>Total Winners</label>
                <input type="number" name="total_winners" class="form-control" min="1" value="1" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="running">Running</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <button class="btn btn-primary">Save Lottery</button>

        </form>

    </div>
</div>

@endsection