<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserRolePermission;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function Index()
    {
        $users = User::orderby('id', 'desc')->get();
        $sideMenuPermissions = collect();
        if (!Auth::guard('admin')->check()) {
            $user = Auth::guard('subadmin')->user()->load('roles');
            $roleId = $user->role_id;
            $permissions = UserRolePermission::with(['permission', 'sideMenue'])
                ->where('role_id', $roleId)
                ->get();
            $sideMenuPermissions = $permissions->groupBy('sideMenue.name')->map(function ($items) {
                return $items->pluck('permission.name');
            });
        }
        return view('users.index', compact('users' , 'sideMenuPermissions'));
    }

    public function toggleStatus(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            $user->toggle = $request->status;
            $user->save();
            if ($request->status == 0 && $request->reason) {
                $this->sendDeactivationEmail($user, $request->reason);
            }
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $user->toggle ? 'Activated' : 'Deactivated'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    protected function sendDeactivationEmail($user, $reason)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'reason' => $reason
        ];
        try {
            Mail::send('emails.user_deactivated', $data, function($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Account Deactivation Notification');
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send deactivation email: " . $e->getMessage());
        }
    }


    public function createview() {
        return view('users.create');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
             'email' => [
            'required',
            'email',
            'regex:/^[\w\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/'
        ],
        'phone' => 'required|regex:/^[0-9]+$/|max:15',
            'password' => 'required|min:6',
        ]);
    
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        return redirect()->route('user.index')->with('success', 'User created successfully');
    }
    


    public function edit($id) {
        $user = User::find($id);
        return view('users.edit', compact('user'));
    }
    
   public function update(Request $request, $id) {
    $request->validate([
        'name' => 'required',
         'email' => [
            'required',
            'email',
            'regex:/^[\w\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/'
        ],
        'phone' => 'required|regex:/^[0-9]+$/|max:15',
        'password' => 'nullable|min:6',
    ]);

    // Pehle user ko find karo
    $user = User::findOrFail($id);

    // Update fields
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;

    // Agar password diya gaya hai toh update karo
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect('/admin/user')->with('success', 'User updated successfully');
}


    public function delete($id) {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'User deleted successfully');
        } else {
            return redirect()->back()->with('error', 'User not found');
        }
    } 
}
