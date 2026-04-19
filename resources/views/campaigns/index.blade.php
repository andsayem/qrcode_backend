@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Campaigns</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">Campaigns</li>
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
                            Campaigns List
                            <span class="badge badge-info fill"> {{ count($campaigns) }}</span>
                        </h2>
                    </div>
                    <div class="col-lg-6 text-right">
                        @can('category-create')
                        <a href="{{ route('campaigns.create') }}" class="btn btn-sm px-3 btn-info"><i class="fa fa-plus"></i> <span>Create</span></a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="body pt-0">  
                <div class="table-responsive">
                    <table class="table" id="campaigns-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>Campaign Category Id</th>
                            <th>Campaign Date</th>
                            <th>Campaign With Product</th>
                            <th>Title</th>
                            <th>Image / Youtube Video Link</th>
                            <th colspan="3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($campaigns as $campaign)
                            <tr>
                                <td>
                                    {{$campaign->category ? $campaign->category->category_name : 'N/A' }}
                                </td>
                                <td>
                                    <b>Start Date:</b> {{ date('Y-m-d',strtotime($campaign->start_date)) }}<br/>
                                    <b>End Date:</b> {{ date('Y-m-d',strtotime($campaign->end_date)) }}
                                </td>
                                <td>
                                    <b>Campaign Type:</b> {{$campaign->campaign_type}}<br/>
                                    <b>Product:</b> {{$campaign->product != null ? $campaign->product->product_name : 'N/A' }} <br/>
                                    <b>Point:</b> {{ $campaign->point }}
                                </td>
                                <td style="width: 300px;max-width: 300px;white-space: normal;overflow-wrap: break-word;word-break: normal;">{{ \Illuminate\Support\Str::limit($campaign->title, 100) }}</td>
                                <td>
                                    <b>Content Type:</b> {{$campaign->content_type == "link" ? 'Youtube Link': $campaign->content_type}}<br/>
                                    @if($campaign->content_type == "image")
                                        @if($campaign->image)
                                            <img src="{{ asset('storage/campaign/'.$campaign->image) }}" style="width: 120px;" alt="Image ss">
                                        @else
                                            <img src="{{ asset('no-image.png') }}" style="width: 120px;" alt="Image">
                                        @endif
                                    @else
                                        <a href="{{ $campaign->link }}" target="_blank">Click here for Link</a>
                                    @endif
                                </td>
                                <td width="120">
                                    {!! Form::open(['route' => ['campaigns.destroy', $campaign->id], 'method' => 'delete']) !!}
                                    <div class='btn-group'>
                                        {{--                        <a href="{{ route('campaigns.show', [$campaign->id]) }}" class='btn btn-default btn-xs'>--}}
                                        {{--                            <i class="fa fa-eye" aria-hidden="true"></i>--}}
                                        {{--                        </a>--}}
                                        <a href="{{ route('campaigns.edit', [$campaign->id]) }}" class='btn btn-default btn-xs'>
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        </a>
                                        {!! Form::button('<i class="fa fa-trash" aria-hidden="true"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                                    </div>
                                    {!! Form::close() !!}
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


</div>
@endsection