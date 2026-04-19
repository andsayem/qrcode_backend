<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoDivision extends Model
{
    use SoftDeletes;

    protected $table = 'geo_divisions';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'country_id',
        'name',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // One division has many technicians
    public function technicians()
    {
        return $this->hasMany(Technician::class, 'division_id');
    }
}