<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Learning extends Model
{

    public $table = 'learnings';

    public $fillable = [
        'title',
        'description',
        'type',
        'path',
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

    public function images()
    {
        return $this->hasMany(LearningImage::class,'learning_id');
    }

}