<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackReply extends Model
{
    use HasFactory;
    
    public $table = 'feedback_reply';
     
 
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    
    public $fillable = [
        'technician_id',
        'feedback_id',
        'complain',
        'picture',
        'sku'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = []; 

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}
