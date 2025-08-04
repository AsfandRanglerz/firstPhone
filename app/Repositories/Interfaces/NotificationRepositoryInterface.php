<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    public function createNotification(array $data, array $userIds): void;
}