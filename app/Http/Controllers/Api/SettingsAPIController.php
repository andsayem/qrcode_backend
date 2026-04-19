<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingsAPIRequest;
use App\Http\Requests\API\UpdateSettingsAPIRequest;
use App\Models\Settings;
use App\Models\PointRateSetting;
use App\Models\Technician;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\SettingsResource;
use Response;
use Auth;

/**
 * Class SettingsController
 * @package App\Http\Controllers\API
 */

class SettingsAPIController extends AppBaseController
{
    /** @var  SettingsRepository */
    private $settingsRepository;

    public function __construct(SettingsRepository $settingsRepo)
    {
        $this->settingsRepository = $settingsRepo;
    }

    /**
     * Display a listing of the Settings.
     * GET|HEAD /settings
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $settings = $this->settingsRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        // return $this->sendResponse(SettingsResource::collection($settings), 'Settings retrieved successfully');


        $user_data = Auth::user();
        $technicianinfo = Technician::where('user_id', $user_data->id)->first();
        $result = PointRateSetting::with('settings')->where('country_id', $technicianinfo->country_id)->first();  
        if($technicianinfo->country_id == 1){
            $result['currency'] = 'BDT';
        }else{
            $result['currency'] = 'INR';
        }
        
        return $this->sendResponse($result, 'Settings retrieved successfully');



        // $technicianinfo = Technician::where('user_id', 4817)->first();
        // $result = PointRateSetting::with('settings')->where('country_id', $technicianinfo->country_id)->first();  
        // if($technicianinfo->country_id == 1){
        //     $result['currency'] = 'BDT';
        // }else{
        //     $result['currency'] = 'INR';
        // }
        

    }

    /**
     * Store a newly created Settings in storage.
     * POST /settings
     *
     * @param CreateSettingsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingsAPIRequest $request)
    {
        $input = $request->all();

        $settings = $this->settingsRepository->create($input);

        return $this->sendResponse(new SettingsResource($settings), 'Settings saved successfully');
    }

    /**
     * Display the specified Settings.
     * GET|HEAD /settings/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->find($id);

        if (empty($settings)) {
            return $this->sendError('Settings not found');
        }

        return $this->sendResponse(new SettingsResource($settings), 'Settings retrieved successfully');
    }

    /**
     * Update the specified Settings in storage.
     * PUT/PATCH /settings/{id}
     *
     * @param int $id
     * @param UpdateSettingsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Settings $settings */
        $settings = $this->settingsRepository->find($id);

        if (empty($settings)) {
            return $this->sendError('Settings not found');
        }

        $settings = $this->settingsRepository->update($input, $id);

        return $this->sendResponse(new SettingsResource($settings), 'Settings updated successfully');
    }

    /**
     * Remove the specified Settings from storage.
     * DELETE /settings/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->find($id);

        if (empty($settings)) {
            return $this->sendError('Settings not found');
        }

        $settings->delete();

        return $this->sendSuccess('Settings deleted successfully');
    }
}
