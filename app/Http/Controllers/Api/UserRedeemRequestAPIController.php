<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserRedeemRequestAPIRequest;
use App\Http\Requests\API\UpdateUserRedeemRequestAPIRequest;
use App\Models\UserRedeemRequest;
use App\Models\Settings;
use App\Models\PointRateSetting;
use App\Repositories\UserRedeemRequestRepository;
use Illuminate\Http\Request; 
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\UserRedeemRequestResource;
use App\Models\Technician; 
use App\Models\User; 
use Response; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserRedeemRequestController
 * @package App\Http\Controllers\API
 */

class UserRedeemRequestAPIController extends AppBaseController
{
    /** @var  UserRedeemRequestRepository */
    private $userRedeemRequestRepository;

    public function __construct(UserRedeemRequestRepository $userRedeemRequestRepo)
    {
        $this->userRedeemRequestRepository = $userRedeemRequestRepo;
    }

    /**
     * Display a listing of the UserRedeemRequest.
     * GET|HEAD /userRedeemRequests
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user_data = Auth::user(); 
        // $request['user_id'] =   $user_data->id ; 
        // $userRedeemRequests = $this->userRedeemRequestRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        $q =  UserRedeemRequest::with(['user','technician'])->where('status' , $request->status ? $request->status :  0 );
        $q->whereHas('technician',function( $query ) use ($request){  
            // $query->when($request->division_id, function($q) use ($request){
            //     return $q->where('division_id', $request->division_id);
            // });
            $query->when($request->district_id, function($q) use ($request){
                return $q->where('district_id', $request->district_id);
            });
            $query->when($request->upazilla_id, function($q) use ($request){
                return $q->where('upazilla_id', $request->upazilla_id);
            });
            $query->when($request->union_id, function($q) use ($request){
                return $q->where('union_id', $request->union_id);
            }); 
        });
        $q->whereHas('user',function( $query ) use ($request){  
            $query->when($request->phone_number, function($q) use ($request){
                return $q->where('email', $request->phone_number)
                        ->orWhere('phone_number',$request->phone_number);
            }); 
        });
        if($user_data && $user_data->id){
            $q->where('user_id' , $user_data->id);
        }
        $q->orderBy('id', 'DESC');
        $userRedeemRequests = $q->skip($request->skip ? $request->skip :  0 )->take($request->limit ? $request->limit :  10)->get(); 

        return $this->sendResponse(UserRedeemRequestResource::collection($userRedeemRequests), 'User Redeem Requests retrieved successfully');
    }
    //cancelRedeem


public function cancelRedeem($id, Request $request)
{
    $user_data = Auth::user();
    $technicianinfo = Technician::where('user_id', $user_data->id)->first();
    $redeeminfo = UserRedeemRequest::where('id', $id)
        ->where('user_id', $user_data->id)
        ->first();

    if (!$redeeminfo || $redeeminfo->status != 0) {
        return response()->json([
            'status' => 0,
            'message' => 'Your Redeem Request not found or already processed'
        ], 200);
    }

    try {
        DB::beginTransaction();

        // Update technician points
        $pointValue = [
            'current_point' => $technicianinfo->current_point + $redeeminfo->point,
            'pending_point' => $technicianinfo->pending_point - $redeeminfo->point,
        ];
        Technician::where('user_id', $user_data->id)->update($pointValue);

        // Update redeem request status
        $upData = [
            'status' => 3, // canceled
            'remarks' => 'Canceled by user',
        ];
        $this->userRedeemRequestRepository->update($upData, $id);

        DB::commit();

        return response()->json([
            'status' => 1,
            'message' => 'Your Redeem Request successfully canceled'
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 0,
            'message' => 'Failed to cancel redeem request: ' . $e->getMessage()
        ], 500);
    }
}

    public function ssforceIndex(Request $request)
    { 
        $user_data = Auth::user(); 
        // $request['user_id'] =   $user_data->id ; 
        // $userRedeemRequests = $this->userRedeemRequestRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );
       
        $q =  UserRedeemRequest::with(['user','technician'])->where('status' , $request->status ? $request->status :  0 );
      
        if($request->phone_number){ 
            $q->whereHas('user',function( $query ) use ($request){  
                $query->when($request->phone_number, function($q) use ($request){
                    return $q->where('email', $request->phone_number)
                            ->orWhere('phone_number',$request->phone_number);
                }); 
            });

        }else{
            $q->whereHas('technician',function( $query ) use ($request){  
           
                $query->when($request->district_id, function($q) use ($request){
                    return $q->where('district_id', $request->district_id);
                });
                $query->when($request->upazilla_id, function($q) use ($request){
                    return $q->where('upazilla_id', $request->upazilla_id);
                });
                $query->when($request->union_id, function($q) use ($request){
                    return $q->where('union_id', $request->union_id);
                }); 
            });
        }
       
        if($user_data && $user_data->id){
            $q->where('user_id' , $user_data->id);
        }
        $q->orderBy('id', 'DESC');
        $userRedeemRequests = $q->skip($request->skip ? $request->skip :  0 )->take($request->limit ? $request->limit :  100)->get(); 

        return $this->sendResponse(UserRedeemRequestResource::collection($userRedeemRequests), 'User Redeem Requests retrieved successfully');
    }
    public function ssforceUserRedeemRequestsBlankGatewayNumber(Request $request)
    {  

        $q =  UserRedeemRequest::with(['user','technician'])
            ->where('status' , $request->status ? $request->status :  0 )
            ->whereNull('gateway_number')
            ->orWhere('gateway_number', '');
        $q->whereHas('technician',function( $query ) use ($request){ 
            $query->when($request->district_id, function($q) use ($request){
                return $q->where('district_id', $request->district_id);
            });
            $query->when($request->upazilla_id, function($q) use ($request){
                return $q->where('upazilla_id', $request->upazilla_id);
            });
            $query->when($request->union_id, function($q) use ($request){
                return $q->where('union_id', $request->union_id);
            }); 
        });
        $q->whereHas('user',function( $query ) use ($request){  
            $query->when($request->phone_number, function($q) use ($request){
                return $q->where('email', $request->phone_number)
                        ->orWhere('phone_number',$request->phone_number);
            }); 
        }); 
        $userRedeemRequests = $q->get(); 
        foreach ($userRedeemRequests as $key => $value) {            
            $technicianInfo = array(
                'payment_gateway' =>  $value->technician->payment_gateway,
                'gateway_number' => $value->technician->gatway_number,
            ); 
            UserRedeemRequest::where('user_id', $value->user_id)->update($technicianInfo);            
        } 
        return $this->sendResponse($userRedeemRequests, 'Users Redeem Requests retrieved successfully');
    }

    public function redeemHistory(Request $request)
    {
        $user_data = Auth::user(); 
        $q =  UserRedeemRequest::with(['user','technician'])->where('status' , $request->status ? $request->status :  1 )
            ->when($request->sender_sap_code, function($q) use ($request){
                return $q->where('sender_sap_code', $request->sender_sap_code);
            });
            $q->when($request->start_date, function($q) use ($request){
                return $q->whereDate('otp_send_time', '>=', $request->start_date);
            }); 
            $q->when($request->end_date, function($q) use ($request){
                return $q->whereDate('otp_send_time', '<=', $request->end_date);
            }); 
        $q->whereHas('technician',function( $query ) use ($request){  
            // $query->when($request->division_id, function($q) use ($request){
            //     return $q->where('division_id', $request->division_id);
            // });
            $query->when($request->district_id, function($q) use ($request){
                return $q->where('district_id', $request->district_id);
            });
            $query->when($request->upazilla_id, function($q) use ($request){
                return $q->where('upazilla_id', $request->upazilla_id);
            });
            $query->when($request->union_id, function($q) use ($request){
                return $q->where('union_id', $request->union_id);
            }); 
        });
        $q->whereHas('user',function( $query ) use ($request){  
            $query->when($request->phone_number, function($q) use ($request){
                return $q->where('email', $request->phone_number)
                        ->orWhere('phone_number',$request->phone_number);
            }); 
        });
        if($user_data && $user_data->id){
            $q->where('user_id' , $user_data->id);
        }
        $q->orderBy('id', 'DESC');
        $userRedeemRequests = $q->skip($request->skip ? $request->skip :  0 )->take($request->limit ? $request->limit :  100)->get(); 

        return $this->sendResponse(UserRedeemRequestResource::collection($userRedeemRequests), 'User Redeem Requests retrieved successfully');
    }
    public function redeemDashboard(Request $request)
    {  
        $q = UserRedeemRequest::with('technician');
                $q->whereHas('technician',function( $query ) use ($request){  
                    // $query->when($request->division_id, function($q) use ($request){
                    //     return $q->where('division_id', $request->division_id);
                    // });
                    $query->when($request->district_id, function($q) use ($request){
                        return $q->where('district_id', $request->district_id);
                    });
                    $query->when($request->upazilla_id, function($q) use ($request){
                        return $q->where('upazilla_id', $request->upazilla_id);
                    });
                    $query->when($request->union_id, function($q) use ($request){
                        return $q->where('union_id', $request->union_id);
                    });
                });
                $q->when($request->start_date, function($q) use ($request){
                    return $q->whereDate('otp_send_time', '>=', $request->start_date);
                }); 
                $q->when($request->end_date, function($q) use ($request){
                    return $q->whereDate('otp_send_time', '<=', $request->end_date);
                });
                $q->where('sender_sap_code', $request->sender_sap_code);
        $t = Technician::where('district_id', $request->district_id)
            ->where('upazilla_id', $request->upazilla_id)
            ->where('union_id', $request->union_id);

        $data = array(
            'technician' => $t->count(),
            'request_count' => $q->count(),
            'redeem_count' => $q->where('status',1)->count(),
            'paid_amount' => $q->where('status',1)->sum('amount'),
        );
        return $this->sendResponse($data, 'User Redeem Requests retrieved successfully');
    }

    /**
     * Store a newly created UserRedeemRequest in storage.
     * POST /userRedeemRequests
     *
     * @param CreateUserRedeemRequestAPIRequest $request
     *
     * @return Response
     */
    // public function store(CreateUserRedeemRequestAPIRequest $request)
    // { 
    //     $user_data = Auth::user();  

