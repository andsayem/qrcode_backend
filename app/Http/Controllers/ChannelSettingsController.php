<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChannelSettingsRequest;
use App\Http\Requests\UpdateChannelSettingsRequest;
use App\Repositories\ChannelSettingsRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Channel;
use App\Models\ChannelSettings;
use Illuminate\Http\Request;
use Flash;
use Response;

class ChannelSettingsController extends AppBaseController
{
    /** @var ChannelSettingsRepository $channelSettingsRepository*/
    private $channelSettingsRepository;

    public function __construct(ChannelSettingsRepository $channelSettingsRepo)
    {
        $this->channelSettingsRepository = $channelSettingsRepo;
    }

    /**
     * Display a listing of the ChannelSettings.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $channelSettings = $this->channelSettingsRepository->all();

        return view('channel_settings.index')
            ->with('channelSettings', $channelSettings);
    }

    /**
     * Show the form for creating a new ChannelSettings.
     *
     * @return Response
     */
    public function create()
    {
        return view('channel_settings.create');
    }

    /**
     * Store a newly created ChannelSettings in storage.
     *
     * @param CreateChannelSettingsRequest $request
     *
     * @return Response
     */
    public function store(CreateChannelSettingsRequest $request)
    {
        $input = $request->all();

        $channelSettings = $this->channelSettingsRepository->create($input);

        Flash::success('Channel Settings saved successfully.');

        return redirect(route('channelSettings.index'));
    }

    /**
     * Display the specified ChannelSettings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $channelSettings = $this->channelSettingsRepository->find($id);

        if (empty($channelSettings)) {
            Flash::error('Channel Settings not found');

            return redirect(route('channelSettings.index'));
        }

        return view('channel_settings.show')->with('channelSettings', $channelSettings);
    }

    /**
     * Show the form for editing the specified ChannelSettings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {

        //$channel = ChannelSettings::where('channel_id',$id)->first()->toArray();

        $data['channel'] =ChannelSettings::where('channel_id',$id)->first();

        // if (empty($channel)) {
        //     Flash::error('Channel not found');

        //     return redirect(route('admin.channels.index'));
        // }

        return view('channel_settings.edit')->with($data);
    }

    /**
     * Update the specified ChannelSettings in storage.
     *
     * @param int $id
     * @param UpdateChannelSettingsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChannelSettingsRequest $request)
    {
        $channelSettings = $this->channelSettingsRepository->find($id);

        if (empty($channelSettings)) {
            Flash::error('Channel Settings not found');

            return redirect(route('admin.channels.index'));
        }

        $channelSettings = $this->channelSettingsRepository->update($request->all(), $id);

        Flash::success('Channel Settings updated successfully.');

        return redirect(route('admin.channels.index'));
    }

    /**
     * Remove the specified ChannelSettings from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $channelSettings = $this->channelSettingsRepository->find($id);

        if (empty($channelSettings)) {
            Flash::error('Channel Settings not found');

            return redirect(route('channelSettings.index'));
        }

        $this->channelSettingsRepository->delete($id);

        Flash::success('Channel Settings deleted successfully.');

        return redirect(route('channelSettings.index'));
    }
}
