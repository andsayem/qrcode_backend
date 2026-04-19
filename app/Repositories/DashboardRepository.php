<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRedeemRequestRepository
 * @package App\Repositories
 * @version March 13, 2022, 5:30 pm +06
*/

class DashboardRepository
{

    public function getTotalTechnician()
    {
        return \App\Models\Technician::whereHas('user_info', function ($query) {
            $query->where('status', 1);
        })->count() ?? 0;
    }

    public function getTotalPendingTechnician()
    {
        return \App\Models\Technician::whereHas('user_info', function ($query) {
            $query->where('status', 0);
        })->count() ?? 0;
    }

    public function getTotalRedeemRequestAmount()
    {
        return \App\Models\UserRedeemRequest::where('status', 1)->sum('amount') ?? 0;
    }

    public function getTotalScannedCode()
    {
        return \App\Models\SSGCodeDetail::where('status', 1)->count() ?? 0;
    }

    public function getTotalUserPoint()
    {
        return \App\Models\UserPoint::count() ?? 0;
    }
}
