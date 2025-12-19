<?php

namespace App\Repositories\Api;

use App\Models\VendorMobile;

class VendorMobileListingRepository
{
    public function create(array $data)
    {
        return VendorMobile::create($data);
    }

    public function find($id)
    {
        return VendorMobile::find($id);
    }

    public function update($id, array $data)
    {
        $listing = VendorMobile::find($id);
        $listing->update($data);
        return $listing;
    }

    public function findWithRelations($id)
    {
        return VendorMobile::with(['brand', 'model'])->where('id', $id)->firstOrFail();
    }
}
