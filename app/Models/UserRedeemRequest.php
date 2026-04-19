<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class UserRedeemRequest
 * @package App\Models
 * @version March 13, 2022, 5:30 pm +06
 *
 * @property integer $user_id
 * @property string $point
 * @property string $amount
 * @property string $details
 * @property integer $status
 */
class UserRedeemRequest extends Model
{


    public $table = 'user_redeem_requests';
    



    public $fillable = [
        'user_id',
        'point',
        'payment_gateway',
        'gateway_number',
        'gatway_number',
        'amount',
        'details',
        'status', 
        'db_pay_status',
        'opt_code',
        'sender_sap_code',
        'otp_send_time',
        'sender_info',
        'paid_at', 
        'note'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'point' => 'string',
        'amount' => 'string',
        'details' => 'string',
        'status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        //'user_id' => 'required',
        'point' => 'required',
        //'amount' => 'required',
        //'status' => 'required'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'user_id', 'user_id');
    }
}
