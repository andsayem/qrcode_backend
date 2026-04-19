<?php

namespace App\Repositories;

use App\Models\CampaignCategory;
use App\Repositories\BaseRepository;

/**
 * Class CampaignCategoryRepository
 * @package App\Repositories
 * @version April 2, 2022, 12:40 pm +06
*/

class CampaignCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'details'
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
        return CampaignCategory::class;
    }
}
