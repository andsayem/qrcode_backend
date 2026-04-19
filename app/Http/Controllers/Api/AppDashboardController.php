<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTechnicianAPIRequest;
use App\Http\Requests\API\UpdateTechnicianAPIRequest;
use App\Models\Technician;
use App\Repositories\TechnicianRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\TechnicianResource;
use Response;
use Auth, DB;

/**
 * Class TechnicianController
 * @package App\Http\Controllers\API
 */

class AppDashboardController extends AppBaseController
{
    /** @var  TechnicianRepository */
    private $technicianRepository;

    public function __construct(TechnicianRepository $technicianRepo)
    {
        $this->technicianRepository = $technicianRepo;
    }

    /**
     * Display a listing of the Technician.
     * GET|HEAD /technicians
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $technicians = $this->technicianRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        $user_data = Auth::user(); 
        $technicians = Technician::where('user_id',$user_data->id)->get(); 
        //return $this->sendResponse( New TechnicianResource($technicians), 'Technicians retrieved successfully');
         return $this->sendResponse(TechnicianResource::collection($technicians) , 'Technicians retrieved successfully');
    }

    /**
     * Store a newly created Technician in storage.
     * POST /technicians
     *
     * @param CreateTechnicianAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTechnicianAPIRequest $request)
    {
        $input = $request->all();

        $technician = $this->technicianRepository->create($input);

        return $this->sendResponse(new TechnicianResource($technician), 'Technician saved successfully');
    }

    /**
     * Display the specified Technician.
     * GET|HEAD /technicians/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Technician $technician */
        $technician = $this->technicianRepository->find($id);

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        return $this->sendResponse(new TechnicianResource($technician), 'Technician retrieved successfully');
    }

    /**
     * Update the specified Technician in storage.
     * PUT/PATCH /technicians/{id}
     *
     * @param int $id
     * @param UpdateTechnicianAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTechnicianAPIRequest $request)
    {
        $input = $request->all();

        /** @var Technician $technician */
        $technician = $this->technicianRepository->find($id);

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        $technician = $this->technicianRepository->update($input, $id);

        return $this->sendResponse(new TechnicianResource($technician), 'Technician updated successfully');
    }

    /**
     * Remove the specified Technician from storage.
     * DELETE /technicians/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Technician $technician */
        $technician = $this->technicianRepository->find($id);

        if (empty($technician)) {
            return $this->sendError('Technician not found');
        }

        $technician->delete();

        return $this->sendSuccess('Technician deleted successfully');
    }
}
