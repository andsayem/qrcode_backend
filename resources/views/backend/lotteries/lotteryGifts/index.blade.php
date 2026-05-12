@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Lottery Gifts</h2>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Lottery Gifts</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">

    <div class="header">
        <div class="row align-items-center">

            <div class="col-lg-6">
                <h2>
                    Lottery Gifts
                    <span class="badge badge-info fill">
                        {{ $gifts->total() }}
                    </span>
                </h2>
            </div>

            <div class="col-lg-6 text-right">
                <a href="{{ route('admin.lottery-gifts.create') }}" class="btn btn-sm px-3 btn-info">
                    <i class="fa fa-plus"></i> Create Gift
                </a>
            </div>

        </div>
    </div>

    <div class="body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Title</th>
                        <th>Gift Name</th>
                        <th>Image</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($gifts as $key => $gift)
                        <tr>
                            <td>{{ ($gifts->currentPage() - 1) * $gifts->perPage() + $loop->iteration }}</td>
                            <td>{{ $gift->title }}</td>
                            <td>{{ $gift->gift_name }}</td>

                            <td>
                                @if($gift->gift_image)
                                    <img src="{{ asset('uploads/lottery_gifts/'.$gift->gift_image) }}"
                                         width="50" height="50"
                                         style="object-fit: cover; border-radius: 6px;">
                                @else
                                    <span class="badge badge-secondary">No Image</span>
                                @endif
                            </td>

                            <td class="text-right">
                                <a href="{{ route('admin.lottery-gifts.edit', $gift->id) }}"
                                   class="btn btn-outline-info btn-sm mr-2">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.lottery-gifts.destroy', $gift->id) }}"
                                      method="POST"
                                      style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this gift?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No lottery gifts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('/includes/paginate', ['paginator' => $gifts])
    </div>

</div>

@endsection