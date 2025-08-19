<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use App\Repositories\Interfaces\VendorRepositoryInterface;

class VendorService
{
    protected $vendorRepo;

    public function __construct(VendorRepositoryInterface $vendorRepo)
    {
        $this->vendorRepo = $vendorRepo;
    }

    public function getAllUsers()
    {
        return $this->vendorRepo->all();
    }

    public function createUser($request)
    {
        $data = $request->only(['name', 'email', 'phone', 'password']);
        $data['password'] = bcrypt($data['password']);
         if ($request->hasFile('image')) {
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $file->move(public_path('admin/assets/images/users/'), $filename);
        $data['image'] = 'public/admin/assets/images/users/' . $filename;
    } else {
        $data['image'] = 'public/admin/assets/images/avator.png';
    }
        return $this->vendorRepo->create($data);
    }

    public function updateUser($id, $data)
    {
         $user = $this->vendorRepo->find($id);
          // Handle image upload
        if (isset($data['image']) && $data['image']->isValid()) {
            // Delete old image
            if ($user->image && File::exists($user->image)) {
                File::delete($user->image);
            }

            $file = $data['image'];
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;

            // Store inside public/ folder
            $file->move('public/admin/assets/images/users', $filename);

            // Save full public path
            $data['image'] = 'public/admin/assets/images/users/' . $filename;
        } else {
            $data['image'] = $user->image;
        }
        return $this->vendorRepo->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->vendorRepo->delete($id);
    }

    public function findUser($id)
    {
        return $this->vendorRepo->find($id);
    }

    public function toggleUserStatus($id, $status, $reason = null)
    {
        return $this->vendorRepo->toggleStatus($id, $status, $reason);
    }
}
