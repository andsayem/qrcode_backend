<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserPointAPIRequest;
use App\Http\Requests\API\UpdateUserPointAPIRequest;
use App\Models\UserPoint;
use App\Models\Product;
use App\Repositories\UserPointRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\UserPointResource;
use App\Models\CodeVerifyLog;
use App\Models\SSGCodeDetail; 
use App\Models\Campaign; 
use App\Models\CampaignDetails; 
use App\Http\Requests\CheckCodeURLValidateRequest;
use Response;
// use Auth, DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Technician;
use App\Models\ChannelSettings;
use Illuminate\Support\Carbon; 
use App\Models\User;


/**
 * Class UserPointController
 * @package App\Http\Controllers\API
 */

class UserPointAPIController extends AppBaseController
{
    /** @var  UserPointRepository */
    private $userPointRepository;

    public function __construct(UserPointRepository $userPointRepo)
    {
        $this->userPointRepository = $userPointRepo;
    }

    /**
     * Display a listing of the UserPoint.
     * GET|HEAD /userPoints
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user_data = Auth::user(); 
        $request['user_id']  =  $user_data->id ; 
        $q = UserPoint::orderBy('id', 'DESC');
        if($user_data){
            $q->where('user_id',$user_data->id);
        } 
        $userPoints = $q->skip($request->skip ?? 0 )->take($request->limit ?? 10 )->get();
        return $this->sendResponse(UserPointResource::collection($userPoints), 'User Points retrieved successfully');
    }

    /**
     * Store a newly created UserPoint in storage.
     * POST /userPoints
     *
     * @param CreateUserPointAPIRequest $request
     *
     * @return Response
     */ 
public function getHeroOfDay()
{
    $today = Carbon::today();

    // আজকের points grouped by user, top 3
    $userPoints = UserPoint::select(
            'users.id',
            'users.name',
            'users.email',
            'users.profile_image',
            DB::raw('SUM(user_points.point) as total_points')
        )
        ->join('users', 'users.id', '=', 'user_points.user_id')
        ->whereDate('user_points.created_at', $today)
        ->groupBy('users.id', 'users.name', 'users.email')
        ->orderByDesc('total_points')
        ->limit(3)
        ->get();

    if ($userPoints->isEmpty()) {
        return $this->sendResponse([], 'No points earned today', 200);
    }

    // response structure
    $heroes = $userPoints->map(function ($hero) {
        return [
            'id' => $hero->id,
            'name' => $hero->name,
            'email' => $hero->email,
            'profile_image' => $hero->profile_image,
            'total_points' => $hero->total_points,
        ];
    });

    $data = [
        'date' => $today->toDateString(),
        'heroes' => $heroes,
    ];

    return $this->sendResponse($data, 'Top 3 heroes of the day fetched successfully');
}

public function getHeroesOfPreviousMonth()
{
    $startOfPrevMonth = Carbon::now()->startOfMonth(); // 1st day of this month
    $endOfPrevMonth = Carbon::now(); // current date and time

    $heroes = UserPoint::select(
            'users.id',
            'users.name',
            'users.email',
            'users.profile_image',
            DB::raw('SUM(user_points.point) as total_points')
        )
        ->join('users', 'users.id', '=', 'user_points.user_id')
        ->whereBetween('user_points.created_at', [$startOfPrevMonth, $endOfPrevMonth])
        ->groupBy('users.id', 'users.name', 'users.email')
        ->orderByDesc('total_points')
        ->limit(3)
        ->get();

 

    if ($heroes->isEmpty()) {
        return $this->sendResponse([], 'No points earned last month', 200);
    }

    $data = [
        'month' => $startOfPrevMonth->format('F Y'),
        'heroes' => $heroes,
    ];

    return $this->sendResponse($data, 'Top heroes of the previous month fetched successfully');
}

    //scanQrCodeGeneral

