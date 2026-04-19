<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTechnicianAPIRequest;
use App\Http\Requests\API\UpdateTechnicianAPIRequest;
use App\Models\Technician;
use App\Models\Campaign;
use App\Repositories\TechnicianRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\TechnicianResource;
use App\Models\User;
use Response; 
use Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class TechnicianController
 * @package App\Http\Controllers\API
 */

class TechnicianAPIController extends AppBaseController
{
    /** @var  TechnicianRepository */
    private $technicianRepository;

    public function __construct(TechnicianRepository $technicianRepo)
    {
        $this->technicianRepository = $technicianRepo;
    }

    /**
     * Display a listing of the Technician.
     * GET|HEAD /technicians
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $technicians = $this->technicianRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );
        return response()->json(TechnicianResource::collection($technicians), 200);
        // return $this->sendResponse(TechnicianResource::collection($technicians), 'Technicians retrieved successfully');
    }



    /**
     * Store a newly created Technician in storage.
     * POST /technicians
     *
     * @param CreateTechnicianAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTechnicianAPIRequest $request)
    {
        $input = $request->all();

        $technician = $this->technicianRepository->create($input);

        return $this->sendResponse(new TechnicianResource($technician), 'Technician saved successfully');
    }

    /**
     * Display the specified Technician.
     * GET|HEAD /technicians/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Technician $technician */
        $user_data = Auth::user();
        $technician = Technician::where('user_id', $user_data->id)->first();

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
    }

    public function details($user_id)
    {
        $technician = Technician::select('technicians.*','users.name', 'users.email', 'users.phone_verification_status')
        ->join('users', 'users.id', 'technicians.user_id')
        ->where('user_id', $user_id)->first();
            if (empty($technician)) {
                return $this->sendError('Technician not found');
            }

        return $this->sendResponse(  $technician, 'Technician retrieved successfully');

    }
    public function showInfo()
    {
        /** @var Technician $technician */
        $user_data = Auth::user();
        $technician = Technician::where('user_id', $user_data->id)->first();

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        $info = new TechnicianResource($technician);
        return response()->json($info, 200);

        //return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
    }
    public function getTechnician(Request $request)
    {
        $type =  $request->type;
        //dd($type);
        if ($type) {
            $query = Technician::select(
                "technicians.id",
                "technicians.user_id",
                "technicians.fo_code",
                "technicians.fo_name",
                "technicians.fo_verify",
                "technicians.tsm_code",
                "technicians.tsm_name",
                "technicians.tsm_verify",
                "technicians.point_code",
                "technicians.point_name",
                "technicians.point_verify",
                "technicians.created_at",
                "technicians.updated_at",
                "users.email",
                "users.name",
                "users.phone_verification_status"
            );
            //->where('user_id', $user_data->id)
            // $query->with('user_info');
            $query->join('users', 'users.id', 'technicians.user_id');
            if ($request->fo_code) {
                $query->where('technicians.fo_code', $request->fo_code);
            }
            if ($request->tsm_code) {
                $query->where('technicians.tsm_code', $request->tsm_code);
            }
            if ($request->point_code) {
                $query->where('technicians.point_code', $request->point_code);
            }
            $query->limit($request->limit ? $request->limit : 50);
            $technician =   $query->get();
            return $this->sendResponse($technician, 'Technician retrieved successfully');
        } else {
            return $this->sendResponse(0, 'Data Not Found');
        }
    }
    public function technicianInfo(Request $request)
    {
        $phone_number =  $request->phone_number;
        //dd($phone_number);
        if ($phone_number) {
            $query = User::where('email', $phone_number)->with(['technician'])->first(); 
            return $this->sendResponse($query, 'Technician retrieved successfully');
        } else {
            return $this->sendResponse(0, 'Data Not Found');
        }
    }
    public function technician_status_update(Request $request)
    {
        $type =  $request->type;
        $query = Technician::where('id', $request->id);
        if ($type == 'point') {
            $query->update(['point_verify' => $request->status]);
        } else if ($type == 'fo') {
            $query->update(['fo_verify' => $request->status]);
        } else if ($type == 'tsm') {
            $query->update(['tsm_verify' => $request->status]);
        }

        return $this->sendResponse(1, 'Successfully updated');
    }
    public function infocheck()
    {
        /** @var Technician $technician */
        $user_data = Auth::user();
        $technician = Technician::select('update_status')->where('user_id', $user_data->id)->first();

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        //$info = new TechnicianResource($technician) ;
        return response()->json($technician, 200);

        //return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
    }
    public function campaigns()
    {
        $info = Campaign::all();
        return response()->json($info, 200);

        //return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
    }

    public function technician_update(Request $request)
    {
        $user_data = Auth::user();
        $input = $request->all();
        //dd( $input);

        // if (isset($request->password)){
        //     $input['password'] = Hash::make($input['password']);
        // }


        $user = User::find($user_data->id);


        // if ($request->hasFile('photo')) {
        //     $newFileName = Str::random(64) . '.' . $request->file('photo')->getClientOriginalExtension();
        //     $path = $request->file('photo')->storeAs('public/profile', $newFileName, 'local');
        //     $user->profile_image = $newFileName ;
        // }

        $user->update($input);
        // if($request->nid_number && $request->nid_number !=''){
        //    $technician =  Technician::where('user_id',$user_data->id)->first();

        //    if($technician->nid_number != $request->nid_number){
        //      return $this->sendResponse(0, 'You can\'t change your NID Number');
        //    }

        // }
        $technician =  Technician::where('user_id', $user_data->id)->first();
        if ($technician->nid_number != '') {
            if ($technician->nid_number != $request->nid_number) {
                return $this->sendResponse(0, 'You can\'t change your NID Number');
            }
        } else {
            if ($request->nid_number) {
                if (Technician::where('nid_number', $request->nid_number)->exists()) {
                    return $this->sendResponse(0, 'This NID Number already exists');
                }
            }
        }


        $technician  = Technician::where('user_id', $user_data->id)->first();
        $technician->update($input);
        return $this->sendResponse(1, 'Technician retrieved successfully');

        //return redirect()->route('admin.users.technician_user' ,'status=1')->with('success', ['Technician Information updated']); 

    }

    /**
     * Update the specified Technician in storage.
     * PUT/PATCH /technicians/{id}
     *
     * @param int $id
     * @param UpdateTechnicianAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTechnicianAPIRequest $request)
    {
        $input = $request->all();

        /** @var Technician $technician */
        $technician = $this->technicianRepository->find($id);

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        $technician = $this->technicianRepository->update($input, $id);

        return $this->sendResponse(new TechnicianResource($technician), 'Technician updated successfully');
    }

    /**
     * Remove the specified Technician from storage.
     * DELETE /technicians/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Technician $technician */
        $technician = $this->technicianRepository->find($id);

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        $technician->delete();

        return $this->sendSuccess('Technician deleted successfully');
    }
