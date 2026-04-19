<?php

namespace App\Repositories;

use App\Models\Technician;
use App\Repositories\BaseRepository;

/**
 * Class TechnicianRepository
 * @package App\Repositories
 * @version March 13, 2022, 5:21 pm +06
*/

class TechnicianRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'nid_font',
        'nid_back',
        'total_point',
        'total_redeem_value',
        'current_point'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Technician::class;
    }
}
