<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRolePermission;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        $sideMenuPermissions = collect();

        if (!Auth::guard('admin')->check()) {
            $user = Auth::guard('subadmin')->user()->load('roles');
            $permissions = UserRolePermission::with(['permission', 'sideMenue'])
                ->where('role_id', $user->role_id)
                ->get();
            $sideMenuPermissions = $permissions->groupBy('sideMenue.name')->map(function ($items) {
                return $items->pluck('permission.name');
            });
        }

        return view('users.index', compact('users', 'sideMenuPermissions'));
    }

    public function toggleStatus(Request $request)
    {
        $user = $this->userService->toggleUserStatus($request->id, $request->status, $request->reason);
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $user->toggle ? 'Activated' : 'Deactivated'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    public function createView()
    {
        return view('users.create');
    }

    public function create(CustomerRequest $request)
    {
        $this->userService->createUser([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        return redirect()->route('user.index')->with('success', 'Customer created successfully');
    }

    public function edit($id)
    {
        $user = $this->userService->findUser($id);
        return view('users.edit', compact('user'));
    }

    public function update(CustomerRequest $request, $id)
    {
        $data = $request->only(['name', 'email', 'phone']);
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $this->userService->updateUser($id, $data);
        return redirect('/admin/user')->with('success', 'Customer updated successfully');
    }

    public function delete($id)
    {
        $deleted = $this->userService->deleteUser($id);
        return redirect()->back()->with($deleted ? 'success' : 'error', $deleted ? 'Customer deleted successfully' : 'User not found');
    }
}

