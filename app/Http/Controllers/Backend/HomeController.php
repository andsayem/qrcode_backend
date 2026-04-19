<?php

namespace App\Http\Controllers\Backend;

use App\Models\RequestCode;
use App\Models\SSGCodeDetail;
use App\Models\UserPoint;
use App\Utilities\Enum\RequestCodeStatusEnum;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Repositories\CommonRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\UserController;
use Carbon\Carbon;
use App\Models\UserRedeemRequest;
use DB;

class HomeController extends Controller
{
    private $commonRepository;
    public function dashboard(Request $request)
    {
        $data = [];
        $get_country = request()->filled('country') ? request('country') : '';
        $get_district = request()->filled('district') ? request('district') : '';
        $get_division = request()->filled('division') ? request('division') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : '';
        $get_from_date = request()->filled('from_date') ? request('from_date') : '';
        $get_to_date = request()->filled('to_date') ? request('to_date') : '';  
        $userController = new UserController($request);

       

        $data['country'] = $userController->getSsforceCountry(); 

        $data['divisions'] = $userController->getSsforceDivisions($request); 
        
        $data['district'] = $userController->getSsforceDistrict($request);
        $request->ddivision_id = $get_division;
        $request->district_id = $get_district;
        $request->thana_id = $get_thana;

        if($get_district){
            $data['thanas']  = $userController->getSsforcethana($request); 
        }else{
            $data['thanas'] = array();
        }
        if( $get_thana){
            $data['areas'] =  $userController->getSsforcearea($request);  
        }else{
            $data['areas'] = array();
        }  
        $data['channels']  = $userController->getChannels($request);  
 





        $verifiedProduct = self::getVerifiedProduct($request);
        $verified_product = json_decode($verifiedProduct->getContent(), true);
        $data['verified_product'] = $verified_product;

        $monthlyRedeem = self::getChartData($request);
        $monthly_redeem = json_decode($monthlyRedeem->getContent(), true);
        $data['monthly_redeem'] = $monthly_redeem;

        $earnVsSettlement = self::earnVSSettlementReport($request);
        $earn_settlement = json_decode($earnVsSettlement->getContent(), true);
        $data['earn_settlement'] = $earn_settlement; 

        return view('backend.dashboard')->with($data);
    }

    public function getTechniciansData(Request $request){
        $get_country = request()->filled('country') ? request('country') : '';
        $get_division = request()->filled('division') ? request('division') : '';
        $get_district = request()->filled('district') ? request('district') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : '';
        $get_from_date = request()->filled('from_date') ? request('from_date') : '';
        $get_to_date = request()->filled('to_date') ? request('to_date') : '';  

        $role = 'Technician'; 
        $previousMonthStats = $this->getTechnicianStatusByMonth($request, -1, $role);
        $approvedTechnicianPrevious = $previousMonthStats['approvedTechnician'];

        $currentMonthStats = $this->getTechnicianStatusByMonth($request, 0, $role);
        $approvedTechnicianCurrent = $currentMonthStats['approvedTechnician'];

        $percentageDifference = 0;
        if ($approvedTechnicianPrevious > 0) {
            $percentageDifference = (($approvedTechnicianCurrent - $approvedTechnicianPrevious) / $approvedTechnicianPrevious) * 100;
        }

        $data['percentageDifference'] = $percentageDifference; 
        $formatted_number = $percentageDifference ? number_format($percentageDifference, 2) : $percentageDifference;

            $data = [
                'techniciansCount' =>  $this->getTechnicianCount($request, 1, $role),
                'pendingCount' => $this->getTechnicianCount($request, 0, $role),
                //'percentage' => $formatted_number 
            ];

            return response()->json($data); // Return the data as JSON
        }
        public function getQrPointData(Request $request)
        {
            // Retrieve the necessary data for the QR code
            $qrcodeCount = UserPoint::whereHas('product', function ($query) use ($request) {
                    $query->when($request->channel, function($q) use ($request){
                        return $q->where('products.channel_id', $request->channel);
                    });
                })
                ->whereHas('user.technician',function( $query ) use ($request){   
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
                })
                ->count(); // Replace with your logic to retrieve the QR code count 
            $data = [
                 'value' => $qrcodeCount,
                 //'percentage' =>   $this->calculatePercentageDifferenceForQrPoint( $request)
            ];
        
            return response()->json($data); // Return the data as JSON
        }
    //getQrcodeData

