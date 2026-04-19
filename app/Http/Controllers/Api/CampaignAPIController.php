<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCampaignAPIRequest;
use App\Http\Requests\API\UpdateCampaignAPIRequest;
use App\Models\Campaign;
use App\Repositories\CampaignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CampaignResource;
use Response;

/**
 * Class CampaignController
 * @package App\Http\Controllers\API
 */

class CampaignAPIController extends AppBaseController
{
    /** @var  CampaignRepository */
    private $campaignRepository;

    public function __construct(CampaignRepository $campaignRepo)
    {
        $this->campaignRepository = $campaignRepo;
    }

    /**
     * Display a listing of the Campaign.
     * GET|HEAD /campaigns
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

       
        $campaigns = $this->campaignRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(CampaignResource::collection($campaigns), 'Campaigns retrieved successfully');
    }
    public  function  All_Campaigns(Request $request){
        $campaigns = $this->campaignRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );
        
        return $this->sendResponse(CampaignResource::collection($campaigns), 'Campaign saved successfully'); 
    }
    /**
     * Store a newly created Campaign in storage.
     * POST /campaigns
     *
     * @param CreateCampaignAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCampaignAPIRequest $request)
    {
        $input = $request->all();

        $campaign = $this->campaignRepository->create($input);

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign saved successfully');
    }

    /**
     * Display the specified Campaign.
     * GET|HEAD /campaigns/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            return $this->sendError('Campaign not found');
        }

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign retrieved successfully');
    }

    /**
     * Update the specified Campaign in storage.
     * PUT/PATCH /campaigns/{id}
     *
     * @param int $id
     * @param UpdateCampaignAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCampaignAPIRequest $request)
    {
        $input = $request->all();

        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            return $this->sendError('Campaign not found');
        }

        $campaign = $this->campaignRepository->update($input, $id);

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign updated successfully');
    }

    /**
     * Remove the specified Campaign from storage.
     * DELETE /campaigns/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->find($id);

        if (empty($campaign)) {
            return $this->sendError('Campaign not found');
        }

        $campaign->delete();

        return $this->sendSuccess('Campaign deleted successfully');
    }
}
