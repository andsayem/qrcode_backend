<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Lottery extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'from_date',
        'to_date',
        'draw_date',
        'required_points',
        'total_winners',
        'status',
        'current_position',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'draw_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function giftAssignments()
    {
        return $this->hasMany(LotteryGiftAssign::class);
    }

    public function winners()
    {
        return $this->hasMany(LotteryWinner::class);
    }

    // User IDs who earned the required points within the lottery period
    public function eligibleUsersQuery()
    {
        return UserPoint::select('user_id')
            ->whereBetween('created_at', [
                $this->from_date->copy()->startOfDay(),
                $this->to_date->copy()->endOfDay()
            ])
            ->groupBy('user_id')
            ->havingRaw('SUM(point) >= ?', [$this->required_points]);
    }

    public function eligibleUsersCount()
    {
        return \DB::query()->fromSub($this->eligibleUsersQuery(), 'eligible')->count();
    }

    // Eligible users with their profile, location and total points in the lottery period
    public function eligibleUsersDetailQuery($search = null)
    {
        $points = $this->eligibleUsersQuery()->selectRaw('SUM(point) as total_points');

        $query = \DB::query()
            ->fromSub($points, 'p')
            ->join('users', 'users.id', '=', 'p.user_id')
            ->leftJoin('technicians', 'technicians.user_id', '=', 'p.user_id')
            ->leftJoin('geo_divisions', 'geo_divisions.id', '=', 'technicians.division_id')
            ->leftJoin('geo_district', 'geo_district.id', '=', 'technicians.district_id')
            ->leftJoin('geo_thana', 'geo_thana.id', '=', 'technicians.upazilla_id')
            ->select(
                'p.user_id',
                'users.name',
                'users.email',
                'users.phone_number',
                'geo_divisions.name as division_name',
                'geo_district.district',
                'geo_thana.thana',
                'p.total_points'
            )
            ->orderByDesc('p.total_points');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.phone_number', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // Days left until the draw date (negative when the date has passed)
    public function daysUntilDraw()
    {
        if (!$this->draw_date) {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->draw_date->copy()->startOfDay(), false);
    }
}
