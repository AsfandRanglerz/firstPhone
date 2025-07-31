<?php

namespace App\Observers;

use App\Models\User;
use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->logActivity('created', $user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->logActivity('updated', $user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->logActivity('deleted', $user);
    }

    /**
     * Log activity for admin or subadmin users.
     */
   protected function logActivity(string $action, User $targetUser): void
{
    $authUser = Auth::guard('subadmin')->user();

        LogActivity::create([
            'performed_by' => $authUser->id,
            'role'         => $role,
            'action'       => $action,
            'model'        => 'User',
            'model_id'     => $targetUser->id,
            'description'  => ucfirst($role) . " ({$authUser->name}) {$action} user ({$targetUser->name})",
        ]);
    }
}