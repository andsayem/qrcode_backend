@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Settings</h1>
                </div>
                 
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                 <div class="table-responsive">
                <table class="table" id="settings-table">
                    <thead>
                        <tr>
                            <th>Company Name</th> 
                            <th> {{ $settings[0]->company_name }}</th> 
                            <th> Action</th> 
                        </tr>
                        <tr>
                            <th>Contact Number</th>  
                            <th> {{ $settings[0]->contact_number }}</th> 
                            <th rowspan="6" align="center">  

                                {!! Form::open(['route' => ['settings.destroy', $settings[0]->id], 'method' => 'delete']) !!}
                                <div class='btn-group'> 
                                    <a href="{{ route('admin.settings.edit', [$settings[0]->id]) }}" type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></a>
                                </div>
                                {!! Form::close() !!} 

                            </th> 
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th> {{ $settings[0]->email }}</th>    
                        </tr>
                        <tr>
                            <th>Min Redeem Point</th>
                            <th>{{ $settings[0]->min_redeem_point }}</th>
                        </tr>
                        <tr>
                            <th>Point Rate</th>
                            <th>{{ $settings[0]->point_rate }}</th>
                        </tr>
                        <tr>
                            <th>Code Generator</th>
                            <th> {{ $settings[0]->code_generator }} </th>
                        </tr>
                        <tr>
                            <th>Address</th>    
                            <th>{{ $settings[0]->address }}</th>  
                        </tr>    
                    </thead> 
                </table>
            </div>

                <div class="card-footer clearfix float-right">
                    <div class="float-right">
                        
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