    public function getQrcodeData(Request $request)
    {
        // Retrieve the necessary data for the QR code
        $qrcodeCount = SSGCodeDetail::where('status', 1)
            ->whereHas('product', function ($query) use ($request) {
                $query->when($request->channel, function($q) use ($request){
                    return $q->where('products.channel_id', $request->channel);
                });
            })
            ->whereHas('user.technician',function( $query ) use ($request){   
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
            })
            ->count(); // Replace with your logic to retrieve the QR code count
        
        $data = [
            'qrcodeCount' => $qrcodeCount,
           // 'percentage' => $this->calculatePercentageDifferenceForCodeUsage()
        ];
    
        return response()->json($data); // Return the data as JSON
    }

    public function getRedeemData(Request $request){
        $get_district = request()->filled('district') ? request('district') : '';
        $get_thana = request()->filled('thana') ? request('thana') : '';
        $get_area = request()->filled('area') ? request('area') : '';
        $get_from_date = request()->filled('from_date') ? request('from_date') : '';
        $get_to_date = request()->filled('to_date') ? request('to_date') : '';  
        // Retrieve the necessary data for the redeem section
        $redeemCount = 0; // Replace with your logic to retrieve the redeem count
        $previousMonth = 0; // Replace with your logic to retrieve the previous month's count
        $currentMonth = 0; // Replace with your logic to retrieve the current month's count
        $percentageDifference = 0;

        if ($previousMonth > 0) {
            $percentageDifference = (($currentMonth - $previousMonth) / $previousMonth) * 100;
        }

        $data = [
            'redeemPoint' => $this->getTotalPoint($request),
            'redeemCount' => $this->getTotalAmount($request),
          //  'percentage' => $this->getcalculatePercentageAmount($request)
        ];

        return response()->json($data); // Return the data as JSON
    }
    public function earnVSSettlementReport(Request $request){
        $currentYear = $request->get('year') ?? Carbon::now()->year;  
        $earnings = UserRedeemRequest::select(DB::raw('MONTH(created_at) as month, SUM(amount) as total_earnings'))
            ->whereHas('technician',function( $query ) use ($request){ 
                $query->when($request->country, function($q) use ($request){
                    return $q->where('technicians.country_id', $request->country);
                });
                $query->when($request->division, function($q) use ($request){
                    return $q->where('technicians.division_id', $request->division);
                });
                $query->when($request->district, function($q) use ($request){
                    return $q->where('district_id', $request->district);
                });
                $query->when($request->thana, function($q) use ($request){
                    return $q->where('upazilla_id', $request->thana);
                });
                $query->when($request->area, function($q) use ($request){
                    return $q->where('union_id', $request->area);
                }); 
            })
            ->whereYear('updated_at', $currentYear)
            ->where('status', '<=', 1) // Status 0 and 1 means Earn
            ->groupBy('month')
            ->get();

        $settlements = UserRedeemRequest::select(DB::raw('MONTH(updated_at) as month, SUM(amount) as total_settlements')) 
            ->whereHas('technician',function( $query ) use ($request){ 
                $query->when($request->country, function($q) use ($request){
                    return $q->where('technicians.country_id', $request->country);
                });
                $query->when($request->division, function($q) use ($request){
                    return $q->where('technicians.division_id', $request->division);
                });
                $query->when($request->district, function($q) use ($request){
                    return $q->where('district_id', $request->district);
                });
                $query->when($request->thana, function($q) use ($request){
                    return $q->where('upazilla_id', $request->thana);
                });
                $query->when($request->area, function($q) use ($request){
                    return $q->where('union_id', $request->area);
                }); 
            })
            ->whereYear('updated_at', $currentYear)
            ->where('status', 1) // Status 1 means Settlement
            ->groupBy('month')
            ->get(); 
        $chartData = [];

        foreach ($earnings as $earning) {
            $month = $earning->month;
            $chartData[$month]['earnings'] = $earning->total_earnings;
        }
        
        foreach ($settlements as $settlement) {
            $month = $settlement->month;
            $chartData[$month]['settlements'] = $settlement->total_settlements;
        }

        $labels = [];
        $earningsData = [];
        $settlementsData = [];

        foreach ($chartData as $month => $data) {
            $labels[] = date('M', mktime(0, 0, 0, $month, 1));
            $earningsData[] = $data['earnings'] ?? 0;
            $settlementsData[] = $data['settlements'] ?? 0;
        }
        $chartFormattedData = [
            'labels' => $labels,
            'earnings' => $earningsData,
            'settlements' => $settlementsData, 
        ];
        // dd($chartFormattedData );

        // $chartFormattedData = [
        //     'labels' => $labels,
        //     'series' => [
        //         ['name' => 'Earnings', 'data' => $earningsData],
        //         ['name' => 'Settlements', 'data' => $settlementsData],
        //     ],
        // ];

        return response()->json($chartFormattedData);
            

    }
    public function earnVSSettlementReports(Request $request){
        $earnSettlementData = UserRedeemRequest::select(DB::raw('MONTH(created_at) as month'), 'status', DB::raw('SUM(amount) as total_amount'))
        ->groupBy('month', 'status')
        ->get();

        $earnData = [];
        $settlementData = []; 

        foreach ($earnSettlementData as $row) {
            if ($row->status == 0) {
                $earnData[$row->month] = $row->total_amount;
            } else {
                $settlementData[$row->month] = $row->total_amount;
            }
        }

        $months = range(1, 12);

        $earnSeries = array_map(function ($month) use ($earnData) {
            return isset($earnData[$month]) ? $earnData[$month] : 0;
        }, $months);

        $settlementSeries = array_map(function ($month) use ($settlementData) {
            return isset($settlementData[$month]) ? $settlementData[$month] : 0;
        }, $months); 

        $data = [
            'months' => self::earnVSSettlementReportBack($request), //$months,
            'earnSeries' => $earnSeries,
            'settlementSeries' => $settlementSeries,
        ];
        return response()->json($data);
    }
    public function earnVSSettlementReportBack(Request $request){ 
        $currentYear = $request->get('year') ?? Carbon::now()->year;    
        $earning = UserRedeemRequest::selectRaw('MONTH(created_at) AS month, SUM(amount) AS total_amount') 
           
            ->whereYear('updated_at', $currentYear);
        $settlement = UserRedeemRequest::selectRaw('MONTH(updated_at) AS month, SUM(amount) AS total_amount') 
            ->whereHas('technician',function( $query ) use ($request){ 
                $query->when($request->country, function($q) use ($request){
                    return $q->where('technicians.country_id', $request->country);
                });
                $query->when($request->division, function($q) use ($request){
                    return $q->where('technicians.division_id', $request->division);
                });
                $query->when($request->district, function($q) use ($request){
                    return $q->where('district_id', $request->district);
                });
                $query->when($request->thana, function($q) use ($request){
                    return $q->where('upazilla_id', $request->thana);
                });
                $query->when($request->area, function($q) use ($request){
                    return $q->where('union_id', $request->area);
                }); 
            })
            ->whereYear('updated_at', $currentYear);

            $earnings = $earning->groupBy('month')
                ->orderBy('month')
                ->get();   
            
            $settlements = $settlement->where('status', 1) // If status 0 its means redeem data
                ->groupBy('month')
                ->orderBy('month')
                ->get();   
            
            // Prepare chart data
            $settlements_data = $settlements->pluck('total_amount')->toArray();
            $settlements_months = $settlements->pluck('month')->toArray();  

            $earnings_data = $earnings->pluck('total_amount')->toArray();
            $earnings_months = $earnings->pluck('month')->toArray();  

            $settlements_data_formatted = array_map(function ($value) {
                return (int) str_replace(',', '', $value);
            }, $settlements_data);

            $earnings_formatted = array_map(function ($value) {
                return (int) str_replace(',', '', $value);
            }, $earnings_data);
            return [
                'earnings' => ['data'=> $earnings_formatted, 'months'=> $earnings_months],
                'settlements' => ['data'=> $settlements_data_formatted, 'months'=> $settlements_months]
            ];

        // return response()->json($data); // Return the data as JSON
    }
    private function getTechnicianCount(Request $request, $status, $role)
    {
        return User::where('status', $status)
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            }) 
            ->whereHas('technician',function( $query ) use ($request){   

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
            })
            ->count();
    }

    public function getChartData(Request $request) { 
        $currentYear = $request->get('year') ?? Carbon::now()->year; 
        $get_district = request()->filled('districtid') ? request('districtid') : '';
        $get_thana = request()->filled('thanaid') ? request('thanaid') : '';
        $get_area = request()->filled('areaid') ? request('areaid') : ''; 
         
         // Query for monthly redeem amounts of the current year
         $monthlyRedeemAmounts = UserRedeemRequest::selectRaw('MONTH(otp_send_time) AS month, SUM(amount) AS total_amount')
            ->whereHas('technician',function( $query ) use ($request){   
                $query->when($request->country, function($q) use ($request){
                    return $q->where('technicians.country_id', $request->country);
                });
                $query->when($request->division, function($q) use ($request){
                    return $q->where('technicians.division_id', $request->division);
                });
                $query->when($request->districtid, function($q) use ($request){
                    return $q->where('technicians.district_id', $request->districtid);
                });
                $query->when($request->thanaid, function($q) use ($request){
                    return $q->where('technicians.upazilla_id', $request->thanaid);
                });
                $query->when($request->areaid, function($q) use ($request){
                    return $q->where('technicians.union_id', $request->areaid);
                }); 
            })
            ->where('status', 1)
            ->whereYear('otp_send_time', $currentYear) // Filter by the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // Prepare chart data
            $chartData = $monthlyRedeemAmounts->pluck('total_amount')->toArray();
            $chartLabels = $monthlyRedeemAmounts->pluck('month')->toArray();
            $data = [
                'chartData' => $chartData,
                'chartLabels' => $chartLabels
            ];

        return response()->json($data); // Return the data as JSON
    }
    
    public function getVerifiedProduct(Request $request) { 
        $currentYear = $request->get('year') ?? Carbon::now()->year; 
        $get_district = request()->filled('districtid') ? request('districtid') : '';
        $get_thana = request()->filled('thanaid') ? request('thanaid') : '';
        $get_area = request()->filled('areaid') ? request('areaid') : ''; 
        
        
        $ssgcodes = SSGCodeDetail::selectRaw('MONTH(updated_at) AS month, SUM(status) AS total_amount')
            ->where('total_used', '>', 1)
            ->whereHas('user.technician',function( $query ) use ($request){   
                $query->when($request->country, function($q) use ($request){
                    return $q->where('technicians.country_id', $request->country);
                });
                $query->when($request->division, function($q) use ($request){
                    return $q->where('technicians.division_id', $request->division);
                });
                $query->when($request->districtid, function($q) use ($request){
                    return $q->where('technicians.district_id', $request->districtid);
                });
                $query->when($request->thanaid, function($q) use ($request){
                    return $q->where('technicians.upazilla_id', $request->thanaid);
                });
                $query->when($request->areaid, function($q) use ($request){
                    return $q->where('technicians.union_id', $request->areaid);
                }); 
            })
            ->where('status', 1)
            ->whereYear('updated_at', $currentYear) // Filter by the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get(); 
            // Prepare chart data
            $chartData = $ssgcodes->pluck('total_amount')->toArray();
            $chartLabels = $ssgcodes->pluck('month')->toArray();
            // $chartDataFormatted = array_map('number_format', $chartData);

            $chartDataFormatted = array_map(function ($value) {
                return (int)str_replace(',', '', $value);
            }, $chartData);
            $data = [
                'chartData' => $chartDataFormatted,
                'chartLabels' => $chartLabels
            ];

        return response()->json($data); // Return the data as JSON
    }

    private function getTechnicianStatusByMonth(Request $request, $monthDifference, $role)
    {
        $currentMonthStart = Carbon::now()->addMonths($monthDifference)->startOfMonth();
        $currentMonthEnd = Carbon::now()->addMonths($monthDifference)->endOfMonth();

        return [
            'approvedTechnician' => User::where('status', 1)
                ->whereHas('roles', function ($query) use ($role) {
                    $query->where('name', $role);
                })
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->count(),
        ];
    }


    private function calculatePercentageDifferenceForQrPoint(Request $request )
    {
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $qrcodeCount = UserPoint::whereHas('product', function ($query) use ($request) {
            $query->when($request->channel, function($q) use ($request){
                return $q->where('products.channel_id', $request->channel);
            });
        })
        ->whereHas('user.technician',function( $query ) use ($request){   
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
        })
        ->count(); 

        $percentageDifference = 0;
        if ($totalVerifyPrevious > 0) {
            $percentageDifference = (($totalVerifyCurrent - $totalVerifyPrevious) / $totalVerifyPrevious) * 100;
        }

        return number_format($percentageDifference, 2);
    }


    private function calculatePercentageDifferenceForCodeUsage()
    {
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $totalVerifyPrevious = SSGCodeDetail::where('status', 1)
            ->whereBetween('code_used_time', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $totalVerifyCurrent = SSGCodeDetail::where('status', 1)
            ->whereBetween('code_used_time', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $percentageDifference = 0;
        if ($totalVerifyPrevious > 0) {
            $percentageDifference = (($totalVerifyCurrent - $totalVerifyPrevious) / $totalVerifyPrevious) * 100;
        }

        return number_format($percentageDifference, 2);
    }

    private function getTotalAmount(Request $request)
    {
        return UserRedeemRequest::where('status', 1)
            ->whereHas('technician',function( $query ) use ($request){   
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
            })
            ->sum('amount');
    }

    private function getTotalPoint(Request $request)
    {
        return UserRedeemRequest::where('status', 1)
            // ->whereHas('technician', function ($query)  {})
            ->whereHas('technician',function( $query ) use ($request){   
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
            })
            ->sum('point');
    }

    private function getcalculatePercentageAmount(Request $request) {
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $totalAmountPrevious = UserRedeemRequest::where('status', 1)
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->whereHas('technician',function( $query ) use ($request){ 
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
            })
            ->sum('amount');

        $totalAmountCurrent = UserRedeemRequest::where('status', 1)
            ->whereHas('technician',function( $query ) use ($request){   

                $query->when($request->district, function($q) use ($request){
                    return $q->where('technicians.district_id', $request->district);
                });
                $query->when($request->thana, function($q) use ($request){
                    return $q->where('technicians.upazilla_id', $request->thana);
                });
                $query->when($request->area, function($q) use ($request){
                    return $q->where('technicians.union_id', $request->area);
                }); 
            })
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $percentageDifference = 0;
        if ($totalAmountPrevious > 0) {
            $percentageDifference = (($totalAmountCurrent - $totalAmountPrevious) / $totalAmountPrevious) * 100;
        }

        return number_format($percentageDifference, 2);
    }
}