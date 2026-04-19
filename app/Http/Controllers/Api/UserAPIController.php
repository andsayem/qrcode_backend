<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Technician;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response; 
use Hash, PDF;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Http;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */

class UserAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     * GET|HEAD /users
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully');
    }

    public function Profile()
    {
        $user = Auth::user();
        $user_data = new ProfileResource($user);
        return response()->json($user_data, 200);
        //return $this->sendResponse(new ProfileResource($user_data), 'Users retrieved successfully');
    }

    /**
     * Store a newly created User in storage.
     * POST /users
     *
     * @param CreateUserAPIRequest $request
     *
     * @return Response
     */

    public function changeImage(Request $request)
    {
        $id = Auth::user()->id;
        // $id = auth()->user()->id;
        $client = User::where(['id' => $id])->first();

        if ($request->hasFile('photo')) {
            $newFileName = Str::random(64) . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('public/profile', $newFileName, 'local');
            $client->profile_image = $newFileName;
        }

        //$client->photo = $this->base64_to_image($request->photo, 'Technician');

        if ($client->save()) {
            return response()->json(['message' => 'Image uploaded successfully', 'profile_image' =>  $newFileName ? url('storage/profile/' . $newFileName) : ''], 200);
        }
        return response()->json(['message' => 'Profile picture failed'], 200);
    }

    /** base64 image upload function */
    function base64_to_image($base64_string, $location)
    {
        $filename = time() . rand(100, 999) . ".jpg";
        $local_path  = $_SERVER['DOCUMENT_ROOT'];

        $path        = env('APP_PATH') . "/public/" . $location . "/" . $filename;
        $output_file = $local_path . "/" . $path; //save to local address

        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        $data = explode(',', $base64_string);
        if (sizeof($data) > 1) {
            // we could add validation here with ensuring count( $data ) > 1
            fwrite($ifp, base64_decode($data[1]));
            // clean up the file resource
            fclose($ifp);
        } else {
            $filename = NULL;
        }

        return $filename;
    }


    public function store(Request $request)
    {

        $userFind = User::where('email', $request->email)->first();
        if($userFind ){ 
            
            $otp_code = mt_rand(100001, 999999);

            User::where('id',  $userFind ->id )->update(['otp_code' => $otp_code ]);

            $signature = $request->signature ?? '';

            $msg = "Your Technician sign up verification code is ".$otp_code.". Do not share it with anyone. App Signature " .$signature;
            // $msg = "আপনার টেকনিশিয়ান সাইন আপ যাচাইকরণ কোড হল " . $otp_code . " , কারো সাথে শেয়ার করবেন না। " . $signature;
         
            sendMaskSms( $request->email , $msg);

            return $this->sendResponse($msg, 'User saved successfully');


        }else{

      
        $input = $request->all();
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users,email',
            
        ], [
            'name.required' => 'Name field is required',
            'country_id.required' => 'country field is required',
            'email.required' => 'Mobile Number field is required',
            'email.unique' => 'This mobilenumber already exists', 
        ]);
        // 'ip_address' => 'required|unique:users,ip_address',
        // 'ip_address.required' => 'This ip address is required',
        // 'ip_address.unique' => 'This ip address already exists',
        $otp_code = mt_rand(100001, 999999);
        $referral_code = mt_rand(00001, 99999);
        $input['otp_code'] =  $otp_code;
        $input['status'] = 1; // Auto Active  
        $input['phone_number'] = $request->phone_number ? $request->phone_number : $request->email;
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //Technician
        $technician = Technician::create([
            'user_id' => $user->id,
            'country_id' => $request->country_id,
            'total_point' => 0,
            'referral_code' =>  $user->id . '' . $referral_code,
            'total_redeem_value' => 0,
            'current_point' => 0,
            "payment_gateway" => 1, // 1= bKash
            "gatway_number" => "",
            "nid_font" => "",
            "nid_back" => "",
            "pending_point" => 0,
            "father_name" => "",
            "permanent_address" => "",
            "current_address" => "",
            "birthday" => "",
            "occupation" => "",
            "nid_number" => "",
            "blood_group" => "",
            "experience" => "",
            "organization" => "",
            "dealer_code" => "",
            "dealer_name" => "",
            "zone" => "",
            "education" => "",
            "division_id" => $request->division_id,
            "district_id" => $request->district_id,
            "upazilla_id" =>  $request->thana_id,
            "union_id" => $request->area_id,
            "fo_code" => isset($request->reference_data['fo']) ?  $request->reference_data['fo']['email'] : "",
            "fo_name" => isset($request->reference_data['fo']) ? $request->reference_data['fo']['display_name'] : "",
            "tsm_code" => isset($request->reference_data['tsm']) ? $request->reference_data['tsm']['email'] : "",
            "tsm_name" => isset($request->reference_data['tsm']) ? $request->reference_data['tsm']['display_name'] : "",
            "point_code" => isset($request->reference_data['point']) ? $request->reference_data['point']['sap_code'] : "",
            "point_name" => isset($request->reference_data['point']) ? $request->reference_data['point']['point_name'] : "",
        ]);

        if($technician){
            if($technician->fo_code){
                $user->status = 1;
                $user->update();
            }
        }

        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        $user->assignRole(15);
        $phone_number = $request->email;
        $signature = $request->signature ?? '';

        $msg = "Your Technician sign up verification code is ".$otp_code.". Do not share it with anyone. App Signature " .$signature;
        //$msg = "আপনার টেকনিশিয়ান সাইন আপ যাচাইকরণ কোড হল " . $otp_code . " , কারো সাথে শেয়ার করবেন না। " . $signature;
        // $this->fastSMS($phone_number, $msg);

       //  fastSMS($request->email, $msg);
         sendMaskSms( $request->email , $msg);

        return $this->sendResponse($msg, 'User saved successfully');

    }
        // return $this->sendResponse($request->all(), 'test 3 : User saved successfully');
    }

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

   public function checksms() { 
        $response = sendMaskSms('01313799790', 'I Love You , Akhi');

        print_r($response) ;
    }


    public function send($contacts, $msg)
    { ////Note: Curl Configuration sms
        $api_key  = "C20016585b5d65039143f5.68321617";
        $senderid = 'Super Star';
        $URL      = "www.bangladeshsms.com/smsapi?api_key=" . urlencode($api_key) . "&type=text&contacts=" . urlencode($contacts) . "&senderid=" . urlencode($senderid) . "&msg=" . urlencode($msg);
        return  $this->curlFunc($URL);
    }

    public static function curlFunc($url)
    { //Note: Curl Resoponce MSG
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

    public function varifyPasswordOtp(Request $request)
    {
        $data = $request->validate([
            // 'email'    => 'required|exists:password_resets,phone|size:11|regex:/(01)[0-9]{9}/',
            'email'    => 'required',
        ]);

        $email = $request->email;
        $otp_code = $request->otp_code;
        $exists = User::where(['email' => $email, 'otp_code' => $otp_code])->exists();
        if ($exists) {
            User::where(['email' => $request->email])->update([
                'otp_code' => '',
                'phone_verification_status' => 1
            ]);
            return response()->json(['status' => 1, 'message' => 'Verification code verified successfully.'], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Faild to verified verification code.'], 400);
    }
    public function resend_otp(Request $request)
    {

        $request->validate([
            'email'    => 'required',
        ]);
        $signature = $request->signature ??  '';
        $otp_code =  $otp_code = mt_rand(100001, 999999);
        User::where(['email' => $request->email])->update([
            'otp_code' => $otp_code,
            'status' => 0
        ]);
       
        $msg = "Your Technician sign up verification code is ".$otp_code.". Do not share it with anyone. App Signature " .$signature;
        
        sendMaskSms($request->email, $msg);
        return response()->json(['status' => 1, 'message' => 'Verification code resend successfully.'], 200);

    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            //'phone'    => 'required|exists:users,phone|size:11|regex:/(01)[0-9]{9}/',

            'password' => 'required',
        ]);

        //$phone = $request->email;
        $password = $request->password;

        $changePassword = User::where(['email' => $request->email])->update([
            'password' => bcrypt($password)
        ]);

        if ($changePassword) {
            return response()->json(['status' => 1, 'message' => 'Password Change successfull.'], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Password Change failed.'], 200);
    }
    public function changePassword(Request $request)
    {
        // $data = $request->validate([
        //     'password' => 'required|confirmed|min:6',
        // ]);

        $data = $request->validate([
            'password' => 'required|min:4'
        ], [
            'password.required' => 'Password field is required'
        ]);

        $password = $request->password;
        $user = User::findOrFail(Auth::user()->id);

        //return response()->json(['users' => Auth::user(),'message' => 'Old password does not match.','status' => 0], 200);


        if (Hash::check($request->old_password, $user->password)) {
            $changePassword = User::where(['id' =>  Auth::user()->id])->update([
                'password' => bcrypt($password)
            ]);

            if ($changePassword) {
                return response()->json(['message' => 'Password Changed successfully.', 'status' => 1], 200);
            }
            return response()->json(['message' => 'Password change failed.', 'status' => 0], 200);
        } else {
            return response()->json(['message' => 'Old password does not match.', 'status' => 0], 200);
        }
    }

    public function checkExistsUser(Request $request)
    {
        $data = $request->validate([
            //'phone'    => 'required|size:11|regex:/(01)[0-9]{9}/',
            'email'    => 'required|size:11|regex:/(01)[0-9]{9}/'
        ]);
        //return response()->json(['status' => 1, 'message' => 'The verification code has been sent to your phone number'], 200);
        $email = $request->email;
        $user = User::where(['email' => $email])->first();


        $response = [];
        if (!empty($user)) {
            $otp_code = mt_rand(100000, 999999);
            User::where(['email' => $request->email])->update([
                'otp_code' => $otp_code
            ]);

            // Send SMS 
            $signature = $request->signature ??  '';
            $msg = "আপনার টেকনিশিয়ান অ্যাপ টির পাসওয়ার্ড পরিবর্তনের জন্য যাচাইকরণ কোড হলো " . $otp_code . " , কারো সাথে কোড শেয়ার করবেন না।  " . $signature; 
            sendMaskSms( $request->email , $msg );

            return response()->json(['status' => 1, 'message' => 'The verification code has been sent to your phone number'], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Mobile no can not found.'], 200);
    }
    /**
     * Display the specified User.
     * GET|HEAD /users/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        return $this->sendResponse(new UserResource($user), 'User retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     * PUT/PATCH /users/{id}
     *
     * @param int $id
     * @param UpdateUserAPIRequest $request
     *
     * @return Response
     */


    /**
     * Remove the specified User from storage.
     * DELETE /users/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->delete();

        return $this->sendSuccess('User deleted successfully');
    }

    public function id_card($id)
    {

        $userInfo = User::find($id);

        //view('backend.print_status.idCard')->with($data);
        $pdf = PDF::loadView('backend.users.idCard', compact('userInfo'));

        return $pdf->download('disney.pdf');
    }


    public function checkUserBirthdayOrNot()
    {

        $user = Auth::user(); // Logged in user

        $birthdayToday = false;

        if ($user) {
            $technician = Technician::where('user_id', $user->id)->first();
            if($technician){
                $today = Carbon::now()->format('m-d');
                $userBirthday = Carbon::parse($technician->birthday)->format('m-d');

                if ($today === $userBirthday) {
                    $birthdayToday = true;
                }
            }
        }

        return $this->sendResponse(['birthday'=>$birthdayToday], 'Birthday data retrieved successfully');
    }


    public function divisions(Request $request)
    {
        $data = DB::table('divisions')->get();
        return $data;
    }
    public function districts($id, Request $request)
    {
        $data = DB::table('districts')->where('division_id', $id)->get();
        return $data;
    }
    public function upazilas($id, Request $request)
    {
        $data = DB::table('upazilas')->where('district_id', $id)->get();
        return $data;
    }
    public function unions($id, Request $request)
    {
        $data = DB::table('unions')->where('upazilla_id', $id)->get();
        return $data;
    }
}
