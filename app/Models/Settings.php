<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class Settings
 * @package App\Models
 * @version March 28, 2022, 12:21 pm +06
 *
 * @property string $min_redeem_point
 * @property string $point_rate
 */
class Settings extends Model
{


    public $table = 'settings';
    



    public $fillable = [
        'min_redeem_point', 
        'code_generator',
        'company_name',
        'contact_number',
        'email',
        'address'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'min_redeem_point' => 'string',
        'point_rate' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'min_redeem_point' => 'required',
        'point_rate' => 'required'
    ];
 

    public function pointrate()
    {
        return $this->hasMany(PointRateSetting::class,'setting_id', 'id');
    }

    
}
