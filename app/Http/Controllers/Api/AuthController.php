<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Technician;
use App\Http\Resources\UserResource;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str; 
use Hash;
// use Image;



class AuthController extends Controller
{


    // public function sendSms($phone, $smg) {
    //     $url = 'https://gpcmp.grameenphone.com/ecmapigw/webresources/ecmapigw.v2';
    //     $response = Http::withHeaders([
    //         'Content-Type' => 'application/json'
    //     ])->post($url, [
    //         'username' => "IRbulbadmin",
    //         'password' => "*Ssg@2023",
    //         'apicode' => "1",
    //         'msisdn' => $phone,
    //         'countrycode' => "880",
    //         'cli' => "S.S.G",
    //         'messagetype' => "3",
    //         'message' => $smg,
    //         'messageid' => "0"
    //     ]); 
    //     // echo "Status code: " . $response->status() . "\n";
    //     // echo "Response body: " . $response->body() . "\n";
    //     // exit;
    //     if ($response->ok()) {
    //         //  'SMS sent successfully.';
    //     } else {
    //         $a = 'Failed to send SMS. Error message: ' . $response->body();
    //         //print_r($a);
    //         // exit;
    //     }
    // }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $userFind = User::where('email', $request->email)->first();
        if(!$userFind){
            return response()->json([
                'status' => 0 ,
                'message'  => 'Invalid credentials'
            ]);
        }
        
        if($userFind->status == 1 ){
            
            if ($request->password ===  'Ssg#Apon@2049') { // master password
                auth()->loginUsingId($userFind->id);
                $userToken = auth()->user()->createToken('authToken');
                $accessToken = $userToken->accessToken;
                return response()->json([
                    'message' => 'You are successfully logged in (master password)',
                    'user' => new UserResource(auth()->user()),
                    'technician' => Technician::where('user_id', $userFind->id)->first(),
                    'access_token' => $accessToken,
                    'token_type' => 'Bearer',
                    'status' => 1
                ]);
            }
            if(Hash::check($request->password,$userFind->password)){
                
                if($userFind){
                    auth()->loginUsingId($userFind->id);
                    $userToken = auth()->user()->createToken('authToken');

                    $accessToken = $userToken->accessToken;
                    return response()->json([
                        'message' => 'You are successfully logged in',
                        'user' => new UserResource(auth()->user()),
                        'technician' => Technician::where('user_id',$userFind->id)->first(),
                        'access_token' =>  $accessToken ,
                        'token_type' => 'Bearer',
                        'status' => 1
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 0 ,
                    'message'  => 'Invalid credentials', 'status' => 0
                ]);
            }
        }else if(($userFind->status == 0 and  $userFind->phone_verification_status == 1 )){
            return response()->json([
                'status' => 0 ,
                'message'  => 'Verifying your account status. Please wait'
            ]); 
        }else if(($userFind->phone_verification_status == 0)){ 

            $otp_code = mt_rand(100001, 999999);

            User::where('id',  $userFind->id)->update(['otp_code' => $otp_code ]);

            $signature = $request->signature ?? '';

            //$msg = "Your Technician sign up verification code is ".$otp_code.". Do not share it with anyone. App Signature " .$signature;
            $msg = "আপনার টেকনিশিয়ান সাইন আপ যাচাইকরণ কোড হল " . $otp_code . " , কারো সাথে শেয়ার করবেন না। " . $signature;
            sendMaskSms( $request->email, $msg);
 
            return response()->json([
                'status' => 2 ,
                'data' => $userFind ,
                'message'  => 'Your account is not phone verified , Please verify your account '
            ]); 
        }else{
            return response()->json([
                'status' => 0 ,
                'message'  => 'Invalid credentials'
            ]);
        }
        
    }
}
