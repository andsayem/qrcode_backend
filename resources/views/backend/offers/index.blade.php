@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<!-- start page title -->

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Offers</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active" href="{{ route('admin.offers.index') }}">Offers</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->

{{--<div class="row">--}}
{{--    <div class="col-12">--}}
{{--        <div class="card">--}}
{{--            <div class="header">--}}
{{--                <h2>Filter</h2>--}}
{{--            </div>--}}
{{--            <div class="body pt-0">--}}

{{--                {{Form::open(['method' => 'get'])}}--}}
{{--                <div class="row">--}}

{{--                    --}}{{--<div class="col-md-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="name" class="mb-2">Parent Category</label>--}}
{{--                            {!! Form::select('parent_id', ['' => 'Select category']+ $parentcategories, request('parent_id'), ['id' =>'parent_id','class' => 'form-control select2']) !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="col-md-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="mb-2">Title</label>--}}
{{--                            {!! Form::text('title', request('title') ?? '',['class'=>'form-control ', 'autocomplete'=>'off', 'placeholder'=>'Enter Title'])!!}--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class="col-md-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="mb-2">Status</label>--}}
{{--                            {!! Form::select('is_active', ['' => 'Select Status'] + App\Utilities\Enum\StatusEnum::getKeysValues(), null, ['class' => 'form-control']) !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class="col-md-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <label>&nbsp</label>--}}
{{--                            <div>--}}
{{--                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i>Filter</button>--}}
{{--                                <a type="button" class="btn btn-warning mr-2" href="{{ route('admin.offers.index') }}"><i class="fa fa-refresh mr-1"></i> <span>Reset</span></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!--end /div-->--}}
{{--                {{ Form::close() }}--}}
{{--            </div>--}}
{{--            <!--end card-body-->--}}
{{--        </div>--}}
{{--        <!--end card-->--}}
{{--    </div>--}}
{{--    <!--end col-->--}}
{{--</div>--}}

<div class="card">

    <div class="header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>
                    Offers
                    <span class="badge badge-info fill"> {{ $offers->total() }}</span>
                </h2>
            </div>
            <div class="col-lg-6 text-right">
                @can('category-create')
                    <a href="{{ route('admin.offers.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                @endcan
            </div>
        </div>
    </div>

    <div class="body pt-0">

        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Point Value</th>
                        <th>Image</th>
                        <th>Desc</th>
                        <th>Status</th>
                        <th>Created by</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($offers->count()>0)
                        @foreach ($offers as $i =>  $offer)
                            <tr>
                                <td style="width: 300px;max-width: 300px;white-space: normal;overflow-wrap: break-word;word-break: normal;">{{ \Illuminate\Support\Str::limit($offer->title, 100) }}</td>
                                <td>{{$offer->point_value}}</td>
                                <td>
                                    <img src="{{ asset('storage/offer/' . $offer->image) }}" alt="" height="50">
                                </td>
                                <td style="width: 300px;max-width: 300px;white-space: normal;overflow-wrap: break-word;word-break: normal;">{{ \Illuminate\Support\Str::limit($offer->description, 100) }}</td>
                                <td>
                                    @include('includes.status', ['status' => [['key' => 'Active', 'value' => 1, 'class'=> 'badge-success'], ['key' => 'Inactive', 'value' => 0, 'class'=> 'badge-danger']], 'selected'=> $offer->is_active])
                                </td>
                                <td>{{ $offer->creator->name ?? '' }}</td>
                                <td>
                                    @can('offer-edit')
                                        <a href="{{ route('admin.offers.edit', $offer->id) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>

        @include('/includes/paginate', ['paginator' => $offers])
    </div>
</div>
@endsection
