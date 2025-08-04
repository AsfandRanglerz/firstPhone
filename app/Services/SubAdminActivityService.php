<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SubAdminActivityService
{
    public function log($targetId, $action, $description = null)
    {
        $performerId = Auth::guard('subadmin')->id(); // best practice: use facade

        ActivityLog::create([
            'sub_admin_id' => $targetId,
            'performed_by_sub_admin_id' => $performerId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}