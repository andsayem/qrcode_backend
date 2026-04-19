<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRateSetting extends Model
{
    use HasFactory;

    public $fillable = [
        'point_rate',
        'setting_id',
        'country_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = []; 
 
    public function settings()
    {
        return $this->belongsTo(Settings::class,'setting_id', 'id');
    }
     
}
