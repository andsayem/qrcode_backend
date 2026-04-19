<?php

namespace App\Http\Controllers\Backend;

use App\Models\UserRedeemRequest;
use App\Models\Technician;
use App\Models\User;
use App\Models\Channel;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon; 
use App\Exports\UserPointMonthlyExport;

use App\Http\Controllers\Backend\UserController;

class RedeemController extends Controller
{

    public function index(Request $request)
    {
        $query = UserRedeemRequest::where('status', 1)->with(['technician']);

        $get_country = request()->filled('country') ? request('country') : '';
        $get_division = request()->filled('division') ? request('division') : '';
        $get_district = request()->filled('district') ? request('district') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : '';
        $get_from_date = request()->filled('from_date') ? request('from_date') : '';
        $get_to_date = request()->filled('to_date') ? request('to_date') : '';


        $query->whereHas('technician', function ($query) use ($request) {
            $query->when($request->country, function ($q) use ($request) {
                return $q->where('country_id', $request->country);
            });
            $query->when($request->division, function ($q) use ($request) {
                return $q->where('division_id', $request->division);
            });
            $query->when($request->district, function ($q) use ($request) {
                return $q->where('district_id', $request->district);
            });
            $query->when($request->thana, function ($q) use ($request) {
                return $q->where('upazilla_id', $request->thana);
            });
            $query->when($request->area, function ($q) use ($request) {
                return $q->where('union_id', $request->area);
            });
        });


        if ($request->filled('from_date')) {
            $query->whereDate('paid_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('paid_at', '<=', dateConvertFormtoDB($request->to_date));
        }

        if (isset($request->db_pay_status)) {
            $query->where('db_pay_status', $request->db_pay_status);
        }



        if ($request->filled('sap_code')) {
            $query->where('sender_sap_code', $request->sap_code);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $query->orderBy('paid_at', 'DESC');
        $result = $query->get();
        // dd($result );
        $sap_code = UserRedeemRequest::select('sender_sap_code')
            ->where('status', 1)
            ->groupBy('sender_sap_code')
            ->get();


        $data = [
            'items' => $result,
            'sap_code' => $sap_code
        ];

        // Create an instance of RedeemController
        $userController = app()->make(UserController::class);

        $data['countries'] = $userController->getSsforceCountry();

        // Call the redeemMethod
        $request->district_id = $get_district;
        $request->thana_id = $get_thana;

        $request->country_id = $get_country;
        $request->division_id = $get_division;
        $request->district_id = $get_district;
        $request->thana_id = $get_thana;

        if ($get_country) {
            $data['divisions'] = $userController->getSsforceDivisions($request);;
        } else {
            $data['divisions'] = array();
        }
        if ($get_division) {
            $data['district'] = $userController->getSsforceDistrict($request);
        } else {
            $data['district'] = array();
        }

        if ($get_district) {
            $data['thanas'] = $userController->getSsforcethana($request);
        } else {
            $data['thanas'] = array();
        }
        if ($get_thana) {
            $data['areas'] = $userController->getSsforcearea($request);
        } else {
            $data['areas'] = array();
        }
        return view('backend.redeem.index', $data);
    }

    public function user_point_monthly(Request $request)
    {
        // ✅ default month range
        $startMonth = $request->get('start_month', now()->subMonths(4)->format('Y-m'));
        $endMonth   = $request->get('end_month', now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $endMonth)->startOfMonth();

        // ✅ generate months list
        $months = [];
        while ($start <= $end) {
            $months[] = $start->format('Y-m');
            $start->addMonth();
        }

        // ✅ base select
        $select = [
            'users.id',
            'users.name as user_name',
            'users.email',
            'geo_thana.thana',
            'geo_district.district',
            'geo_divisions.name as division_name',
        ];

        // ✅ dynamic month SQL
        foreach ($months as $month) {
            $alias = 'points_' . str_replace('-', '_', $month);
            $select[] = DB::raw("
            SUM(
                CASE 
                    WHEN DATE_FORMAT(user_points.created_at, '%Y-%m') = '{$month}'
                    THEN user_points.point 
                    ELSE 0 
                END
            ) as {$alias}
        ");
        }

        $query = DB::table('technicians')
            ->join('users', 'users.id', '=', 'technicians.user_id')
            ->leftJoin('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
            ->leftJoin('geo_district', 'geo_district.id', '=', 'technicians.district_id')
            ->leftJoin('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
            ->leftJoin('user_points', 'user_points.user_id', '=', 'technicians.user_id')
            ->select($select)
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'geo_thana.thana',
                'geo_district.district',
                'geo_divisions.name'
            )
            ->orderBy('users.id');

        $items = $query->paginate(20)->withQueryString();

        return view('backend.redeem.user_point_monthly', compact(
            'items',
            'months',
            'startMonth',
            'endMonth'
        ));
    }
    public function user_point_monthly_download(Request $request)
    {
            $startMonth = $request->get('start_month');
            $endMonth   = $request->get('end_month');

            return Excel::download(
                new UserPointMonthlyExport($startMonth, $endMonth),
                'user_point_monthly.xlsx'
            );
    }
    public function user_point(Request $request)
    {
        // -----------------------------
        // 1️⃣ Prepare Filters
        // -----------------------------
        $filters = [
            'country' => $request->country,
            'division' => $request->division,
            'district' => $request->district,
            'thana' => $request->thana,
            'area' => $request->area,
            'channel' => $request->channel,
            'user_id' => $request->user_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'group_by' => $request->group_by,
        ];

        // -----------------------------
        // 2️⃣ Main Query
        // -----------------------------
        $query = UserPoint::selectRaw("
        users.id,
        users.name,
        users.email,
        SUM(point) AS sub_point,
        products.product_name,
        technicians.country_id,
        geo_divisions.name AS division_name,
        technicians.division_id,
        geo_district.district AS district_name,
        technicians.district_id,
        geo_thana.thana AS thana_name,
        technicians.upazilla_id,
        technicians.user_id,
        technicians.union_id,
        geo_area.area AS area_name
    ")
            ->join('technicians', 'technicians.user_id', '=', 'user_points.user_id')
            ->join('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
            ->join('geo_district', 'geo_district.id', '=', 'technicians.district_id')
            ->join('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
            ->join('geo_area', 'geo_area.id', '=', 'technicians.union_id')
            ->join('users', 'users.id', '=', 'user_points.user_id')
            ->join('products', 'products.id', '=', 'user_points.product_id');

        // -----------------------------
        // 3️⃣ Apply Dynamic Filters
        // -----------------------------
        $filterMap = [
            'country' => 'technicians.country_id',
            'division' => 'technicians.division_id',
            'district' => 'technicians.district_id',
            'thana' => 'technicians.upazilla_id',
            'area' => 'technicians.union_id',
            'channel' => 'products.channel_id',
            'user_id' => 'technicians.user_id',
        ];

        foreach ($filterMap as $key => $column) {
            if (!empty($filters[$key])) {
                $query->where($column, $filters[$key]);
            }
        }

        // -----------------------------
        // 4️⃣ Date Range Filter
        // -----------------------------
        if (!empty($filters['from_date'])) {
            $query->whereDate('user_points.created_at', '>=', dateConvertFormtoDB($filters['from_date']));
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('user_points.created_at', '<=', dateConvertFormtoDB($filters['to_date']));
        }

        // -----------------------------
        // 5️⃣ Group By & Order
        // -----------------------------
        $query->groupBy($filters['group_by'] ?: 'user_points.user_id');
        $query->orderBy('sub_point', 'DESC');

        // -----------------------------
        // 6️⃣ Pagination
        // -----------------------------
        $data['items'] = $query->paginate(100);

        // -----------------------------
        // 7️⃣ Load Dropdowns
        // -----------------------------
        $userController = app(UserController::class);

        $data['countries'] = $userController->getSsforceCountry();
        $data['channels'] = Channel::all();

        // Ensure we pass the **Request object** correctly
        $request->country_id = $filters['country'];
        $request->division_id = $filters['division'];
        $request->district_id = $filters['district'];
        $request->thana_id = $filters['thana'];

        $data['divisions'] = $filters['country']
            ? $userController->getSsforceDivisions($request)
            : [];

        $data['district'] = $filters['division']
            ? $userController->getSsforceDistrict($request)
            : [];

        $data['thanas'] = $filters['district']
            ? $userController->getSsforcethana($request)
            : [];

        $data['areas'] = $filters['thana']
            ? $userController->getSsforcearea($request)
            : [];

        // -----------------------------
        // 8️⃣ Pass filters to view
        // -----------------------------
        $data['request'] = $filters;

        return view('backend.redeem.user_point', $data);
    }


    // public function user_point(Request $request)
    // {


    //     $get_country = request()->filled('country') ? request('country') : '';
    //     $get_division = request()->filled('division') ? request('division') : '';
    //     $get_district = request()->filled('district') ? request('district') : '';
    //     $get_thana = request()->filled('thana') ? request('thana') : '';

    //     $query = UserPoint::selectRaw('users.id,  users.name , users.email , 
    //      SUM(point) as sub_point ,
    //      products.product_name ,
    //      technicians.country_id,
    //      geo_divisions.name as division_name , 
    //      technicians.division_id ,
    //      geo_district.district as district_name , 
    //      technicians.district_id,
    //      geo_thana.thana as thana_name,
    //          technicians.upazilla_id ,
    //         technicians.user_id ,
    //       technicians.union_id ,
    //      geo_area.area as area_name')
    //         ->join('technicians', 'technicians.user_id', '=', 'user_points.user_id')
    //         ->join('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
    //         ->join('geo_district', 'geo_district.id', '=', 'technicians.district_id')
    //         ->join('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
    //         ->join('geo_area', 'geo_area.id', '=', 'technicians.union_id')
    //         ->join('users', 'users.id', '=', 'user_points.user_id')
    //         ->join('products', 'products.id', '=', 'user_points.product_id');

    //     $query->when($request->country, function ($q) use ($request) {
    //         return $q->where('technicians.country_id', $request->country);
    //     });
    //     $query->when($request->division, function ($q) use ($request) {
    //         return $q->where('technicians.division_id', $request->division);
    //     });
    //     $query->when($request->district, function ($q) use ($request) {
    //         return $q->where('technicians.district_id', $request->district);
    //     });
    //     $query->when($request->thana, function ($q) use ($request) {
    //         return $q->where('technicians.upazilla_id', $request->thana);
    //     });
    //     $query->when($request->area, function ($q) use ($request) {
    //         return $q->where('technicians.union_id', $request->area);
    //     });
    //     $query->when($request->channel, function ($q) use ($request) {
    //         return $q->where('products.channel_id', $request->channel);
    //     });
    //     $query->when($request->user_id, function ($q) use ($request) {
    //         return $q->where('technicians.user_id', $request->user_id);
    //     });

    //     if ($request->filled('from_date')) {
    //         $query->whereDate('user_points.created_at', '>=', dateConvertFormtoDB($request->from_date));
    //     }
    //     if ($request->filled('to_date')) {
    //         $query->whereDate('user_points.created_at', '<=', dateConvertFormtoDB($request->to_date));
    //     }
    //     if ($request->filled('user_id')) {
    //         $query->where('user_points.user_id', $request->user_id);
    //     }

    //     if ($request->group_by) {
    //         $query->groupBy($request->group_by);
    //     } else {
    //         $query->groupBy('user_points.user_id');
    //     }


    //     $query->orderBy('sub_point', 'DESC');

    //     $data = [
    //         'items' => $query->paginate(100)

    //     ];

    //     // Create an instance of RedeemController
    //     $userController = app()->make(UserController::class);

    //     $data['countries'] = $userController->getSsforceCountry();
    //     $data['channels'] =  Channel::all();
    //     // Call the redeemMethod
    //     $request->district_id = $get_district;
    //     $request->thana_id = $get_thana;

    //     $request->country_id = $get_country;
    //     $request->division_id = $get_division;
    //     $request->district_id = $get_district;
    //     $request->thana_id = $get_thana;

    //     if ($get_country) {
    //         $data['divisions']  = $userController->getSsforceDivisions($request);;
    //     } else {
    //         $data['divisions'] = array();
    //     }
    //     if ($get_division) {
    //         $data['district'] = $userController->getSsforceDistrict($request);
    //     } else {
    //         $data['district'] = array();
    //     }

    //     if ($get_district) {
    //         $data['thanas']  = $userController->getSsforcethana($request);
    //     } else {
    //         $data['thanas'] = array();
    //     }
    //     if ($get_thana) {
    //         $data['areas']  = $userController->getSsforcearea($request);
    //     } else {
    //         $data['areas'] = array();
    //     }
    //     $data['request'] = $request->all();
    //     return view('backend.redeem.user_point', $data);
    // }


    public function getPointSummary(Request $request)
    {
        $query = UserPoint::selectRaw('point')
            ->join('technicians', 'technicians.user_id', '=', 'user_points.user_id')
            //    ->join('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id') 
            //    ->join('geo_district', 'geo_district.id', '=', 'technicians.district_id')
            //    ->join('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
            //    ->join('geo_area', 'geo_area.id', '=', 'technicians.union_id')
            //   ->join('users', 'users.id', '=', 'user_points.user_id')
            ->join('products', 'products.id', '=', 'user_points.product_id');

        $query->when($request->country, function ($q) use ($request) {
            return $q->where('technicians.country_id', $request->country);
        });
        $query->when($request->division, function ($q) use ($request) {
            return $q->where('technicians.division_id', $request->division);
        });
        $query->when($request->district, function ($q) use ($request) {
            return $q->where('technicians.district_id', $request->district);
        });
        $query->when($request->thana, function ($q) use ($request) {
            return $q->where('technicians.upazilla_id', $request->thana);
        });
        $query->when($request->area, function ($q) use ($request) {
            return $q->where('technicians.union_id', $request->area);
        });
        $query->when($request->channel, function ($q) use ($request) {
            return $q->where('products.channel_id', $request->channel);
        });

        $query->when($request->user_id, function ($q) use ($request) {
            return $q->where('technicians.user_id', $request->user_id);
        });


        if ($request->filled('from_date')) {
            $query->whereDate('user_points.created_at', '>=', dateConvertFormtoDB($request->from_date));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('user_points.created_at', '<=', dateConvertFormtoDB($request->to_date));
        }
        if ($request->filled('user_id')) {
            $query->where('user_points.user_id', $request->user_id);
        }

        //    if($request->group_by){
        //        $query->groupBy( $request->group_by );

        //    }else{
        //        $query->groupBy('user_points.user_id');
        //    }



        $result = $query->get();

        $totalPoints = $result->sum('point'); // Calculate total points


        $response = [
            'totalPoints' => $totalPoints,
        ];

        return response()->json($response);
    }
    public function getRedeemSummary(Request $request)
    {
        $query = UserRedeemRequest::where('status', 1)->with(['technician']);

        $query->whereHas('technician', function ($query) use ($request) {
            $query->when($request->filled('country'), function ($q) use ($request) {
                return $q->where('country_id', $request->country);
            });
            $query->when($request->filled('division'), function ($q) use ($request) {
                return $q->where('division_id', $request->division);
            });
            $query->when($request->filled('district'), function ($q) use ($request) {
                return $q->where('district_id', $request->district);
            });
            $query->when($request->filled('thana'), function ($q) use ($request) {
                return $q->where('upazilla_id', $request->thana);
            });
            $query->when($request->filled('area'), function ($q) use ($request) {
                return $q->where('union_id', $request->area);
            });
        });

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', dateConvertFormtoDB($request->to_date));
        }

        if ($request->filled('sap_code')) {
            $query->where('sender_sap_code', $request->sap_code);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        //$result = $query->get();

        $totalPoints = $query->sum('point'); // Calculate total points
        $totalAmount = $query->sum('amount'); // Calculate total amount

        $response = [
            'totalPoints' => $totalPoints,
            'totalAmount' => $totalAmount,
        ];

        return response()->json($response);
    }



    public function pending_redeem(Request $request)
    {
        $query = UserRedeemRequest::with('user');

        $query->where('user_redeem_requests.point', '>=', 200);


        if ($request->filled('payment_gateway')) {
            $query->where('payment_gateway', $request->payment_gateway);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', dateConvertFormtoDB($request->to_date));
        }

        // if ($request->filled('status')) {
        //     $query->where('user_redeem_requests.status', $request->status);
        // }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('user_redeem_requests.status', $request->status);
        }
        //  $query->when($request->name, function ($query) use ($request) {
        //         return $query->whereHas('user', function ($q) use ($request) {
        //             $q->where('name', 'LIKE', '%' . $request->name . '%')
        //                 ->orWhere('email', 'LIKE', '%' . $request->name . '%');
        //         });
        //     });

        $query->when($request->name, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                // Search in related user
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'LIKE', '%' . $request->name . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->name . '%');
                });
                // Search in payment_gateway column of main table
                $q->orWhere('payment_gateway', 'LIKE', '%' . $request->name . '%');
            });
        });


        // ✅ Pagination (e.g. 15 items per page)
        $data['items'] = $query
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->appends($request->all()); // keep filters on page change

        return view('backend.redeem.pending', $data);
    }



