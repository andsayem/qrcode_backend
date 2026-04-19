<?php

namespace App\Http\Controllers;

use App\Repositories\HomeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public $repository;

    public function __construct(HomeRepository $repository)
    {
        $this->repository = $repository;
    }

   
public function __invoke(Request $request)
{
    // Last 12 months labels
    $months = [];
    for ($i = 11; $i >= 0; $i--) {
        $months[] = Carbon::now()->subMonths($i)->format('M Y');
    }

    // Monthly Redeem Points
    $redeemData = DB::table('user_redeem_requests')
        ->selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, SUM(point) as total')
        ->where('status', 1)
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthlyRedeem = [];
    foreach ($months as $month) {
        $monthlyRedeem[$month] = $redeemData[$month] ?? 0;
    }

    // Monthly Scanned Codes
    $scanData = DB::table('user_points')
        ->selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, COUNT(id) as total')
        ->where('point_type', 1)
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthlyScanned = [];
    foreach ($months as $month) {
        $monthlyScanned[$month] = $scanData[$month] ?? 0;
    }

   // dd($this->repository->notScreenedpoints());

    $data = [
        'totalScreenedpoints' => $this->repository->totalScreenedpoints(),
        'notScreenedpoints'  => $this->repository->notScreenedpoints(),
        'products' =>  DB::table('products')->where('status',1)->count(),
        'inactiveProduct' =>  DB::table('products')->where('status',0)->count(),
        'totalDisbursementPoint' => $this->repository->totalDisbursementPoint(),
        'activeTechniciansCount' => $this->repository->getTotalTechnician(),
        'pendingTechniciansCount' => $this->repository->getTotalPendingTechnician(),
        'redeemPendingPoints' => $this->repository->getRedeemPendingPoints(),
        'redeemProcessPoints' => $this->repository->getRedeemProcessPoints(),
        'currentPoints' => $this->repository->getCurrentPoints(),
        'topTechnicians' => $this->repository->getTopTechnicians(),
        'topProducts' => $this->repository->getTopProducts(),
        'monthlyRedeem' => $monthlyRedeem,
        'monthlyScanned' => $monthlyScanned,
    ];  
    return view('home')->with($data);
}
}
