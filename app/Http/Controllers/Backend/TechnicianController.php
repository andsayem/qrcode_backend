<?php
namespace App\Http\Controllers\Backend;
use App\Http\Requests\API\CreateTechnicianAPIRequest;
use App\Http\Requests\API\UpdateTechnicianAPIRequest;
use App\Models\Technician;
use App\Repositories\TechnicianRepository;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\TechnicianResource;
use Response;
use Auth;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\URL;


/**
 * Class TechnicianController
 * @package App\Http\Controllers\API
 */

class TechnicianController extends AppBaseController
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

        return $this->sendResponse(TechnicianResource::collection($technicians), 'Technicians retrieved successfully');
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
        $user = User::find($id);
        $data['user'] = $user  ;
        $technician = Technician::where('user_id',$id)->first();
        $data['technician'] = $technician  ;

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }
        return view('backend.users.technician')->with($data);
       // return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
    }

    public function edit($id)
    { 
       
        $technician  = Technician::where('user_id',$id)->first();  
        $data['technician'] =  $technician ;  
        $data['userinfo'] = User::select('id', 'name', 'email', 'profile_image', 'phone_number', 'remember_token', 'status')->find($id);
        $data['roles'] = Role::pluck('name', 'name')->all();
        $data['userRole'] = $data['userinfo']->roles->pluck('name', 'name')->all(); 
        return view('backend.users.technician_edit')->with($data);
    }
    public function showInfo()
    {
        /** @var Technician $technician */
        $user_data = Auth::user(); 
        $technician = Technician::where('user_id',$user_data->id)->first();

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        $info = new TechnicianResource($technician) ;
        return response()->json( $info , 200);

        //return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
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
    public function update(Request $request, $id)
    {
        //$input = $request->all(); 
        $input['name'] = $request->name ;
        $input['email'] = $request->email ;
        $input['phone_number'] = $request->phone_number ;
        $input['father_name'] = $request->father_name ;
        $input['permanent_address'] = $request->permanent_address ;
        $input['current_address'] = $request->current_address ;
        $input['status'] = $request->status ;
        $input['birthday'] = $request->birthday ;
        $input['fo_code'] = $request->fo_code ;
        $input['blood_group'] = $request->blood_group;
        $input['nid_number'] = $request->nid_number;
        $input['occupation'] = $request->occupation;
        $input['experience'] = $request->experience;
        $input['education'] = $request->education;
        $input['dealer_name'] = $request->dealer_name;
        $input['dealer_code'] = $request->dealer_code;

        $response = $this->getFoInfo($request, $request->fo_code); 
        $foInfo = isset($response['data']) ? $response['data']['fo'] : NULL;
        $tsmInfo = isset($response['data']) ? $response['data']['tsm'] : NULL;
        $pointInfo = isset($response['data']) ? $response['data']['point'] : NULL;

        if($foInfo){ 
            if(isset($foInfo['email'])){ 
                $input['fo_code'] = $foInfo['email'];
            }
            if(isset($foInfo['display_name'])){ 
                $input['fo_name'] = $foInfo['display_name'];
            } 
        }
        if($tsmInfo){  
            if(isset($tsmInfo['email'])){ 
                $input['tsm_code'] = $tsmInfo['email'];
            }
            if(isset($tsmInfo['display_name'])){ 
                $input['tsm_name'] = $tsmInfo['display_name'];
            }  
        }
        if($pointInfo){  
            if(isset($pointInfo['sap_code'])){ 
                $input['point_code'] = $pointInfo['sap_code'];
            }
            if(isset($pointInfo['point_name'])){ 
                $input['point_name'] = $pointInfo['point_name'];
            }  
        } 

        if (isset($request->password)){
            $input['password'] = Hash::make($request->password);
        } 
        $user = User::find($request->user_id); 
        if ($request->hasFile('photo')) {
            $newFileName = Str::random(64) . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('public/profile', $newFileName, 'local');
            $user->profile_image = $newFileName ;
        }
         
        $user->update($input);

        $technician  = Technician::find($id);  
        $technician->update($input); 
        return redirect()->route('admin.users.technician_user' ,'status=1')->with('success', ['Technician Information updated']); 
 
    }

    public function activeTechnicians(Request $request, $id)
    {           
        $input['status'] = 1;   
        //$technician  = Technician::find($id);    
        $user = User::find($id);        
        $user->update($input);
        //$technician->update($input); 
        //dd($user );
        return redirect()->back()->with('success', ['Technician Information updated']);  
        //return redirect()->route('admin.users.technician_user' ,'status=0')->with('success', ['Technician Information updated']); 
 
    }
    public function bulkActiveTechnicians(Request $request)
    {           
        if ($request->approval_value) {
            // Initialize arrays to store conditions and data
            $conditionsArray = [];
            $updateDataArray = ['status' => 1];
        
            foreach ($request->approval_value as $key => $value) {
                // Add conditions for each iteration
                $conditionsArray[] = ['id' => $value];
            }
            User::whereIn('id', $conditionsArray)->update($updateDataArray);
        } 
        return redirect()->back()->with('success', ['Technician Information updated']);   
    }
    public function phoneVerification(Request $request, $id)
    {           
        $input['phone_verification_status'] = 1;   
        $technician  = Technician::find($id);     
        $user = User::find($id);        
        $user->update($input);
        //$technician->update($input); 
        //dd($user );
        return redirect()->back()->with('success', ['Technician Information updated']);    
        //return redirect()->route('admin.users.technician_user' ,'status=0')->with('success', ['Technician Information updated']); 
 
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
    
    public function getFoInfo(Request $request, $foid) {  
        $getServerUrl = $request->server()['HTTP_HOST'];
        $apiUrl = 'https://ssforcenewdev.ssgbd.com/api/fo-check';
        $currentUrl = URL::to('');
        
        if($currentUrl == 'https://qrcdev.ssgbd.com' || $currentUrl == 'http://127.0.0.1:8001' || $currentUrl == 'http://127.0.0.1:8000'){
            $dynamicHost = "https://ssforcenewdev.ssgbd.com";
            $apiUrl = 'https://ssforcenewdev.ssgbd.com/api/fo-check';
        }else if($getServerUrl =='qrcdev.ssgbd.com'){
            $apiUrl = 'https://ssforcenewdev.ssgbd.com/api/fo-check'; 
        }else if($getServerUrl =='qrcuat.ssgbd.com'){
            $apiUrl = 'https://ssforcenewuat.ssgbd.com/api/fo-check'; 
        }else {
            $apiUrl = 'https://ssforce.ssgbd.com/api/fo-check';
        }  
        $response = Http::post($apiUrl, [
            'foid' => $foid, 
        ]); 
        if ($response->successful()) {
            return $response->json(); 
        } else { 
            return $response->status(); 
        } 
    }

}