// public function checkTechnicianPoint()
// {
//     $technicians = DB::table('technicians as t')
//         ->select(
//             't.id', // include primary key for update
//             't.user_id',
//             't.total_point',
//             't.current_point',
//             't.pending_point',
//             DB::raw("(SELECT COALESCE(SUM(urr.point), 0)
//                       FROM user_redeem_requests urr
//                       WHERE urr.user_id = t.user_id
//                         AND urr.status IN (0, 1, 2)) AS total_redeem_points")
//         )
//         ->where('t.total_point', '>', 0)
//         ->where('t.pending_point', '>', 0)
//         ->get();

//     foreach ($technicians as $technician) {
//         // Debug output
//         print_r($technician);
//         echo "Total Redeem Points: " . $technician->total_redeem_points . "\n";

//         // Update only if total_redeem_points is 0
//         if ($technician->total_redeem_points == 0 && $technician->pending_point > 0) {
//             DB::table('technicians')
//                 ->where('id', $technician->id)
//                 ->update([
//                     'current_point' => DB::raw('current_point + pending_point'),
//                     'pending_point' => 0
//                 ]);
//         }elseif($technician->pending_point > 0 && $technician->total_redeem_points > 0 ){

//             if($technician->total_redeem_points == ( $technician->total_point - $technician->current_point)){
                
//                  $p_point = DB::table('user_redeem_requests')
//                     ->where('user_id', $technician->user_id)
//                     ->whereIn('status', [0, 2])
//                     ->sum('point');

