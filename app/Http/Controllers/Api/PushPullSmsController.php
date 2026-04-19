<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CodeVerifyLog;
use App\Models\SSGCodeDetail;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PushPullSmsController extends Controller
{
    public function pushpull(Request $request)
    {

      
       // Log::info('SMS Request' . json_encode($request->all()));
         $data = $request->all();
         
          $sms = $data['sms'] ?? null;
          $msisdns = substr($data['msisdn'], -11) ?? null;
          $code = trim(substr($sms, 3));
        try {
            $status = '';
            $ssgCodeDetail = SSGCodeDetail::where('code', $code)->first();
            //Log::alert([$code, json_encode($ssgCodeDetail)]);
            if (isset($ssgCodeDetail) && $ssgCodeDetail->total_used >= 1) {
                $status = 'failed';
                SSGCodeDetail::where('code', $code)->update([
                    'total_used' => $ssgCodeDetail->total_used + 1,
                    'code_used_time' => getNow()
                ]);
                $this->LogEntry($request, $status, $ssgCodeDetail);

                echo "Alert! This code has already been scanned/checked by" . substr_replace($ssgCodeDetail->mobile, '*****', 5, 5) . " If it is not you please contact with your seller.";
                exit();
            }

            if ($ssgCodeDetail) {
                $status = 'success';
                SSGCodeDetail::where('code', $code)->update([
                    'mobile' => msisdn($msisdns),
                    'status' => 1,
                    'total_used' => $ssgCodeDetail->total_used + 1,
                    'code_used_time' => getNow()
                ]);
            } else {
                $status = 'failed';
            }

            $this->LogEntry($request, $status, $ssgCodeDetail);

            if (!$ssgCodeDetail) {

                echo "This code is invalid. Please enter the right code or contact with seller.";
                exit();
            }

            echo "Verified! This is an original product from Super Star Group (SSG). Thank you for choosing us.";
            exit();
        } catch (\Exception $e) {
            report($e);
            echo "This code is invalid. Please enter the right code or contact with seller";
            exit();
        }

        print_r( $data);
    }

    private function LogEntry($request, $status, $ssgCodeDetail)
    {
        $codeVerifyLog = new CodeVerifyLog;
        $codeVerifyLog->product_id = $ssgCodeDetail->product_id ?? null;
        $codeVerifyLog->mobile_no = $request->msisdn ?? null;
        $codeVerifyLog->code = trim(substr($request->sms, 3)) ?? null;
        $codeVerifyLog->requested_ip = $request->ip() ?? null;
        $codeVerifyLog->sms = $request->sms ?? null;
        $codeVerifyLog->status = $status ?? null;
        $codeVerifyLog->save();
    }
}
