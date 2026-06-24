@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

@php

$get_from_date = request()->filled('from_date') ? request('from_date') : '';
$get_to_date = request()->filled('to_date') ? request('to_date') : '';
$get_payment_gateway = request()->filled('payment_gateway') ? request('payment_gateway') : '';
$get_name = request()->filled('name') ? request('name') : '';
$get_status = request()->filled('status') ? request('status') : '';
@endphp


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
                            <span class="badge badge-info fill">
                                {{ $items->total() }}
                            </span>
                        </h2>
                    </div>
                    <div class="col-lg-6 text-right">

                        <button type="button" class="btn btn-sm px-3 btn-info" id="downloadBtn">
                            <i class="fa fa-download"></i> <span>Download</span>
                        </button>
                        <!-- #endregion -->
                    </div>
                </div>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>User ID</th>
                                <th>Technician</th>
                                <th>Point</th>
                                <th>Amount (BDT)</th>
                                <th>Payment Gateway</th>
                                <th>Gateway Number</th>
                                <th>Join Date</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $i => $item)
                            <tr>
                                <td>{{ $items->firstItem() + $i }}</td>
                                <td>{{ $item->user_id }}</td>
                                <td>{{ $item->user_name }} ({{ $item->email }})</td>
                                <td>{{ $item->current_point }}</td>
                                <td>{{ $item->current_point / 4 }}</td>
                                @php
                                $gateways = [1 => 'bKash', 2 => 'Nagad', 3 => 'Rocket'];
                                @endphp
                                <td>{{ $gateways[$item->payment_gateway] ?? '' }}</td>
                                <td>{{ $item->gatway_number  }}</td>


                                <td>{{ $item->created_at}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $items->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>

<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="upload_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-center mx-auto text-white" id="upload_modal">Payment Paid List Upload</h4>
            </div>
            <form action="{{ route('admin.redeem.redeem_paid_list') }}" method="post" onsubmit="return confirm('Do you really want to proceed?');" enctype="multipart/form-data">
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
<!-- Confirmation Modal -->

<!-- Modal -->
<div class="modal fade" id="confirmDownloadModal" tabindex="-1" aria-labelledby="confirmDownloadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDownloadLabel">Confirm Download</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>This data is currently <strong>Pending</strong>.</p>
                <p>Please confirm you want to mark it as <strong>Processing</strong> and start the download.</p>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="downloadOption" id="processDownload" value="process">
                    <label class="form-check-label" for="processDownload">
                        I confirm to Payment process and download.
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="downloadOption" id="onlyDownload" value="only">
                    <label class="form-check-label" for="onlyDownload">
                        Only download, not process.
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-info disabled" id="confirmDownloadBtn">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
@push('custom_scripts')
<script>
    $(function() {

        $('#downloadBtn').on('click', function(e) {
            e.preventDefault();

            window.location.href =
                "{{ route('admin.redeem.pending_points_download') }}";
        });

    });
</script>
@endpush