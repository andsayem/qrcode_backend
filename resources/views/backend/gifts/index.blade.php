@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Gifts</h2>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Gifts</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">

    <div class="header">
        <div class="row align-items-center">

            <div class="col-lg-6">
                <h2>
                    Gifts
                    <span class="badge badge-info fill">
                        {{ $gifts->total() ?? $gifts->count() }}
                    </span>
                </h2>
            </div>

            <div class="col-lg-6 text-right">

                <a href="{{ route('admin.gifts.create') }}" class="btn btn-sm px-3 btn-info">
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
                        <th>Policy</th>
                        <th>Gift Name</th>
                        <th>Point Slab</th>
                        <th>Type</th>
                        <th>Point Cut</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($gifts as $gift)
                        <tr>

                            <!-- Policy -->
                            <td>
                                {{ $gift->policy->program_name ?? 'N/A' }}
                            </td>

                            <!-- Gift Name -->
                            <td>{{ $gift->gift_name }}</td>

                            <!-- Point Slab -->
                            <td>{{ $gift->point_slab }}</td>

                            <!-- Gift Type -->
                            <td>
                                @if($gift->gift_type == 'instant')
                                    <span class="badge badge-success">Instant</span>
                                @else
                                    <span class="badge badge-warning">Year End</span>
                                @endif
                            </td>

                            <!-- Point Cut -->
                            <td>
                                @if($gift->is_point_cut)
                                    <span class="badge badge-danger">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>

                            <!-- Image -->
                            <td>
                                @if($gift->image)
                                    <img src="{{ asset('storage/' . $gift->image) }}"
                                         width="50" height="50"
                                         style="object-fit: cover; border-radius: 6px;">
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Action -->
                            <td>

                                @can('gift-edit')
                                    <a href="{{ route('admin.gifts.edit', $gift->id) }}"
                                       class="btn btn-outline-info btn-sm mr-2">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan

                                @can('gift-delete')
                                    <form action="{{ route('admin.gifts.destroy', $gift->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No data found</td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        @include('/includes/paginate', ['paginator' => $gifts])

    </div>
</div>

@endsection