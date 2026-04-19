@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Advanced Form Elements</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="index.html"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Advanced</li>
            </ul>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h2>Basic Forms</h2>
            </div>
            <div class="body">
                <form id="basic-form" class="form-row" action="#." novalidate>
                    <div class="form-group col-md-6">
                        <label>Text Input</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Email Input</label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Number Input</label>
                        <input type="number" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Read Only</label>
                        <input type="text" class="form-control" readonly required>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label>File Upload</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputGroupFile02">
                            <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                        </div>

                    </div>

                    <div class="form-group col-md-6">
                        <label>Text Area</label>
                        <textarea class="form-control" rows="5" cols="30" required></textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Inline Checkbox</label>
                        <br/>
                        <label class="fancy-checkbox">
                            <input type="checkbox" name="checkbox" required data-parsley-errors-container="#error-checkbox6">
                            <span>Option 1</span>
                        </label>
                        <label class="fancy-checkbox">
                            <input type="checkbox" name="checkbox">
                            <span>Option 2</span>
                        </label>

                        <label class="fancy-checkbox">
                            <input type="checkbox" name="checkbox">
                            <span>Option 3</span>
                        </label>
                        <br/>
                        <p id="error-checkbox6"></p>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Inline Radio Button</label>
                        <br />
                        <label class="fancy-radio">
                            <input type="radio" name="gender" value="male" required data-parsley-errors-container="#error-radio1">
                            <span><i></i>Male</span>
                        </label>
                        <label class="fancy-radio">
                            <input type="radio" name="gender" value="female" checked="" >
                            <span><i></i>Female</span>
                        </label>
                        <br />
                        <p id="error-radio1"></p>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Custom Checkbox</label>
                        <div class="fancy-checkbox">
                            <label><input type="checkbox" name="checkbox" required data-parsley-errors-container="#error-checkbox"><span>Fancy Checkbox 1</span></label>
                        </div>
                        <div class="fancy-checkbox">
                            <label><input type="checkbox" checked=""><span>Fancy Checkbox 2</span></label>
                        </div>
                        <div class="fancy-checkbox">
                            <label><input type="checkbox"><span>Fancy Checkbox 3</span></label>
                        </div>
                        <p id="error-checkbox"></p>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Custom Radio Button</label>
                        <div class="fancy-radio">
                            <label><input name="gender1" value="male" type="radio" checked="" required data-parsley-errors-container="#error-radio"><span><i></i>Male</span></label>
                        </div>
                        <div class="fancy-radio">
                            <label><input name="gender1" value="female" type="radio"><span><i></i>Female</span></label>
                        </div>

                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h2>Date Picker</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div class="col-lg-3 col-md-12">
                        <label>Default</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar"></i></span>
                            </div>
                            <input data-provide="datepicker" data-date-autoclose="true" class="form-control"  placeholder="Ex: 12/08/2021">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <label>Custom Format (dd/mm/yyyy)</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar"></i></span>
                            </div>
                            <input data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy" placeholder="Ex: 30/07/2021">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <label>Range</label>
                        <div class="row">
                            <div class="input-daterange input-group" data-provide="datepicker">

                                    <div class="col-lg-5 pr-0">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="icon-calendar"></i></span>
                                            </div>
                                            <input type="text" class="input-sm form-control" name="start" placeholder="Ex: 30/07/2021">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-center p-0">
                                        <span class="input-group-addon range-to">to</span>
                                    </div>
                                    <div class="col-lg-5 pl-0">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="icon-calendar"></i></span>
                                            </div>
                                            <input type="text" class="input-sm form-control" name="end" placeholder="Ex: 30/07/2021">
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
    <div class="col-md-4"></div>
</div>

