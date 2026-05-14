{{-- filepath: /c:/laragon/www/qrcode_backend/resources/views/backend/lotteries/winners/show.blade.php --}}
@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')
    

@section('content')
<div class="card">
    <div class="header">
        <h2>Winner Details</h2>
    </div>

    <div class="body">
        <table class="table table-bordered">
            <tr>
                <th>User ID</th>
                <td>{{ $winner->user_id }}</td>
            </tr>
            <tr>
                <th>Winner Name</th>
                <td>{{ $winner->winner_name }}</td>
            </tr>
            <tr>
                <th>Mobile No</th>
                <td>{{ $winner->user->email }}</td>
            </tr>
            <tr>
                <th>Lottery Title</th>
                <td>{{ $winner->lottery->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Gift Name</th>
                <td>{{ $winner->giftAssign->gift->gift_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Winning Position</th>
                <td>{{ $winner->position }}</td>
            </tr>
            <tr>
                <th>Draw Time</th>
                <td>{{ $winner->draw_time ? $winner->draw_time->format('d M Y, h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Joining Date</th>
                <td>{{ date('d M Y', strtotime($winner->user->created_at)) }}</td>
            </tr>
            <tr>
                <th>Required Points for Eligibility During Lottery Period</th>
                <td>{{ $winner->lottery->required_points}}</td>
            </tr>
            <tr>
                <th>Points Achieved During Lottery Period</th>
                <td>{{ number_format($achievedPoints) }}</td>
            </tr>
        </table>

        <a href="{{ route('admin.lottery-winners.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection