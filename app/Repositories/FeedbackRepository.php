<?php

namespace App\Repositories;

use App\Models\Feedback;
use App\Repositories\BaseRepository;

/**
 * Class FeedbackRepository
 * @package App\Repositories
 * @version February 13, 2024, 12:55 pm +06
*/

class FeedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'technician_id',
        'complain',
        'picture',
        'sku'
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
        return Feedback::class;
    }
}
