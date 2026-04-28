<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'point_slab',
        'gift_name',
        'image',
        'policy_type',
        'gift_type',
        'is_point_cut',
    ];

    /**
     * 🔗 Gift belongs to Gift Policy
     */
    public function policy()
    {
        return $this->belongsTo(GiftPolicy::class, 'policy_id');
    }
}