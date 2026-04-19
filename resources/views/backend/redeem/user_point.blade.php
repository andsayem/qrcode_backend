@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@section('content')

<style>
    /* ---------------- Premium UI Styling ---------------- */
    .card-premium { border-radius: 14px !important; border: none !important; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08) !important; }
    .premium-header { padding: 18px 25px; border-bottom: 1px solid #eee; font-weight: 600; font-size: 18px; color: #333; background: linear-gradient(to right, #ffffff, #f9fafc); border-radius: 14px 14px 0 0; }
    .premium-label { font-weight: 600; color: #555; }
    .btn-premium { background: #046eff; border-radius: 8px; color: #fff; font-weight: 600; padding: 10px 18px; transition: 0.3s; }
    .btn-premium:hover { background: #004fcc; color: #fff; }
    .table-premium thead th { background: #f4f6fa; border-bottom: 2px solid #dee2e6; font-weight: 600; color: #444; }
    .table-premium tbody tr:hover { background: #f9fcff; }
    .filter-divider { border-top: 1px dashed #ddd; margin: 15px 0; }
</style>

@php
$filters = [
    'country' => request('country', ''),
    'division' => request('division', ''),
    'district' => request('district', ''),
    'thana' => request('thana', ''),
    'area' => request('area', ''),
    'channel' => request('channel', ''),
    'from_date' => request('from_date', ''),
    'to_date' => request('to_date', ''),
    'sap_code' => request('sap_code', ''),
    'group_by' => request('group_by', ''),
];

$showTechnician = in_array($filters['group_by'], ['user_points.id', 'user_points.user_id']);
$showDivision = in_array($filters['group_by'], ['technicians.union_id','user_points.id','user_points.user_id','technicians.upazilla_id','technicians.district_id','technicians.division_id']);
$showDistrict = in_array($filters['group_by'], ['technicians.union_id','user_points.id','user_points.user_id','technicians.upazilla_id','technicians.district_id']);
$showThana = in_array($filters['group_by'], ['technicians.union_id','user_points.id','user_points.user_id','technicians.upazilla_id']);
$showArea = in_array($filters['group_by'], ['technicians.union_id','user_points.id','user_points.user_id']);
$showProduct = ($filters['group_by'] === 'user_points.id');
@endphp

<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8">
            <h2 class="font-weight-bold" style="color:#333;">User Point Dashboard</h2>
        </div>
        <div class="col-lg-5 col-md-4 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><i class="icon-home"></i></li>
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item active">User Point</li>
            </ul>
        </div>
    </div>
</div>

{{-- ===================== FILTERS ===================== --}}
<div class="row">
    <div class="col-12">
        <div class="card card-premium">

            <div class="premium-header">🔍 Filter Options</div>

            <div class="body pt-2">
                {{ Form::open(['method'=>'get']) }}
                <div class="row">

                    {{-- COUNTRY --}}
                    <div class="col-md-3 mb-3">
                        <label class="premium-label">Country</label>
                        <select name="country" id="country-select" class="form-control select2" onchange="loadDivision(this)">
                            <option value="">Select</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c['id'] }}" {{ $c['id']==$filters['country']?'selected':'' }}>{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DIVISION --}}
                    <div class="col-md-2 mb-3">
                        <label class="premium-label">Division</label>
                        <select name="division" id="division-select" class="form-control select2" onchange="loadDistrict(this)">
                            <option value="">Select</option>
                            @foreach ($divisions as $d)
                                <option value="{{ $d['id'] }}" {{ $d['id']==$filters['division']?'selected':'' }}>{{ $d['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DISTRICT --}}
                    <div class="col-md-2 mb-3">
                        <label class="premium-label">District</label>
                        <select name="district" id="district-select" class="form-control select2" onchange="loadThana(this)">
                            <option value="">Select</option>
                            @foreach ($district as $d)
                                <option value="{{ $d['id'] }}" {{ $d['id']==$filters['district']?'selected':'' }}>{{ $d['district'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- THANA --}}
                    <div class="col-md-2 mb-3">
                        <label class="premium-label">Thana</label>
                        <select name="thana" id="thana-select" class="form-control select2" onchange="loadArea(this)">
                            <option value="">Select</option>
                            @foreach ($thanas as $t)
                                <option value="{{ $t['id'] }}" {{ $t['id']==$filters['thana']?'selected':'' }}>{{ $t['thana'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- AREA --}}
                    <div class="col-md-2 mb-3">
                        <label class="premium-label">Area</label>
                        <select name="area" id="area-select" class="form-control select2">
                            <option value="">Select</option>
                            @foreach ($areas as $a)
                                <option value="{{ $a['id'] }}" {{ $a['id']==$filters['area']?'selected':'' }}>{{ $a['area'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CHANNEL --}}
                    <div class="col-md-3 mb-3">
                        <label class="premium-label">Channel</label>
                        <select name="channel" class="form-control">
                            <option value="">Select</option>
                            @foreach ($channels as $ch)
                                <option value="{{ $ch['id'] }}" {{ $ch['id']==$filters['channel']?'selected':'' }}>{{ $ch['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- GROUP BY --}}
                    <div class="col-md-3 mb-3">
                        <label class="premium-label">Group By</label>
                        <select name="group_by" class="form-control">
                            @foreach([
                                'user_points.id'=>'All Row',
                                'technicians.division_id'=>'Division',
                                'technicians.district_id'=>'District',
                                'technicians.upazilla_id'=>'Thana',
                                'technicians.union_id'=>'Area',
                                'user_points.user_id'=>'Technician'
                            ] as $val=>$label)
                                <option value="{{ $val }}" {{ $filters['group_by']==$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DATE RANGE --}}
                    <div class="col-md-2 mb-3">
                        <label class="premium-label">From Date</label>
                        <input type="date" name="from_date" value="{{ $filters['from_date'] }}" class="form-control">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="premium-label">To Date</label>
                        <input type="date" name="to_date" value="{{ $filters['to_date'] }}" class="form-control">
                    </div>

                    {{-- SEARCH BUTTON --}}
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button class="btn btn-premium w-100"><i class="fa fa-search"></i> Search</button>
                    </div>

                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>
</div>

{{-- ===================== TABLE ===================== --}}
<div class="row">
    <div class="col-12">

        <div class="card card-premium">
            <div class="premium-header">📄 User Point List</div>

            <div class="body pt-0">

                <div class="table-responsive">
                    <table class="table table-premium table-bordered">
                        <thead>
                            <tr>
                                <th>SI</th>
                                @if($showTechnician)<th>Technician</th><th>ID</th>@endif
                                @if($showDivision)<th>Division</th>@endif
                                @if($showDistrict)<th>District</th>@endif
                                @if($showThana)<th>Thana</th>@endif
                                @if($showArea)<th>Area</th>@endif
                                @if($showProduct)<th>Product</th>@endif
                                <th class="text-right">Scan Point</th>
                                <th class="text-right">Tk.</th>
                            </tr>
                        </thead>

                        <tbody>
                           @foreach ($items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                {{-- Technician --}}
                                @if($showTechnician)
                                    <td>
                                        <a class="text-primary font-weight-bold"
                                        href="{{ url('/admin/user_point?' . http_build_query(array_merge($filters, [
                                                'group_by' => 'user_points.id',
                                                'user_id'  => $item->user_id,
                                                'division' => $item->division_id,
                                                'district' => $item->district_id,
                                                'thana'    => $item->upazilla_id,
                                                'area'     => $item->union_id
                                        ]))) }}">
                                            {{ $item->name }} : {{ $item->id }} ({{ $item->email }})
                                        </a>
                                    </td>
                                    <td>{{ $item->id }}</td>
                                @endif

                                {{-- Division --}}
                                @if($showDivision)
                                    <td>
                                        <a href="{{ url('/admin/user_point?' . http_build_query(array_merge($filters, [
                                            'group_by' => 'technicians.division_id',
                                            'division' => $item->division_id
                                        ]))) }}">
                                            {{ $item->division_name }}
                                        </a>
                                    </td>
                                @endif

                                {{-- District --}}
                                @if($showDistrict)
                                    <td>
                                        <a href="{{ url('/admin/user_point?' . http_build_query(array_merge($filters, [
                                            'group_by' => 'technicians.district_id',
                                            'district' => $item->district_id
                                        ]))) }}">
                                            {{ $item->district_name }}
                                        </a>
                                    </td>
                                @endif

                                {{-- Thana --}}
                                @if($showThana)
                                    <td>
                                        <a href="{{ url('/admin/user_point?' . http_build_query(array_merge($filters, [
                                            'group_by' => 'technicians.upazilla_id',
                                            'thana'    => $item->upazilla_id
                                        ]))) }}">
                                            {{ $item->thana_name }}
                                        </a>
                                    </td>
                                @endif

                                {{-- Area --}}
                                @if($showArea)
                                    <td>
                                        <a href="{{ url('/admin/user_point?' . http_build_query(array_merge($filters, [
                                            'group_by' => 'technicians.union_id',
                                            'area'     => $item->union_id
                                        ]))) }}">
                                            {{ $item->area_name }}
                                        </a>
                                    </td>
                                @endif

                                {{-- Product --}}
                                @if($showProduct)
                                    <td>
                                        <a href="{{ url('/admin/user_point?' . http_build_query(array_merge($filters, [
                                            'group_by'   => 'user_points.id',
                                            'product_id' => $item->product_id
                                        ]))) }}">
                                            {{ $item->product_name }}
                                        </a>
                                    </td>
                                @endif

                                <td class="text-right font-weight-bold">{{ $item->sub_point }}</td>
                                <td class="text-right font-weight-bold text-success">{{ $item->sub_point / 4 }} Tk</td>
                            </tr>
                            @endforeach

                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="{{ 1 + ($showTechnician ? 2 : 0) + ($showDivision ? 1 : 0) + ($showDistrict ? 1 : 0) + ($showThana ? 1 : 0) + ($showArea ? 1 : 0) + ($showProduct ? 1 : 0) }}">Total</th>
                                <th class="text-right font-weight-bold">{{ $items->sum('sub_point') }}</th>
                                <th class="text-right font-weight-bold text-success">{{ $items->sum('sub_point') / 4 }} Tk</th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <nav class="m-3 d-flex justify-content-between">
                    <span class="text-muted">
                        Showing {{ $items->firstItem() }} — {{ $items->lastItem() }} of {{ $items->total() }} entries
                    </span>
                    <div>{{ $items->appends($filters)->links() }}</div>
                </nav>

            </div>
        </div>

    </div>
