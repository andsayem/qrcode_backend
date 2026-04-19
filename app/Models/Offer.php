<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{

    public $table = 'offers';

    public $fillable = [
        'title',
        'description',
        'image',
        'point_value',
        'created_by',
        'is_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'technician_id' => 'integer',
        // 'complain' => 'string',
        // 'sku' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

}