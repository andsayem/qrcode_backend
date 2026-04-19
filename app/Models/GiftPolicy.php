<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_name',
        'start_date',
        'end_date',
    ];

    /**
     * 🔗 A policy has many gifts
     */
    public function gifts()
    {
        return $this->hasMany(Gift::class, 'policy_id');
    }
}