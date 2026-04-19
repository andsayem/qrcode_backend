<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Scopes\CodeRequestScope;
use App\Models\CodeDetail;
use Illuminate\Database\Eloquent\Model;

class RequestCode extends Model
{
    protected $table = 'request_codes';
    protected $guarded = ['id'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new CodeRequestScope());
    }
    public function  printNumber(){
        return $this->belongsTo(CodeDetail::class, 'id', 'request_code_id');
        //return 1;
        //CodeDetail::where('request_code_id',$this->id)->where('is_print',1)->count();
    }


}
