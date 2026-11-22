<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\TechnicianRegistrationService;
use Illuminate\Http\Request;

/**
 * Admin registers a technician on their behalf, with the same checks as the Apon app sign-up.
 */
class TechnicianRegistrationController extends Controller
{
    private $service;

    public function __construct(TechnicianRegistrationService $service)
    {
        $this->service = $service;
    }

    public function create()
    {
        abort_unless(auth()->user()->can('user-create'), 403);

        return view('backend.users.technician-register');
    }

    /** AJAX: FOs of the selected area (name, phone, FO ID). */
    public function foList(Request $request)
    {
        abort_unless(auth()->user()->can('user-create'), 403);
        $request->validate(['area_id' => 'required|integer|min:1']);

        try {
            return response()->json(['status' => true, 'data' => $this->service->foListByArea((int) $request->area_id)]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => false, 'message' => 'FO তালিকা লোড করা যায়নি। আবার চেষ্টা করুন।'], 502);
        }
    }

    /** AJAX: look up an FO reference id and return FO / TSM / Point names for display. */
    public function foCheck(Request $request)
    {
        abort_unless(auth()->user()->can('user-create'), 403);
        $request->validate(['foid' => 'required|string|max:50']);

        try {
            $reference = $this->service->foReference(trim($request->foid));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => false, 'message' => 'FO যাচাই করা যায়নি। কিছুক্ষণ পর আবার চেষ্টা করুন।'], 502);
        }

        if ($reference === null) {
            return response()->json(['status' => false, 'message' => 'ভুল FO রেফারেন্স আইডি'], 422);
        }

        return response()->json([
            'status' => true,
            'fo' => trim(($reference['fo']['display_name'] ?? '') . ' (' . ($reference['fo']['email'] ?? '') . ')'),
            'tsm' => trim(($reference['tsm']['display_name'] ?? '') . ' (' . ($reference['tsm']['email'] ?? '') . ')'),
            'point' => trim(($reference['point']['point_name'] ?? '') . ' (' . ($reference['point']['sap_code'] ?? '') . ')'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('user-create'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => ['required', 'regex:/^01[3-9]\d{8}$/', 'unique:users,email'],
            'division_id' => 'required|integer|min:1',
            'district_id' => 'required|integer|min:1',
            'thana_id' => 'required|integer|min:1',
            'area_id' => 'required|integer|min:1',
            'fo_id' => 'required|string|max:50',
            'password' => 'required|string|min:4|max:16|confirmed',
        ], [
            'name.required' => 'নাম লিখুন',
            'mobile.required' => 'মোবাইল নম্বর লিখুন',
            'mobile.regex' => 'সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (01XXXXXXXXX)',
            'mobile.unique' => 'এই মোবাইল নম্বর দিয়ে আগেই রেজিস্ট্রেশন করা আছে',
            'division_id.*' => 'বিভাগ নির্বাচন করুন',
            'district_id.*' => 'জেলা নির্বাচন করুন',
            'thana_id.*' => 'থানা নির্বাচন করুন',
            'area_id.*' => 'এলাকা নির্বাচন করুন',
            'fo_id.required' => 'রেফারেন্স FO আইডি লিখুন',
            'password.required' => 'পাসওয়ার্ড লিখুন',
            'password.min' => 'পাসওয়ার্ড ৪ থেকে ১৬ অক্ষরের মধ্যে হতে হবে',
            'password.max' => 'পাসওয়ার্ড ৪ থেকে ১৬ অক্ষরের মধ্যে হতে হবে',
            'password.confirmed' => 'পাসওয়ার্ড মিলেনি',
        ]);
        $data['name'] = trim($data['name']);
        $data['fo_id'] = trim($data['fo_id']);

        // Same external checks as the app, in the same order
        try {
            if ($this->service->isSsgEmployee($data['mobile'])) {
                return $this->fail('এই নম্বরটি একজন SSG কর্মীর');
            }
            if ($this->service->isRetailer($data['mobile'])) {
                return $this->fail('এই নম্বরটি একজন নিবন্ধিত রিটেইলারের');
            }
            $reference = $this->service->foReference($data['fo_id']);
            if ($reference === null) {
                return $this->fail('ভুল FO রেফারেন্স আইডি', 'fo_id');
            }
        } catch (\Throwable $e) {
            report($e);
            return $this->fail('বাইরের সার্ভারে যাচাই করা যায়নি (HRIS / ssforce)। কিছুক্ষণ পর আবার চেষ্টা করুন।');
        }

        try {
            $this->service->register($data, $reference);
        } catch (\Throwable $e) {
            report($e);
            return $this->fail('রেজিস্ট্রেশন সেভ করা যায়নি। আবার চেষ্টা করুন।');
        }

        return redirect()->route('admin.users.technician_user', ['status' => 1])
            ->with('success', ['Technician registered: ' . $data['name'] . ' (' . $data['mobile'] . ')']);
    }

    private function fail(string $message, ?string $field = null)
    {
        $redirect = back()->withInput($this->safeInput());

        return $field
            ? $redirect->withErrors([$field => $message])
            : $redirect->with('fail', [$message]);
    }

    private function safeInput(): array
    {
        return request()->except(['password', 'password_confirmation']);
    }
}