    //     $technician = Technician::where('user_id', $user_data->id)->first();
    //     if($technician->payment_gateway == null || $technician->payment_gateway == '' || $technician->gatway_number == null || $technician->gatway_number == '' ){
    //         return response()->json(['status' => 0 ,'message' => 'Please set your Payment Gateway in your Profile'], 200); 
    //     }
    //     if($technician->payment_gateway != 1  ){
    //         return response()->json(['status' => 0 ,'message' => 'Please set your Payment Gateway to bKash'], 200); 
    //     }   

    //     $result = PointRateSetting::with('settings')->where('country_id',$technician->country_id)->first();  
    //     $user_data = Auth::user();
    //     $technicianinfo = Technician::where('user_id', $user_data->id)->first();
    //     // dd( $technicianinfo->gatway_number);
    //     if ($technicianinfo->current_point >= $request->point) {
    //         if ($result->settings->min_redeem_point <= $request->point) {             
    //             $input = $request->all();
    //             $input['user_id'] = $technicianinfo->user_id;
    //             $input['status'] = 0;
    //             $point  =  $request->point;
    //             //$input['amount'] = number_format(($point / $result->point_rate),2);
    //             $input['amount'] = number_format(($point / 4),2);
    //             $input['payment_gateway'] =  $technicianinfo->payment_gateway ;
    //             $input['gateway_number'] = $technicianinfo->gatway_number ; 
    //             $poitValue = array(
    //                 'current_point' =>  $technicianinfo->current_point - $request->point,
    //                 'pending_point' => $technicianinfo->pending_point + $request->point,
    //             ); 
    //             Technician::where('user_id', $technicianinfo->user_id)->update($poitValue);
    //             $this->userRedeemRequestRepository->create($input); 
    //             return response()->json(['status' => 1 ,'message' => 'User Redeem Request saved successfully'], 200); 
    //         } else {
    //             return response()->json(['status' => 0 ,'message' => 'Minimum redeem points ' . $result->settings->min_redeem_point ], 200);  
    //         }
    //     } else {
    //         return response()->json(['status' => 0 ,'message' => 'Your Current point is ' . $technicianinfo->current_point ], 200);  
    //     } 
    // }

 
        public function store(CreateUserRedeemRequestAPIRequest $request)
        {

            return response()->json(['status' =>0 ,'message' => 'The redeem option temporarily off.'], 200); 
            $user = Auth::user();
            $technician = Technician::where('user_id', $user->id)->first(); 
              
            // ✅ Basic validation checks
            if (!$technician) {
                return response()->json(['status' => 0, 'message' => 'Technician not found'], 200);
            }

            if (empty($technician->payment_gateway) || empty($technician->gatway_number)) {
                return response()->json(['status' => 0, 'message' => 'Please set your Payment Gateway in your Profile'], 200);
            }

            if ($technician->payment_gateway != 1) {
                return response()->json(['status' => 0, 'message' => 'Please set your Payment Gateway to bKash'], 200);
            }

            $rate = PointRateSetting::with('settings')->where('country_id', $technician->country_id)->first();
            if (!$rate || !$rate->settings) {
                return response()->json(['status' => 0, 'message' => 'Rate setting not found'], 200);
            }

            // ✅ Validate point amount
            if ($technician->current_point < $request->point) {
                return response()->json(['status' => 0, 'message' => 'Your Current point is ' . $technician->current_point], 200);
            }

            if ($rate->settings->min_redeem_point > $request->point) {
                return response()->json(['status' => 0, 'message' => 'Minimum redeem points ' . $rate->settings->min_redeem_point], 200);
            }

          

            if (empty($technician->gatway_number)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Please set your Payment Gateway Number in your Profile'
                ], 200);
            }  

            
            // 11 digit and numeric check
            $number = $technician->gatway_number;

            // Remove +88 or 88 from start
            $number = preg_replace('/^\+?88/', '', $number);

            // Now check if remaining part is exactly 11 digits
            if (strlen($number) != 11 || !ctype_digit($number)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Payment Gateway Number must be exactly 11 digits and numeric'
                ], 200);
            }

            // ✅ Prepare input
            $input = $request->all();
            $input['user_id'] = $technician->user_id;
            $input['status'] = 0;
            $input['amount'] = number_format(($request->point / 4), 2);
            $input['payment_gateway'] = $technician->payment_gateway;
            $input['gateway_number'] = $technician->gatway_number;

           

            // ✅ Transaction starts
            DB::beginTransaction();

            try {
                // 1️⃣ Update technician points
                Technician::where('user_id', $technician->user_id)->update([
                    'current_point' => $technician->current_point - $request->point,
                    'pending_point' => $technician->pending_point + $request->point,
                ]);

                // 2️⃣ Create redeem request
                $this->userRedeemRequestRepository->create($input);

                // 3️⃣ Commit transaction
                DB::commit();

                return response()->json(['status' => 1, 'message' => 'User Redeem Request saved successfully'], 200);
            } catch (\Exception $e) {
                // 4️⃣ Rollback on error
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong: ' . $e->getMessage(),
                ], 200);
            }
        }

    public function storeBackup(CreateUserRedeemRequestAPIRequest $request)
    { 
        $settings =  Settings::with('pointrate')->find(1); 
        $user_data = Auth::user();
        $technicianinfo = Technician::where('user_id', $user_data->id)->first();
        // dd( $technicianinfo->gatway_number);
        if ($technicianinfo->current_point >= $request->point) {
            if ($settings->min_redeem_point <= $request->point) {
             
                $input = $request->all();
                $input['user_id'] = $user_data->id;
                $input['status'] = 0;
                $point  =  $request->point;
                $input['amount'] = ($point / $settings->point_rate);
                $input['payment_gateway'] =  $technicianinfo->payment_gateway ;
                $input['gateway_number'] = $technicianinfo->gatway_number ; 
                $poitValue = array(
                    'current_point' =>  $technicianinfo->current_point - $request->point,
                    'pending_point' => $technicianinfo->pending_point + $request->point,
                );
                Technician::where('user_id', $user_data->id)->update($poitValue);
                $userRedeemRequest = $this->userRedeemRequestRepository->create($input); 
                //return $this->sendResponse($userRedeemRequest, 'User Redeem Request saved successfully');
                return response()->json(['status' => 0 ,'message' => 'User Redeem Request saved successfully'], 200); 
            } else {
                return response()->json(['status' => 0 ,'message' => 'Minimum redeem points ' . $settings->min_redeem_point ], 200); 
                //return $this->sendResponse(0, 'Minimum redeem points ' . $settings->min_redeem_point);
            }
        } else {
            return response()->json(['status' => 0 ,'message' => 'Your Current point is ' . $technicianinfo->current_point ], 200); 
           // return $this->sendResponse(0, 'Your Current point is ' . $technicianinfo->current_point);
        }
        //print_r($settings->min_redeem_point);


        //     return $this->sendResponse( $userRedeemRequest , 'User Redeem Request saved successfully');
    }
    public function storess(Request $request)
    { 
        $result = PointRateSetting::with('settings')->where('country_id', $request->country_id)->first();  
        $user_data = Auth::user();
        $technicianinfo = Technician::where('user_id', $user_data->id)->first();
        // dd( $technicianinfo->gatway_number);
        if ($technicianinfo->current_point >= $request->point) {
            if ($result->settings->min_redeem_point <= $request->point) {             
                $input = $request->all();
                $input['user_id'] = $technicianinfo->user_id;
                $input['status'] = 0;
                $point  =  $request->point;
                $input['amount'] = ($point / $result->point_rate);
                $input['payment_gateway'] =  $technicianinfo->payment_gateway ;
                $input['gateway_number'] = $technicianinfo->gatway_number ; 
                $poitValue = array(
                    'current_point' =>  $technicianinfo->current_point - $request->point,
                    'pending_point' => $technicianinfo->pending_point + $request->point,
                ); 
                Technician::where('user_id', $technicianinfo->user_id)->update($poitValue);
                $userRedeemRequest = $this->userRedeemRequestRepository->create($input); 
                return response()->json(['status' => 0 ,'message' => 'User Redeem Request saved successfully'], 200); 
            } else {
                return response()->json(['status' => 0 ,'message' => 'Minimum redeem points ' . $result->settings->min_redeem_point ], 200);  
            }
        } else {
            return response()->json(['status' => 0 ,'message' => 'Your Current point is ' . $technicianinfo->current_point ], 200);  
        } 
    }

    public function generateOTP(Request $request)
    {    
        $technicianinfo = $this->userRedeemRequestRepository->find($request->redeem_request_id); 
        if ($technicianinfo) { 
            $otp = $request->otp_code; //mt_rand(100000,999999);
            $phone_number = $technicianinfo->gateway_number;
            $upData = array(
                'opt_code' => $otp, 
                'sender_sap_code' => $request->sender_sap_code, 
                'otp_send_time' => date('Y-m-d H:i:s')
            ); 
            $this->userRedeemRequestRepository->update($upData, $request->redeem_request_id);
            $msg = "আপনার টেকনিশিয়ান OTP কোড হল " . $otp;
            $this->sendSms($phone_number, $msg); 
            return response()->json(['status' => 200 ,'message' => 'Redeem Request OTP successfully generated, Please check Technician Mobile phone'], 200);  
        } else {
            return response()->json(['status' => 100 ,'message' => 'Your Request technician not found!'], 200);  
        }        
    }
    public function otpCheck(Request $request)
    {    
 
        $technicianinfo = $this->userRedeemRequestRepository->find($request->redeem_request_id);   
        if ($technicianinfo) {  
            if(($technicianinfo->opt_code == $request->sender_opt_code) && ($request->status ==0) && ($technicianinfo->opt_code)){
                $incremnetalDate = strtotime('+20 minutes', strtotime($technicianinfo->otp_send_time)); 
                $currentDateTime = strtotime(date('Y-m-d H:i:s')); 
                if($incremnetalDate > $currentDateTime){
                    $upData = array(
                        'status' => 1, 
                        'paid_at' =>  date('Y-m-d H:i:s'),
                        'sender_info' => $request->userInfo ? $request->userInfo :''
                    ); 
                    $this->userRedeemRequestRepository->update($upData, $request->redeem_request_id); 
                    return response()->json(['status' => 201,'message' => 'Success'], 200); 
                }else{
                    return response()->json(['status' => 200,'message' => 'OTP Code expired, Please resend OPT Code '], 200); 
                } 
            }else{
                return response()->json(['status' => 200,'message' => 'OTP Code Invalid'], 200); 
            } 
        } else {
            return response()->json(['status' => 100 ,'message' => 'Your Request not found!'], 200);  
        }
        
    }

    /**
     * Display the specified UserRedeemRequest.
     * GET|HEAD /userRedeemRequests/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UserRedeemRequest $userRedeemRequest */
        $userRedeemRequest = $this->userRedeemRequestRepository->find($id);

        if (empty($userRedeemRequest)) {
            return $this->sendError('User Redeem Request not found');
        }

        return $this->sendResponse(new UserRedeemRequestResource($userRedeemRequest), 'User Redeem Request retrieved successfully');
    }


    public function sendSms($phone, $smg) {
        $url = 'https://gpcmp.grameenphone.com/ecmapigw/webresources/ecmapigw.v2';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, [
            'username' => "IRbulbadmin",   //   "IRbulbadmin", // 2023-
            'password' => "*Ssg@2023",   //   "*Ssg@2023",
            'apicode' => "1",
            'msisdn' => $phone,
            'countrycode' => "880",
            'cli' => "S.S.G",
            'messagetype' => "3",
            'message' => $smg,
            'messageid' => "0"
        ]);  
        $data = array('status'=> $response->status(),'body'=> $response->body());
        return response()->json($data, 200);
        if ($response->ok()) {
            //  'SMS sent successfully.';
        } else {
            $a = 'Failed to send SMS. Error message: ' . $response->body();
            //print_r($a);
            // exit;
        }
    }

    /**
     * Update the specified UserRedeemRequest in storage.
     * PUT/PATCH /userRedeemRequests/{id}
     *
     * @param int $id
     * @param UpdateUserRedeemRequestAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRedeemRequestAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserRedeemRequest $userRedeemRequest */
        $userRedeemRequest = $this->userRedeemRequestRepository->find($id);

        if (empty($userRedeemRequest)) {
            return $this->sendError('User Redeem Request not found');
        }

        $userRedeemRequest = $this->userRedeemRequestRepository->update($input, $id);

        return $this->sendResponse(new UserRedeemRequestResource($userRedeemRequest), 'UserRedeemRequest updated successfully');
    }

    /**
     * Remove the specified UserRedeemRequest from storage.
     * DELETE /userRedeemRequests/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UserRedeemRequest $userRedeemRequest */
        $userRedeemRequest = $this->userRedeemRequestRepository->find($id);

        if (empty($userRedeemRequest)) {
            return $this->sendError('User Redeem Request not found');
        }

        $userRedeemRequest->delete();

        return $this->sendSuccess('User Redeem Request deleted successfully');
    }


    public function demoRequest(Request $request)
    { 

        $settings =  Settings::find(1);
        $user_data = User::where('id', $request->id)->first();
        $technicianinfo = Technician::where('user_id', $user_data->id)->first();
        // dd( $technicianinfo->gatway_number);
        if ($technicianinfo->current_point >= $request->point) {
            if ($settings->min_redeem_point <= $request->point) {
             
                $input = $request->all();
                $input['user_id'] = $user_data->id;
                $input['status'] = 0;
                $point  =  $request->point;
                $input['amount'] = ($point / $settings->point_rate);
                $input['payment_gateway'] =  $technicianinfo->payment_gateway ;
                $input['gateway_number'] = $technicianinfo->gatway_number ; 
                $poitValue = array(
                    'current_point' =>  $technicianinfo->current_point - $request->point,
                    'pending_point' => $technicianinfo->pending_point + $request->point,
                );
                Technician::where('user_id', $user_data->id)->update($poitValue);
                $userRedeemRequest = $this->userRedeemRequestRepository->create($input); 
                //return $this->sendResponse($userRedeemRequest, 'User Redeem Request saved successfully');
                return response()->json(['status' => 0 ,'message' => 'User Redeem Request saved successfully'], 200); 
            } else {
                return response()->json(['status' => 0 ,'message' => 'Minimum redeem points ' . $settings->min_redeem_point ], 200); 
                return $this->sendResponse(0, 'Minimum redeem points ' . $settings->min_redeem_point);
            }
        } else {
           return response()->json(['status' => 0 ,'message' => 'Your Current point is ' . $technicianinfo->current_point ], 200); 
           return $this->sendResponse(0, 'Your Current point is ' . $technicianinfo->current_point);
        } 
    }
}
