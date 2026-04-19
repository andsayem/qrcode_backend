<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianNominee extends Model
{
    use HasFactory;

    // Table name (optional যদি Laravel convention মানো)
    protected $table = 'technician_nominees';

    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'nominee_name',
        'relation',
        'nominee_address',
        'amount_percentage',
        'national_id_no',
    ];

    /**
     * Technician relation (user)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
