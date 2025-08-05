<?php

namespace App\Services;

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

    public function createUser($data)
    {
        return $this->userRepo->create($data);
    }

    public function updateUser($id, $data)
    {
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
