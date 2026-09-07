<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoUpazila extends Model
{
   
    use HasFactory;

    protected $table = 'geo_thana';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'district_id',
        'thana',
    ];

}