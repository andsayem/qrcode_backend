<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;



/**
 * Class Feedback
 * @package App\Models
 * @version February 13, 2024, 12:55 pm +06
 *
 * @property integer $technician_id
 * @property string $complain
 * @property varcher $picture
 * @property string $sku
 */
class Feedback extends Model
{


    public $table = 'feedback';
    

 
    public $fillable = [
        'technician_id',
        'complain',
        'picture',
        'sku'
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

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    
}
