<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Technician;
use Carbon\Carbon;

class MgtDashboardRepository
{
    /**
     * Registered Technician Report
     */
    public function registeredReport($from = null, $to = null, $divisionId = null)
    {
        $from = $from ? Carbon::parse($from)->startOfMonth() : now()->startOfYear();
        $to = $to ? Carbon::parse($to)->endOfMonth() : now()->endOfMonth();

        // Last 3 months 
        $monthColumns = [];
        $current = $from->copy();

        while ($current <= $to) {
            $month = $current->month;
            $year = $current->year;
            $alias = strtolower($current->format('M')) . '_' . $year;

            $monthColumns[] = DB::raw("
            SUM(
                CASE 
                    WHEN MONTH(users.created_at) = {$month}
                    AND YEAR(users.created_at) = {$year}
                    THEN 1 ELSE 0
                END
            ) as {$alias}
        ");

            $current->addMonth();
        }



        $query = DB::table('technicians')
            ->select(
                'geo_divisions.id as division_id',
                'geo_divisions.name as division',
                DB::raw('COUNT(users.id) as total_users'),
                ...$monthColumns
            )
            ->join('geo_divisions', 'technicians.division_id', '=', 'geo_divisions.id')
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->where('users.status', 1);

        if ($divisionId) {
            $query->where('technicians.division_id', $divisionId);
        }

        $query->groupBy('technicians.division_id', 'geo_divisions.name')
            ->orderBy('geo_divisions.name');

        return $query->get();
    }
    public function activeReport($from = null, $to = null, $divisionId = null)
    {
        $from = $from ? Carbon::parse($from)->startOfMonth() : now()->startOfYear();
        $to = $to ? Carbon::parse($to)->endOfMonth() : now()->endOfMonth();

        // Generate all months between $from and $to
        $months = [];
        $current = $from->copy();
        while ($current <= $to) {
            $months[] = [
                'month' => $current->month,
                'year' => $current->year,
                'alias' => strtolower($current->format('M')) . '_' . $current->year
            ];
            $current->addMonth();
        }

        // Month-wise columns (minimum 3 scan)
        $monthColumns = [];
        foreach ($months as $m) {
            $monthColumns[] = DB::raw("
            SUM(
                CASE 
                    WHEN (
                        SELECT COUNT(*) 
                        FROM user_points up
                        WHERE up.user_id = users.id
                        AND up.point_type = 1
                        AND YEAR(up.created_at) = {$m['year']}
                        AND MONTH(up.created_at) = {$m['month']}
                    ) >= 3
                    THEN 1 ELSE 0
                END
            ) as {$m['alias']}
        ");
        }

        // Active user condition (>=12 scan each month)
        $activeCondition = implode(' AND ', array_map(function ($m) {
            return "(
            SELECT COUNT(*) 
            FROM user_points up
            WHERE up.user_id = users.id
            AND up.point_type = 1
            AND YEAR(up.created_at) = {$m['year']}
            AND MONTH(up.created_at) = {$m['month']}
        ) >= 12";
        }, $months));

        $query = DB::table('technicians')
            ->select(
                'geo_divisions.id as division_id',
                'geo_divisions.name as division',

                // Active Users (>=12 scan each month in range)
                DB::raw("
                COUNT(DISTINCT CASE 
                    WHEN {$activeCondition} 
                    THEN users.id
                END) as active_users
            "),

                // Total Points for Active Users
                DB::raw("
                SUM(
                    CASE 
                        WHEN {$activeCondition} 
                        THEN technicians.current_point
                        ELSE 0
                    END
                ) as total_points
            "),

                ...$monthColumns
            )
            ->join('geo_divisions', 'technicians.division_id', '=', 'geo_divisions.id')
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->where('users.status', 1);

        if ($divisionId) {
            $query->where('technicians.division_id', $divisionId);
        }

        $query->groupBy('technicians.division_id', 'geo_divisions.name')
            ->orderBy('geo_divisions.name');
        return $query->get();
    }

    public function inactiveReport($from = null, $to = null, $divisionId = null)
    {

        $from = $from ? Carbon::parse($from)->startOfMonth() : now()->startOfYear();
        $to = $to ? Carbon::parse($to)->endOfMonth() : now()->endOfMonth();

        // Generate all months between $from and $to
        $months = [];
        $current = $from->copy();
        while ($current <= $to) {
            $months[] = [
                'month' => $current->month,
                'year' => $current->year,
                'alias' => strtolower($current->format('M')) . '_' . $current->year
            ];
            $current->addMonth();
        }

        // Month-wise columns (minimum 3 scan)
        $monthColumns = [];
        foreach ($months as $m) {
            $monthColumns[] = DB::raw("
            SUM(
                CASE 
                    WHEN (
                        SELECT COUNT(*) 
                        FROM user_points up
                        WHERE up.user_id = users.id
                        AND up.point_type = 1
                        AND YEAR(up.created_at) = {$m['year']}
                        AND MONTH(up.created_at) = {$m['month']}
                    ) < 3
                    THEN 1 ELSE 0
                END
            ) as {$m['alias']}
        ");
        }

        // Active user condition (>=12 scan each month)
        $inactiveCondition = implode(' AND ', array_map(function ($m) {
            return "(
            SELECT COUNT(*) 
            FROM user_points up
            WHERE up.user_id = users.id
            AND up.point_type = 1
            AND YEAR(up.created_at) = {$m['year']}
            AND MONTH(up.created_at) = {$m['month']}
        ) < 12";
        }, $months));

        $query = DB::table('technicians')
            ->select(
                'geo_divisions.id as division_id',
                'geo_divisions.name as division',

                // Active Users (>=12 scan each month in range)
                DB::raw("
                COUNT(DISTINCT CASE 
                    WHEN {$inactiveCondition} 
                    THEN users.id
                END) as users 
            "),

                // Total Points for Active Users
                DB::raw("
                SUM(
                    CASE 
                        WHEN {$inactiveCondition} 
                        THEN technicians.current_point
                        ELSE 0
                    END
                ) as total_points
            "),

                ...$monthColumns
            )
            ->join('geo_divisions', 'technicians.division_id', '=', 'geo_divisions.id')
            ->join('users', 'technicians.user_id', '=', 'users.id');
        // ->where('users.status', 1);

        if ($divisionId) {
            $query->where('technicians.division_id', $divisionId);
        }

        $query->groupBy('technicians.division_id', 'geo_divisions.name')
            ->orderBy('geo_divisions.name');
        return $query->get();

        //     $from = $from ? Carbon::parse($from)->startOfMonth() : now()->startOfYear();
        //     $to = $to ? Carbon::parse($to)->endOfMonth() : now()->endOfMonth();

        //     // Last 3 months for active user condition
        //     $last3Months = [];
        //     for ($i = 0; $i < 3; $i++) {
        //         $m = Carbon::now()->subMonths($i);
        //         $last3Months[] = ['year' => $m->year, 'month' => $m->month];
        //     }

        //     // Build dynamic month-wise columns with active user condition
        //     $monthColumns = [];
        //     $current = $from->copy();
        //     while ($current <= $to) {
        //         $monthNumber = $current->month;
        //         $yearNumber = $current->year;
        //         $alias = strtolower($current->format('M')) . '_' . $yearNumber . '_qty';

        //         // Active user condition for last 3 months
        //         $conditions = implode(' AND ', array_map(function ($lm) {
        //             return "(SELECT COUNT(*) FROM user_points up 
        //                  WHERE up.user_id = users.id 
        //                  AND up.point_type = 1
        //                  AND YEAR(up.created_at) = {$lm['year']} 
        //                  AND MONTH(up.created_at) = {$lm['month']})  < 12";
        //         }, $last3Months));

        //         $monthColumns[] = DB::raw("
        //         SUM(
        //             CASE 
        //                 WHEN {$conditions} 
        //                 AND MONTH(users.created_at) = {$monthNumber} 
        //                 AND YEAR(users.created_at) = {$yearNumber} 
        //                 THEN 1 ELSE 0
        //             END
        //         ) as {$alias}
        //     ");

        //         $current->addMonth();
        //     }

        //     // Main query
        //     $query = DB::table('technicians')
        //         ->select(
        //             'geo_divisions.id as division_id',
        //             'geo_divisions.name as division',
        //             // Count only active users
        //             DB::raw('COUNT(DISTINCT CASE 
        //                     WHEN ' .
        //                 implode(' AND ', array_map(function ($lm) {
        //                     return "(SELECT COUNT(*) FROM user_points up 
        //                                  WHERE up.user_id = users.id 
        //                                  AND up.point_type = 1
        //                                  AND YEAR(up.created_at) = {$lm["year"]} 
        //                                  AND MONTH(up.created_at) = {$lm["month"]}) < 12";
        //                 }, $last3Months)) .
        //                 ' THEN users.id END) as users'),
        //             DB::raw('SUM(CASE 
        // WHEN ' . implode(' AND ', array_map(function ($lm) {
        //                 return "(SELECT COUNT(*) FROM user_points up 
        //              WHERE up.user_id = users.id 
        //              AND up.point_type = 1
        //              AND YEAR(up.created_at) = {$lm["year"]} 
        //              AND MONTH(up.created_at) = {$lm["month"]}) < 12";
        //             }, $last3Months)) . '
        // THEN technicians.current_point ELSE 0 END) as total_points'),
        //             ...$monthColumns
        //         )
        //         ->join('geo_divisions', 'technicians.division_id', '=', 'geo_divisions.id')
        //         ->join('users', 'technicians.user_id', '=', 'users.id');

        //     if ($divisionId) {
        //         $query->where('technicians.division_id', $divisionId);
        //     }

        //     $query->groupBy('technicians.division_id', 'geo_divisions.name')
        //         ->orderBy('geo_divisions.name');



        //     return $query->get();

    }

    /**
     * Total Scan Report
     */
    public function totalScanReport($from = null, $to = null, $division = null)
    {
        $from = $from ? Carbon::parse($from)->startOfMonth() : now()->startOfYear();
        $to = $to ? Carbon::parse($to)->endOfMonth() : now()->endOfMonth();

        // Generate months dynamically
        $months = [];
        $current = $from->copy();

        while ($current <= $to) {
            $months[] = [
                'month' => $current->month,
                'year' => $current->year,
                'alias' => strtolower($current->format('M')) . '_' . $current->year
            ];
            $current->addMonth();
        }

        $query = DB::table('user_points')
            ->join('technicians', 'technicians.user_id', '=', 'user_points.user_id')
            ->join('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
            ->select(
                'geo_divisions.id as division_id',
                'geo_divisions.name as division'
            );

        // 🔥 Month wise dynamic columns
        foreach ($months as $m) {
            $query->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN MONTH(user_points.created_at) = {$m['month']}
                    AND YEAR(user_points.created_at) = {$m['year']}
                    THEN 1
                    ELSE 0
                END
            ) as {$m['alias']}
        "));
        }

        // Optional division filter
        if (!empty($division) && $division != 'all') {
            $query->where('geo_divisions.id', $division);
        }

        $result = $query
            ->whereBetween('user_points.created_at', [$from, $to])
            ->groupBy('geo_divisions.id', 'geo_divisions.name')
            ->get();  
        return $result ;
    }
    // public function totalScanReport($from, $to, $division = null)
    // {
    //     $from = Carbon::parse($from)->startOfDay();
    //     $to = Carbon::parse($to)->endOfDay();

    //     $query = DB::table('user_points as up')
    //         ->select(
    //             'gd.id as division_id',
    //             'gd.name as division',
    //             DB::raw('COUNT(up.id) as total_scans'),
    //             DB::raw('COUNT(DISTINCT up.user_id) as total_users')
    //         )
    //         ->join('users as u', 'up.user_id', '=', 'u.id')
    //         ->join('technicians as t', 'u.id', '=', 't.user_id')
    //         ->join('geo_divisions as gd', 't.division_id', '=', 'gd.id')
    //         ->where('up.point_type', 1) // only QR scans
    //         ->whereBetween('up.created_at', [$from, $to])
    //         ->where('u.status', 1);

    //     if ($division) {
    //         $query->where('t.division_id', $division);
    //     }

    //     $query->groupBy('gd.id', 'gd.name')
    //         ->orderBy('gd.name');

    //     return $query->get();
    // }

    /**
     * Redeem Point Report
     */
    public function redeemReport($from, $to, $division)
    {
        return DB::table('redeems')
            ->when($division, fn($q) => $q->where('division', $division))
            ->when($from, fn($q) => $q->whereDate('redeem_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('redeem_date', '<=', $to))
            ->select('division', DB::raw('SUM(points) as total_redeem_points'))
            ->groupBy('division')
            ->get();
    }

    /**
     * Month Wise New Entry Report
     */
    public function newEntryReport($from, $to, $division)
    {
        return Technician::query()
            ->when($division, fn($q) => $q->where('division', $division))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}