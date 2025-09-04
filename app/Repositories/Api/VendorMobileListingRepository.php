<?php

namespace App\Repositories\Api;

use App\Models\VendorMobile;

class VendorMobileListingRepository
{
    public function create(array $data)
    {
        return VendorMobile::create($data);
    }

    public function findWithRelations($id)
    {
        return VendorMobile::with(['brand', 'model'])->where('id', $id)->firstOrFail();
    }
}
