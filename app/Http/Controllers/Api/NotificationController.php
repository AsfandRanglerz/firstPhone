<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationTarget;
use App\Repositories\Api\Interfaces\NotificationRepoInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationRepository;
    

    public function __construct(NotificationRepoInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;

    }

    public function index(Request $request)
    {
        try {
            $user = auth()->guard('vendors')->user() ?? auth()->user();
            if (!$user) {
                return ResponseHelper::error(null, 'Unauthorized', 'error', 401);
            }
            $notifications = $this->notificationRepository->getUserNotifications($user);
            return ResponseHelper::success($notifications, 'Notifications fetched successfully', 'success', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Failed to fetch notifications', 'error', 500);
        }
    }


    public function seenNotification(Request $request)
    {
        try {
            $user = auth()->guard('vendors')->user() ?? auth()->user();

            if (!$user) {
                return ResponseHelper::error(null, 'Unauthorized', 'error', 401);
            }
            $notificationId = $request->input('notification_id');
            $result = $this->notificationRepository->markAsSeen($user, $notificationId);
            if (!$result['status']) {
                return ResponseHelper::error(null, $result['message'], 'error', 404);
            }
            return ResponseHelper::success(['seen' => $result['seen']], 'Notification marked as seen', 'success', 200);

        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Failed to mark notification as seen', 'error', 500);
        }
    }
}
