<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFeedbackAPIRequest;
use App\Http\Requests\API\UpdateFeedbackAPIRequest;
use App\Models\Feedback;
use App\Models\Technician;
use App\Repositories\FeedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\FeedbackResource;
use Response;
use Auth;

/**
 * Class FeedbackController
 * @package App\Http\Controllers\API
 */

class FeedbackAPIController extends AppBaseController
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
        $user_data = Auth::user();
        $technicianinfo = Technician::where('user_id', $user_data->id)->first(); 
        // $technicianinfo = Technician::where('user_id', 495)->first();
        $feedback = $this->feedbackRepository->allQuery()->when($technicianinfo, function($q, $technicianinfo){
            return $q->where('technician_id', $technicianinfo->id);
        })->get();
        // ->where('technician_id', auth()->user()->id)
        // $feedback = $this->feedbackRepository->allQuery()

        return $this->sendResponse($feedback, 'Feedback retrieved successfully');
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

        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $fileExt    = $file->getClientOriginalExtension();
            $fileName   = $this->uploadFile($file, 'feedback', 'feedback'); 
            $input['picture'] = $fileName;
        }

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
