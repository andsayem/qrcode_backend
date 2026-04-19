<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $table = 'sms_logs';

    protected $fillable = [
        'user_id',
        'mobile',
        'message',
        'status',
        'response',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * The user who sent the SMS (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
