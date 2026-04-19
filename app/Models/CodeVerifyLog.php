<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeVerifyLog extends Model
{
    use HasFactory;

    protected $table = 'code_verify_logs';
    protected $guarded = ['id'];
}
