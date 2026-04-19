<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Technician;
use App\Repositories\LearningRepository;
use Illuminate\Http\Request;

class LearningAndTutorialAPIController extends AppBaseController
{
    private $learningRepository;

    public function __construct(LearningRepository $_learningRepository)
    {
        $this->learningRepository = $_learningRepository;
    }

    public function index(Request $request)
    {

        $data = $this->learningRepository->allQuery()->with('images')
            ->where('is_active',1)
            ->orderBy('id','desc')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'type' => $item->type,
                    'link' => $item->path,
                    'images' => $item->images->map(function ($img) {
                        return [
                            'id' => $img->id,
                            'image' =>asset('storage/' . $img->path),
                        ];
                    })
                ];
            });

        return $this->sendResponse($data, 'Learning and Tutorial retrieved successfully');
    }

}