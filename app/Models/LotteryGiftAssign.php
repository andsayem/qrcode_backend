<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryGiftAssign extends Model
{
    use HasFactory;

    protected $table = 'lottery_gift_assigns';
    protected $fillable = [
        'lottery_id',
        'gift_id',
        'position'
    ];

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function gift()
    {
        return $this->belongsTo(LotteryGift::class, 'gift_id');
    }

    public function winner()
    {
        return $this->hasOne(LotteryWinner::class, 'gift_assign_id');
    }
}
