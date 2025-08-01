<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Vendor;
use App\Models\NotificationTarget;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function createNotification(array $data, array $userIds): void
    {
        // Identify model class based on user_type
        $modelClass = $data['user_type'] === 'customers' ? User::class : Vendor::class;

        // Save notification
        $notification = Notification::create([
            'user_type' => $data['user_type'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        // Link targets
        foreach ($userIds as $userId) {
            NotificationTarget::create([
                'notification_id' => $notification->id,
                'targetable_id' => $userId,
                'targetable_type' => $modelClass,
            ]);
        }
    }
}
