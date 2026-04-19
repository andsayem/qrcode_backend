@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h2>Edit Settings</h2>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card"> 
            {!! Form::model($settings, ['route' => ['admin.settings.update', $settings->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Min Redeem Point</label>
                            <div class="col-sm-8">
                                {{Form::text('min_redeem_point', $settings->min_redeem_point, ['class' => 'form-control',  'placeholder'=>'Point Rate'])}}
                                @include('/includes/validationmessages', ['field_name'=>'min_redeem_point'])
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Code Generator</label>
                            <div class="col-sm-8">
                                {{Form::text('code_generator', $settings->code_generator, ['class' => 'form-control', 'placeholder'=>'Point Rate'])}} 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Company Name</label>
                            <div class="col-sm-8">
                                {{Form::text('company_name', $settings->company_name, ['class' => 'form-control', 'placeholder'=>'Company Name'])}} 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Contact Number</label>
                            <div class="col-sm-8">
                                {{Form::text('contact_number', $settings->contact_number, ['class' => 'form-control', 'placeholder'=>'Contact Number'])}} 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Email</label>
                            <div class="col-sm-8">
                                {{Form::text('email', $settings->email, ['class' => 'form-control', 'placeholder'=>'Email'])}} 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Address</label>
                            <div class="col-sm-8">
                                {{Form::text('address', $settings->address, ['class' => 'form-control', 'placeholder'=>'Address'])}} 
                            </div>
                        </div>
                    </div>
                </div>
                
                @foreach ($countries as $country) 
                @php
                    $desiredCountryId = $country['id']; // Replace with the desired country_id

                    // Filter the array based on the country_id
                    $filteredArray = array_filter($settings->pointRate->toArray(), function ($item) use ($desiredCountryId) {
                        return $item['country_id'] == $desiredCountryId;
                    });

                    // Reset array keys if needed
                    $filteredArray = array_values($filteredArray);
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Country Name</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="country_id[{{$country['id']}}]" >
                                <option value="{{$country['id']}}">
                                    {{ $country['name']}}
                                </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row required">
                            <label class="col-sm-4 col-form-label control-label">Point Rate</label>
                            <div class="col-sm-8">
                                {{Form::text('point_rate['.$country['id'].']', $filteredArray ? $filteredArray[0]['point_rate'] : '', ['class' => 'form-control', 'placeholder'=>'Point Rate'])}} 
                            </div>
                        </div>
                    </div> 
                </div>
                @endforeach
            </div>

            <div class="card-footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('settings.index') }}" class="btn btn-default">Cancel</a>
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