//                 // Update the technician's pending_point
//                 DB::table('technicians')
//                     ->where('id', $technician->id)
//                     ->update([ 
//                         'pending_point' => $p_point
//                     ]);

                
//             }else if(($technician->current_point + $technician->pending_point) == $technician->total_point ){
//                 if(($technician->current_point + $technician->pending_point) != $technician->total_redeem_points){
//                     if($technician->pending_point > $technician->total_redeem_points ){
//                           $p_point = DB::table('user_redeem_requests')
//                             ->where('user_id', $technician->user_id)
//                             ->whereIn('status', [0, 2])
//                             ->sum('point');


//                         DB::table('technicians')
//                         ->where('id', $technician->id)
//                         ->update([
//                             'current_point' => DB::raw("current_point + pending_point - $p_point"),
//                             'pending_point' => $p_point
//                         ]);
//                     }else{

//                          $p_point = DB::table('user_redeem_requests')
//                             ->where('user_id', $technician->user_id)
//                             ->whereIn('status', [0, 2])
//                             ->sum('point');

//                                DB::table('technicians')
//                                 ->where('id', $technician->id)
//                                 ->update([
//                                     'current_point' => DB::raw("total_point - $technician->total_redeem_points  "),
//                                     'pending_point' => $p_point
//                                 ]);



//                     }
//                 }
//             }else{
                

//                          $p_point = DB::table('user_redeem_requests')
//                             ->where('user_id', $technician->user_id)
//                             ->whereIn('status', [0, 2])
//                             ->sum('point');

//                                DB::table('technicians')
//                                 ->where('id', $technician->id)
//                                 ->update([
//                                     'current_point' => DB::raw("total_point - $technician->total_redeem_points  "),
//                                     'pending_point' => $p_point
//                                 ]);

//             }

//         } 
//     }

//     return $technicians;
// }
public function checkTechnicianPoint()
{
    $technicians = DB::table('technicians')
        ->select(
            'id',
            'user_id',
            'total_point',
            'current_point',
            'pending_point',
            DB::raw("(SELECT COALESCE(SUM(urr.point), 0)
                      FROM user_redeem_requests urr
                      WHERE urr.user_id = technicians.user_id
                        AND urr.status IN (0, 1, 2)) AS total_redeem_points")
        )
        ->where('total_point', '>', 0)
        ->where('pending_point', '>', 0)
        ->get();

    foreach ($technicians as $technician) {
        $request_point = $technician->total_point - $technician->current_point;

        // Sum of pending redeem requests (status 0 or 2) for this user
        $p_point = DB::table('user_redeem_requests')
            ->where('user_id', $technician->user_id)
            ->whereIn('status', [0, 2])
            ->sum('point');

        // Case 1: No redeemed points yet
        if ($technician->total_redeem_points == 0 && $technician->pending_point > 0) {
            DB::table('technicians')
                ->where('id', $technician->id)
                ->update([
                    'current_point' => DB::raw('total_point'),
                    'pending_point' => 0
                ]);
        }
        // Case 2: All requested points have been redeemed
        elseif ($technician->total_redeem_points == $request_point) {
            DB::table('technicians')
                ->where('id', $technician->id)
                ->update([
                    'current_point' => DB::raw("total_point - {$technician->total_redeem_points}"),
                    'pending_point' => $p_point
                ]);
        } 
        // Case 3: Current + pending equals total_point, adjust based on redeem points
        elseif (($technician->current_point + $technician->pending_point) == $technician->total_point) {

            print_r($technician->id) ;
              echo '<br>'; // use <br> for HTML line break, not <per>

            
            // $new_current = ($technician->pending_point > $technician->total_redeem_points)
            //     ? $technician->current_point + $technician->pending_point - $technician->total_redeem_points
            //     : $technician->total_point - $technician->total_redeem_points;

            DB::table('technicians')
                ->where('id', $technician->id)
                ->update([
                    'current_point' => DB::raw("total_point - {$technician->total_redeem_points}"),
                    'pending_point' => $p_point
                ]);
        } 
        // Case 4: Default adjustment for pending points
         elseif(($technician->total_point - $technician->current_point ) == $technician->total_redeem_points){
             DB::table('technicians')
                ->where('id', $technician->id)
                ->update([
                    'current_point' => DB::raw("total_point - {$technician->total_redeem_points}"),
                    'pending_point' => $p_point
                ]);
        }
        else {
            DB::table('technicians')
                ->where('id', $technician->id)
                ->update([
                    'current_point' => DB::raw("total_point - {$technician->total_redeem_points}"),
                    'pending_point' => $p_point
                ]);
        }
    }

  //  return $technicians;
}




}
