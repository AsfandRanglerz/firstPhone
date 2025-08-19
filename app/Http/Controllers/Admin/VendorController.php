<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\VendorService;
use App\Models\UserRolePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use Illuminate\Support\Facades\Auth;


class VendorController extends Controller
{
     protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function index()
    {
        $users = $this->vendorService->getAllUsers();
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

        return view('admin.vendor.index', compact('users', 'sideMenuPermissions'));
    }

    public function toggleStatus(Request $request)
    {
        $user = $this->vendorService->toggleUserStatus($request->id, $request->status, $request->reason);
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $user->toggle ? 'Activated' : 'Deactivated'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
    }

    public function createView()
    {
        return view('admin.vendor.create');
    }

    public function create(VendorRequest $request)
    {
        $this->vendorService->createUser($request);
        return redirect()->route('vendor.index')->with('success', 'Vendor Created Successfully');
    }

    public function edit($id)
    {
        $user = $this->vendorService->findUser($id);
        return view('admin.vendor.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
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

        $data = $request->only(['name', 'email', 'phone']);
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

         if ($request->hasFile('image')) {
        $data['image'] = $request->file('image');
    }

        $this->vendorService->updateUser($id, $data);
        return redirect('/admin/vendor')->with('success', 'Vendor Updated Successfully');
    }

    public function delete($id)
    {
        $deleted = $this->vendorService->deleteUser($id);
        return redirect()->back()->with($deleted ? 'success' : 'error', $deleted ? 'Vendor Deleted Successfully' : 'User not found');
    }
}