    public function scanQrCodeGeneral(Request $request)
    {  
        if($request->code){ 
            $ssgCodeDetail = SSGCodeDetail::where('code', $request->code)->first();  
            if($ssgCodeDetail){  
                $input = $request->all();
               
                if (isset($ssgCodeDetail) && $ssgCodeDetail->total_used >= 1) {
                    $status = 'failed';
                    SSGCodeDetail::where('code', $request->code)->update([
                        'total_used' => $ssgCodeDetail->total_used + 1,
                        'code_used_time' => getNow()
                    ]); 
                    $codeVerifyLog = new CodeVerifyLog;
                    $codeVerifyLog->product_id = $ssgCodeDetail->product_id ?? null;
                   // $codeVerifyLog->mobile_no = $user_data->phone_number ? $user_data->phone_number : $user_data->email;
                    $codeVerifyLog->code = $request->code ?? null;
                    $codeVerifyLog->requested_ip = $request->ip() ?? null;
                    $codeVerifyLog->status = $status ?? null;
                    $codeVerifyLog->save(); 
                    return response()->json(['status' => 2 ,'message' => 'Alert! This code has already been scanned/checked by +8801xxxxxxx'.substr($ssgCodeDetail->mobile, - 3).'. If it is not you please contact with your seller'], 200); 
                } 
                return response()->json(['status' => 1 ,'message' => 'Verified! This is an original product from Super Star Group (SSG). Thank you for choosing us.'], 200); 
            }else{
                return response()->json(['status' => 0 ,'message' => 'This code is invalid. Please enter the right code or contact with seller.'], 200); 
            }
        }else{
            return response()->json(['status' => 0 ,'message' => 'This code is invalid. Please enter the right code or contact with seller.'], 200); 
        }
    }
    public function scanQrCode(Request $request)
    { 
        $user_data = Auth::user();  
       // return response()->json(['data' => $user_data  ], 200);   
        if($request->code){ 
            $ssgCodeDetail = SSGCodeDetail::where('code', $request->code)->first();  
            if($ssgCodeDetail){ 
               // return response()->json(['status' => 1 ,'message' => 'The point scanning server is currently under maintenance and will be restored within 3rd February.'], 200); 
 
                $input = $request->all();
                $input['mobile'] = $user_data->phone_number;
                $input['user_id'] = $user_data->id; 
                //return response()->json(['status' => 2 ,'message' => 'Alert! This code has already been scanned/checked by '.$ssgCodeDetail->mobile.'. If it is not you please contact with your seller'], 200);   
                if (isset($ssgCodeDetail) && $ssgCodeDetail->total_used >= 1) {
                    $status = 'failed';
                    SSGCodeDetail::where('code', $request->code)->update([
                        'total_used' => $ssgCodeDetail->total_used + 1,
                        'code_used_time' => getNow()
                    ]);
                    
                    $codeVerifyLog = new CodeVerifyLog;
                    $codeVerifyLog->product_id = $ssgCodeDetail->product_id ?? null;
                    $codeVerifyLog->mobile_no = $user_data->phone_number ? $user_data->phone_number : $user_data->email;
                    $codeVerifyLog->code = $request->code ?? null;
                    $codeVerifyLog->requested_ip = $request->ip() ?? null;
                    $codeVerifyLog->status = $status ?? null;
                    $codeVerifyLog->save();

                    return response()->json(['status' => 2 ,'message' => 'Alert! This code has already been scanned/checked by +8801xxxxxxx'.substr($ssgCodeDetail->mobile, - 3).'. If it is not you please contact with your seller'], 200); 
                }

                if ($ssgCodeDetail) {
                    $status = 'success';
                    SSGCodeDetail::where('code', $request->code)->update([
                        'mobile' =>  $user_data->phone_number ? $user_data->phone_number : $user_data->email,
                        'status' => 1,
                        'total_used' => $ssgCodeDetail->total_used + 1,
                        'code_used_time' => getNow(),
                        'lat' =>  $request->lat,
                        'long' =>  $request->long,
                        'address' =>  $request->address,
                    ]);
                    // $userPoint = new UserPoint;
                    // $userPoint->product_id = $ssgCodeDetail->product_id ;
                    // $userPoint->user_id = $user_data->id ;
                    // $userPoint->point = $this->getPoint($ssgCodeDetail) ;
                    // $userPoint->save(); 
                    $point = $this->getPoint($ssgCodeDetail);
                    $userPoint = UserPoint::create([
                        'ssg_code_details' =>$ssgCodeDetail->id ,
                        'product_id' => $ssgCodeDetail->product_id ,
                        'user_id' => $user_data->id  ,
                        'point' =>  $point ,
                    ]);
                    $technician = Technician::where('user_id',$user_data->id )->first(); 
            
                        $poitValue = array(
                            'total_point' =>  $technician->total_point + $point  ,
                            'current_point' => $technician->current_point + $point , 
                        );
                        if( $technician){
                            Technician::where('user_id', $user_data->id)->update($poitValue); 
                        } 
                    $codeVerifyLog = new CodeVerifyLog;
                    $codeVerifyLog->product_id = $ssgCodeDetail->product_id ?? null;
                    $codeVerifyLog->mobile_no = $user_data->phone_number ? $user_data->phone_number : $user_data->email;
                    $codeVerifyLog->code = $request->code ?? null;
                    $codeVerifyLog->requested_ip = $request->ip() ?? null;
                    $codeVerifyLog->status = $status ?? null;
                    $codeVerifyLog->save();
                    $this->campaignsCheck($ssgCodeDetail);
                    return response()->json(['status' => 1 ,'message' => 'Verified! This is an original product from Super Star Group (SSG). Thank you for choosing us.'], 200); 
                    //return $this->sendResponse( 1 , 'Verified! This is an original product from Super Star Group (SSG). Thank you for choosing us.');
                } else {
                    $status = 'failed';
                    $codeVerifyLog = new CodeVerifyLog;
                    $codeVerifyLog->product_id = $ssgCodeDetail->product_id ?? null;
                    $codeVerifyLog->mobile_no = $user_data->phone_number ? $user_data->phone_number : $user_data->email ;
                    $codeVerifyLog->code = $request->code ?? null;
                    $codeVerifyLog->requested_ip = $request->ip() ?? null;
                    $codeVerifyLog->status = $status ?? null;
                    $codeVerifyLog->save();
                    return response()->json(['status' => 0 ,'message' => 'This code is invalid. Please enter the right code or contact with seller.'], 200); 
                    //return $this->sendResponse( 0 , 'This code is invalid. Please enter the right code or contact with seller.');
                } 
            }else{
                return response()->json(['status' => 0 ,'message' => 'This code is invalid. Please enter the right code or contact with seller.'], 200); 
            }
        }else{
            return response()->json(['status' => 0 ,'message' => 'This code is invalid. Please enter the right code or contact with seller.'], 200); 
        }
    }
    public function campaignsCheck( $ssgCodeDetail){
        $user_data = Auth::user();
        $technician = Technician::where('user_id',$user_data->id )->first(); 
        $campaign = Campaign::where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->get();
        if($campaign){
            foreach ($campaign as $key => $value) { 
                $campaignDetails =  CampaignDetails::where('user_id',$user_data->id)->where('campaign_id', $value->id )->first(); 
                if($campaignDetails){
                    $number_of_scan = $campaignDetails->number_of_scan + 1 ;
                    if($number_of_scan < $value->number_of_scan ){
                        $camp = array(
                            'number_of_scan' => $number_of_scan
                         );
                         CampaignDetails::where('user_id', $technician->user_id)->where('campaign_id', $value->id )->update($camp); 
                    } 
                    if($number_of_scan == $value->number_of_scan ){ 
                        $userPoint = UserPoint::create([
                            'ssg_code_details' =>$ssgCodeDetail->id ,
                            'product_id' => $ssgCodeDetail->product_id ,
                            'note' =>  $value->title ,
                            'point_type' => 2 , // Campaign
                            'user_id' => $technician->user_id  ,
                            'point' =>  $value->point ,
                        ]); 
                        $poitValue = array(
                            'total_point' =>  $technician->total_point + $value->point  ,
                            'current_point' => $technician->current_point + $value->point , 
                        );
                        if( $technician){
                            Technician::where('user_id', $technician->user_id)->update($poitValue); 
                        } 
                    } 
                }else{
                    CampaignDetails::create(['user_id' => $user_data->id, 'campaign_id' =>$value->id , 'number_of_scan'=> 1]);
                }
                 
                
            }
        }

        //$campaign = Campaign::

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
    public function getPoint($ssgCodeDetail){
    //    $product =  Product::find($ssgCodeDetail->product_id);
    //    if( $product){
    //        return  $product->point_slab ? $product->point_slab : 0 ;
    //    }else{
    //        return 0 ; 
    //    }
    $settings = ChannelSettings::select('slab_value','products.point_slab')
      ->where('products.id',$ssgCodeDetail->product_id)
      ->join('products','products.channel_id', 'channel_settings.channel_id')
      ->first();
      if($settings){
        return $settings->slab_value *  $settings->point_slab ;
      }else{
          return 0 ; 
      }
       
    }
    /**
     * Display the specified UserPoint.
     * GET|HEAD /userPoints/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UserPoint $userPoint */
        $userPoint = $this->userPointRepository->find($id);

        if (empty($userPoint)) {
            return $this->sendError('User Point not found');
        }

        return $this->sendResponse(new UserPointResource($userPoint), 'User Point retrieved successfully');
    }

    /**
     * Update the specified UserPoint in storage.
     * PUT/PATCH /userPoints/{id}
     *
     * @param int $id
     * @param UpdateUserPointAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserPointAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserPoint $userPoint */
        $userPoint = $this->userPointRepository->find($id);

        if (empty($userPoint)) {
            return $this->sendError('User Point not found');
        }

        $userPoint = $this->userPointRepository->update($input, $id);

        return $this->sendResponse(new UserPointResource($userPoint), 'UserPoint updated successfully');
    }

    /**
     * Remove the specified UserPoint from storage.
     * DELETE /userPoints/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UserPoint $userPoint */
        $userPoint = $this->userPointRepository->find($id);

        if (empty($userPoint)) {
            return $this->sendError('User Point not found');
        }

        $userPoint->delete();

        return $this->sendSuccess('User Point deleted successfully');
    }
}