    public function redeem_request_download(Request $request)
    {
        $query = UserRedeemRequest::with('user');

        if ($request->filled('payment_gateway')) {
            $query->where('payment_gateway', $request->payment_gateway);
        }
        $query->where('user_redeem_requests.point', '>=', 200);
        // ->where('user_redeem_requests.gateway_number', '!=', '')
        // ->whereRaw('LENGTH(user_redeem_requests.gateway_number) = 11')
        // ->whereRaw('user_redeem_requests.gateway_number REGEXP "^[0-9]+$"');

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', dateConvertFormtoDB($request->to_date));
        }
        // if ($request->filled('status')) {
        //     $query->where('user_redeem_requests.status', $request->status);

        // } else {
        //     $query->where('user_redeem_requests.status', 0);
        // }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('user_redeem_requests.status', $request->status);
        }

        $query->when($request->name, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                // Search in related user
                $q->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'LIKE', '%' . $request->name . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->name . '%');
                });
                // Search in payment_gateway column of main table
                $q->orWhere('payment_gateway', 'LIKE', '%' . $request->name . '%');
            });
        });



        $items = $query->get();

        // $items 


        $redeems = [];
        foreach ($items as $item) {

            switch ($item->payment_gateway) {
                case 1:
                    $gateway = 'bKash';
                    break;
                case 2:
                    $gateway = 'Nagad';
                    break;
                case 3:
                    $gateway = 'Rocket';
                    break;
                default:
                    $gateway = 'Unknown';
                    break;
            }

            $redeem = [
                'Request ID' => $item->id,
                'User ID' => $item->user_id,
                'User Name' => optional($item->user)->name,
                'Point Code' => Technician::where('user_id', $item->user_id)->value('point_code'),
                'Redeem Date' => optional($item->created_at)->format('Y-m-d'),
                'Gateway Type' => $gateway,
                'Gateway Number' => $item->gateway_number,
                'Request Point' => (float) $item->point,
                'Amount (BDT)' => (float) $item->point / 4,
                'Status' => $item->status == 1 ? 'PAID' : 'PENDING',
                'Remarks' => $item->note ?? '',
            ];

            $redeems[] = $redeem;
        }

        $list = collect($redeems);

        // ✅ Update status to 2 after preparing export
        if ($request->has('process_payment') && $request->process_payment == 1) {
            UserRedeemRequest::whereIn('id', $items->pluck('id'))
                ->where('status', 0)
                ->update(['status' => 2]);
        }

        return (new FastExcel($list))->download('redeem-pending-list-' . time() . '.xlsx');
    }

    public function pending_redeem_list(Request $request)
    {
        $data['items'] = UserRedeemRequest::where('status', 0)->with('user')->get();
        return view('backend.redeem.pending_list')->with($data);
    }
    public function pending_redeem_delete(Request $request, $id)
    {
        $item = UserRedeemRequest::find($id);
        $item->delete();
        return redirect(route('admin.redeem.pending_redeem_list'));
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

        return $response['data'];
    }
    public function approval_redeem(Request $request)
    {
        //print_r($request->all());
        $id = $request->id;
        $status = $request->status;
        if ($status == 1) {
            $redeeminfo = UserRedeemRequest::find($id);
            $technicianinfo = Technician::where('user_id', $redeeminfo->user_id)->first();
            $userinfo = User::find($redeeminfo->user_id);
            if ($technicianinfo->pending_point >= $redeeminfo->point) {
                // print_r('Yes');
                $poitValue = array(
                    'total_redeem_value' => $technicianinfo->total_redeem_value + $redeeminfo->amount,
                    'pending_point' => $technicianinfo->pending_point - $redeeminfo->point,
                );
                Technician::where('user_id', $userinfo->id)->update($poitValue);
                UserRedeemRequest::where('id', $id)->update(['status' => $status, 'details' => $request->details]);
                return redirect()->route('admin.redeem.index')->with('success', ['Successfully data update']);
            } else {
                return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
            }
        } else {
            return back()->withInput()->with('fail', ['Something went wrong. Please try again later.']);
        }
        // $data['items'] = UserRedeemRequest::where('status',0)->with('user')->get(); 
        // return view('backend.redeem.pending')->with($data);

    }


    public function redeem_paid_download(Request $request)
    {
        $query = UserRedeemRequest::where('status', 1)->with(['technician']);

        $query->whereHas('technician', function ($query) use ($request) {
            $query->when($request->filled('district'), function ($q) use ($request) {
                return $q->where('district_id', $request->district);
            });
            $query->when($request->filled('thana'), function ($q) use ($request) {
                return $q->where('upazilla_id', $request->thana);
            });
            $query->when($request->filled('area'), function ($q) use ($request) {
                return $q->where('union_id', $request->area);
            });
        });

        if ($request->filled('from_date')) {
            $query->whereDate('paid_at', '>=', dateConvertFormtoDB($request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('paid_at', '<=', dateConvertFormtoDB($request->to_date));
        }
        $query->whereNotNull('sender_sap_code')
            ->where('sender_sap_code', '!=', '');

        if ($request->filled('sap_code')) {
            $query->where('sender_sap_code', $request->sap_code);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $query->where('db_pay_status', 0);

        $result = $query->get();
        $redeems = [];
        foreach ($result as $key => $item) {
            $jsonDecode = json_decode($item->sender_info);
            $gateway = '';
            if ($item->payment_gateway == 1) {
                $gateway = 'bKash';
            } elseif ($item->payment_gateway == 2) {
                $gateway = 'Nagad';
            } elseif ($item->payment_gateway == 3) {
                $gateway = 'Rocket';
            }
            $redeem = [];
            $redeem['Request ID'] = $item->id;
            $redeem['User Name'] = User::find($item->user_id)->name;
            $redeem['Request Point'] = $item->point;
            $redeem['Amount (BDT)'] = $item->amount;
            // $redeem['Distributor Name']  = $jsonDecode ? $jsonDecode->display_name : ''; //$gateway ; //$item->payment_gateway == 1 ? 'bKash' :  $item->payment_gateway == 2 ? 'Nagad' : $item->payment_gateway == 3 ? 'Rocket' : '';
            //$redeem['Gateway Number']  = $item->gateway_number;
            $redeem['SAP Code'] = $item->sender_sap_code;
            $redeem['Technician Pay Date'] = $item->paid_at;
            $redeem['Technician Pay Status'] = $item->status == 1 ? 'PAID' : 'UNPAID';
            $redeem['DB Pay Status'] = $item->db_pay_status == 1 ? 'PAID' : 'UNPAID';
            $redeems[] = $redeem;
        };
        $list = collect($redeems);
        return (new FastExcel($list))->download('redeem-paid-list-' . time() . '.xlsx');
    }

    public function redeem_db_paid_list(Request $request)
    {
        ini_set('max_execution_time', 180000); // 30 mins
        ini_set('memory_limit', '100024M'); // 1024 MB

        if ($request->hasFile('csv_file')) {
            $extension = File::extension($request->csv_file->getClientOriginalName());
            if (!in_array($extension, ["xlsx", "xls", "csv"])) {

                return redirect()->back()->withErrors('The file must be a file of type: csv, xlsx, xls.');
            }
        } else {

            return redirect()->back()->withErrors('No file selected');
        }

        // try {
        if ($request->hasFile('csv_file')) {
            $newFileName = Str::random(64) . '.' . $request->file('csv_file')->getClientOriginalExtension();
            $path = $request->file('csv_file')->storeAs('code_upload_dir', $newFileName, 'local');

            $contents = [];

            (new FastExcel)->import(storage_path() . '/app/' . $path, function ($line) use (&$contents, &$monthYear) {

                $contents[] = [
                    'id' => $line['Request ID'],
                    'db_pay_status' => $line['DB Pay Status'],
                ];
            });

            $update_list = 0;
            //$failedList = [];
            if (count($contents) <= 100000) {

                foreach ($contents as $content) {
                    $status = $content['db_pay_status'] == 'PAID' ? 1 : 0;

                    UserRedeemRequest::where('id', $content['id'])
                        ->update(['db_pay_status' => $status]);
                    $update_list += 1;
                }
            } else {

                return redirect()->back()->with('fail', ['Max upload 100000 rows']);
            }
        }
        if (isset($newFileName) && file_exists(storage_path('/app/code_upload_dir/' . $newFileName))) {
            unlink(storage_path('/app/code_upload_dir/' . $newFileName));
        }

        return redirect()->back()->with('success', ['Successfully uploaded ' . $update_list . ' rows']);
    }
    public function redeem_paid_list(Request $request)
    {
        ini_set('max_execution_time', 180000);
        ini_set('memory_limit', '1024M');

        if (!$request->hasFile('csv_file')) {
            return redirect()->back()->withErrors('No file selected');
        }

        $extension = $request->file('csv_file')->getClientOriginalExtension();
        if (!in_array($extension, ["xlsx", "xls", "csv"])) {
            return redirect()->back()->withErrors('The file must be a file of type: csv, xlsx, xls.');
        }

        $newFileName = Str::random(64) . '.' . $extension;
        $path = $request->file('csv_file')->storeAs('code_upload_dir', $newFileName, 'local');

        // Counters
        $paidCount = 0;
        $failedCount = 0;
        $rollbackCount = 0;
        $notPendingCount = 0;   // NEW
        $failedRows = [];       // only for serious errors

        (new FastExcel)->import(storage_path('app/' . $path), function ($line) use (&$paidCount, &$failedCount, &$rollbackCount, &$notPendingCount, &$failedRows) {
            DB::transaction(function () use ($line, &$paidCount, &$failedCount, &$rollbackCount, &$notPendingCount, &$failedRows) {

                $redeem = UserRedeemRequest::find($line['Request ID']);
                if (!$redeem) {
                    $failedRows[] = "ID {$line['Request ID']} not found";
                    return;
                }

                $technician = Technician::where('user_id', $redeem->user_id)
                    ->lockForUpdate()
                    ->first();

                if (!$technician) {
                    $failedRows[] = "Technician missing for Request ID {$redeem->id}";
                    return;
                }

                $status = strtolower($line['Status']);
                $remarks = $line['Remarks'] ?? '';

                if (!in_array($status, ['paid', 'failed', 'rollback'])) {
                    $failedRows[] = "Invalid status for Request ID {$redeem->id}";
                    return;
                }

                // Instead of pushing repeated messages → just count
                if ($redeem->status != 2) {
                    $notPendingCount++;
                    return;
                }

                // ============= PAID =============
                if ($status == 'paid') {

                    $technician->pending_point -= $redeem->point;
                    $technician->save();

                    $redeem->update([
                        'status' => 1,
                        'sender_sap_code' => '',
                        'remarks' => $remarks,
                        'paid_at' => now(),
                    ]);

                    $paidCount++;
                }

                // ============= FAILED =============
                elseif ($status == 'failed') {

                    $technician->pending_point -= $redeem->point;
                    $technician->current_point += $redeem->point;
                    $technician->save();

                    $redeem->update([
                        'status' => 3,
                        'sender_sap_code' => '',
                        'remarks' => $remarks,
                        'paid_at' => null,
                    ]);

                    $failedCount++;
                }

                // ============= ROLLBACK =============
                // elseif ($status == 'rollback') {

                //     $technician->pending_point += $redeem->point;
                //     $technician->current_point -= $redeem->point;
                //     $technician->save();

                //     $redeem->update([
                //         'status' => 0,
                //         'sender_sap_code' => '',
                //         'remarks' => $remarks,
                //         'paid_at' => null,
                //     ]);

                //     $rollbackCount++;
                // }

            }, 5);
        });

        // Delete uploaded file
        if (file_exists(storage_path('app/code_upload_dir/' . $newFileName))) {
            unlink(storage_path('app/code_upload_dir/' . $newFileName));
        }

        // Final success message
        $message = "Paid: {$paidCount}, Failed: {$failedCount}.  ";

        if ($notPendingCount > 0) {
            $message .= "{$notPendingCount} rows not in pending status. ";
        }

        if (!empty($failedRows)) {
            $message .= "Other failed rows: " . implode(', ', $failedRows);
        }

        return redirect()->back()->with('success', [$message]);
    }

    // public function redeem_paid_list(Request $request)
    // {
    //     ini_set('max_execution_time', 180000);
    //     ini_set('memory_limit', '1024M');

    //     if (!$request->hasFile('csv_file')) {
    //         return redirect()->back()->withErrors('No file selected');
    //     }

    //     $extension = $request->file('csv_file')->getClientOriginalExtension();
    //     if (!in_array($extension, ["xlsx", "xls", "csv"])) {
    //         return redirect()->back()->withErrors('The file must be a file of type: csv, xlsx, xls.');
    //     }

    //     $newFileName = Str::random(64) . '.' . $extension;
    //     $path = $request->file('csv_file')->storeAs('code_upload_dir', $newFileName, 'local');

    //     // Counters
    //     $paidCount = 0;
    //     $failedCount = 0;
    //     $rollbackCount = 0;
    //     $failedRows = [];

    //     // Read file in chunks using FastExcel
    //     (new FastExcel)->import(storage_path('app/' . $path), function ($line) 
    //         use (&$paidCount, &$failedCount, &$rollbackCount, &$failedRows) 
    //     {
    //         DB::transaction(function () use ($line, &$paidCount, &$failedCount, &$rollbackCount, &$failedRows) {

    //             $redeem = UserRedeemRequest::find($line['Request ID']);
    //             if (!$redeem) {
    //                 $failedRows[] = "ID {$line['Request ID']} not found";
    //                 return;
    //             }

    //             $technician = Technician::where('user_id', $redeem->user_id)
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$technician) {
    //                 $failedRows[] = "Technician missing for Request ID {$redeem->id}";
    //                 return;
    //             }

    //             $status = strtolower($line['Status']);
    //             $remarks = $line['Remarks'] ?? '';

    //             if (!in_array($status, ['paid', 'failed', 'rollback'])) {
    //                 $failedRows[] = "Invalid status for Request ID {$redeem->id}";
    //                 return;
    //             }

    //             if ($redeem->status != 2) {
    //                 $failedRows[] = "Request ID {$redeem->id} not in pending status";
    //                 return;
    //             }

    //             // ============= PAID =============
    //             if ($status == 'paid') {

    //                 $technician->pending_point -= $redeem->point;
    //                 $technician->save();

    //                 $redeem->update([
    //                     'status' => 1,
    //                     'sender_sap_code' => '',
    //                     'remarks' => $remarks,
    //                     'paid_at' => now(),
    //                 ]);

    //                 $paidCount++;
    //             }

    //             // ============= FAILED =============
    //             elseif ($status == 'failed') {

    //                 $technician->pending_point -= $redeem->point;
    //                 $technician->current_point += $redeem->point;
    //                 $technician->save();

    //                 $redeem->update([
    //                     'status' => 3,
    //                     'sender_sap_code' => '',
    //                     'remarks' => $remarks,
    //                     'paid_at' => null,
    //                 ]);

    //                 $failedCount++;
    //             }

    //             // ============= ROLLBACK =============
    //             elseif ($status == 'rollback') {

    //                 $technician->pending_point += $redeem->point;
    //                 $technician->current_point -= $redeem->point;
    //                 $technician->save();

    //                 $redeem->update([
    //                     'status' => 0,
    //                     'sender_sap_code' => '',
    //                     'remarks' => $remarks,
    //                     'paid_at' => null,
    //                 ]);

    //                 $rollbackCount++;
    //             }

    //         }, 5); // Retry 5 times on deadlock
    //     });

    //     // Delete uploaded file
    //     if (file_exists(storage_path('app/code_upload_dir/' . $newFileName))) {
    //         unlink(storage_path('app/code_upload_dir/' . $newFileName));
    //     }

    //     // Final success message
    //     $message =
    //         "Paid: {$paidCount}, Failed: {$failedCount}, Rollback: {$rollbackCount}. ";

    //     if (!empty($failedRows)) {
    //         $message .= "Some rows failed: " . implode(', ', $failedRows);
    //     }

    //     return redirect()->back()->with('success', [$message]);
    // }

    //     public function redeem_paid_list(Request $request)
    // {
    //     ini_set('max_execution_time', 180000); // 30 mins
    //     ini_set('memory_limit', '1024M'); // 1GB

    //     if (!$request->hasFile('csv_file')) {
    //         return redirect()->back()->withErrors('No file selected');
    //     }

    //     $extension = $request->file('csv_file')->getClientOriginalExtension();
    //     if (!in_array($extension, ["xlsx", "xls", "csv"])) {
    //         return redirect()->back()->withErrors('The file must be a file of type: csv, xlsx, xls.');
    //     }

    //     $newFileName = Str::random(64) . '.' . $extension;
    //     $path = $request->file('csv_file')->storeAs('code_upload_dir', $newFileName, 'local');

    //     $update_list = 0;
    //     $failedRows = [];

    //     // Read file in chunks using FastExcel
    //     (new FastExcel)->import(storage_path('app/' . $path), function ($line) use (&$update_list, &$failedRows) {
    //         DB::transaction(function () use ($line, &$update_list, &$failedRows) {

    //             $redeem = UserRedeemRequest::find($line['Request ID']);
    //             if (!$redeem) {
    //                 $failedRows[] = $line['Request ID'] . ' not found';
    //                 return;
    //             }

    //             $technician = Technician::where('user_id', $redeem->user_id)->lockForUpdate()->first();

    //             if (!$technician) {
    //                 $failedRows[] = $line['Request ID'] . ' technician not found';
    //                 return;
    //             }

    //             $status = strtolower($line['Status']);
    //             $remarks = $line['Remarks'] ?? '';

    //             // Skip if status not applicable
    //             if ($redeem->status != 2) {
    //                 $failedRows[] = 'Request ID ' . $redeem->id . ' not in pending status';
    //                 return;
    //             }

    //             // Process failed status
    //             if ($status == 'failed') {
    //                 $technician->pending_point -= $redeem->point;
    //                 $technician->current_point += $redeem->point;
    //                 $technician->save();

    //                 $redeem->update([
    //                     'status' => 3,
    //                     'sender_sap_code' => '',
    //                     'remarks' => $remarks,
    //                     'paid_at' => null,
    //                 ]);

    //                 $update_list++;
    //             }

    //             // Process paid status
    //             elseif ($status == 'paid') {
    //                 $technician->pending_point -= $redeem->point;
    //                 $technician->save();

    //                 $redeem->update([
    //                     'status' => 1,
    //                     'sender_sap_code' => '',
    //                     'remarks' => $remarks,
    //                     'paid_at' => now(),
    //                 ]);

    //                 $update_list++;
    //             }

    //         }, 5); // Retry 5 times if deadlock occurs
    //     });

    //     // Delete uploaded file
    //     if (file_exists(storage_path('app/code_upload_dir/' . $newFileName))) {
    //         unlink(storage_path('app/code_upload_dir/' . $newFileName));
    //     }

    //     $message = 'Successfully uploaded ' . $update_list . ' rows.';
    //     if (!empty($failedRows)) {
    //         $message .= ' Some rows failed: ' . implode(', ', $failedRows);
    //     }

    //     return redirect()->back()->with('success', [$message]);
    // }



    public function vendorFilterProcess(Request $request) {}

    public function create() {}


    public function store(Request $request) {}

    public function edit($id) {}


    public function update(Request $request, $id) {}
}
