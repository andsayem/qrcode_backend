<?php

namespace App\Repositories;

use App\Models\UserPoint;
use App\Repositories\BaseRepository;

/**
 * Class UserPointRepository
 * @package App\Repositories
 * @version March 13, 2022, 4:44 pm +06
*/

class UserPointRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'product_id',
        'user_id',
        'point'
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
        return UserPoint::class;
    }
}
