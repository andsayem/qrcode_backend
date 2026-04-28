<?php

namespace App\Http\Controllers\Backend;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Requests\UserRequest;
use App\Repositories\CommonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use App\Models\Technician;
use App\Models\Channel;
use App\Traits\FileHandlerTrait;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    use FileHandlerTrait;

    private $view_path = 'backend.users.';
    private $route_path = 'users.';
    private $commonRepository;
    private $user;

    public function __construct(Request $request)
    {
        $this->user = new User;
        // $this->commonRepository = $commonRepository;
    }


    public function index(Request $request)
    {
        if (!auth()->user()->can('user-list')) {
            abort(403);
        }

        $data['request'] = $request->all();
        $query = $this->getQuery($request);
        $data['tableData'] = $query->paginate(20);

        return view($this->view_path . 'index')->with($data);
    }
    public function technicianUser(Request $request)
    {
        $get_country = request()->filled('country') ? request('country') : '';
        $get_division = request()->filled('division') ? request('division') : '';
        $get_district = request()->filled('district') ? request('district') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : '';
        $get_from_date = request()->filled('from_date') ? request('from_date') : '';
        $get_to_date = request()->filled('to_date') ? request('to_date') : '';  

        $role = 'Technician';
        $query = $this->user->with(['user_types:id,name'])->orderBy('id', 'desc');
        $query->where('status', $request->status)
        ->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        }); 

        $query->when($request->name, function($query) use ($request){
            return $query->where('users.name', 'LIKE', '%' . $request->name. '%')
                    ->orWhere('users.email', 'LIKE', '%' . $request->name. '%');
        }); 

        if (isset($request->from_date)) {
            $query->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if (isset($request->to_date)) {
            $query->whereDate('created_at', '<=', dateConvertFormtoDB($request->to_date));
        } 

        $query->whereHas('technician',function( $query ) use ($request){   
            $query->when($request->country, function($q) use ($request){
                return $q->where('technicians.country_id', $request->country);
            });
            $query->when($request->division, function($q) use ($request){
                return $q->where('technicians.division_id', $request->division);
            });
            $query->when($request->district, function($q) use ($request){
                return $q->where('technicians.district_id', $request->district);
            });
            $query->when($request->thana, function($q) use ($request){
                return $q->where('technicians.upazilla_id', $request->thana);
            });
            $query->when($request->area, function($q) use ($request){
                return $q->where('technicians.union_id', $request->area);
            }); 
        });

        
        $data['request'] = $request->all();
        // $query = $this->getQuery($request);

        $data['tableData'] = $query->paginate(20);

        $data['countries'] = self::getSsforceCountry(); 

        $request->country_id = $get_country;
        $request->division_id = $get_division;
        $request->district_id = $get_district;
        $request->thana_id = $get_thana;

        if($get_country){
            $data['divisions']  = self::getSsforceDivisions($request);; 
        }else{
            $data['divisions'] = array();
        }
        if($get_division){
            $data['district'] = self::getSsforceDistrict($request); 
        }else{
            $data['district'] = array();
        }
        if($get_district){
            $data['thanas']  = self::getSsforcethana($request); 
        }else{
            $data['thanas'] = array();
        }
        if( $get_thana){
            $data['areas'] =  self::getSsforcearea($request);  
        }else{
            $data['areas'] = array();
        }  
 
        return view($this->view_path . 'technician-user')->with($data);
    }

   public function technician_download(Request $request)
{
    $role = 'Technician';

   $query = $this->user->with(['user_types:id,name'])->orderBy('id', 'desc')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            'technicians.fo_name',
            'technicians.tsm_name',
            'technicians.point_name',
            'technicians.total_point',
            'technicians.current_point',
            'technicians.pending_point',
            'technicians.total_redeem_value',
            'geo_divisions.name as division_name'
        )  
        ->leftJoin('technicians', 'technicians.user_id', '=', 'users.id')
        ->leftJoin('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id');
        $query->where('users.status', $request->status)
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })
        ->orderBy('users.id', 'desc');

    // 🔎 Name / Email search
    if ($request->name) {
        $query->where(function ($q) use ($request) {
            $q->where('users.name', 'LIKE', '%' . $request->name . '%')
              ->orWhere('users.email', 'LIKE', '%' . $request->name . '%');
        });
    }

    // 📅 Date filters
    if ($request->from_date) {
        $query->whereDate('users.created_at', '>=', dateConvertFormtoDB($request->from_date));
    }
    if ($request->to_date) {
        $query->whereDate('users.created_at', '<=', dateConvertFormtoDB($request->to_date));
    }

    // 🌍 Technician location filters
    if ($request->country)  $query->where('technicians.country_id', $request->country);
    if ($request->division) $query->where('technicians.division_id', $request->division);
    if ($request->district) $query->where('technicians.district_id', $request->district);
    if ($request->thana) $query->where('technicians.upazilla_id', $request->thana);
    if ($request->area) $query->where('technicians.union_id', $request->area);

    $techStatus = $request->status == 1 ? 'paid' : 'pending';

    $data = $query->get();

    // Prepare array for Excel
    $redeems = $data->map(function ($item) use ($techStatus) {
        return [
            'ID' => $item->id,
            'Name' => $item->name,
            'Phone' => $item->email,
            'Division' => $item->division_name ?? '',
            'FO Name' => $item->fo_name ?? '',
            'TSM Name' => $item->tsm_name ?? '',
            'Point Name' => $item->point_name ?? '',
            'Total Point' => $item->total_point ?? '',
            //'Current Point' => $item->current_point ?? '', 
            'Reg Date' => $item->created_at 
                        ? $item->created_at->format('d-m-Y') 
                        : '',
            // 'Pending Point' => $item->pending_point ?? '',
            // 'Total Redeem Value (TK)' => $item->total_redeem_value ?? '',
            //'Status' => $techStatus,
        ];
    });

    return (new \Rap2hpoutre\FastExcel\FastExcel($redeems))
        ->download('users-' . $techStatus . '-' . time() . '.xlsx');
}
    public function technician_download_old(Request $request)
    {
        // $role = 'Technician';
        // $query = $this->user->with(['user_types:id,name'])->orderBy('id', 'desc');
        // $query->where('status', $request->status)
        //     ->whereHas('roles', function ($query) use ($role) {
        //         $query->where('name', $role);
        //     });
        
        // if (isset($request->name)) {
        //     $query->where(function ($query) use ($request) {
        //         $query->where('name', 'LIKE', '%' . $request->name . '%')
        //             ->orWhere('email', 'LIKE', '%' . $request->name . '%');
        //     });
        // }
    
        // if (isset($request->from_date)) {
        //     $query->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        // }
    
        // if (isset($request->to_date)) {
        //     $query->whereDate('created_at', '<=', dateConvertFormtoDB($request->to_date));
        // }
    
        // if (isset($request->district)) {
        //     $query->whereHas('technician', function ($query) use ($request) {
        //         $query->where('district_id', $request->district);
        //     });
        // }
    
        // if (isset($request->thana)) {
        //     $query->whereHas('technician', function ($query) use ($request) {
        //         $query->where('upazilla_id', $request->thana);
        //     });
        // }
    
        // if (isset($request->area)) {
        //     $query->whereHas('technician', function ($query) use ($request) {
        //         $query->where('union_id', $request->area);
        //     });
        // }
        $role = 'Technician'; 
        $query = $this->user->with(['user_types:id,name', 'technician'])->orderBy('id', 'desc');
        $query->where('status', $request->status);
        $data = $query->get();
       
    
        $redeems = [];
        foreach ($data as $key => $item) {
            $redeem = [];
            $redeem['ID'] = $item->id;
            $redeem['Name'] = $item->name;
            $redeem['Phone'] = $item->email;
            $redeem['FO Name'] = $item->technician ? $item->technician->fo_name : '' ;
            $redeem['TSM Name'] = $item->technician ? $item->technician->tsm_name : '' ;
            $redeem['Point Name'] = $item->technician ? $item->technician->point_name : '' ;
            $redeem['Total Point'] = $item->technician ? $item->technician->total_point : '' ;
            $redeem['Current Point'] = $item->technician ? $item->technician->current_point : '' ;
            $redeem['Pending Point'] = $item->technician ? $item->technician->pendin : '' ;
            $redeem['Total Redeem Value (TK)'] = $item->technician ? $item->technician->total_redeem_value : '' ;
            $redeem['status'] = $item->status;
            //Name 
            $redeems[] = $redeem;
        } 
        if($item->status==1){
            $techStatus = 'paid';
        }else{
            $techStatus = 'pending';
        }
        $list = collect($redeems);
    
        return (new FastExcel($list))->download('users-'.$techStatus . time() . '.xlsx');
    }
    
    function getSsforce()
    {
        $url = 'https://ssforce.ssgbd.com/api/district'; 
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
    
        return $response['data'] ?? [];
    }
    function getSsforceCountry()
    { 
        $url = self::dynamicUrl().'country'; 
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
    
        return $response['data'] ?? [];
    }
    function getSsforceDivisions(Request $request)
    {
        // $url = 'https://ssforce.ssgbd.com/api/district';
        $country_id = $request->country_id ? 'country_id='.$request->country_id : '';
        
        $url = self::dynamicUrl().'divisions?'.$country_id;
         
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
    
        return $response['data'] ?? [];
    }
    function getSsforceDistrict(Request $request)
    {
        // $url = 'https://ssforce.ssgbd.com/api/district';
        $division_id = $request->division_id ? 'division_id='.$request->division_id : '';
        
        $url = self::dynamicUrl().'district?'.$division_id;
         
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
    
        return $response['data'] ?? [];
    }


    function getSsforcethana(Request $request)
    {
        // $url = 'https://ssforce.ssgbd.com/api/thana?district_id='.$request->district_id;
        $url = self::dynamicUrl().'thana?district_id='.$request->district_id;
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
    
        return $response['data'] ?? [];
    }

    //getChannels
    function getChannels(Request $request){ 
       return Channel::all();

    }

    //getSsforcearea

    function getSsforcearea(Request $request)
    {
        // $url = 'https://ssforce.ssgbd.com/api/area?district_id='.$request->district_id.'&thana_id='.$request->thana_id;
        $url = self::dynamicUrl().'area?thana_id='.$request->thana_id;
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
    
        return $response['data'] ?? [];
    }

    public function dynamicUrl(){
        $dynamicHost = "https://ssforce.ssgbd.com";
        
        $currentUrl = URL::to('');
    
        if($currentUrl == 'https://qrcdev.ssgbd.com' || $currentUrl == 'http://127.0.0.1:8001' || $currentUrl == 'http://127.0.0.1:8000'){
            // $dynamicHost = "https://ssforcenewdev.ssgbd.com";
            $dynamicHost = "http://127.0.0.1:8000";
        } else if($currentUrl == 'https://qrcdevuat.ssgbd.com'){
            $dynamicHost = "https://ssforceuat.ssgbd.com";
        } else {
            $dynamicHost = "https://ssforce.ssgbd.com";
        }  
        $dynamicHost = "https://ssforce.ssgbd.com";
    
        return $dynamicHost . '/api/';
    }

    public function districtUpdateDivisionWise(Request $request){
        $response = self::getSsforceDivisions($request);  
 
        foreach ($response as $value) { 
            $request->merge(['division_id' => $value['id']]); 
            $district_arr = self::getSsforceDistrict($request); 
            foreach ($district_arr as $key => $district) { 
               Technician::where('district_id', $district['id'])->update(['division_id'=> $value['id']]);  
            }
        }  
        return response()->json([], 200, ['message'=>'Technician update successfully']); 
    }
    

    public function create()
    {

        //$data['roleList'] = $this->commonRepository->userTypeList();
        $data['roleList'] = Role::pluck('name', 'name')->all();

        return view($this->view_path . 'create')->with($data);
    }
    public function new_technician()
    {

        //$data['roleList'] = $this->commonRepository->userTypeList();
        $data['roleList'] = Role::pluck('name', 'name')->all();

        return view($this->view_path . 'technician-create')->with($data);
    }


  



    public function store(UserRequest $request)
    {
        if (!auth()->user()->can('user-create')) {
            abort(403);
        }

        try {
            // $fileName = $this->processImage($request, 'photo', 'user/', 413, 531, [
            //     [
            //         'path' => 'thumbnails/',
            //         'width' => 50,
            //         'height' => 50,
            //         'crop_resize' => 'resize'
            //     ]
            // ], 'resize');

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            // $input['photo'] = $fileName;


            \DB::beginTransaction();

            $user = $this->user->create($input);
            $user->assignRole($request->input('roles'));


            \DB::commit();

            return redirect()->route('admin.users.index')->with('success', ['User created']);
        } catch (\Exception $e) {
            \DB::rollback();

            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            // alert()->error('Failed', trans('alert_message.error_message'));

            // return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
            return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }
    public function store_technician(UserRequest $request)
    {
        try {
            $input = $request->all();
            $input['roles'] = 13;
            if ($request->hasFile('photo')) {
                $newFileName = Str::random(64) . '.' . $request->file('photo')->getClientOriginalExtension();
                $path = $request->file('photo')->storeAs('public/profile', $newFileName, 'local');
                $input['profile_image'] = $newFileName;
            }
            $input['password'] = Hash::make($input['password']);
            \DB::beginTransaction();
            $user = $this->user->create($input);
            $user->assignRole($request->input('roles'));

            $input['user_id'] =  $user->id;

            Technician::create($input);

            \DB::commit();

            return redirect()->route('admin.users.index')->with('success', ['User created']);
        } catch (\Exception $e) {
            \DB::rollback();

            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            // alert()->error('Failed', trans('alert_message.error_message'));

            // return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
            return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }


    public function show($id)
    {
        $data['user'] = $this->user->findorFail($id);

        return view($this->view_path . 'show')->with($data);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('user-edit')) {
            abort(403);
        }

        $data['editModeData'] = $this->user->findOrFail($id);
        $data['roles'] = Role::pluck('name', 'name')->all();
        $data['userRole'] = $data['editModeData']->roles->pluck('name', 'name')->all();

        return view($this->view_path . 'edit')->with($data);
    }

    public function update(UserRequest $request, $id)
    {
        if (!auth()->user()->can('user-edit')) {
            abort(403);
        }

        try {
            // $fileName = $this->processImage($request, 'photo', 'user/', 413, 531, [
            //     [
            //         'path' => 'thumbnails/',
            //         'width' => 50,
            //         'height' => 50,
            //         'crop_resize' => 'resize'
            //     ]
            // ], 'resize');


            $input = $request->all();


            // if (!is_null($fileName)) {
            //     $input['photo'] = $fileName;
            // }
            if (isset($request->password)) {

                $input['password'] = Hash::make($input['password']);
            }
            $user = $this->user->findorFail($id);
            $user->update($input);
            DB::table('model_has_roles')->where('model_id', $id)->delete();

            $user->assignRole($request->input('roles'));

            return redirect()->route('admin.users.technician_user', 'status=0')->with('success', ['User updated']);
        } catch (\Exception $e) {
            \DB::rollback();

            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');

            return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function destroy($id)
    {
        User::find($id)->delete();

        return redirect()->route('admin.users.index')->with('success', ['User deleted successfully']);
    }

    private function getQuery($request)
    {
        $query = $this->user->with(['user_types:id,name'])->orderBy('id', 'desc');

        if (isset($request->name)) {
            $query->where('name', $request->name);
        }

        if (isset($request->from_date)) {
            $query->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if (isset($request->to_date)) {
            $query->whereDate('created_at', '<=', dateConvertFormtoDB($request->to_date));
        }

        return $query;
    }
}
