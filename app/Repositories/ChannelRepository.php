<?php

namespace App\Repositories;

use App\Models\Channel;
use App\Repositories\BaseRepository;

/**
 * Class ChannelRepository
 * @package App\Repositories
 * @version March 23, 2022, 9:55 am +06
*/

class ChannelRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
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
        return Channel::class;
    }
}
