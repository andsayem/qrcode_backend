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
            <div class="header">
                <h2>Filter </h2>
            </div>
            <div class="body pt-0">
                {{ Form::open(['method' => 'get', 'id' => 'filterForm']) }}
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name" class="mb-2">Name Or Phone Number</label>
                            {!! Form::text('name', $get_name, [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'placeholder' => 'Enter Name or Phone Number'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="name" class="mb-2">Payment Gateway</label>
                            {!! Form::select('payment_gateway', [ '' => 'Select One', 1 => 'bKash', 2 => 'Nagad', 3 => 'Rocket' ], $get_payment_gateway, [
                            'class' => 'select2 form-control mb-3 custom-select',
                            'id' => 'payment_gateway'
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status" class="mb-2">Status</label>
                            {!! Form::select(
                            'status',
                            [
                            'all' => 'All',
                            0 => 'Pending',
                            2 => 'Processing',
                            1 => 'Paid',
                            ],
                            request()->filled('status') ? request('status') : 0,
                            [
                            'class' => 'select2 form-control mb-3 custom-select',
                            'id' => 'status'
                            ]
                            ) !!}
                        </div>

                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from_date" class="mb-2">From Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('from_date', $get_from_date,['class'=>'form-control', 'id'=>'from_date', 'autocomplete'=>'off', 'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="to_date" class="mb-2">To Date</label>
                            <div class="input-group mb-3">
                                @include('includes.calender_prepend')
                                {!! Form::text('to_date', $get_to_date,['class'=>'form-control', 'id'=>'to_date', 'autocomplete'=>'off', 'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp</label>
                            <div>
                                <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search mr-1"></i></button>
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
                        <!-- <a role="button" href="#" data-toggle="modal" data-target="#upload_modal" class="btn btn-sm btn-info px-3"><i class="fa fa-upload mr-2"></i> <span>Upload</span></a> -->
                        <!-- <a href="{{ route('admin.redeem.redeem_request_download') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Download</span></a> -->
                        <!-- <a href="{{ route('admin.redeem.redeem_request_download', request()->all()) }}"
                            class="btn btn-sm px-3 btn-info">
                            <i class="fa fa-download"></i> <span>Download</span>
                        </a> -->
                        <!-- Download Button -->
                        <button type="button" class="btn btn-sm px-3 btn-info" id="downloadBtn">
                            <i class="fa fa-download"></i> <span>Download</span>
                        </button>
                        <!-- <button type="button" class="btn btn-info disabled" id="confirmDownloadBtn"></button> -->


                        <a role="button" href="#" data-toggle="modal" data-target="#upload_modal" class="btn btn-sm btn-info px-3"><i class="fa fa-upload mr-2"></i> <span>Upload</span></a>
                    </div>
                </div>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Request ID</th>
                                <th>Technician</th>
                                <th>Point</th>
                                <th>Amount (BDT)</th>
                                <th>Payment Gateway</th>
                                <th>Gateway Number</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $i => $item)
                            <tr>
                                <td>{{ $items->firstItem() + $i }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->name }} ({{ $item->user->email }})</td>
                                <td>{{ $item->point }}</td>
                                <td>{{ $item->amount }}</td>
                                @php
                                $gateways = [1 => 'bKash', 2 => 'Nagad', 3 => 'Rocket'];
                                @endphp
                                <td>{{ $gateways[$item->payment_gateway] ?? '' }}</td>
                                <td>{{ $item->gateway_number  }}</td>
                                <td>
                                    @include('includes.status', [
                                    'status' => [
                                    ['key' => 'Paid', 'value' => 1, 'class'=> 'badge-success'],
                                    ['key' => 'Pending', 'value' => 0, 'class'=> 'badge-danger'],
                                    ['key' => 'Processing', 'value' => 2, 'class'=> 'badge-warning'],
                                    ['key' => 'Cancel', 'value' => 3, 'class'=> 'badge-secondary'],
                                    ],
                                    'selected'=> $item->status
                                    ])
                                </td>

                                <td>{{ $item->created_at}}</td>
                                <td>{{ $item->note }}</td>

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
    document.getElementById('downloadBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const status = document.getElementById('status').value;

        if (status === '0') {
            // Show modal if status = Pending
            const modal = new bootstrap.Modal(document.getElementById('confirmDownloadModal'));
            modal.show();
        } else {
            // Direct download if already Processing
            window.location.href = "{{ route('admin.redeem.redeem_request_download', request()->all()) }}";
        }
    });
    //  document.getElementById('downloadBtn').addEventListener('click', function(e) {
    //   e.preventDefault();
    //   const status = document.getElementById('status').value;

    //   if (status === '0') {
    //     // Show confirmation modal if pending
    //     const modal = new bootstrap.Modal(document.getElementById('confirmDownloadModal'));
    //     modal.show();
    //   } else {
    //     // Direct download if already Processing
    //     window.location.href = "{{ route('admin.redeem.redeem_request_download', request()->all()) }}";
    //   }
    // });

    // // Enable Download button when any option selected
    // document.querySelectorAll('input[name="downloadOption"]').forEach(radio => {
    //   radio.addEventListener('change', function() {
    //     document.getElementById('confirmDownloadBtn').classList.remove('disabled');
    //   });
    // });

    // // Handle download button click
    // document.getElementById('confirmDownloadBtn').addEventListener('click', function() {
    //   if (this.classList.contains('disabled')) return;

    //   const selectedOption = document.querySelector('input[name="downloadOption"]:checked').value;

    //   // Close modal
    //   const modalEl = document.getElementById('confirmDownloadModal');
    //   const modal = bootstrap.Modal.getInstance(modalEl);
    //   modal.hide();

    //   // Redirect with query parameter to indicate user choice
    //   let url = "{{ route('admin.redeem.redeem_request_download', request()->all()) }}";

    //   if (selectedOption === 'process') {
    //     url += (url.includes('?') ? '&' : '?') + 'process_payment=1';
    //   } else {
    //     url += (url.includes('?') ? '&' : '?') + 'process_payment=0';
    //   }

    //   // Go to backend route (same controller)
    //   window.location.href = url;
    // });
    document.addEventListener('DOMContentLoaded', function() {
        const downloadBtn = document.getElementById('downloadBtn');
        const confirmDownloadBtn = document.getElementById('confirmDownloadBtn');
        const modalEl = $('#confirmDownloadModal'); // Bootstrap 4 uses jQuery
        const filterForm = $('form').first(); // your filter form

        // Build URL with filters + extra params
        function buildDownloadUrl(extra = {}) {
            const baseUrl = "{{ route('admin.redeem.redeem_request_download') }}";

            const form = document.querySelector('#filterForm');
            if (!form) {
                console.error('Form #filterForm not found');
                return baseUrl;
            }

            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // Merge extra params
            for (const key in extra) {
                if (extra.hasOwnProperty(key)) {
                    params.set(key, extra[key]);
                }
            }

            return `${baseUrl}?${params.toString()}`;
        }


        // Main Download button click
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const status = document.getElementById('status').value;

            if (status === '0' || status === 0) {
                // Show confirmation modal if Pending
                modalEl.modal('show');
            } else {
                // Direct download if already Processing
                window.location.href = buildDownloadUrl();
            }
        });

        // Enable Confirm Download when radio selected
        $('input[name="downloadOption"]').on('change', function() {
            confirmDownloadBtn.classList.remove('disabled');
        });

        // Confirm Download click
        confirmDownloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.classList.contains('disabled')) return;

            const selectedOption = $('input[name="downloadOption"]:checked').val();
            if (!selectedOption) return;

            // Close modal
            modalEl.modal('hide');

            // Redirect to backend with process flag
            const url = buildDownloadUrl({
                process_payment: selectedOption === 'process' ? 1 : 0
            });

            window.location.href = url;
            // Create a hidden iframe to trigger download
            let iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);

            // Optional: remove iframe after download
            setTimeout(() => document.body.removeChild(iframe), 5000);
        });
    });
</script>
@endpush