<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class Notification
 * @package App\Models
 * @version March 13, 2024, 9:59 am +06
 *
 */
class Notification extends Model
{


    public $table = 'notifications';
    

    public $fillable = [
        'user_id','messages','module','type','status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public static function getNotifications()
    { 
        return self::orderBy('id', 'desc')->paginate(10);
    }
    
}
