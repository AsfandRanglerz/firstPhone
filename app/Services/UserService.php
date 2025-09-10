<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getAllUsers()
    {
        return $this->userRepo->all();
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
        $data['image'] = 'public/admin/assets/images/default.png';
    }
        return $this->userRepo->create($data);
    }

    public function updateUser($id, $data)
    {
        $user = $this->userRepo->find($id);

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
        return $this->userRepo->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->userRepo->delete($id);
    }

    public function findUser($id)
    {
        return $this->userRepo->find($id);
    }

    public function toggleUserStatus($id, $status, $reason = null)
    {
        return $this->userRepo->toggleStatus($id, $status, $reason);
    }
}
