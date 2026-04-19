<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $repository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = []; // Prepare your data here
        $data['current_year'] = Carbon::now()->year;
        $data['total_technician'] = $this->repository->getTotalTechnician();
        $data['total_pending_technician'] = $this->repository->getTotalPendingTechnician();
        $data['total_redeem_request_amount'] = $this->repository->getTotalRedeemRequestAmount();
        $data['total_scanned_code'] = $this->repository->getTotalScannedCode($request);
        $data['total_user_point'] = $this->repository->getTotalUserPoint();
        return view('dashboard')->with($data);
    }
}
