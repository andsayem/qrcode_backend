@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Gift Policies</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Gift Policies</li>
            </ul>
        </div>
    </div>
</div>

@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ Session::get('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>
                    Gift Policies
                    <span class="badge badge-info fill"> {{ $giftPolicies->total() }}</span>
                </h2>
            </div>
            <div class="col-lg-6 text-right">
                <button type="button" class="btn btn-sm px-3 btn-info" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-plus"></i> <span>Create</span>
                </button>
            </div>
        </div>
    </div>

    <div class="body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-b-0 c_list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Program Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Gifts</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($giftPolicies as $i => $policy)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $policy->program_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($policy->start_date)->format('d-M-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($policy->end_date)->format('d-M-Y') }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $policy->gifts_count }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-success btn-sm mr-2 view-btn" 
                                    data-policy="{{ json_encode($policy) }}" data-toggle="modal" data-target="#viewModal" title="View">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button type="button" class="btn btn-outline-info btn-sm mr-2 edit-btn" 
                                    data-policy="{{ json_encode($policy) }}" data-toggle="modal" data-target="#editModal" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                
                                {{ Form::open(['route' => ['admin.gift-policies.destroy', $policy->id], 'method' => 'DELETE', 'style' => 'display:inline-block;']) }}
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this policy?')" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No Data Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('/includes/paginate', ['paginator' => $giftPolicies])
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.gift-policies.store', 'method' => 'POST', 'files' => true]) }}
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add Gift Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-12 mb-3">
                        <label for="program_name" class="mb-2">Program Name <span class="text-danger">*</span></label>
                        {{ Form::text('program_name', old('program_name'), ['class' => 'form-control', 'required', 'placeholder' => 'Enter Program Name']) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="mb-2">Start Date <span class="text-danger">*</span></label>
                        {{ Form::date('start_date', old('start_date'), ['class' => 'form-control', 'required']) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="mb-2">End Date <span class="text-danger">*</span></label>
                        {{ Form::date('end_date', old('end_date'), ['class' => 'form-control', 'required']) }}
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="image" class="mb-2">Policy Details Image</label>
                        {{ Form::file('image', ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.gift-policies.update', 0], 'method' => 'PUT', 'id' => 'editForm', 'files' => true]) }}
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Gift Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-12 mb-3">
                        <label for="edit_program_name" class="mb-2">Program Name <span class="text-danger">*</span></label>
                        {{ Form::text('program_name', null, ['class' => 'form-control', 'id' => 'edit_program_name', 'required']) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_start_date" class="mb-2">Start Date <span class="text-danger">*</span></label>
                        {{ Form::date('start_date', null, ['class' => 'form-control', 'id' => 'edit_start_date', 'required']) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_end_date" class="mb-2">End Date <span class="text-danger">*</span></label>
                        {{ Form::date('end_date', null, ['class' => 'form-control', 'id' => 'edit_end_date', 'required']) }}
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_image" class="mb-2">Update Image</label>
                        {{ Form::file('image', ['class' => 'form-control', 'id' => 'edit_image']) }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Update</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Gift Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="40%">Program Name</th>
                                    <td id="view_program_name"></td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td id="view_start_date"></td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td id="view_end_date"></td>
                                </tr>
                                <tr>
                                    <th>Gifts Count</th>
                                    <td id="view_gifts_count"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-5 text-center">
                        <h6>Policy Image</h6>
                        <img id="view_policy_image" src="" alt="No Image" style="max-width: 100%; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Edit button click
        $('.edit-btn').on('click', function() {
            let policy = $(this).data('policy');
            
            $('#edit_program_name').val(policy.program_name);
            $('#edit_start_date').val(policy.start_date);
            $('#edit_end_date').val(policy.end_date);
            
            let formAction = "{{ route('admin.gift-policies.update', ':id') }}";
            formAction = formAction.replace(':id', policy.id);
            $('#editForm').attr('action', formAction);
        });

        // View button click
        $('.view-btn').on('click', function() {
            let policy = $(this).data('policy');
            
            $('#view_program_name').text(policy.program_name);
            $('#view_gifts_count').text(policy.gifts_count);
            
            let startDate = new Date(policy.start_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-');
            let endDate = new Date(policy.end_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-');

            $('#view_start_date').text(startDate);
            $('#view_end_date').text(endDate);

            if (policy.image) {
                $('#view_policy_image').attr('src', "{{ asset('storage') }}/" + policy.image);
            } else {
                $('#view_policy_image').attr('src', 'https://via.placeholder.com/200?text=No+Image');
            }
        });
    });
</script>
@endsection