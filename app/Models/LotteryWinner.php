<?php

namespace App\Models;

use Illuminarte\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryWinner extends Model
{
    use HasFactory;
    protected $fillable = [
        'lottery_id',
        'gift_assign_id',
        'user_id',
        'position',
        'winner_name',
        'mobile_no',
        'draw_time'
    ];

    protected $casts = [
        'draw_time' => 'datetime'
    ];

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giftAssign()
    {
        return $this->belongsTo(LotteryGiftAssign::class, 'gift_assign_id');
    }
}
