<?php

namespace App\Http\Controllers;

use App\Repositories\MgtDashboardRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MgtDashboardController extends Controller
{
    public $repository;

    public function __construct(MgtDashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request)
    {
        $reportType = $request->report_type;
        // Default dates
        $toDate = $request->to_date ?: Carbon::today()->toDateString(); // today
        $fromDate = $request->from_date ?: Carbon::today()->subMonths(3)->firstOfMonth()->toDateString(); // 3 months back, first day
        $division = $request->division;
        $divisionId = $request->division; // selected division id

        $reportData = [];


        // Fetch all divisions dynamically
        $divisions = DB::table('geo_divisions')
            ->select('id', 'name')
            ->where('country_id', 1)
            ->orderBy('name')
            ->get();

        // Example: Different report অনুযায়ী repository call
        if ($reportType == 'registered') {
            $reportData = $this->repository->registeredReport($fromDate, $toDate, $division);
        } elseif ($reportType == 'active') {
            $reportData = $this->repository->activeReport($fromDate, $toDate, $division);
        } elseif ($reportType == 'inactive') {
            $reportData = $this->repository->inactiveReport($fromDate, $toDate, $division);
        } elseif ($reportType == 'total_scan') {
            $reportData = $this->repository->totalScanReport($fromDate, $toDate, $division);
        } elseif ($reportType == 'redeem') {
            $reportData = $this->repository->redeemReport($fromDate, $toDate, $division);
        } elseif ($reportType == 'new_entry') {
            $reportData = $this->repository->newEntryReport($fromDate, $toDate, $division);
        }

        return view('MgtDashboard', compact(
            'reportType',
            'reportData',
            'fromDate',
            'toDate',
            'divisionId',
            'divisions'
        ));
    }
}