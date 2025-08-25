<?php

namespace App\Repositories\Api;

use App\Models\MobileListing;

class MobileListingRepository
{
    public function create(array $data)
    {
        return MobileListing::create($data);
    }

    public function findWithRelations($id)
    {
        return MobileListing::with(['brand', 'model'])->where('id', $id)->firstOrFail();
    }
}
