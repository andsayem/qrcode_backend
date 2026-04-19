<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyRedeemPieChartController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $result = \App\Models\UserRedeemRequest::selectRaw('SUM(amount) as amount, MONTH(otp_send_time) as month')
            ->where('status', 1)
            ->whereYear('otp_send_time', '!=', "")
            ->whereYear('otp_send_time', Carbon::now()->year) // Filter by the current year
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('amount', 'month');
        return response()->json($result);
    }
}
