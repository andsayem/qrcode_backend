@extends('backend.layouts.app')
@extends('backend.layouts.topbar')
@extends('backend.layouts.leftsidebar')
@extends('backend.layouts.footer')

@push('custom_styles')
<style>
    .treg .step-card { border: 1px solid #e6e9ef; border-radius: 10px; background: #fff; margin-bottom: 18px; }
    .treg .step-head { display: flex; align-items: center; padding: 14px 18px; border-bottom: 1px solid #eef0f4; }
    .treg .step-num {
        width: 30px; height: 30px; border-radius: 50%; background: #17a2b8; color: #fff;
        display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-right: 12px; flex: none;
    }
    .treg .step-num.done { background: #28a745; }
    .treg .step-title { font-size: 16px; font-weight: 600; margin: 0; }
    .treg .step-sub { font-size: 12px; color: #8a94a6; margin: 0; }
    .treg .step-body { padding: 18px; }
    .treg label.req::after { content: " *"; color: #dc3545; }
    .treg .form-control, .treg .input-group-text { height: 42px; }
    .treg .select2-container .select2-selection--single { height: 42px; }
    .treg .select2-container .select2-selection__rendered { line-height: 40px !important; }
    .treg .select2-container .select2-selection__arrow { height: 40px !important; }
    .treg .input-group-text { background: #f5f7fa; color: #5a6270; }
    .treg .field-error { color: #dc3545; font-size: 12px; margin-top: 4px; display: block; }

    .treg .fo-card { border-radius: 8px; padding: 12px 14px; margin-top: 12px; display: none; }
    .treg .fo-card.ok { display: block; background: #eafaf0; border: 1px solid #b7ebc9; }
    .treg .fo-card.bad { display: block; background: #fdecee; border: 1px solid #f5c2c7; color: #b02a37; }
    .treg .fo-card.wait { display: block; background: #f5f7fa; border: 1px solid #e6e9ef; color: #5a6270; }
    .treg .fo-row { display: flex; font-size: 13px; padding: 3px 0; }
    .treg .fo-row span:first-child { width: 60px; color: #5a6270; font-weight: 600; }

    .treg .side { position: sticky; top: 80px; }
    .treg .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eef0f4; font-size: 13px; }
    .treg .summary-row:last-child { border-bottom: 0; }
    .treg .summary-row .k { color: #8a94a6; }
    .treg .summary-row .v { font-weight: 600; text-align: right; max-width: 60%; word-break: break-word; }
    .treg .summary-row .v.empty { color: #c3c9d4; font-weight: 400; }
    .treg .check-list li { font-size: 13px; padding: 5px 0; list-style: none; }
    .treg .check-list { padding-left: 0; margin: 0; }
    .treg .check-list i { width: 20px; color: #17a2b8; }
    .treg .note { font-size: 12px; background: #fff8e6; border: 1px solid #ffe2a8; border-radius: 8px; padding: 10px 12px; color: #7a5b00; }
    .treg .btn-submit { height: 46px; font-size: 16px; font-weight: 600; }
    .treg .pw-match { font-size: 12px; margin-top: 4px; display: block; }

    .treg .fo-list-title { font-size: 13px; font-weight: 600; color: #5a6270; margin-bottom: 8px; }
    .treg .fo-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .treg .fo-item {
        border: 1px solid #e6e9ef; border-radius: 8px; padding: 10px 12px; display: flex; align-items: center;
        cursor: pointer; transition: border-color .15s, background .15s;
    }
    .treg .fo-item:hover { border-color: #17a2b8; background: #f3fbfd; }
    .treg .fo-item.selected { border-color: #28a745; background: #eafaf0; }
    .treg .fo-avatar {
        width: 36px; height: 36px; border-radius: 50%; background: #e3f4f7; color: #17a2b8; flex: none;
        display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-right: 10px;
    }
    .treg .fo-item.selected .fo-avatar { background: #28a745; color: #fff; }
    .treg .fo-meta { min-width: 0; flex: 1; }
    .treg .fo-name { font-weight: 600; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .treg .fo-sub { font-size: 12px; color: #8a94a6; }
    .treg .fo-sub a { color: #17a2b8; }
    .treg .fo-empty { font-size: 13px; color: #8a94a6; background: #f5f7fa; border-radius: 8px; padding: 12px; margin-bottom: 16px; }
</style>
@endpush

@section('content')

<!-- start page title -->
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Technician Registration</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.technician_user') }}">Technician</a></li>
                <li class="breadcrumb-item active">Registration</li>
            </ul>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="treg">
    {{ Form::open(['route' => 'admin.technician.register', 'id' => 'technician-register-form', 'autocomplete' => 'off']) }}
    <div class="row">

        {{-- ================= LEFT: form steps ================= --}}
        <div class="col-lg-8">

            {{-- Step 1: Personal --}}
            <div class="step-card">
                <div class="step-head">
                    <span class="step-num" data-step="1">1</span>
                    <div>
                        <p class="step-title">ব্যক্তিগত তথ্য</p>
                        <p class="step-sub">টেকনিশিয়ানের নাম ও মোবাইল নম্বর (লগইন আইডি)</p>
                    </div>
                </div>
                <div class="step-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label class="req mb-2">পুরো নাম</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i></span></div>
                                    {!! Form::text('name', old('name'), ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'যেমন: মোঃ রহিম উদ্দিন', 'maxlength' => 255, 'required']) !!}
                                </div>
                                @error('name')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="req mb-2">মোবাইল নম্বর</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">+88</span></div>
                                    {!! Form::text('mobile', old('mobile'), ['id' => 'mobile', 'class' => 'form-control', 'placeholder' => '01XXXXXXXXX', 'maxlength' => 11, 'inputmode' => 'numeric', 'pattern' => '01[3-9][0-9]{8}', 'title' => '১১ ডিজিট, 01 দিয়ে শুরু', 'required']) !!}
                                </div>
                                @error('mobile')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Location --}}
            <div class="step-card">
                <div class="step-head">
                    <span class="step-num" data-step="2">2</span>
                    <div>
                        <p class="step-title">এলাকা</p>
                        <p class="step-sub">বিভাগ → জেলা → থানা → এলাকা ক্রমে নির্বাচন করুন</p>
                    </div>
                </div>
                <div class="step-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="req mb-2">বিভাগ</label>
                                <select name="division_id" id="division_id" class="form-control select2" required></select>
                                @error('division_id')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="req mb-2">জেলা</label>
                                <select name="district_id" id="district_id" class="form-control select2" required></select>
                                @error('district_id')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label class="req mb-2">থানা</label>
                                <select name="thana_id" id="thana_id" class="form-control select2" required></select>
                                @error('thana_id')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="req mb-2">এলাকা</label>
                                <select name="area_id" id="area_id" class="form-control select2" required></select>
                                @error('area_id')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: FO reference --}}
            <div class="step-card">
                <div class="step-head">
                    <span class="step-num" data-step="3">3</span>
                    <div>
                        <p class="step-title">FO রেফারেন্স</p>
                        <p class="step-sub">এলাকার FO তালিকা থেকে বেছে নিন, অথবা FO আইডি নিজে লিখুন</p>
                    </div>
                </div>
                <div class="step-body">
                    <div id="fo-list-wrap">
                        <div class="fo-empty"><i class="fa fa-map-marker mr-1"></i>এলাকা নির্বাচন করলে ওই এলাকার FO তালিকা এখানে দেখাবে।</div>
                    </div>
                    <label class="req mb-2">রেফারেন্স FO আইডি</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-badge"></i></span></div>
                        {!! Form::text('fo_id', old('fo_id'), ['id' => 'fo_id', 'class' => 'form-control', 'placeholder' => 'FO ID', 'maxlength' => 50, 'required']) !!}
                        <div class="input-group-append">
                            <button type="button" id="fo-check-btn" class="btn btn-info px-4"><i class="fa fa-search mr-1"></i>যাচাই</button>
                        </div>
                    </div>
                    @error('fo_id')<span class="field-error">{{ $message }}</span>@enderror
                    <div id="fo-card" class="fo-card"></div>
                </div>
            </div>

            {{-- Step 4: Password --}}
            <div class="step-card">
                <div class="step-head">
                    <span class="step-num" data-step="4">4</span>
                    <div>
                        <p class="step-title">পাসওয়ার্ড</p>
                        <p class="step-sub">৪ থেকে ১৬ অক্ষর — টেকনিশিয়ানকে জানিয়ে দিন</p>
                    </div>
                </div>
                <div class="step-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label class="req mb-2">পাসওয়ার্ড</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></div>
                                    {!! Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => '৪ থেকে ১৬ অক্ষর', 'minlength' => 4, 'maxlength' => 16, 'required', 'autocomplete' => 'new-password']) !!}
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary pw-toggle" data-target="#password" title="দেখান / লুকান"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>
                                @error('password')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="req mb-2">পাসওয়ার্ড নিশ্চিত করুন</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></div>
                                    {!! Form::password('password_confirmation', ['id' => 'password_confirmation', 'class' => 'form-control', 'placeholder' => 'আবার লিখুন', 'minlength' => 4, 'maxlength' => 16, 'required', 'autocomplete' => 'new-password']) !!}
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary pw-toggle" data-target="#password_confirmation" title="দেখান / লুকান"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>
                                <span id="pw-match" class="pw-match"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= RIGHT: summary ================= --}}
        <div class="col-lg-4">
            <div class="side">
                <div class="step-card">
                    <div class="step-head">
                        <p class="step-title"><i class="fa fa-list-alt mr-2 text-info"></i>সারাংশ</p>
                    </div>
                    <div class="step-body pt-2 pb-2">
                        <div class="summary-row"><span class="k">নাম</span><span class="v empty" id="s-name">—</span></div>
                        <div class="summary-row"><span class="k">মোবাইল</span><span class="v empty" id="s-mobile">—</span></div>
                        <div class="summary-row"><span class="k">এলাকা</span><span class="v empty" id="s-location">—</span></div>
                        <div class="summary-row"><span class="k">FO</span><span class="v empty" id="s-fo">—</span></div>
                        <div class="summary-row"><span class="k">Role</span><span class="v">Technician</span></div>
                        <div class="summary-row"><span class="k">স্ট্যাটাস</span><span class="v text-success">Active · Phone Verified</span></div>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-body">
                        <p class="step-title mb-2" style="font-size:14px">Register চাপলে যা যাচাই হবে</p>
                        <ul class="check-list mb-3">
                            <li><i class="fa fa-id-card-o"></i>SSG কর্মী নয় (HRIS)</li>
                            <li><i class="fa fa-shopping-bag"></i>নিবন্ধিত রিটেইলার নয় (ssforce)</li>
                            <li><i class="fa fa-user-circle"></i>FO রেফারেন্স সঠিক (ssforce)</li>
                            <li><i class="fa fa-mobile"></i>নম্বরটি আগে রেজিস্টার্ড নয়</li>
                        </ul>
                        <div class="note mb-3">
                            <i class="fa fa-info-circle mr-1"></i>
                            অ্যাডমিন রেজিস্ট্রেশনে OTP লাগবে না, কোনো SMS যাবে না। টেকনিশিয়ান সাথে সাথেই অ্যাপে লগইন করতে পারবে।
                        </div>
                        <button type="submit" id="register-submit" class="btn btn-success btn-block btn-submit">
                            <i class="fa fa-check-circle mr-1"></i>Register
                        </button>
                        <a href="{{ route('admin.users.technician_user') }}" class="btn btn-link btn-block text-muted">বাতিল</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>
@endsection

@push('custom_scripts')
<script>
    (function () {
        // Previously selected values (after a failed submit) so the cascade can be restored
        var old = {
            division: @json(old('division_id')),
            district: @json(old('district_id')),
            thana: @json(old('thana_id')),
            area: @json(old('area_id'))
        };

        var $division = $('#division_id'), $district = $('#district_id'),
            $thana = $('#thana_id'), $area = $('#area_id');

        // ---------- helpers ----------
        function setSummary(id, text) {
            var $el = $('#' + id);
            if (text) { $el.text(text).removeClass('empty'); } else { $el.text('—').addClass('empty'); }
        }
        function selectedText($s) { return $s.val() ? $s.find('option:selected').text() : ''; }
        function markStep(n, done) { $('.step-num[data-step="' + n + '"]').toggleClass('done', !!done).html(done ? '<i class="fa fa-check"></i>' : n); }

        function refreshSummary() {
            var name = $.trim($('#name').val()), mobile = $.trim($('#mobile').val());
            var mobileOk = /^01[3-9]\d{8}$/.test(mobile);
            setSummary('s-name', name);
            setSummary('s-mobile', mobile);
            markStep(1, name && mobileOk);

            var parts = [selectedText($area), selectedText($thana), selectedText($district), selectedText($division)].filter(Boolean);
            setSummary('s-location', parts.join(', '));
            markStep(2, $area.val());

            var pw = $('#password').val(), pc = $('#password_confirmation').val();
            var pwOk = pw.length >= 4 && pw.length <= 16 && pw === pc;
            markStep(4, pwOk);
            var $m = $('#pw-match');
            if (!pc) { $m.text('').removeClass('text-success text-danger'); }
            else if (pw === pc) { $m.text('✓ পাসওয়ার্ড মিলেছে').removeClass('text-danger').addClass('text-success'); }
            else { $m.text('✗ পাসওয়ার্ড মেলেনি').removeClass('text-success').addClass('text-danger'); }
        }

        // ---------- location cascade ----------
        function fill($select, items, labelKey, selected, done) {
            $select.empty().append('<option value="">নির্বাচন করুন</option>');
            (items || []).forEach(function (item) {
                var opt = $('<option>').val(item.id).text(item[labelKey]);
                if (selected && String(selected) === String(item.id)) opt.prop('selected', true);
                $select.append(opt);
            });
            $select.trigger('change.select2');
            refreshSummary();
            if ($select.is($area)) loadFoList(); // area list changed (cleared / reloaded) -> refresh FO list
            if (done) done();
        }

        function load(url, params, $select, labelKey, selected, done) {
            $select.empty().append('<option value="">লোড হচ্ছে...</option>').trigger('change.select2');
            $.getJSON(url, params)
                .done(function (items) { fill($select, items, labelKey, selected, done); })
                .fail(function () {
                    fill($select, [], labelKey);
                    alert('লোকেশন লোড করা যায়নি। আবার চেষ্টা করুন।');
                });
        }

        function loadDivisions(sel, done) { load(@json(route('admin.users.getSsforceDivisions')), {country_id: 1}, $division, 'name', sel, done); }
        function loadDistricts(sel, done) { load(@json(route('admin.users.getSsforceDistrict')), {division_id: $division.val()}, $district, 'district', sel, done); }
        function loadThanas(sel, done) { load(@json(route('admin.users.getSsforcethana')), {district_id: $district.val()}, $thana, 'thana', sel, done); }
        function loadAreas(sel, done) { load(@json(route('admin.users.getSsforcearea')), {thana_id: $thana.val()}, $area, 'area', sel, done); }

        $division.on('change', function () {
            fill($thana, [], 'thana'); fill($area, [], 'area');
            $division.val() ? loadDistricts() : fill($district, [], 'district');
        });
        $district.on('change', function () {
            fill($area, [], 'area');
            $district.val() ? loadThanas() : fill($thana, [], 'thana');
        });
        $thana.on('change', function () {
            $thana.val() ? loadAreas() : fill($area, [], 'area');
        });
        $area.on('change', function () { refreshSummary(); loadFoList(); });

        // ---------- FO list of the selected area (same API as the app) ----------
        var foListReq = null;
        function highlightFo() {
            var cur = $.trim($('#fo_id').val());
            $('.fo-item').each(function () { $(this).toggleClass('selected', String($(this).data('fo')) === cur); });
        }
        function loadFoList() {
            var $wrap = $('#fo-list-wrap');
            if (foListReq) foListReq.abort();
            if (!$area.val()) {
                $wrap.html('<div class="fo-empty"><i class="fa fa-map-marker mr-1"></i>এলাকা নির্বাচন করলে ওই এলাকার FO তালিকা এখানে দেখাবে।</div>');
                return;
            }
            $wrap.html('<div class="fo-empty"><i class="fa fa-spinner fa-spin mr-1"></i>FO তালিকা লোড হচ্ছে...</div>');
            foListReq = $.getJSON(@json(route('admin.technician.register.fo_list')), {area_id: $area.val()})
                .done(function (r) {
                    var list = r.data || [];
                    if (!list.length) {
                        $wrap.html('<div class="fo-empty"><i class="fa fa-user-times mr-1"></i>এই এলাকায় কোনো FO পাওয়া যায়নি। FO আইডি জানা থাকলে নিচে লিখুন।</div>');
                        return;
                    }
                    var $grid = $('<div class="fo-list">');
                    list.forEach(function (fo) {
                        var initial = (fo.name || '?').trim().charAt(0).toUpperCase();
                        var $phone = fo.phone
                            ? $('<a>').attr('href', 'tel:' + fo.phone).text(fo.phone).on('click', function (e) { e.stopPropagation(); })
                            : $('<span>').text('—');
                        $grid.append(
                            $('<div class="fo-item" title="নির্বাচন করতে ক্লিক করুন">').attr('data-fo', fo.fo_id).append(
                                $('<span class="fo-avatar">').text(initial),
                                $('<div class="fo-meta">').append(
                                    $('<div class="fo-name">').text(fo.name),
                                    $('<div class="fo-sub">').append($('<i class="fa fa-phone mr-1">'), $phone),
                                    $('<div class="fo-sub">').text('FO ID: ' + fo.fo_id)
                                )
                            ).on('click', function () {
                                $('#fo_id').val(fo.fo_id);
                                highlightFo();
                                checkFo(true);
                            })
                        );
                    });
                    $wrap.empty().append($('<div class="fo-list-title">').text('এই এলাকার FO (' + list.length + ' জন) — ক্লিক করে বেছে নিন'), $grid);
                    highlightFo();
                })
                .fail(function (x, status) {
                    if (status === 'abort') return;
                    var msg = (x.responseJSON && x.responseJSON.message) || 'FO তালিকা লোড করা যায়নি।';
                    $wrap.empty().append($('<div class="fo-empty text-danger">').text(msg));
                });
        }

        fill($district, [], 'district'); fill($thana, [], 'thana'); fill($area, [], 'area');
        loadDivisions(old.division, function () {
            if (!old.division) return;
            loadDistricts(old.district, function () {
                if (!old.district) return;
                loadThanas(old.thana, function () {
                    if (!old.thana) return;
                    loadAreas(old.area);
                });
            });
        });

        // ---------- FO check ----------
        var lastFo = null;
        function checkFo(force) {
            var foid = $.trim($('#fo_id').val());
            var $card = $('#fo-card');
            if (!foid) { $card.attr('class', 'fo-card'); setSummary('s-fo', ''); markStep(3, false); return; }
            if (!force && foid === lastFo) return;
            lastFo = foid;
            $card.attr('class', 'fo-card wait').html('<i class="fa fa-spinner fa-spin mr-1"></i> যাচাই হচ্ছে...');
            $.getJSON(@json(route('admin.technician.register.fo_check')), {foid: foid})
                .done(function (r) {
                    $card.attr('class', 'fo-card ok').empty().append(
                        $('<div class="mb-1 text-success font-weight-bold">').html('<i class="fa fa-check-circle mr-1"></i>FO রেফারেন্স সঠিক'),
                        $('<div class="fo-row">').append($('<span>').text('FO'), $('<span>').text(r.fo)),
                        $('<div class="fo-row">').append($('<span>').text('TSM'), $('<span>').text(r.tsm)),
                        $('<div class="fo-row">').append($('<span>').text('Point'), $('<span>').text(r.point))
                    );
                    setSummary('s-fo', r.fo);
                    markStep(3, true);
                })
                .fail(function (x) {
                    var msg = (x.responseJSON && x.responseJSON.message) || 'FO যাচাই করা যায়নি';
                    $card.attr('class', 'fo-card bad').empty().append($('<span>').html('<i class="fa fa-times-circle mr-1"></i>').append(document.createTextNode(msg)));
                    setSummary('s-fo', '');
                    markStep(3, false);
                    lastFo = null;
                });
        }
        $('#fo-check-btn').on('click', function () { checkFo(true); });
        $('#fo_id').on('blur', function () { checkFo(false); })
                   .on('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); checkFo(true); } })
                   .on('input', function () { lastFo = null; $('#fo-card').attr('class', 'fo-card'); setSummary('s-fo', ''); markStep(3, false); highlightFo(); });
        if ($.trim($('#fo_id').val())) checkFo(true);

        // ---------- inputs ----------
        $('#mobile').on('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 11); });
        $('#name, #mobile, #password, #password_confirmation').on('input', refreshSummary);

        $('.pw-toggle').on('click', function () {
            var $input = $($(this).data('target'));
            var show = $input.attr('type') === 'password';
            $input.attr('type', show ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye', !show).toggleClass('fa-eye-slash', show);
        });

        // Prevent double submit (external checks can take a few seconds)
        $('#technician-register-form').on('submit', function () {
            if ($('#password').val() !== $('#password_confirmation').val()) {
                alert('পাসওয়ার্ড মেলেনি');
                return false;
            }
            $('#register-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>যাচাই হচ্ছে...');
        });

        refreshSummary();
    })();
</script>
@endpush
