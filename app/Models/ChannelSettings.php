<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class ChannelSettings
 * @package App\Models
 * @version March 23, 2022, 12:55 pm +06
 *
 * @property integer $channel_id
 * @property string $slab_value
 */
class ChannelSettings extends Model
{


    public $table = 'channel_settings';
    



    public $fillable = [
        'channel_id',
        'slab_value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'channel_id' => 'integer',
        'slab_value' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
