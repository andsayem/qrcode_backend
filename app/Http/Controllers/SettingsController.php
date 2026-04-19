<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Repositories\SettingsRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Backend\UserController;
use App\Models\PointRateSetting;
use Illuminate\Http\Request;
use Flash;
use Response;

class SettingsController extends AppBaseController
{
    /** @var SettingsRepository $settingsRepository*/
    private $settingsRepository;

    public function __construct(SettingsRepository $settingsRepo)
    {
        $this->settingsRepository = $settingsRepo;
    }

    /**
     * Display a listing of the Settings.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data['settings'] = $this->settingsRepository->all(); 
        return view('backend.settings.index')->with($data); 
    } 

    /**
     * Show the form for creating a new Settings.
     *
     * @return Response
     */
    public function create()
    {
        return view('settings.create');
    }

    /**
     * Store a newly created Settings in storage.
     *
     * @param CreateSettingsRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingsRequest $request)
    {
        $input = $request->all();

        $settings = $this->settingsRepository->create($input);

        Flash::success('Settings saved successfully.');

        return redirect(route('backend.settings.index'));
    }

    /**
     * Display the specified Settings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settings = $this->settingsRepository->find($id);

        if (empty($settings)) {
            Flash::error('Settings not found');

            return redirect(route('settings.index'));
        }

        return view('settings.show')->with('settings', $settings);
    }

    /**
     * Show the form for editing the specified Settings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id, Request $request)
    { 
        $settings = $this->settingsRepository->find($id);
        $data['settings'] = $settings;
        $userController = new UserController($request);

        $data['countries'] = $userController->getSsforceCountry();

        if (empty($settings)) {
            Flash::error('Settings not found');

            return redirect(route('settings.index'));
        } 

        return view('backend.settings.edit')->with($data);
    }

    /**
     * Update the specified Settings in storage.
     *
     * @param int $id
     * @param UpdateSettingsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingsRequest $request)
    {
        $point_rates = array();
        $settings = $this->settingsRepository->find($id);

        if (empty($settings)) {
            Flash::error('Settings not found');

            return redirect(route('admin.settings.index'));
        }

        $settings = $this->settingsRepository->update($request->all(), $id);
        foreach ($request->country_id as $key => $country_id) {   
            $pointRate = [
                'country_id' => $country_id,  
                'point_rate' => $request->point_rate[$key],
            ];
        
            // Use updateOrCreate to update or insert records based on 'country_id'
            $settings->pointRate()->updateOrCreate(
                ['setting_id' => $settings->id, 'country_id' => $country_id],
                $pointRate
            );
        }
        Flash::success('Settings updated successfully.');

        return redirect(route('admin.settings.index'));
    }

    /**
     * Remove the specified Settings from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settings = $this->settingsRepository->find($id);

        if (empty($settings)) {
            Flash::error('Settings not found');

            return redirect(route('settings.index'));
        }

        $this->settingsRepository->delete($id);

        Flash::success('Settings deleted successfully.');

        return redirect(route('settings.index'));
    }
}
