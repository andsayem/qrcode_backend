<?php

namespace App\Models;
use App\Models\User;
use App\Models\Product;
use App\Models\CodeDetail;
use Illuminate\Database\Eloquent\Model;

class SSGCodeDetail extends Model
{
    protected $table = 'ssg_code_details';
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
    public function codeDetail()
    {
        return $this->belongsTo(CodeDetail::class, 'serial', 'serial');
    }    

    public function user()
    {
        return $this->belongsTo(User::class, 'mobile', 'email');
        // return $this->belongsTo(User::class, 'mobile', 'email');
    }
}
