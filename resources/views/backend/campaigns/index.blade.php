 
@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Champaign</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
{{--                @include('channel_settings.table')--}}
                <!--start table -->
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
                                    {{$campaign->category ? $campaign->category?->category_name : 'N/A' }}
                                </td>
                                <td>
                                    <b>Start Date:</b> {{ date('Y-m-d',strtotime($campaign->start_date)) }}<br/>
                                    <b>End Date:</b> {{ date('Y-m-d',strtotime($campaign->end_date)) }}
                                </td>
                                <td style="width: 300px;max-width: 300px;white-space: normal;overflow-wrap: break-word;word-break: normal;">
                                    <b>Campaign Type:</b> {{$campaign->campaign_type}}<br/>
                                    <b>Product:</b>  {{$campaign->product != "" ? $campaign->product?->product_name.' - SKU - ('.$campaign->product?->sku.')' : 'N/A' }} <br/>
                                    <b>Point:</b> {{ $campaign->point }}
                                </td>
                                <td style="width: 300px;max-width: 300px;white-space: normal;overflow-wrap: break-word;word-break: normal;">{{ $campaign->title }}</td>
                                <td style="width: 300px;max-width: 300px;white-space: normal;overflow-wrap: break-word;word-break: normal;">
                                    <b>Content Type:</b> {{$campaign->content_type == "link" ? 'Youtube Link': $campaign->content_type}}<br/>
                                    @if($campaign->content_type == "image")
                                        @if($campaign->image)
                                            <img src="{{ asset('storage/campaign/'.$campaign->image) }}" style="width: 120px;" alt="Image ss">
                                        @else
                                            <img src="{{ asset('no-image.png') }}" style="width: 120px;" alt="Image">
                                        @endif
                                    @else
                                        <a href="{{ $campaign->link }}" target="_blank">{{ $campaign->link }}</a>
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
                <!--end table -->

                <div class="card-footer clearfix float-right">
                    <div class="float-right">
                        
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

 
@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Channel Settings</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('channelSettings.create') }}">
                        Add New
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('channel_settings.table')

                <div class="card-footer clearfix float-right">
                    <div class="float-right">
                        
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

