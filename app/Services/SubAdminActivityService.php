<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SubAdminActivityService
{
 public function log($targetId, $action, $description = null)
{
    $performerId = Auth::guard('subadmin')->id();

    ActivityLog::create([
        'target_sub_admin_id' => $targetId, // ✅ correct column
        'performed_by_sub_admin_id' => $performerId,
        'action' => $action,
        'description' => $description,
    ]);
}

} 