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
        $notification = Notification::create([
            'user_type' => $data['user_type'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        foreach ($userIds as $user) {
            $modelClass = $user['type'] === 'users' ? User::class : Vendor::class;

            NotificationTarget::create([
                'notification_id' => $notification->id,
                'targetable_id' => $user['id'],
                'targetable_type' => $modelClass,
            ]);
        }
    }
}
