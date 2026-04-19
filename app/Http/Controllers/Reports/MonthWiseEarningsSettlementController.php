<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthWiseEarningsSettlementController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $result = \App\Models\UserRedeemRequest::selectRaw('SUM(amount) as amount, MONTH(created_at) as month')
            ->where('status', '<=', 1)
            ->whereYear('updated_at', '!=', "")
            ->whereYear('updated_at', Carbon::now()->year) // Filter by the current year
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('amount', 'month');
        return response()->json($result);
    }
}
