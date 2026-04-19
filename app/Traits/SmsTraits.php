<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait SmsTraits
{
    private function sendSms($mobile_no, $sms_body)
    {
        $url = "https://smsplus.sslwireless.com/api/v3/send-sms";
        $request_param = json_encode([
            "api_token" => 'marksquiz',
            "sid" => 'sdf',
            "msisdn" => $mobile_no,
            "sms" => $sms_body,
            "csms_id" => rand(100000000, 999999999)
        ]);
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_param);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request_param),
            'accept:application/json'
        ));
        $response = curl_exec($ch);
        Log::info('SMS response ========>' . json_encode($response));
        curl_close($ch);
    }
}
