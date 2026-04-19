@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Redeem </h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Redeem</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

            <!--end card-body-->
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2>
                            Redeem List 
                            <span class="badge badge-info fill"> {{ count($items) }}</span>
                        </h2>
                    </div>
                     
                </div>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>Technician</th>
                                <th>Point</th>
                                <th>Amount (BDT)</th>
                                <th>Status</th>
                                <th>Approved Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $i => $item)
                            <tr>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->point }}</td>
                                <td>{{ $item->amount }}</td>
                                <td>
                                    @include('includes.status', ['status' => [['key' => 'Paid', 'value' => 1, 'class'=> 'badge-success'], ['key' => 'Pending', 'value' => 0, 'class'=> 'badge-danger']], 'selected'=> $item->status])
                                </td>
                                <td>{{ $item->updated_at}}</td>
                                <td>
                                    <a role="button" href="#" data-toggle="modal" data-target="#approval_modal" data-id='{{ $item->id }}' data-status='{{ $item->id }}' type="button" class="btn btn-outline-info btn-sm mr-2" title="Aprrove">
                                        <i class="fa fa-shield"></i>
                                    </a>

                                    <a role="button" href="{{ route('admin.redeem.pending_redeem_delete',$item->id) }}" type="button" class="btn btn-outline-danger btn-sm mr-2" title="Aprrove">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="approval_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="approval_modal">Approval</h4>
            </div>
            <form action="{{ route('admin.redeem.approval') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');">
                @csrf
                <div class="modal-body">
                    {!! Form::number('id', null, ['class' => 'form-control id', 'hidden'=>'hidden']) !!}
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label control-label">Status</label>
                            <div class="col-sm-8">

                                <select name="status" id="status" class="form-control status">
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="comment-section">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label control-label">Comments</label>
                            <div class="col-sm-8">
                                {!! Form::textarea('details', null, ['id'=> 'details','class' => 'form-control comments','rows' => '4']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button>
                        <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="upload_modal" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="upload_modal">Payment Paid List Upload</h4>
            </div>
            <form action="{{ route('admin.redeem.redeem_paid_list') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');"  enctype="multipart/form-data" >
                @csrf

                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">CSV File</label>
                            <div class="col-sm-8">
                                {!! Form::file('csv_file',['class' => 'form-control','required' => true ]) !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-right mr-1" data-dismiss="modal">Cancel</button> 
                    <button data-toggle="modal" type="submit" class="btn btn-primary mr-2 float-right" id="formSubmit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
@push('custom_scripts')
<script type="text/javascript">
    $(function() {
        $('#approval_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var status = button.data('status');
            var modal = $(this);
            modal.find('.modal-body .id').val(id);
            //modal.find('.modal-body .status').val(status);
        });
    });
</script>
@endpush