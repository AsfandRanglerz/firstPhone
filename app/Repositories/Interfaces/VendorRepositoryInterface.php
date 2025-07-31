<?php

namespace App\Repositories\Interfaces;

interface VendorRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleStatus($id, $status, $reason = null);
}
