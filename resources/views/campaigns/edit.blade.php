@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

    <!-- start page title -->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <h2>Campaigns</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Campaigns</a></li>
                    <li class="breadcrumb-item active">Edit Campaigns</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-12 mx-auto">
            <div class="card">
                <div class="card-body order-list">
                    <form class="form-row" id="basic-form" action="{{route('campaigns.update',$campaign->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                    <h3 class="header-title mt-0 mb-3">
                        Edit Campaigns
                    </h3>


                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group row required">
                                <label class="col-sm-12 col-form-label control-label">Campaign Name</label>
                                <div class="col-sm-12">
                                    {{Form::text('title', $campaign->title, ['class' => 'form-control',  'placeholder'=>'Please write Campaign Title'])}}
                                    @include('/includes/validationmessages', ['field_name'=>'title'])
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group row required">
                                <label class="col-sm-12 col-form-label control-label">Campaign Type</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="campaign_type" id="campaignType">
                                        @foreach($campaignTypes as $k => $type)
                                            <option value="{{$k}}" {{ (isset($campaign) && $k == $campaign->campaign_type) ? 'selected' : '' }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                    @include('/includes/validationmessages', ['field_name'=>'type'])
                                </div>
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="row" id="campaign_container">
                                <div class="col-md-6">
                                    <div class="form-group row required">
                                        <label class="col-sm-3 col-form-label control-label">Product</label>
                                        <div class="col-sm-12">
                                            {!! Form::select('product_id', ['' => 'Select Product']+ $parentproducts, $campaign->product_id, ['id' =>'product_id','class' => 'form-control select2']) !!}
                                            @include('/includes/validationmessages', ['field_name'=>'product_id'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row required">
                                        <label class="col-sm-3 col-form-label control-label">Point</label>
                                        <div class="col-sm-12">
                                            {{Form::text('point', $campaign->point, ['class' => 'form-control',  'placeholder'=>'point'])}}
                                            @include('/includes/validationmessages', ['field_name'=>'point'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group row required">
                                <label class="col-sm-3 col-form-label control-label">Start Date</label>
                                <div class="col-sm-12">
                                    @include('includes.calender_prepend')
                                    {!! Form::text('start_date', date('d-m-Y',strtotime($campaign->start_date)),['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                                    @include('/includes/validationmessages', ['field_name'=>'start_date'])
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row required">
                                <label class="col-sm-3 col-form-label control-label">End Date</label>
                                <div class="col-sm-12">
                                    @include('includes.calender_prepend')
                                    {!! Form::text('end_date', date('d-m-Y',strtotime($campaign->end_date)),['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>'DD-MM-YYYY', 'data-provide'=>'datepicker', 'data-date-autoclose'=>"true", "data-date-format"=>"dd-mm-yyyy"])!!}
                                    @include('/includes/validationmessages', ['field_name'=>'end_date'])
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group row required">
                                <label class="col-sm-12 col-form-label control-label">Content Type</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="content_type" id="contentType">
                                        @foreach($types as $k => $type)
                                            <option value="{{$k}}" {{ (isset($campaign) && $k == $campaign->content_type) ? 'selected' : '' }}>{{$type}}</option>
                                        @endforeach
                                    </select>
                                    @include('/includes/validationmessages', ['field_name'=>'content_type'])
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <div class="form-group row" id="imageField">
                                <label for="image" class="col-sm-12 col-form-label control-label">Image</label>
                                <input type="file" id="image" class="form-control mb-3" name="image" accept="image/*" />
                                <img src="{{ asset('storage/campaign/' . $campaign->image) }}" alt="" height="50">
                                @if($errors->has('image'))
                                    <strong style="color:red;">{{ $errors->first('image') }}</strong>
                                @endif

                            </div>

                            <div class="form-group row" id="linkField">
                                <label class="col-sm-12 col-form-label control-label">Youtube Link</label>
                                <div class="col-sm-12">
                                    {{Form::text('link', $campaign->link, ['class' => 'form-control',  'placeholder'=>'Enter Youtube Link'])}}
                                    @include('/includes/validationmessages', ['field_name'=>'link'])
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12 text-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success waves-effect waves-light mt-2"><i class="ti-check-box mr-2"></i>Save Now</button>
                            </div>
                        </div>
                    </div>
                    <!--end /div-->
                    </form>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentType = document.getElementById('contentType');
            const imageField = document.getElementById('imageField');
            const linkField = document.getElementById('linkField');





            // শুরুতে দুইটাকেই লুকিয়ে রাখো
            //imageField.style.display = 'none';
            linkField.style.display = 'none';

            @if($campaign->content_type == 'image')
                linkField.style.display = 'none';
                imageField.style.display = 'flex';
            @else
                linkField.style.display = 'flex';
                imageField.style.display = 'none';
            @endif
            // ড্রপডাউন চেঞ্জ ইভেন্ট
            contentType.addEventListener('change', function() {
                const value = this.value;

                if (value === 'image') {
                    imageField.style.display = 'flex'; // row visible
                    linkField.style.display = 'none';
                }
                else if (value === 'link') {
                    linkField.style.display = 'flex';
                    imageField.style.display = 'none';
                }
                else {
                    imageField.style.display = 'none';
                    linkField.style.display = 'none';
                }
            });


            const campaignType = document.getElementById('campaignType');
            const campaign_container = document.getElementById('campaign_container');
            @if($campaign->campaign_type == 'generale')
                campaign_container.style.display = 'none';
            @else
                campaign_container.style.display = 'flex';
            @endif


            campaignType.addEventListener('change', function() {
                const value = this.value;
                if (value === 'generale') {
                    campaign_container.style.display = 'none';
                }
                else if (value === 'campaign_with_product') {
                    campaign_container.style.display = 'flex';
                }
                else {
                    campaign_container.style.display = 'none';
                }
            });



        });
    </script>
@endsection