<!-- Advanced Select2 -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="header">
                <h2><strong>Advanced</strong> Select2 </h2>
                <ul class="header-dropdown">
                    <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0);">Action</a></li>
                            <li><a href="javascript:void(0);">Another action</a></li>
                            <li><a href="javascript:void(0);">Something else</a></li>
                        </ul>
                    </li>
                    <li class="remove">
                        <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div class="col-lg-3 col-md-6">
                        <p> <b>Basic</b> </p>
                        <select class="form-control show-tick ms select2" data-placeholder="Select">
                            <option></option>
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <p> <b>With OptGroups</b> </p>
                        <select class="form-control show-tick ms select2" data-placeholder="Select">
                            <option></option>
                            <optgroup label="Picnic">
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                            </optgroup>
                            <optgroup label="Camping">
                            <option>Tent</option>
                            <option>Flashlight</option>
                            <option>Toilet Paper</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <p> <b>Multiple Select</b> </p>
                        <select class="form-control show-tick ms select2" multiple data-placeholder="Select">
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <p> <b>With Clear Button</b> </p>
                        <select class="form-control show-tick ms search-select" data-placeholder="Select">
                            <option></option>
                            <option>Hot Dog, Fries and a Soda</option>
                            <option>Burger, Shake and a Smile</option>
                            <option>Sugar, Spice and all things nice</option>
                        </select>
                    </div>
                </div>
                <div class="row clearfix m-t-30">
                    <div class="col-lg-3 col-md-6">
                        <p> <b>Max Selection Limit: 2</b> </p>
                        <select id="max-select" class="form-control show-tick ms" multiple>
                            <option></option>
                            <optgroup label="Condiments" data-max-options="2">
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                            </optgroup>
                            <optgroup label="Breads" data-max-options="2">
                            <option>Plain</option>
                            <option>Steamed</option>
                            <option>Toasted</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <p> <b>Loading Data</b> </p>
                        <input type="hidden" id="loading-select" class="form-control"/>

                    </div>
                    <div class="col-lg-3 col-md-6">
                        <p> <b>Loading Array Data</b> </p>
                        <input type="hidden" id="array-select" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <p> <b>Disabled Option</b> </p>
                        <select class="form-control show-tick ms select2" data-placeholder="Select">
                            <option></option>
                            <option>Mustard</option>
                            <option disabled>Ketchup</option>
                            <option>Relish</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Select2 -->

