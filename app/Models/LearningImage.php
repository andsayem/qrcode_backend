<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningImage extends Model
{
    public $table = 'learning_images';

    public $fillable = [
        'learning_id',
        'path',
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

}