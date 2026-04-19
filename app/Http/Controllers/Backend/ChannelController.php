<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateChannelRequest;
use App\Models\Channel;
use App\Http\Requests\UpdateChannelRequest;
use App\Repositories\ChannelRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class ChannelController extends AppBaseController
{
    /** @var ChannelRepository $channelRepository*/
    private $channelRepository;

    public function __construct(ChannelRepository $channelRepo)
    {
        $this->channelRepository = $channelRepo;
    }

    /**
     * Display a listing of the Channel.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $result  = Channel::all();  
        $data['channels'] =  $result ;
        return view('backend.channels.index')
            ->with($data);
        //return view('backend.product.index')->with($data);
    }

    /**
     * Show the form for creating a new Channel.
     *
     * @return Response
     */
    public function create()
    {
        return view('channels.create');
    }

    /**
     * Store a newly created Channel in storage.
     *
     * @param CreateChannelRequest $request
     *
     * @return Response
     */
    public function store(CreateChannelRequest $request)
    {
        $input = $request->all();

        $channel = $this->channelRepository->create($input);

        //Flash::success('Channel saved successfully.');
        // $data['channels']  $channel ;
        // return redirect(route('channels.index'));
        // return view('backend.product.index')->with($data);
    }

    /**
     * Display the specified Channel.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $channel = $this->channelRepository->find($id);

        if (empty($channel)) {
            //Flash::error('Channel not found');

            return redirect(route('channels.index'));
        }

        return view('channels.show')->with('channel', $channel);
    }

    /**
     * Show the form for editing the specified Channel.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $channel = $this->channelRepository->find($id);

        if (empty($channel)) {
            // Flash::error('Channel not found');

            return redirect(route('channels.index'));
        }

        return view('channels.edit')->with('channel', $channel);
    }

    /**
     * Update the specified Channel in storage.
     *
     * @param int $id
     * @param UpdateChannelRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChannelRequest $request)
    {
        $channel = $this->channelRepository->find($id);

        if (empty($channel)) {
            // Flash::error('Channel not found');

            return redirect(route('channels.index'));
        }

        $channel = $this->channelRepository->update($request->all(), $id);

        //Flash::success('Channel updated successfully.');

        return redirect(route('channels.index'));
    }

    /**
     * Remove the specified Channel from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $channel = $this->channelRepository->find($id);

        if (empty($channel)) {
            //Flash::error('Channel not found');

            return redirect(route('channels.index'));
        }

        $this->channelRepository->delete($id);

        //Flash::success('Channel deleted successfully.');

        return redirect(route('channels.index'));
    }
}
