@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Edit Lottery Gift</h2>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.lottery-gifts.index') }}">Lottery Gifts</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="header">
        <h2>Edit Gift Details</h2>
    </div>
    <div class="body">

        <form action="{{ route('admin.lottery-gifts.update', $gift->id) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Title</label>
                <input type="text"
                       name="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $gift->title) }}"
                       placeholder="Enter gift title"
                       required>
                @error('title')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label>Gift Name</label>
                <input type="text"
                       name="gift_name"
                       class="form-control @error('gift_name') is-invalid @enderror"
                       value="{{ old('gift_name', $gift->gift_name) }}"
                       placeholder="Enter specific gift name"
                       required>
                @error('gift_name')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label>Gift Image</label>
                <input type="file"
                       name="gift_image"
                       class="form-control @error('gift_image') is-invalid @enderror">
                
                @if($gift->gift_image)
                    <div class="mt-2">
                        <label>Current Image:</label><br>
                        <img src="{{ asset('uploads/lottery_gifts/' . $gift->gift_image) }}" alt="Gift Image" width="100" class="img-thumbnail">
                    </div>
                @endif

                @error('gift_image')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update Gift
                </button>
                <a href="{{ route('admin.lottery-gifts.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
