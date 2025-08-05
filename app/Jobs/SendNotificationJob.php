<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use App\Models\Vendor;
use App\Models\NotificationTarget;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;
    protected array $userIds;

    public function __construct(array $data, array $userIds)
    {
        $this->data = $data;
        $this->userIds = $userIds;
    }

    public function handle(): void
    {
        $notification = Notification::create([
            'user_type' => $this->data['user_type'],
            'title' => $this->data['title'],
            'description' => $this->data['description'],
        ]);

        foreach ($this->userIds as $user) {
            // Determine model class and ID
            if (is_array($user) && isset($user['id'], $user['type'])) {
                $modelClass = $user['type'] === 'users' ? User::class : Vendor::class;
                $model = $modelClass::find($user['id']);
            } elseif (is_numeric($user)) {
                $modelClass = $this->data['user_type'] === 'users' ? User::class : Vendor::class;
                $model = $modelClass::find($user);
            } else {
                continue;
            }

            if (!$model) {
                continue; // skip if not found
            }

            // Save target
            NotificationTarget::create([
                'notification_id' => $notification->id,
                'targetable_id' => $model->id,
                'targetable_type' => $modelClass,
            ]);

            // Send FCM notification if token exists
            if (!empty($model->fcm_token)) {
                try {
                    NotificationHelper::sendFcmNotification(
                        $model->fcm_token,
                        $this->data['title'],
                        $this->data['description'],
                        [
                            'user_type' => $this->data['user_type'],
                            'notification_id' => $notification->id,
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::error('FCM sending failed: ' . $e->getMessage());
                }
            }
        }
    }
}