<!-- Masked Input -->
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Masked Input with icons </h2>
                <ul class="header-dropdown dropdown dropdown-animated scale-left">
                    <li> <a href="javascript:void(0);" data-toggle="cardloading" data-loading-effect="pulse"><i class="icon-refresh"></i></a></li>
                    <li><a href="javascript:void(0);" class="full-screen"><i class="icon-size-fullscreen"></i></a></li>
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0);">Action</a></li>
                            <li><a href="javascript:void(0);">Another Action</a></li>
                            <li><a href="javascript:void(0);">Something else</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="demo-masked-input">
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-6">
                            <b>Date</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control date" placeholder="Ex: 30/07/2016">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Time (24 hour)</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-clock"></i></span>
                                </div>
                                <input type="text" class="form-control time24" placeholder="Ex: 23:59">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Time (12 hour)</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-clock"></i></span>
                                </div>
                                <input type="text" class="form-control time12" placeholder="Ex: 11:59 pm">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Date Time</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control datetime" placeholder="Ex: 30/07/2016 23:59">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Mobile Phone Number</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>
                                </div>
                                <input type="text" class="form-control mobile-phone-number" placeholder="Ex: +00 (000) 000-00-00">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Phone Number</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                </div>
                                <input type="text" class="form-control phone-number" placeholder="Ex: +00 (000) 000-00-00">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Money (Dollar)</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                </div>
                                <input type="text" class="form-control money-dollar" placeholder="Ex: 99,99 $">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>IP Address</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-desktop"></i></span>
                                </div>
                                <input type="text" class="form-control ip" placeholder="Ex: 255.255.255.255">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Credit Card</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                </div>
                                <input type="text" class="form-control credit-card" placeholder="Ex: 0000 0000 0000 0000">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <b>Email Address</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-envelope-o"></i></span>
                                </div>
                                <input type="text" class="form-control email" placeholder="Ex: example@example.com">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <b>Serial Key</b>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-key"></i></span>
                                </div>
                                <input type="text" class="form-control key" placeholder="Ex: XXX0-XXXX-XX00-0XXX">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Multi Select -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h2>Multiselect</h2>
            </div>
            <div class="body demo-card">
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-12">
                        <label>Default</label>
                        <div class="multiselect_div">
                            <select id="multiselect1" name="multiselect1[]" class="multiselect" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                                <option value="mushrooms">Mushrooms</option>
                                <option value="pepperoni">Pepperoni</option>
                                <option value="onions">Onions</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <label>"Select All" Option Enabled</label>
                        <div class="multiselect_div">
                            <select id="multiselect3-all" name="multiselect3[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="multiselect-all">Select All</option>
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                                <option value="mushrooms">Mushrooms</option>
                                <option value="pepperoni">Pepperoni</option>
                                <option value="onions">Onions</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <label>Options Group</label>
                        <div class="multiselect_div">
                            <select id="multiselect5" name="multiselect5" class="multiselect-custom" multiple="multiple">
                                <optgroup label="Mathematics">
                                    <option value="analysis">Analysis</option>
                                    <option value="algebra">Linear Algebra</option>
                                    <option value="discrete">Discrete Mathematics</option>
                                    <option value="numerical">Numerical Analysis</option>
                                    <option value="probability">Probability Theory</option>
                                </optgroup>
                                <optgroup label="Computer Science">
                                    <option value="programming">Introduction to Programming</option>
                                    <option value="automata">Automata Theory</option>
                                    <option value="complexity">Complexity Theory</option>
                                    <option value="software">Software Engineering</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <label>Smaller Size</label>
                        <div class="multiselect_div">
                            <select id="multiselect-size" name="multiselect7[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <label>Custom Checkbox</label>
                        <div class="multiselect_div">
                            <select id="multiselect2" name="multiselect2[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                                <option value="mushrooms">Mushrooms</option>
                                <option value="pepperoni">Pepperoni</option>
                                <option value="onions">Onions</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <label>Single Selection</label>
                        <div class="multiselect_div">
                            <select id="single-selection" name="single_selection" class="multiselect multiselect-custom">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                                <option value="mushrooms">Mushrooms</option>
                                <option value="pepperoni">Pepperoni</option>
                                <option value="onions">Onions</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <label>Disabled Options</label>
                        <div class="multiselect_div">
                            <select id="multiselect6" name="multiselect6[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                                <option value="mushrooms" disabled="disabled">Mushrooms</option>
                                <option value="pepperoni" disabled="disabled">Pepperoni</option>
                                <option value="onions" disabled="disabled">Onions</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <label>Link (btn-link)</label>
                        <div class="multiselect_div">
                            <select id="multiselect-link" name="multiselect8[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <label>Custom Button Class (btn-primary)</label>
                        <div class="multiselect_div">
                            <select id="multiselect-color" name="multiselect9[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <label>Custom Button Class (btn-success)</label>
                        <div class="multiselect_div">
                            <select id="multiselect-color2" name="multiselect10[]" class="multiselect multiselect-custom" multiple="multiple">
                                <option value="cheese">Cheese</option>
                                <option value="tomatoes">Tomatoes</option>
                                <option value="mozarella">Mozzarella</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tags Input -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Tags Input  </h2>
            </div>
            <div class="body">
                <div class="input-group demo-tagsinput-area">
                    <input type="text" class="form-control" data-role="tagsinput" value="Hello, Hi">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Buttons</h2>
            </div>
            <div class="body">
                <button type="button" class="btn btn-primary">Primary</button>
                <button type="button" class="btn btn-secondary">Secondary</button>
                <button type="button" class="btn btn-success">Success</button>
                <button type="button" class="btn btn-danger">Danger</button>
                <button type="button" class="btn btn-warning">Warning</button>
                <button type="button" class="btn btn-info">Info</button>
                <button type="button" class="btn btn-light">Light</button>
                <button type="button" class="btn btn-dark">Dark</button>
                <button type="button" class="btn btn-link">Link</button>
            </div>

        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Outline buttons</h2>
            </div>
            <div class="body">
                <button type="button" class="btn btn-outline-primary">Primary</button>
                <button type="button" class="btn btn-outline-secondary">Secondary</button>
                <button type="button" class="btn btn-outline-success">Success</button>
                <button type="button" class="btn btn-outline-danger">Danger</button>
                <button type="button" class="btn btn-outline-warning">Warning</button>
                <button type="button" class="btn btn-outline-info">Info</button>
                <button type="button" class="btn btn-outline-light">Light</button>
                <button type="button" class="btn btn-outline-dark">Dark</button>
            </div>
        </div>
    </div>

</div>

@endsection


@push('custom_scripts')


@endpush
