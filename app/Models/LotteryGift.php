<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryGift extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'gift_name',
        'gift_image'
    ];

    public function assignments()
    {
        return $this->hasMany(LotteryGiftAssign::class, 'gift_id');
    }
}
