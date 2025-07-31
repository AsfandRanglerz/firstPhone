<?php

namespace App\Services;

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

    public function createUser($data)
    {
        return $this->vendorRepo->create($data);
    }

    public function updateUser($id, $data)
    {
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
