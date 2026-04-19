@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

    <!-- start page title -->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <h2>Learning Edit</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.learnings.index') }}">Learning</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-12 mx-auto">
            <div class="card">
                <div class="card-body order-list">
                    <h3 class="header-title mt-0 mb-3">
                        Edit Learning and Tutorial
                    </h3>
                    <hr/>
                    <form action="{{route('admin.learnings.update',$data->id)}}" method="post" id="roles-form" enctype="multipart/form-data">
                    @csrf
                        @method('put')
                    <div class="row">


                        <div class="col-md-6">
                            <div class="form-group row required">
                                <label class="col-sm-4 col-form-label control-label">Title</label>
                                <div class="col-sm-8">
                                    {{Form::text('title', $data->title, ['class' => 'form-control',  'placeholder'=>'Enter title'])}}
                                    @include('/includes/validationmessages', ['field_name'=>'title'])
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group row required">
                                <label class="col-sm-4 col-form-label control-label">Content Type</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="type" id="contentType">
                                        @foreach($types ?? [] as $k => $type)
                                            <option value="{{ $k }}" {{ (isset($data) && $k == $data->type) ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @include('/includes/validationmessages', ['field_name'=>'type'])
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group row" id="imageField">
                                <label class="col-sm-4 col-form-label control-label">Image Files</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name="files[]" multiple="multiple">
                                    @if($data->type == 'image')
                                        <div style="margin-top: 10px">
                                        @foreach($data->images as $img)
                                            <img src="{{ asset('storage/' . $img->path) }}" alt="" height="50">
                                        @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row" id="linkField">
                                <label class="col-sm-4 col-form-label control-label">Youtube Link</label>
                                <div class="col-sm-8">
                                    {{Form::text('path', $data->path, ['class' => 'form-control',  'placeholder'=>'Enter Youtube Link'])}}
                                    @include('/includes/validationmessages', ['field_name'=>'path'])
                                </div>
                            </div>
                        </div>



                        {{--<div class="col-md-6">
                            <div class="form-group row ">
                                <label for="role" class="col-sm-4 col-form-label control-label">Parent Category</label>
                                <div class="col-sm-8">
                                    {!! Form::select('parent_id', ['' => 'Select Parent Category']+ $parentcategories, null, ['id' =>'parent_id','class' => 'form-control select2']) !!}
                                    @include('/includes/validationmessages', ['field_name'=>'parent_id'])
                                </div>
                            </div>
                        </div>--}}

                        <div class="col-md-6">
                            <div class="form-group row required">
                                <label class="col-sm-4 col-form-label control-label">Status</label>
                                <div class="col-sm-8">
                                    {!! Form::select('is_active', App\Utilities\Enum\StatusEnum::getKeysValues(), App\Utilities\Enum\StatusEnum::Active, ['class' => 'form-control']) !!}
                                    @include('/includes/validationmessages', ['field_name'=>'status'])
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row ">
                                <label class="col-sm-4 col-form-label control-label">Description</label>
                                <div class="col-sm-8">
                                    {!! Form::textarea('description', $data->description, ['id' =>'desc','class' => 'form-control', 'rows' => 5, 'placeholder'=>'Description']) !!}
                                    @include('/includes/validationmessages', ['field_name'=>'desc'])
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
            @if($data->type == 'image')
                linkField.style.display = 'none';
            @else
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
        });
    </script>
@endsection
