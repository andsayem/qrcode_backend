@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

    <!-- start page title -->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <h2>Product</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Product</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="header">
                    <h2>Filter</h2>
                </div>
                <div class="body pt-0">
                    {{Form::open(['method' => 'get'])}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="category_id" class="mb-2">Category</label>
                                {!! Form::select('category_id', ['' => 'Select Category'] + $parentcategories, request('category_id'), ['id' => 'category_id', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="product_name" class="mb-2">Product Name</label>
                                {!! Form::text('product_name', request('product_name') ?? '', ['class' => 'form-control ', 'autocomplete' => 'off', 'placeholder' => 'Product Name'])!!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sku" class="mb-2">SKU</label>
                                {!! Form::text('sku', request('sku') ?? '', ['class' => 'form-control ', 'autocomplete' => 'off', 'placeholder' => 'SKU'])!!}
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp</label>
                                <div>
                                    <button type="submit" class="btn btn-success mr-2"><i
                                            class="fa fa-search mr-1"></i>Filter</button>
                                    <a type="button" class="btn btn-warning mr-2"
                                        href="{{ route('admin.products.index') }}"><i class="fa fa-refresh mr-1"></i>
                                        <span>Reset</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end /div-->
                    {{ Form::close() }}
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>

    <div class="card">
        <div class="header">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2>
                        Product List
                        <span class="badge badge-info fill"> {{ $products->total() }}</span>
                    </h2>
                </div>
                <div class="col-lg-6 text-right">
                    @can('product-create')
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm px-3 btn-info"><i
                                class="fa fa-plus"></i> <span>Create</span></a>
                        <a href="{{ route('admin.products.download') }}" class="btn btn-sm px-3 btn-info">
                            <i class="fa fa-download"></i>
                            <span>Download</span>
                        </a>
                        <a role="button" href="#" data-toggle="modal" data-target="#upload_modal"
                            class="btn btn-sm btn-info px-3"><i class="fa fa-upload mr-2"></i> <span>Upload</span></a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="body pt-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped m-b-0 c_list">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>SKU</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Point</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($products->count() > 0)
                            @foreach ($products as $i => $product)
                                <tr>
                                    <td>{{$product->product_name}}</td>
                                    <td>{{ $product->category->category_name ?? '' }}</td>
                                    <td>{{$product->sku}}</td>
                                    <td>
                                        @include('includes.status', ['status' => [['key' => 'Active', 'value' => 1, 'class' => 'badge-success'], ['key' => 'Inactive', 'value' => 0, 'class' => 'badge-danger']], 'selected' => $product->status])
                                    </td>
                                    <td>{{$product->desc}}</td>
                                    <td>{{$product->point_slab}}</td>
                                    <td>
                                        @can('product-edit')
                                            <a href="{{ route('admin.products.edit', $product->id) }}" type="button"
                                                class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

            </div>

            @include('/includes/paginate', ['paginator' => $products])
        </div>

        <div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="upload_modal"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h4 class="modal-title text-center mx-auto text-white">
                            Product Point List Upload
                        </h4>
                    </div>

                    <form action="{{ route('admin.products.upload') }}" method="POST" enctype="multipart/form-data"
                        onsubmit="return confirm('Do you really want to proceed?');">
                        @csrf

                        <div class="modal-body">
                            <div class="form-group row required">
                                <label class="col-sm-4 col-form-label">CSV File</label>
                                <div class="col-sm-8">
                                    {!! Form::file('file', ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection