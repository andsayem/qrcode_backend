<?php

namespace App\Repositories;

use App\Models\UserRedeemRequest;
use App\Repositories\BaseRepository;

/**
 * Class UserRedeemRequestRepository
 * @package App\Repositories
 * @version March 13, 2022, 5:30 pm +06
*/

class UserRedeemRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'point',
        'amount',
        'details',
        'status'
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
        return UserRedeemRequest::class;
    }
}
