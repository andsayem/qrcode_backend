<?php

namespace App\Repositories;

use DB;
use Spatie\Permission\Models\Role;

class CommonRepository
{
    public function userTypeList()
    {
        return Role::pluck('name', 'id');
    }

}
