<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoDistrict extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'geo_district';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'country_id',
        'division_id',
        'district',
    ];

    protected $dates = [
        'deleted_at',
    ];

 
}