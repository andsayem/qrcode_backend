<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory; 

    protected $fillable = ['technician_id', 'complain', 'picture', 'sku'];

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}
