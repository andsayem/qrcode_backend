<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class Technician
 * @package App\Models
 * @version March 13, 2022, 5:21 pm +06
 *
 * @property integer $user_id
 * @property string $nid_font
 * @property string $nid_back
 * @property string $total_point
 * @property string $total_redeem_value
 * @property string $current_point
 */
class Technician extends Model
{


    public $table = 'technicians';


    public $fillable = [
        'user_id',
        'country_id',
        'division_id',
        'district_id',
        'upazilla_id',
        'union_id',
        'payment_gateway',
        'gatway_number',
        'nid_font',
        'nid_back',
        'total_point',
        'referral_code',
        'total_redeem_value',
        'current_point',
        'pending_point',
        'father_name',
        'permanent_address',
        'current_address',
        'birthday',
        'occupation',
        'nid_number',
        'blood_group',
        'experience',
        'dealer_code',
        'dealer_name',
        'organization',
        'zone',
        'company',
        'education',
        'update_status',
        'fo_code',
        'fo_name',
        'tsm_code',
        'tsm_name',
        'point_code',
        'point_name',
        'fo_verify',
        'tsm_verify',
        'point_verify',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'nid_font' => 'string',
        'nid_back' => 'string',
        'total_point' => 'double',
        'total_redeem_value' => 'double',
        'current_point' => 'double',
        'pending_point' => 'double'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required'
    ];
    public function user_info()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function division()
    {
        return $this->belongsTo(GeoDivision::class, 'division_id');
    }
}
