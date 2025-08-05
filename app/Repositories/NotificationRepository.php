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

        if ($data['user_type'] === 'all') {
            // array of ['id' => 1, 'type' => 'users']
            foreach ($userIds as $user) {
                $modelClass = $user['type'] === 'users' ? User::class : Vendor::class;

                NotificationTarget::create([
                    'notification_id' => $notification->id,
                    'targetable_id' => $user['id'],
                    'targetable_type' => $modelClass,
                ]);
            }
        } else {
            // array of just IDs
            $modelClass = $data['user_type'] === 'users' ? User::class : Vendor::class;

            foreach ($userIds as $userId) {
                NotificationTarget::create([
                    'notification_id' => $notification->id,
                    'targetable_id' => $userId,
                    'targetable_type' => $modelClass,
                ]);
            }
        }
    }
}
