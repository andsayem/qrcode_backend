<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Repositories\LearningRepository;
use App\Repositories\OfferRepository;
use Illuminate\Http\Request;

class OfferAPIController extends AppBaseController
{

    private $offerRepository;

    public function __construct(OfferRepository $_offerRepository)
    {
        $this->offerRepository = $_offerRepository;
    }

    public function index(Request $request)
    {

        $data = $this->offerRepository->allQuery()
            ->where('is_active',1)
            ->orderBy('id','desc')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'point_value' => $item->point_value,
                    'image' => asset('storage/offer/'.$item->image),
                ];
            });

        return $this->sendResponse($data, 'Offer data retrieved successfully');
    }
}