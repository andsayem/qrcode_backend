<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'ssg_code_details';
    protected $guarded = ['id'];
    // protected $fillable = ['users'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'code_used_time', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id', 'name');
    }

    public function technicians()
    {
        return $this->belongsTo(User::class, 'mobile' , 'email');
    }
}
