<?php

namespace App\Repositories;

use App\Models\ChannelSettings;
use App\Repositories\BaseRepository;

/**
 * Class ChannelSettingsRepository
 * @package App\Repositories
 * @version March 23, 2022, 12:55 pm +06
*/

class ChannelSettingsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'channel_id',
        'slab_value'
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
        return ChannelSettings::class;
    }
}
