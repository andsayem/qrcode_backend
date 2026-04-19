<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Technician;

class HomeRepository
{
    /**
     * Get total active technicians
     */

    public function totalScreenedpoints(): int
    { 
        return (float) Technician::join('users', 'technicians.user_id', '=', 'users.id')
            ->where('users.status', 1)
            ->sum('technicians.total_point');
    }

    public function notScreenedPoints(): float
    {
        $total = DB::table('products')
            ->join('channel_settings', 'products.channel_id', '=', 'channel_settings.channel_id')
            ->where('products.status', 1)
            ->where('products.point_slab', '>', 0)
            ->where('channel_settings.slab_value', '>', 0)
            ->selectRaw('SUM(products.point_slab * channel_settings.slab_value * (
                SELECT COUNT(*) 
                FROM ssg_code_details 
                WHERE ssg_code_details.product_id = products.id 
                AND ssg_code_details.status = 0
            )) as total')
            ->value('total');

        return (float) $total;
    }




    

    public function totalDisbursementPoint(): int
    { 
        return (float) DB::table('user_redeem_requests')
            ->where('status', 1)
            ->sum('point');
    }
    

    public function getTotalTechnician(): int
    {
        // Use exists join instead of whereHas for faster performance on large tables
        return Technician::join('users', 'technicians.user_id', '=', 'users.id')
            ->where('users.status', 1)
            ->count();
    }

    /**
     * Get total pending technicians
     */
    public function getTotalPendingTechnician(): int
    {
        return Technician::join('users', 'technicians.user_id', '=', 'users.id')
            ->where('users.status', 0)
            ->count();
    }

    /**
     * Get total pending redeem points
     */
    public function getRedeemPendingPoints(): float
    {
        return (float) DB::table('user_redeem_requests')
            ->where('status', 0)
            ->sum('point');
    }

    /**
     * Get total redeem process points
     */
    public function getRedeemProcessPoints(): float
    {
        return (float) DB::table('user_redeem_requests')
            ->where('status', 2)
            ->sum('point');
    }

    /**
     * Get total current points of active technicians
     */
    public function getCurrentPoints(): float
    {
        return (float) Technician::join('users', 'technicians.user_id', '=', 'users.id')
            ->where('users.status', 1)
            ->sum('technicians.current_point');
    }

    /**
     * Get top technicians by total_point
     */
    public function getTopTechnicians(int $limit = 5)
    {
        return DB::table('technicians')
            ->join('users', 'technicians.user_id', '=', 'users.id')
            ->leftJoin('user_redeem_requests as urr', function ($join) {
                $join->on('technicians.user_id', '=', 'urr.user_id')
                    ->where('urr.status', 1); // Only redeemed requests
            })
            ->where('users.status', 1) // Active users only
            ->groupBy('technicians.user_id', 'users.name', 'technicians.total_point')
            ->select(
                'users.name',
                'users.email',
                'technicians.user_id',
                'technicians.total_point as points',
                'technicians.point_name',
                'technicians.point_code',
                DB::raw('COALESCE(SUM(urr.point), 0) as redeem')
            )
            ->orderByDesc('technicians.total_point')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top products by total scans
     */
    public function getTopProducts(int $limit = 5)
    {
        return DB::table('user_points as up')
            ->join('products as p', 'up.product_id', '=', 'p.id')
            ->where('up.point_type', 1) // Only QR code scans
            ->groupBy('up.product_id', 'p.product_name')
            ->select('p.product_name', DB::raw('COUNT(up.id) as total_scans'))
            ->orderByDesc('total_scans')
            ->limit($limit)
            ->get();
    }
}
