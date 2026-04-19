<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\API\CreateFeedbackAPIRequest;
use App\Http\Requests\API\UpdateFeedbackAPIRequest;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Repositories\FeedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\FeedbackResource;
use Response;

/**
 * Class FeedbackController
 * @package App\Http\Controllers\API
 */

class FeedbackController extends AppBaseController
{
    /** @var  FeedbackRepository */
    private $feedbackRepository;

    public function __construct(FeedbackRepository $feedbackRepo)
    {
        $this->feedbackRepository = $feedbackRepo;
    }

    /**
     * Display a listing of the Feedback.
     * GET|HEAD /feedback
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $data['feedback'] = $this->feedbackRepository->all();



        $data['feedback'] = $this->feedbackRepository->allQuery()->with('technician.user_info')->orderBy('id','desc')->paginate(10); //->orderBy($columns[$column], $dir); 
        
        return view('backend.feedback.index')->with($data);
        // $feedback = $this->feedbackRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        // return $this->sendResponse(FeedbackResource::collection($feedback), 'Feedback retrieved successfully');
    }
    public function reply($id, Request $request)
    { 
        $data['feedback'] = $this->feedbackRepository->allQuery()->where('id', $id)->with('technician.user_info')->orderBy('id','desc')->first(); //->orderBy($columns[$column], $dir); 
        
        return view('backend.feedback.reply')->with($data); 
    }

    public function storeReply(Request $request){
        $feedbackReplay = new FeedbackReply();
        $feedbackReplay->status = FeedbackReply::STATUS_PENDING;
        $feedbackReplay->save();
    }

    /**
     * Store a newly created Feedback in storage.
     * POST /feedback
     *
     * @param CreateFeedbackAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFeedbackAPIRequest $request)
    {
        $input = $request->all();

        $feedback = $this->feedbackRepository->create($input);

        return $this->sendResponse(new FeedbackResource($feedback), 'Feedback saved successfully');
    }

    /**
     * Display the specified Feedback.
     * GET|HEAD /feedback/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Feedback $feedback */
        $feedback = $this->feedbackRepository->find($id);

        if (empty($feedback)) {
            return $this->sendError('Feedback not found');
        }

        return $this->sendResponse(new FeedbackResource($feedback), 'Feedback retrieved successfully');
    }

    /**
     * Update the specified Feedback in storage.
     * PUT/PATCH /feedback/{id}
     *
     * @param int $id
     * @param UpdateFeedbackAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFeedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var Feedback $feedback */
        $feedback = $this->feedbackRepository->find($id);

        if (empty($feedback)) {
            return $this->sendError('Feedback not found');
        }

        $feedback = $this->feedbackRepository->update($input, $id);

        return $this->sendResponse(new FeedbackResource($feedback), 'Feedback updated successfully');
    }

    /**
     * Remove the specified Feedback from storage.
     * DELETE /feedback/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Feedback $feedback */
        $feedback = $this->feedbackRepository->find($id);

        if (empty($feedback)) {
            return $this->sendError('Feedback not found');
        }

        $feedback->delete();

        return $this->sendSuccess('Feedback deleted successfully');
    }
}
