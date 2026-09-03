<?php

namespace App\Http\Controllers\Frontend;

use App\Models\CodeVerifyLog;
use Illuminate\Http\Request;
use App\Models\SSGCodeDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckCodeURLValidateRequest;

class CheckCodeController extends Controller
{
    public function checkCodeURL(Request $request, $unique_code = '')
    {
        return view('frontend.ssgcodecheck.index');
    }

    public function checkCodeURLValidate(CheckCodeURLValidateRequest $request)
    {
        try {
            $ssgCodeDetail = SSGCodeDetail::where('code', $request->code)->first();

            if (!$ssgCodeDetail) {
                $this->LogEntry($request, 'failed', $ssgCodeDetail);

                return redirect()->route('checkCodeURLValidate.fail', $request->code);
            }

            if ($ssgCodeDetail->total_used >= 1) {
                $ssgCodeDetail->update([
                    'total_used' => $ssgCodeDetail->total_used + 1,
                    'code_used_time' => getNow(),
                ]);
                $this->LogEntry($request, 'failed', $ssgCodeDetail);

                return redirect()->route('checkCodeURLValidate.already_check', $request->code);
            }

            $ssgCodeDetail->update([
                'mobile' => msisdn($request->mobile),
                'status' => 1,
                'total_used' => $ssgCodeDetail->total_used + 1,
                'code_used_time' => getNow(),
            ]);
            $this->LogEntry($request, 'success', $ssgCodeDetail);

            return redirect()->route('checkCodeURLValidate.success', $request->code);
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('checkCodeURLValidate.fail', $request->code);
        }
    }


    public function checkCodeURLValidateSuccess(Request $request, $unique_code)
    {
        $existCode = SSGCodeDetail::where('code', $unique_code)->first();
        if ($existCode) {
            $data['ssgcodedetail'] = $existCode;

            return view('frontend.ssgcodecheck.success')->with($data);
        } else {
            abort(404);
        }
    }

    public function checkCodeURLValidateFail(Request $request, $unique_code)
    {
        $data['code'] = $unique_code;

        return view('frontend.ssgcodecheck.fail')->with($data);
    }

    public function alreadyCheck(Request $request, $unique_code)
    {
        $existCode = SSGCodeDetail::where('code', $unique_code)->first();
        if ($existCode) {
            $data['ssgcodedetail'] = $existCode;

            return view('frontend.ssgcodecheck.already_check')->with($data);
        } else {
            abort(404);
        }
    }


    private function LogEntry($request, $status, $ssgCodeDetail)
    {
        $codeVerifyLog = new CodeVerifyLog;
        $codeVerifyLog->product_id = $ssgCodeDetail->product_id ?? null;
        $codeVerifyLog->mobile_no = $request->mobile ?? null;
        $codeVerifyLog->code = $request->code ?? null;
        $codeVerifyLog->code_id = $ssgCodeDetail->id ?? null;
        $codeVerifyLog->requested_ip = $request->ip() ?? null;
        $codeVerifyLog->status = $status ?? null;
        $codeVerifyLog->lat = $request->lat ?? null;
        $codeVerifyLog->long = $request->long ?? null;
        $codeVerifyLog->address = $this->resolveAddress($request->lat, $request->long);
        $codeVerifyLog->save();
    }

    private function resolveAddress($lat, $long)
    {
        if (!$lat || !$long) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                    'User-Agent' => 'SSGEShop/1.0 (sayed@ssgbd.com)',
                ])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $long,
                ]);

            return $response->successful() ? $response->json('display_name') : null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

}
