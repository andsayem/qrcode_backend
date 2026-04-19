<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class CampaignCategory
 * @package App\Models
 * @version April 2, 2022, 12:40 pm +06
 *
 * @property string $name
 * @property string $details
 */
class CampaignCategory extends Model
{


    public $table = 'campaign_categories';
    



    public $fillable = [
        'name',
        'details'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'details' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];

    
}
