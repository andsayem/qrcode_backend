<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gift;
use App\Models\GiftPolicy;

class GiftTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'gift_id',
        'policy_id',
        'request_status',
        'delivery_status',
        'requested_at',
        'approved_at',
        'sent_at',
        'received_at',
        'admin_note',
        'user_note',
    ];
    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    public function policy()
    {
        return $this->belongsTo(GiftPolicy::class);
    }
}