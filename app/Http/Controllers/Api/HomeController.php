<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Interfaces\HomeRepositoryInterface;

class HomeController extends Controller
{
    protected $homeRepository;

    public function __construct(HomeRepositoryInterface $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    public function homeScreen(Request $request)
    {
        try {
            $data = $this->homeRepository->getHomeScreenData($request);
            return ResponseHelper::success($data, 'Home screen data retrieved successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving home screen data', 'error', 500);
        }
    }

    public function deviceDetails($id)
    {
        try {
            $data = $this->homeRepository->getDeviceDetails($id);
            return ResponseHelper::success($data, 'Device details retrieved successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving device details', 'error', 500);
        }
    }
}
