<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCampaignDetailsAPIRequest;
use App\Http\Requests\API\UpdateCampaignDetailsAPIRequest;
use App\Models\CampaignDetails;
use App\Repositories\CampaignDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CampaignDetailsResource;
use Response;

/**
 * Class CampaignDetailsController
 * @package App\Http\Controllers\API
 */

class CampaignDetailsAPIController extends AppBaseController
{
    /** @var  CampaignDetailsRepository */
    private $campaignDetailsRepository;

    public function __construct(CampaignDetailsRepository $campaignDetailsRepo)
    {
        $this->campaignDetailsRepository = $campaignDetailsRepo;
    }

    /**
     * Display a listing of the CampaignDetails.
     * GET|HEAD /campaignDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $campaignDetails = $this->campaignDetailsRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(CampaignDetailsResource::collection($campaignDetails), 'Campaign Details retrieved successfully');
    }

    /**
     * Store a newly created CampaignDetails in storage.
     * POST /campaignDetails
     *
     * @param CreateCampaignDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCampaignDetailsAPIRequest $request)
    {
        $input = $request->all();

        $campaignDetails = $this->campaignDetailsRepository->create($input);

        return $this->sendResponse(new CampaignDetailsResource($campaignDetails), 'Campaign Details saved successfully');
    }

    /**
     * Display the specified CampaignDetails.
     * GET|HEAD /campaignDetails/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CampaignDetails $campaignDetails */
        $campaignDetails = $this->campaignDetailsRepository->find($id);

        if (empty($campaignDetails)) {
            return $this->sendError('Campaign Details not found');
        }

        return $this->sendResponse(new CampaignDetailsResource($campaignDetails), 'Campaign Details retrieved successfully');
    }

    /**
     * Update the specified CampaignDetails in storage.
     * PUT/PATCH /campaignDetails/{id}
     *
     * @param int $id
     * @param UpdateCampaignDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCampaignDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CampaignDetails $campaignDetails */
        $campaignDetails = $this->campaignDetailsRepository->find($id);

        if (empty($campaignDetails)) {
            return $this->sendError('Campaign Details not found');
        }

        $campaignDetails = $this->campaignDetailsRepository->update($input, $id);

        return $this->sendResponse(new CampaignDetailsResource($campaignDetails), 'CampaignDetails updated successfully');
    }

    /**
     * Remove the specified CampaignDetails from storage.
     * DELETE /campaignDetails/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CampaignDetails $campaignDetails */
        $campaignDetails = $this->campaignDetailsRepository->find($id);

        if (empty($campaignDetails)) {
            return $this->sendError('Campaign Details not found');
        }

        $campaignDetails->delete();

        return $this->sendSuccess('Campaign Details deleted successfully');
    }
}
