<?php

use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeleteAccountController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FilterMobileController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MobileFilterController;
use App\Http\Controllers\Api\MobileListingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OnlinePaymentController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RequestFormController;
use App\Http\Controllers\Api\ShippingAddressController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SideMenueController;
use App\Http\Controllers\SideMenuPermissionController;
use Illuminate\Support\Facades\Route;




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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sendOtp', [AuthController::class, 'sendOtp']);
Route::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/getProfile',[AuthController::class,'getProfile']);
    Route::post('/updateProfile',[AuthController::class,'updateProfile']);
    Route::post('/changePassword',[AuthController::class,'changePassword']);
    Route::delete('/deleteaccount', [DeleteAccountController::class, 'deleteAccount']);

    //notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notification-seen', [NotificationController::class, 'seenNotification']);
    // Mobile Request API
    Route::post('/mobilerequestform', [RequestFormController::class, 'mobilerequestform']);

 
    //place order api
Route::post('/place-order', [OnlinePaymentController::class, 'placeOrder']);

    //order and tracking
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{id}/track', [OrderController::class, 'track']);
    Route::post('/shipping', [OrderController::class, 'shippingAddress']);

    // Mobile Listing API
    Route::post('/mobilelisting', [MobileListingController::class, 'mobileListing']);
    Route::get('/getmobilelisting', [MobileListingController::class, 'getmobileListing']);
    
    // Delete Account api
    Route::delete('/deleteaccount', [DeleteAccountController::class, 'deleteAccount']);

    //faq
    Route::get('/faqs', [FaqController::class, 'index']);

    
});

//filter searchers api

Route::get('/models', [MobileFilterController::class, 'getModels']);
Route::get('/brands', [MobileFilterController::class, 'getBrands']);
Route::get('/data', [MobileFilterController::class, 'getData']);

//Mobile listing preview api
 Route::get('/mobilelistingpreview/{id}', [MobileListingController::class, 'previewListing']);




