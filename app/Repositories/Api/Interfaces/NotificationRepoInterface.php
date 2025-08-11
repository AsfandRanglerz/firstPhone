<?php

namespace App\Repositories\Api\Interfaces;

interface NotificationRepoInterface
{
    public function getUserNotifications($user);
    public function markAsSeen($user, $notificationId);

}
