<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCampaignCategoryAPIRequest;
use App\Http\Requests\API\UpdateCampaignCategoryAPIRequest;
use App\Models\CampaignCategory;
use App\Repositories\CampaignCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CampaignCategoryResource;
use Response;

/**
 * Class CampaignCategoryController
 * @package App\Http\Controllers\API
 */

class CampaignCategoryAPIController extends AppBaseController
{
    /** @var  CampaignCategoryRepository */
    private $campaignCategoryRepository;

    public function __construct(CampaignCategoryRepository $campaignCategoryRepo)
    {
        $this->campaignCategoryRepository = $campaignCategoryRepo;
    }

    /**
     * Display a listing of the CampaignCategory.
     * GET|HEAD /campaignCategories
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $campaignCategories = $this->campaignCategoryRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(CampaignCategoryResource::collection($campaignCategories), 'Campaign Categories retrieved successfully');
    }

    /**
     * Store a newly created CampaignCategory in storage.
     * POST /campaignCategories
     *
     * @param CreateCampaignCategoryAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCampaignCategoryAPIRequest $request)
    {
        $input = $request->all();

        $campaignCategory = $this->campaignCategoryRepository->create($input);

        return $this->sendResponse(new CampaignCategoryResource($campaignCategory), 'Campaign Category saved successfully');
    }

    /**
     * Display the specified CampaignCategory.
     * GET|HEAD /campaignCategories/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CampaignCategory $campaignCategory */
        $campaignCategory = $this->campaignCategoryRepository->find($id);

        if (empty($campaignCategory)) {
            return $this->sendError('Campaign Category not found');
        }

        return $this->sendResponse(new CampaignCategoryResource($campaignCategory), 'Campaign Category retrieved successfully');
    }

    /**
     * Update the specified CampaignCategory in storage.
     * PUT/PATCH /campaignCategories/{id}
     *
     * @param int $id
     * @param UpdateCampaignCategoryAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCampaignCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CampaignCategory $campaignCategory */
        $campaignCategory = $this->campaignCategoryRepository->find($id);

        if (empty($campaignCategory)) {
            return $this->sendError('Campaign Category not found');
        }

        $campaignCategory = $this->campaignCategoryRepository->update($input, $id);

        return $this->sendResponse(new CampaignCategoryResource($campaignCategory), 'CampaignCategory updated successfully');
    }

    /**
     * Remove the specified CampaignCategory from storage.
     * DELETE /campaignCategories/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CampaignCategory $campaignCategory */
        $campaignCategory = $this->campaignCategoryRepository->find($id);

        if (empty($campaignCategory)) {
            return $this->sendError('Campaign Category not found');
        }

        $campaignCategory->delete();

        return $this->sendSuccess('Campaign Category deleted successfully');
    }
}