</div>

@endsection

@push('custom_scripts')
<script>
function loadOptions(url, params, target, labelKey, valueKey = 'id') {
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        data: params,
        success: function(response) {
            let sel = $(target);
            sel.empty().append('<option value="">Select One</option>');
            response.forEach(item => sel.append(`<option value="${item[valueKey]}">${item[labelKey]}</option>`));
        }
    });
}

function loadDivision(el) {
    loadOptions('{{ route("admin.users.getSsforceDivisions") }}', { country_id: el.value }, '#division-select', 'name');
    $('#district-select,#thana-select,#area-select').empty().append('<option value="">Select One</option>');
}

function loadDistrict(el) {
    loadOptions('{{ route("admin.users.getSsforceDistrict") }}', { division_id: el.value }, '#district-select', 'district');
    $('#thana-select,#area-select').empty().append('<option value="">Select One</option>');
}

function loadThana(el) {
    loadOptions('{{ route("admin.users.getSsforcethana") }}', { district_id: el.value }, '#thana-select', 'thana');
    $('#area-select').empty().append('<option value="">Select One</option>');
}

function loadArea(el) {
    loadOptions('{{ route("admin.users.getSsforcearea") }}', { thana_id: el.value }, '#area-select', 'area');
}

$(document).ready(function() {
    $('#division-select').change(function() { loadDistrict(this); });
    $('#district-select').change(function() { loadThana(this); });
    $('#thana-select').change(function() { loadArea(this); });
});
</script>
@endpush
