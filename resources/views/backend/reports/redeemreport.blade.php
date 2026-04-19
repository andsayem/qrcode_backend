@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Redeem</h2>
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


</div>
@endsection