<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class CodeDetailSummary extends Model
{
    protected $table = 'code_details_summary';
    protected $guarded = ['id'];

}
