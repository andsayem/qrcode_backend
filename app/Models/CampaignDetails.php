<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class CampaignDetails
 * @package App\Models
 * @version April 25, 2022, 1:55 pm +06
 *
 * @property integer $user_id
 * @property integer $campaign_id
 * @property integer $number_of_scan
 */
class CampaignDetails extends Model
{


    public $table = 'campaign_details';
    



    public $fillable = [
        'user_id',
        'campaign_id',
        'number_of_scan'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'campaign_id' => 'integer',
        'number_of_scan' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
