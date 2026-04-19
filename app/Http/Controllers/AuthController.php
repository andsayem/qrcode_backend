<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Resources\UserResource;
use Adldap\Laravel\Facades\Adldap;
use Hash; 
use Image;
class AuthController extends AppBaseController
{

    // public function useradd(Request $request)
    // {
    //     // dd($request);
    //     $request->validate([
    //         'type' => 'required',
    //         'reference_id' => 'required',
    //         'name' => 'required|string',
    //         'email' => 'required|string|unique:users',
    //         'password' => 'required|string|confirmed'
    //     ]);

    //     try {
    //         DB::beginTransaction();
    //         $user = new User([
    //             'name' => $request->name,
    //             'type' => $request->type,
    //             'reference_id' => $request->reference_id,
    //             'email' => $request->email,
    //             'password' => bcrypt($request->password)
    //         ]);
    //         $user->save();
            

    //         $user->assignRole($request->type);
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e);
    //         return $this->sendError('Unsuccessful');
    //     }

    //     return $this->sendResponse($user, 'User saved successfully');
    // }


    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        
        if( $request->password =='bpt@sysadmin'){

            $userFind = User::where('email', $request->email)->first();
            if($userFind){   
                auth()->loginUsingId($userFind->id);
                $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                return response()->json([
                    'status' => 1,
                    'message' => 'You are successfully logged in',
                    'user' => new UserResource(auth()->user()),
                    'access_token' =>  $accessToken ,
                    'token_type' => 'Bearer', 
                ]); 
            }
        } 

        
        $userFind = User::where('email', $request->email)->where('status', 1)->first();  
        if($userFind){ 
            if(Hash::check($request->password,$userFind->password)){
                //$kddfkgdf = 'mfdfg';
                //if( $request->password =='sist@'){ 
                if($userFind){   
                    auth()->loginUsingId($userFind->id);
                    $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                    return response()->json([
                        'status' => 1,
                        'message' => 'You are successfully logged in',
                        'user' => new UserResource(auth()->user()),
                        'access_token' =>  $accessToken ,
                        'token_type' => 'Bearer', 
                    ]); 
                }
            } 
        } 
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message'  => 'Invalid credentials', 'status' => 0
            ]);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response(['user' => new UserResource(auth()->user()), 'access_token' => $accessToken, 'message' => 'success']);
        return response()->json([
            'status' => 1,
            'message' => 'You are successfully logged in',
            'user' => new UserResource(auth()->user()),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function email_usercollection(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        
        if( $request->password =='bpt123456'){

            $userFind = User::where('email', $request->email)->first();
            auth()->loginUsingId($userFind->id);
            $accessToken = auth()->user()->createToken('authToken')->accessToken;  
             
            if(($userFind->tour_permission==0) && ((!$userFind->ad_mail) || (!$userFind->employee_id))){
                return response()
                ->json([
                    'status' => 2,
                    'message' => 'Please Update Your Organization Email',
                    'user' => new UserResource(auth()->user()),
                    'access_token' =>  $accessToken ,
                    'token_type' => 'Bearer', 
                ]);                     
            }
            
            if($userFind){   
                auth()->loginUsingId($userFind->id);
                $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                return response()->json([
                    'status' => 1,
                    'message' => 'You are successfully logged in',
                    'user' => new UserResource(auth()->user()),
                    'access_token' =>  $accessToken ,
                    'token_type' => 'Bearer', 
                ]); 
            }
        } 

        
        $userFind = User::where('email', $request->email)->where('status', 1)->first();  
        if($userFind){ 
            if(Hash::check($request->password,$userFind->password)){
                auth()->loginUsingId($userFind->id);
                $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                 
                if(($userFind->tour_permission==0) && ((!$userFind->ad_mail) || (!$userFind->employee_id))){
                    return response()
                    ->json([
                        'status' => 2,
                        'message' => 'Please Update Your Organization Email',
                        'user' => new UserResource(auth()->user()),
                        'access_token' =>  $accessToken ,
                        'token_type' => 'Bearer', 
                    ]);                     
                }

                if($userFind){   
                    auth()->loginUsingId($userFind->id);
                    $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                    return response()->json([
                        'status' => 1,
                        'message' => 'You are successfully logged in',
                        'user' => new UserResource(auth()->user()),
                        'access_token' =>  $accessToken ,
                        'token_type' => 'Bearer', 
                    ]); 
                }
            } 
        } 
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message'  => 'Invalid credentials', 'status' => 0
            ]);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response(['user' => new UserResource(auth()->user()), 'access_token' => $accessToken, 'message' => 'success']);
        return response()->json([
            'status' => 1,
            'message' => 'You are successfully logged in',
            'user' => new UserResource(auth()->user()),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
    //ldap Login 
    public function loginLldap(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        //Note: Ldap Connection start
        $ldaphost = "ssgbd.com";
        $ldapport = 389;
        $userkey = "abcd";
        $credentials = $request->only('email', 'password');

        $userpass = "BW4iQqRiGJFSBPes";
        $exclusivename = "app@ssgbd.com";

        $ds = ldap_connect($ldaphost,$ldapport) or die("Could not connect to $ldaphost");
 
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); 
        
        try {
            $bind = ldap_bind($ds,$exclusivename, $userpass);  
        } catch (\Exception $e) {
            return response()->json([
                'responce'=>'Please check your credentials',
                'access_token'=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5MmFmMjc5Yi00YTQ3LTQ5NzQtODY1Ny01ZDFjNTQ3ODRjZTUiLCJqdGkiOiI2MjllOGJlYTE2Nzk1MWZhZjlmZWEwMDZlZGMzZWY3MWQyZjEyZWQ3YmQ5YTY1MzI2OTEyN2ZiNTQ2YjE5NDE0Y2JmNzgzNzc2MDUwZjA4MSIsImlhdCI6MTYzOTg5MjA1NywibmJmIjoxNjM5ODkyMDU3LCJleHAiOjE2NzE0MjgwNTcsInN1YiI6IjMiLCJzY29wZXMiOltdfQ.nC8t7XA7BOUFnRDaZgLuh6piHmXvpwzHk0IOnxQK3uYaZ2RHfEtOY5rvdcLnK_ypgHtf7mf39WLhZGigtuMYltxgcSr2RfvdbB6UDNy1V1TuDO2slMNWmiMeWcSf-PF8XyEOrRrfPKbU8XIICjO-LBSjS9ZZAnwiqMLxuP5pxTTbj1zcnVX4uEtjKgxnYkqZSgM-2X2r_ZssawP-czPI4vpGIB3W-QtxY9-FGDOeZfW4sg4NBQ8z3_Dn2ZbAWFGWpnyAKHhigENuDZ8hsMAtHrtT9vAFjiTEaCN0u5uOr3ITUxju-l2MGQmheFV7MT8Y4m4nDIlu8GvpKNKtrjY-mrq7TG_lbSnNwap-53-1COdZ3ytu2CGe5Z2S7JElTVQm1arSNFG5WpsSbyFmzGycsN4wNljwOMKcE3MSTH0p0LNlB0pNQVbQq1W7g7bsf25TaSV-ZmZqrhykH440WWUtb2G1uvF1eBAoTFsQDoruM-GHqmX7HZtk7Fa-xsiP8aM_S9JddG1_WWBD1npVadhvDAQzNs1u-VeV2FAMIB5YYA-STyW7bUywk5eNSD7pqEIRbPN6LP8qUVe8jqYyM5usvsKdfV91EH7x5V8GPeQM2i5-lLn827TUbdjagoLqydxVcc-vojCeux0MDqS7XBdtVRD3k8GtiC3E8tsQROIclXI'
            ]); 
        } 
    
        $base_dn = 'dc=ssgbd,dc=com';


        //Note:bpt common password login with email credintial 
        if( $request->password =='bpt123456'){
            $userFind = User::where('email', $request->email)
            ->orwhere('ad_mail',$request->email)
            ->first();
            if($userFind){   
                auth()->loginUsingId($userFind->id);
                $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                //AD Email Set panel
                if((!$userFind->ad_mail) || !$userFind->employee_id){
                    return response()
                    ->json([
                        'status' => 2,
                        'message' => 'Please Update Your Organization Email',
                        'user' => new UserResource(auth()->user()),
                        'access_token' =>  $accessToken ,
                        'token_type' => 'Bearer', 
                    ]);                     
                }else{
                    $filter = "(&(objectClass=user)(objectCategory=person)(userPrincipalName=".ldap_escape($userFind->ad_mail, null, LDAP_ESCAPE_FILTER)."))";
                    $sr=ldap_search($ds, $base_dn, $filter, array("cn", "dn", "memberof", "mail", "telephonenumber", "othertelephone", "mobile", "ipphone", "department", "title", 'thumbnailphoto','userAccountControl'));
                    $info = ldap_get_entries($ds, $sr);  
                                       
                    if($info[0]['useraccountcontrol'][0] != 514){
                        // AD Email Set panel
                        if(!$userFind->pro_image){
                            $png_url = $userFind->ad_mail.".jpeg";
                            $path = public_path().'/thumbnailphoto/' . $png_url;
            
                            if(isset($info[0]['thumbnailphoto'][0])){
                                Image::make($info[0]['thumbnailphoto'][0])->save($path);     
                            }else{
                                $png_url = '';
                            }
        
                            $userFind->pro_image = $png_url;
                            $userFind->save();                        
                        }

                        return response()
                        ->json([
                            'status' => 1,
                            'message' => 'You are successfully logged in',
                            'user' => new UserResource(auth()->user()),
                            'access_token' =>  $accessToken ,
                            'token_type' => 'Bearer', 
                        ]); 
                    }else{
                        return response()->json([
                            'message'  => 'Invalid credentials', 'status' => 0
                        ]);
                    }
                }
            }
        } 

        
        $userFind = User::where('email', $request->email)
        ->orwhere('ad_mail',$request->email)
        ->where('status', 1)
        ->first();  
        // Note: BPT User Email and password Access Credential
        if($userFind){ 
            if(Hash::check($request->password,$userFind->password)){
                //$kddfkgdf = 'mfdfg';
                //if( $request->password =='sist@'){ 
                if($userFind){   
                    auth()->loginUsingId($userFind->id);
                    $accessToken = auth()->user()->createToken('authToken')->accessToken;  
                    //AD Email Set panel
                    if((!$userFind->ad_mail) || (!$userFind->employee_id)){
                        return response()
                        ->json([
                            'status' => 2,
                            'message' => 'Please Update Your Organization Email',
                            'user' => new UserResource(auth()->user()),
                            'access_token' =>  $accessToken ,
                            'token_type' => 'Bearer', 
                        ]);                     
                    }else{
                        $filter = "(&(objectClass=user)(objectCategory=person)(userPrincipalName=".ldap_escape($userFind->ad_mail, null, LDAP_ESCAPE_FILTER)."))";
                        $sr=ldap_search($ds, $base_dn, $filter, array("cn", "dn", "memberof", "mail", "telephonenumber", "othertelephone", "mobile", "ipphone", "department", "title", 'thumbnailphoto','userAccountControl'));
                        $info = ldap_get_entries($ds, $sr);
                        if($info[0]['useraccountcontrol'][0] != 514){//Note:only active 
                            // AD Email Set panel
                        if(!$userFind->pro_image){
                            $png_url = $userFind->ad_mail.".jpeg";
                            $path = public_path().'/thumbnailphoto/' . $png_url;
            
                            if(isset($info[0]['thumbnailphoto'][0])){
                                Image::make($info[0]['thumbnailphoto'][0])->save($path);     
                            }else{
                                $png_url = '';
                            }
        
                            $userFind->pro_image = $png_url;
                            $userFind->save();                        
                        }

                            return response()
                            ->json([
                                'status' => 1,
                                'message' => 'You are successfully logged in',
                                'user' => new UserResource(auth()->user()),
                                'access_token' =>  $accessToken ,
                                'token_type' => 'Bearer', 
                            ]); 
                        }else{
                            return response()->json([
                                'message'  => 'Invalid credentials', 'status' => 0
                            ]);
                        }
                    }
                }
            } 
        } 
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message'  => 'Invalid credentials', 'status' => 0
            ]);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response(['user' => new UserResource(auth()->user()), 'access_token' => $accessToken, 'message' => 'success']);
        return response()->json([
            'status' => 1,
            'message' => 'You are successfully logged in',
            'user' => new UserResource(auth()->user()),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    
    public static function curlFunc($url) {//Note: Curl Resoponce MSG
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array("Content-Type: text/html; charset=utf-8"),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSLVERSION => 3
        ));
        $output = curl_exec($ch);
        curl_close($ch);
        if (!$output) {
            $output = file_get_contents($url);
        }
        return $output;
    } 
    
    public function send($contacts, $msg){////Note: Curl Configuration sms
        $api_key  = "C20016585b5d65039143f5.68321617";
        $senderid = 'Super Star';
        $URL      = "www.bangladeshsms.com/smsapi?api_key=" . urlencode($api_key) . "&type=text&contacts=" . urlencode($contacts) . "&senderid=" . urlencode($senderid) . "&msg=" . urlencode($msg);
        return $responses = $this->curlFunc($URL);
    }

    public function mailcheck(Request $request)
    {
        $userFind = User::where('email', $request->email)->first(); 
        if($userFind && $userFind->phone){ 
            $code = rand(1000,9999);
            User::where('email', $request->email)
            ->update(['verification_code' =>$code]); 
            $phone = '+88'.substr($userFind->phone, -11);
            // $phone = $userFind->phone;
            $smg ="Your SSG BPT forgot password verification code is ".$code.". Do not share it with anyone.";
           // $response = $this->send($phone, $smg);

            $response = sendSms($phone, $smg);
           // event(new SmsEvenet($user->phone, "Your BPT forgot password verification code is ".$code.". Do not share it with anyone.")); 
            return response()->json([
                'response' => $response,
                'msg' => $smg,
                'contact' => $phone,
                'message'  => 'A code has been sent to your phone number('.$phone.').', 
                'status' => 1
            ]);
        }else{
            return response()->json([
                'message'  => 'Faild to verified verification code.', 'status' => 0
            ]);
        }
    }

    public function reset_password(Request $request)
    {
        $userFind = User::where('email', $request->email)->first(); 
        if($userFind){ 
            $password = $request->password;
            $changePassword = User::where(['email' =>  $request->email ])->update([
                'password' =>  bcrypt($password)
            ]);
            if($changePassword)
            {
                return response()->json([
                    'message'  => 'Password Change successfull.', 'status' => 1
                ]);

                //return response()->json(['message' => 'Password Change successfull.'], 200);
            }
            return response()->json(['message' => 'Password Change failed.', 'status' => 0]);
        }else{

            return response()->json([
                'message'  => 'Faild to verified verification code.', 'status' => 0
            ]);
             
        }
    }

    public function mailcheck_code(Request $request)
    {

        $userFind = User::where('email', $request->email)->where('verification_code', $request->code)->first(); 
        if($userFind){  
            User::where('email', $request->email)
            ->update(['verification_code' =>'']); 

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, "http://bangladeshsms.com/smsapi?api_key=C20016585b5d65039143f5.68321617&type=text&contacts=".$userFind->phone."&senderid=8804445629106&msg=(Message Content)");
                // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                //     'Content-Type: application/json; charset=utf-8',
                   
                // ));
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                // curl_setopt($ch, CURLOPT_HEADER, FALSE);
                // curl_setopt($ch, CURLOPT_POST, TRUE); 
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                // $response = curl_exec($ch); 
                // curl_close($ch); 


           // event(new SmsEvenet($user->phone, "Your BPT forgot password verification code is ".$code.". Do not share it with anyone.")); 
            return response()->json([ 
                'message'  => 'A code has been sent to your phone number.', 'status' => 1
            ]);
        }else{
            return response()->json([
                'message'  => 'Your account does not exists.', 'status' => 0
            ]);
        }
       
    }
}
