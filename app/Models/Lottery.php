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
}
