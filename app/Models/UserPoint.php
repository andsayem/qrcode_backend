<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class UserPoint
 * @package App\Models
 * @version March 13, 2022, 4:44 pm +06
 *
 * @property integer $product_id
 * @property integer $user_id
 * @property string $point
 */
class UserPoint extends Model
{


    public $table = 'user_points';
    



    public $fillable = [
        'product_id',
        'user_id',
        'ssg_code_details',
        'point',
        'point_type',
        'note'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'integer',
        'user_id' => 'integer',
        'point' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'product_id' => 'required',
        'user_id' => 'required'
    ];
  
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function procode()
    {
        return $this->belongsTo(CodeDetail::class, 'ssg_code_details');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
        // return $this->belongsTo(User::class, 'mobile', 'email');
    }
 
    public function technician()
    {
        return $this->belongsTo(Technician::class, 'user_id', 'user_id');
    }
 
    
}
