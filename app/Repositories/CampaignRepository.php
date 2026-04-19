<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Repositories\BaseRepository;

/**
 * Class CampaignRepository
 * @package App\Repositories
 * @version April 2, 2022, 12:44 pm +06
*/

class CampaignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'campaign_category_id',
        'start_date',
        'end_date',
        'product_id',
        'point',
        'title',
        'image'
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
        return Campaign::class;
    }
}
