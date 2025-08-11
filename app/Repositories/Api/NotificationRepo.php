<?php

namespace App\Repositories\Api;

use App\Models\Notification;
use App\Models\NotificationTarget;
use App\Repositories\Api\Interfaces\NotificationRepoInterface;

class NotificationRepo implements NotificationRepoInterface
{
    public function getUserNotifications($user)
    {
        return Notification::whereHas('targets', function ($q) use ($user) {
                $q->where('targetable_id', $user->id)
                  ->where('targetable_type', get_class($user));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsSeen($user, $notificationId)
    {
        $notification = Notification::find($notificationId);

        if (!$notification) {
            return ['status' => false, 'message' => 'Notification not found'];
        }

        $target = NotificationTarget::where('notification_id', $notificationId)
            ->where('targetable_id', $user->id)
            ->where('targetable_type', get_class($user))
            ->first();

        if ($target) {
            $target->seen = true;
            $target->save();
        }

        return ['status' => true, 'seen' => $target->seen ?? false];
    }
}
