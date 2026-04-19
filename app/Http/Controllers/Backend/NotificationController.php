<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\API\CreateNotificationAPIRequest;
use App\Http\Requests\API\UpdateNotificationAPIRequest;
use App\Models\Notification; 
use App\Models\Technician;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\NotificationResource;
use Response;

/**
 * Class NotificationController
 * @package App\Http\Controllers\API
 */

class NotificationController extends AppBaseController
{
    /** @var  NotificationRepository */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Notification.
     * GET|HEAD /Notification
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    { 
        $data['notifications'] = $this->notificationRepository->allQuery()->with('user')->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('backend.notifications.index')->with($data); 
    } 



    public function create(){
        // if (!auth()->user()->can('notification-create')) {
        //     abort(403);
        // } 
        $data['technician'] = Technician::select('users.id', 'users.name')->join('users', 'users.id', '=', 'technicians.user_id')->get();
        return view('backend.notifications.create')->with($data);
    }

    public function store(CreateNotificationAPIRequest $request)
    {
        $input = $request->all();
        if($request->get('type') == 'all'){
            $input['user_id'] = NULL;  
        } 
        $input['module'] = 'general';

        $Notification = $this->notificationRepository->create($input);

        return redirect(url('admin/notification'));

        //return $this->sendResponse(new NotificationResource($Notification), 'Notification saved successfully');
    }

    /**
     * Display the specified Notification.
     * GET|HEAD /Notification/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Notification $Notification */
        $Notification = $this->notificationRepository->find($id);

        if (empty($Notification)) {
            return $this->sendError('Notification not found');
        }

        return $this->sendResponse(new NotificationResource($Notification), 'Notification retrieved successfully');
    }

    /**
     * Update the specified Notification in storage.
     * PUT/PATCH /Notification/{id}
     *
     * @param int $id
     * @param UpdateNotificationAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotificationAPIRequest $request)
    {
        $input = $request->all();

        /** @var Notification $Notification */
        $Notification = $this->notificationRepository->find($id);

        if (empty($Notification)) {
            return $this->sendError('Notification not found');
        }

        $Notification = $this->notificationRepository->update($input, $id);

        return $this->sendResponse(new NotificationResource($Notification), 'Notification updated successfully');
    }

    /**
     * Remove the specified Notification from storage.
     * DELETE /Notification/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Notification $Notification */
        $Notification = $this->notificationRepository->find($id);

        if (empty($Notification)) {
            return $this->sendError('Notification not found');
        }

        $Notification->delete();

        return $this->sendSuccess('Notification deleted successfully');
    }
}
