@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-6">
            <h2>Gift Assign - {{ $lottery->title }}</h2>
        </div>
        <div class="col-lg-6 text-right">
            <a href="{{ route('admin.lotteries.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="body">

        <form action="{{ route('admin.lottery-gift-assign.store', $lottery->id) }}" method="POST">
            @csrf

            @for($i = 1; $i <= $lottery->total_winners; $i++)

                @php
                $existing = $assignedGifts->where('position', $i)->first();
                @endphp

                <div class="row mb-3">

                    <!-- POSITION -->
                    <div class="col-md-2">
                        <label>Position</label>
                        <input type="text" class="form-control"
                            value="{{ $i }}" readonly>
                    </div>

                    <!-- GIFT SELECT -->
                    <div class="col-md-6">
                        <label>Select Gift</label>
                        <select name="gifts[{{ $i }}][gift_id]" class="form-control" required>
                            <option value="">-- Select Gift --</option>

                            @foreach($gifts as $gift)
                            <option value="{{ $gift->id }}"
                                {{ $existing && $existing->gift_id == $gift->id ? 'selected' : '' }}>
                                {{ $gift->gift_name }}
                            </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- hidden position -->
                    <input type="hidden" name="gifts[{{ $i }}][position]" value="{{ $i }}">

                </div>

                @endfor

                <button class="btn btn-primary">
                    Save Gift Assign
                </button>

        </form>

        <hr>

        <!-- Assigned Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Gift Name</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($assignedGifts as $item)
                    <tr>

                        <td>
                            <span class="badge badge-info">
                                {{ $item->position }}
                            </span>
                        </td>

                        <td>{{ $item->gift->gift_name ?? 'N/A' }}</td>

                        <td>
                            @if($item->gift && $item->gift->gift_image)
                            <img src="{{ asset('uploads/lottery_gifts/' . $item->gift->gift_image) }}"
                                width="40" height="40">
                            @else
                            N/A
                            @endif
                        </td>

                        <td>
                            <form action="{{ route('admin.lottery-gift-assign.destroy', $item->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No gifts assigned yet</td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection