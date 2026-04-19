@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')


<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Table Example</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="index.html"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">Table</li>
                <li class="breadcrumb-item active">Table Example</li>
            </ul>
        </div>
    </div>
</div>


<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Filter</h2>
            </div>
            <div class="body pt-0">
                <form class="filter-form form-row" action="#." novalidate>
                    <div class="form-group col-md-3 mb-2">
                        <label>Text Input</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3 mb-3">
                        <label>Number Input</label>
                        <input type="number" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3 mb-3">
                        <label>File Upload</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputGroupFile02">
                            <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group col-lg-3 col-md-3 mb-3">
                        <label>Basic Selection</label>
                        <select class="form-control show-tick ms select2" data-placeholder="Select">
                            <option></option>
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-3 col-md-3 mb-3">
                        <label>Date Picker</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar"></i></span>
                            </div>
                            <input data-provide="datepicker" data-date-autoclose="true" class="form-control"  placeholder="Ex: 12/08/2021">
                        </div>
                    </div>


                    <div class="form-group col-lg-4 col-md-4">
                        <label>&nbsp</label>
                        <div>
                            <button type="button" class="btn btn-success mr-2"><i class="fa fa-search"></i> <span>Filter</span></button>
                            <button type="button" class="btn btn-warning mr-2"><i class="fa fa-refresh"></i> <span>Reset</span></button>
                            <button type="button" class="btn btn-outline-danger"><i class="fa fa-times"></i> <span>Cancel</span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2>Basic Table 1</h2>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="#." class="btn btn-sm px-3 btn-primary mr-2"><i class="fa fa-download"></i> <span>Download</span></a>
                        <a href="#." class="btn btn-sm px-3 btn-success"><i class="fa fa-upload"></i> <span>Bulk Upload</span></a>
                    </div>
                </div>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table m-b-0 table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patients</th>
                                <th>Adress</th>
                                <th>START Date</th>
                                <th>END Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><span>John</span></td>
                                <td><span class="text-info">70 Bowman St. South Windsor, CT 06074</span></td>
                                <td>Sept 13, 2017</td>
                                <td>Sept 16, 2017</td>
                                <td><span class="badge badge-success">Approved</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><span>Jack Bird</span></td>
                                <td><span class="text-info">123 6th St. Melbourne, FL 32904</span></td>
                                <td>Sept 13, 2017</td>
                                <td>Sept 22, 2017</td>
                                <td><span class="badge badge-default">Deactivated</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><span>Dean Otto</span></td>
                                <td><span class="text-info">123 6th St. Melbourne, FL 32904</span></td>
                                <td>Sept 13, 2017</td>
                                <td>Sept 23, 2017</td>
                                <td><span class="badge badge-primary">Activated</span></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><span>Jack Bird</span></td>
                                <td><span class="text-info">4 Shirley Ave. West Chicago, IL 60185</span></td>
                                <td>Sept 17, 2017</td>
                                <td>Sept 16, 2017</td>
                                <td><span class="badge badge-danger">Cancelled</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="..." class="float-right mt-3">
                    <ul class="pagination">
                        <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">1</a></li>
                        <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">2 <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                        <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h2>Basic Example 2</h2>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>
                                    <label class="fancy-checkbox">
                                        <input class="select-all" type="checkbox" name="checkbox">
                                        <span></span>
                                    </label>
                                </th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                            <tbody>
                            <tr>
                                <td style="width: 50px;">
                                    <label class="fancy-checkbox">
                                        <input class="checkbox-tick" type="checkbox" name="checkbox">
                                        <span></span>
                                    </label>
                                </td>

                                <td>
                                    <img src="{{ asset('/backend_assets/assets/images/xs/avatar1.jpg') }}" class="rounded-circle avatar" alt="">
                                    <p class="c_name">John Smith</p>
                                </td>
                                <td>
                                   <span class="badge badge-success m-l-10 hidden-sm-down">Admin</span>
                                </td>
                                <td>
                                    <span class="phone"><i class="fa fa-phone m-r-10"></i>264-625-2583</span>
                                </td>
                                <td>
                                    <address><i class="fa fa-map-marker"></i>123 6th St. Melbourne, FL 32904</address>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></button>
                                    <button type="button" data-type="confirm" class="btn btn-outline-danger btn-sm js-sweetalert" title="Delete"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="fancy-checkbox">
                                        <input class="checkbox-tick" type="checkbox" name="checkbox">
                                        <span></span>
                                    </label>
                                </td>
                                <td>
                                    <img src="{{ asset('/backend_assets/assets/images/xs/avatar3.jpg')}}" class="rounded-circle avatar" alt="">
                                    <p class="c_name">Hossein Shams </p>
                                </td>
                                <td>
                                    <span class="badge badge-info m-l-10 hidden-sm-down">Vendor</span>
                                </td>
                                <td>
                                    <span class="phone"><i class="fa fa-phone m-r-10"></i>264-625-5689</span>
                                </td>
                                <td>
                                    <address><i class="fa fa-map-marker"></i>44 Shirley Ave. West Chicago, IL 60185</address>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Edit"><i class="fa fa-edit"></i></button>
                                    <button type="button" data-type="confirm" class="btn btn-outline-danger btn-sm js-sweetalert" title="Delete"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label class="fancy-checkbox">
                                        <input class="checkbox-tick" type="checkbox" name="checkbox">
                                        <span></span>
                                    </label>
                                </td>
                                <td>
                                    <img src="{{ asset('/backend_assets/assets/images/xs/avatar10.jpg')}}" class="rounded-circle avatar" alt="">
                                    <p class="c_name">Tim Hank</p>
                                </td>
                                <td>
                                    <span class="badge badge-success m-l-10 hidden-sm-down">Admin</span>
                                </td>
                                <td>
                                    <span class="phone"><i class="fa fa-phone m-r-10"></i>264-625-1212</span>
                                </td>
                                <td>
                                    <address><i class="fa fa-map-marker"></i>70 Bowman St. South Windsor, CT 06074</address>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info mr-2" title="Edit"><i class="fa fa-edit"></i></button>
                                    <button type="button" data-type="confirm" class="btn btn-sm btn-outline-danger js-sweetalert" title="Delete"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="..." class="float-right mt-3">
                    <ul class="pagination">
                        <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">1</a></li>
                        <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">2 <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                        <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h2>Basic Example 5</h2>
            </div>
            <div class="body pt-0">
                <div class="table-responsive">
                    <table class="table m-b-0 table-hover">
                        <thead>
                            <tr>
                                <th>Application</th>
                                <th>Team</th>
                                <th>Change</th>
                                <th>Sales</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <h6>Alpino 4.1</h6>
                                    <span>thememakker To By Again</span>
                                </td>
                                <td>
                                    <ul class="list-unstyled team-info">
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar1.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar2.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar3.jpg')}}" alt="Avatar"></li>
                                    </ul>
                                </td>
                                <td>
                                    <div class="sparkline text-left" data-type="line" data-width="8em" data-height="20px" data-line-Width="1.5" data-line-Color="#00c5dc"
                                    data-fill-Color="transparent">3,5,1,6,5,4,8,3</div>
                                </td>
                                <td>11,200</td>
                                <td>$83</td>
                                <td><strong>$22,520</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <h6>Compass 2.0</h6>
                                    <span>thememakker To By Again</span>
                                </td>
                                <td>
                                    <ul class="list-unstyled team-info">
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar2.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar3.jpg')}}" alt="Avatar"></li>
                                    </ul>
                                </td>
                                <td>
                                    <div class="sparkline text-left" data-type="line" data-width="8em" data-height="20px" data-line-Width="1.5" data-line-Color="#f4516c"
                                    data-fill-Color="transparent">4,6,3,2,5,6,5,4</div>
                                </td>
                                <td>11,200</td>
                                <td>$66</td>
                                <td><strong>$13,205</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <h6>Nexa 1.1</h6>
                                    <span>thememakker To By Again</span>
                                </td>
                                <td>
                                    <ul class="list-unstyled team-info">
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar4.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar6.jpg')}}" alt="Avatar"></li>
                                    </ul>
                                </td>
                                <td>
                                    <div class="sparkline text-left" data-type="line" data-width="8em" data-height="20px" data-line-Width="1.5" data-line-Color="#31db3d"
                                    data-fill-Color="transparent">7,3,2,1,5,4,6,8</div>
                                </td>
                                <td>12,080</td>
                                <td>$93</td>
                                <td><strong>$17,700</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <h6>Oreo 2.2</h6>
                                    <span>ThemeMakker To By Again</span>
                                </td>
                                <td>
                                    <ul class="list-unstyled team-info">
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar1.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar3.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar2.jpg')}}" alt="Avatar"></li>
                                        <li><img src="{{ asset('/backend_assets/assets/images/xs/avatar9.jpg')}}" alt="Avatar"></li>
                                    </ul>
                                </td>
                                <td>
                                    <div class="sparkline text-left" data-type="line" data-width="8em" data-height="20px" data-line-Width="1.5" data-line-Color="#2d342e"
                                    data-fill-Color="transparent">3,1,2,5,4,6,2,3</div>
                                </td>
                                <td>18,200</td>
                                <td>$178</td>
                                <td><strong>$17,700</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="..." class="float-right mt-3">
                    <ul class="pagination">
                        <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">1</a></li>
                        <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">2 <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                        <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>


@endsection

@push('custom_scripts')
@endpush
