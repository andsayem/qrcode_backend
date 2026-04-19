<?php

namespace App\Repositories;

use App\Models\CampaignDetails;
use App\Repositories\BaseRepository;

/**
 * Class CampaignDetailsRepository
 * @package App\Repositories
 * @version April 25, 2022, 1:55 pm +06
*/

class CampaignDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'campaign_id',
        'number_of_scan'
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
        return CampaignDetails::class;
    }
}
