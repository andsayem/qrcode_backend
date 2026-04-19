<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'is_active'
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
        return Offer::class;
    }
}