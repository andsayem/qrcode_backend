<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class Channel
 * @package App\Models
 * @version March 23, 2022, 9:55 am +06
 *
 * @property string $name
 * @property integer $status
 */
class Channel extends Model
{


    public $table = 'channels';
    
 
    public $fillable = [
        'name',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
