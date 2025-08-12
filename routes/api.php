<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\SideMenueController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SideMenuPermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

 

Route::post('/roles', [RoleController::class, 'store']);
Route::post('/permissions', [PermissionController::class, 'store']);
Route::post('/sidemenue', [SideMenueController::class, 'store']);
Route::post('/permission-insert', [SideMenuPermissionController::class, 'assignPermissions']);
Route::post('/seo-bulk', [SeoController::class, 'storeBulk'])
     ->name('seo.bulk-update');

// Auth APIs
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/sendOtp',[AuthController::class,'sendOtp']);
Route::post('/verifyOtp',[AuthController::class,'verifyOtp']);
Route::post('/resetPassword',[AuthController::class,'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/getProfile',[AuthController::class,'getProfile']);
    Route::get('/updateProfile',[AuthController::class,'updateProfile']);
    Route::post('/changePassword',[AuthController::class,'changePassword']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccout']);

    //notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notification-seen', [NotificationController::class, 'seenNotification']);
});