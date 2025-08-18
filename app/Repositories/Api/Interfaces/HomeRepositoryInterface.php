<?php

namespace App\Repositories\Api\Interfaces;

use Illuminate\Http\Request;

interface HomeRepositoryInterface
{
    public function getHomeScreenData(Request $request);
}
