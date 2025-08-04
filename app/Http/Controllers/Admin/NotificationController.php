<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\SubAdmin;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Jobs\NotificationJob;
use App\Models\AdminNotification;
use App\Models\UserRolePermission;
use App\Models\Vendor;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Models\NotificationTarget;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{

    protected NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function index()
    {
        $notifications = Notification::latest()->get();
        $users = User::all();
        $subadmin = SubAdmin::all();
        $vendors = Vendor::all();

        $sideMenuPermissions = collect();

        if (!Auth::guard('admin')->check()) {

            $user = Auth::guard('subadmin')->user()->load('roles');

            // ✅ 1. Get role_id of subadmin
            $roleId = $user->role_id;

            // ✅ 2. Get all permissions assigned to this role
            $permissions = UserRolePermission::with(['permission', 'sideMenue'])
                ->where('role_id', $roleId)
                ->get();

            // ✅ 3. Group permissions by side menu
            $sideMenuPermissions = $permissions->groupBy('sideMenue.name')->map(function ($items) {
                return $items->pluck('permission.name');
            });
        }

        return view('admin.notification.index', compact('notifications', 'sideMenuPermissions', 'users', 'subadmin', 'vendors'));
    }

    public function store(NotificationRequest $request)
    {
        if ($request->user_type === 'customers') {
            $request->validate([
                'users.*' => 'exists:users,id',
            ]);
        } else {
            $request->validate([
                'users.*' => 'exists:vendors,id',
            ]);
        }

        // Pass data to repository
        $this->notificationRepository->createNotification([
            'user_type' => $request->user_type,
            'title' => $request->title,
            'description' => $request->description,
        ], $request->users);

        return redirect()->route('notification.index')->with('success', 'Notification sent successfully.');
    }


    public function destroy(Request $request, $id)
    {
        $notification = Notification::find($id);
        if (Auth::guard('subadmin')->check()) {
            $subadmin = Auth::guard('subadmin')->user();
            $subadminName = $subadmin->name;
            SubAdminLog::create([
                'subadmin_id' => Auth::guard('subadmin')->id(),
                'section' => 'Notifications',
                'action' => 'Delete',
                'message' => "SubAdmin: {$subadminName} Deleted Notification",
            ]);
        }
        $notification->delete();
        return redirect()->route('notification.index')->with(['success' => 'Notification Deleted Successfully']);
    }



    public function deleteAll()
    {
        NotificationTarget::query()->delete();
        Notification::query()->delete();

        return redirect()->route('notification.index')->with('message', 'All notifications have been deleted');
    }

    public function getUsersByType(Request $request)

    {
        $type = $request->type;
        $users = [];
        switch ($type) {
            case 'subadmin':
                $users = SubAdmin::select('id', 'name', 'email')->get();
                break;
            case 'web':
                $users = User::select('id', 'name', 'email')->get();
                break;
        }

        return response()->json($users);
    }
}
