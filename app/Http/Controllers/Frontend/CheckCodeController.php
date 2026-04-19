<?php

namespace App\Http\Controllers\Frontend;

use App\Models\CodeVerifyLog;
use Illuminate\Http\Request;
use App\Models\SSGCodeDetail;
use Illuminate\Support\Facades\DB;
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
            $status = '';
            $ssgCodeDetail = SSGCodeDetail::where('code', $request->code)->first();
            if (isset($ssgCodeDetail) && $ssgCodeDetail->total_used >= 1) {
                $status = 'failed';
                SSGCodeDetail::where('code', $request->code)->update([
                    'total_used' => $ssgCodeDetail->total_used + 1,
                    'code_used_time' => getNow()
                ]);
                $this->LogEntry($request, $status, $ssgCodeDetail);

                return redirect()->route('checkCodeURLValidate.already_check', $request->code);
            }

            if ($ssgCodeDetail) {
                $status = 'success';
                SSGCodeDetail::where('code', $request->code)->update([
                    'mobile' => msisdn($request->mobile),
                    'status' => 1,
                    'total_used' => $ssgCodeDetail->total_used + 1,
                    'code_used_time' => getNow()
                ]);
            } else {
                $status = 'failed';
            }

            $this->LogEntry($request, $status, $ssgCodeDetail);

            if (!$ssgCodeDetail) {

                return redirect()->route('checkCodeURLValidate.fail', $request->code);
            }

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
        $codeVerifyLog->requested_ip = $request->ip() ?? null;
        $codeVerifyLog->status = $status ?? null;
        $codeVerifyLog->save();
    }

}
