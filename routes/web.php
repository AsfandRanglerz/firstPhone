<?php

use App\Models\Role;
use App\Models\SideMenue;
use App\Models\Permission;
use App\Models\UserRolePermission;
use App\Models\SideMenuHasPermission;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ModelController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SecurityController;



use App\Http\Controllers\Admin\SubAdminController;

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\MobileListingController;
use App\Http\Controllers\Admin\MobileRequestController;
use App\Http\Controllers\Admin\RolePermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Admin routes
 * */

Route::get('/admin', [AuthController::class, 'getLoginPage']);
Route::post('/login', [AuthController::class, 'Login']);
Route::get('/admin-forgot-password', [AdminController::class, 'forgetPassword']);
Route::post('/admin-reset-password-link', [AdminController::class, 'adminResetPasswordLink']);
Route::get('/change_password/{id}', [AdminController::class, 'change_password']);
Route::post('/admin-reset-password', [AdminController::class, 'ResetPassword']);

Route::prefix('admin')->middleware(['admin', 'check.subadmin.status'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'getdashboard'])->name('admin.dashboard');
    Route::get('profile', [AdminController::class, 'getProfile']);
    Route::post('update-profile', [AdminController::class, 'update_profile']);
    Route::get('logout', [AdminController::class, 'logout'])->name('user.logout');

   
    // ############ Roles #################
   Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index')->name('roles.index')->middleware('check.permission:Roles,view');
        Route::get('/roles-create', 'create')->name('create.role')->middleware('check.permission:Roles,create');
        Route::post('/store-role', 'store')->name('store.role')->middleware('check.permission:Roles,create');
        Route::get('/roles-permissions/{id}', 'permissions')->name('role.permissions')->middleware('check.permission:Roles,edit');
        Route::post('/admin/roles/{id}/permissions/store', 'storePermissions')->name('roles.permissions.store')->middleware('check.permission:role,create');
        Route::delete('/delete-role/{id}', 'delete')->name('delete.role');
    });

    // ############ Users #################
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'Index')->name('user.index')->middleware('check.permission:Users,view');
        Route::get('/user-create', 'createview')->name('user.createview')->middleware('check.permission:Users,create');
        Route::post('/user-store', 'create')->name('user.create')->middleware('check.permission:Users,create');
        Route::get('/user-edit/{id}', 'edit')->name('user.edit')->middleware('check.permission:Users,edit');
        Route::post('/user-update/{id}', 'update')->name('user.update')->middleware('check.permission:Users,edit');
        Route::delete('/users-destory/{id}', 'delete')->name('user.delete')->middleware('check.permission:Users,delete');
        Route::delete('/users/{id}/force', 'forceDelete')->name('user.forceDelete')->middleware('check.permission:Users,delete');
        Route::post('/users/toggle-status', 'toggleStatus')->name('user.toggle-status');
    });


    // ############ Vendors #################
    Route::controller(VendorController::class)->group(function () {
        Route::get('/vendor', 'index')->name('vendor.index')->middleware('check.permission:Vendors,view');
        Route::get('/vendor-create', 'createView')->name('vendor.createview')->middleware('check.permission:Vendors,create');
        Route::post('/vendor-store', 'create')->name('vendor.create')->middleware('check.permission:Vendors,create');
        Route::get('/vendor-edit/{id}', 'edit')->name('vendor.edit')->middleware('check.permission:Vendors,edit');
        Route::post('/vendor-update/{id}', 'update')->name('vendor.update')->middleware('check.permission:Vendors,edit');
        Route::delete('/vendor-destroy/{id}', 'delete')->name('vendor.delete')->middleware('check.permission:Vendors,delete');
        Route::post('/vendor/toggle-status', 'toggleStatus')->name('vendor.toggle-status');
    });

    // ############ Brands #################
    Route::controller(BrandsController::class)->group(function () {
         Route::get('/brands', 'index')->name('brands.index')->middleware('check.permission:brands,view');

         Route::get('/brands/create', 'create')->name('brands.create')->middleware('check.permission:create,view');
         Route::post('/brands/store', 'store')->name('brands.store');
         Route::get('/brands/edit', 'edit')->name('brands.edit')->middleware('check.permission:edit,view');
        //  Route::post('/brands/update/{id}', 'update')->name('brands.update');
         Route::delete('/brands/delete/{id}', 'delete')->name('brands.delete')->middleware('check.permission:brands,delete');
            
    });

    Route::post('/brands/update/{id}', [BrandsController::class, 'update'])->name('brands.update');

        // ############ Models of Mobile #################

           Route::controller(ModelController::class)->group(function () {
             Route::get('/brands/models/{id}', 'index')->name('brands.model.view')->middleware('check.permission:brands,view');
              Route::post('/brands/models/store', 'store')->name('brands.model.store')->middleware('check.permission:brands,create');
               Route::post('/brands/models/update/{id}', 'update')->name('brands.model.update')->middleware('check.permission:brands,update');
                Route::delete('/brands/models/delete/{id}', 'destroy')->name('brands.model.delete')->middleware('check.permission:brands,delete');
            });


    

    // ############ Mobile Listings #################
    Route::controller(MobileListingController::class)->group(function () {
        Route::get('/mobilelisting/count', 'mobileListingCounter')->name('mobile.counter');
        Route::get('/mobilelisting', 'index')->name('mobile.index')->middleware('check.permission:MobileListing,view');
        Route::get('/mobilelisting-show/{id}', 'show')->name('mobile.show');
         Route::post('/mobilelisting/approve/{id}', 'approve')->name('mobilelisting.approve');
        Route::post('/mobilelisting/reject/{id}', 'reject')->name('mobilelisting.reject');
        Route::post('/mobilelisting-update/{id}', 'update')->name('mobile.update')->middleware('check.permission:MobileListing,edit');
        Route::delete('/mobilelisting-destroy/{id}', 'delete')->name('mobile.delete')->middleware('check.permission:MobileListing,delete');
        Route::post('/mobilelistingActivate/{id}', 'active')->name('mobile.activate');
        Route::post('/mobilelistingDeactivate/{id}', 'deactive')->name('mobile.deactivate');
    });

    // ############ Mobile Requests #################
    Route::controller(MobileRequestController::class)->group(function () {
        Route::get('/mobilerequest/count', 'mobileRequestCounter')->name('mobilerequest.counter');
        Route::get('/mobilerequest', 'index')->name('mobilerequest.index')->middleware('check.permission:MobileRequest,view');
        Route::get('/mobilerequest-show/{id}', 'show')->name('mobilerequest.show');
        Route::delete('/mobilerequest-destroy/{id}', 'delete')->name('mobilerequest.delete')->middleware('check.permission:MobileRequest,delete');
    });

    // ############ Sub Admin #################
    Route::controller(SubAdminController::class)->group(function () {
        Route::get('/subadmin',  'index')->name('subadmin.index')->middleware('check.permission:Sub Admins,view');
        Route::get('/subadmin-create',  'create')->name('subadmin.create')->middleware('check.permission:Sub Admins,create');
        Route::post('/subadmin-store',  'store')->name('subadmin.store')->middleware('check.permission:Sub Admins,create');
        Route::get('/subadmin-edit/{id}',  'edit')->name('subadmin.edit')->middleware('check.permission:Sub Admins,edit');
        Route::post('/subadmin-update/{id}',  'update')->name('subadmin.update')->middleware('check.permission:Sub Admins,edit');
        Route::delete('/subadmin-destroy/{id}',  'destroy')->name('subadmin.destroy')->middleware('check.permission:Sub Admins,delete');
        Route::post('/update-permissions/{id}', 'updatePermissions')->name('update.permissions');
        Route::post('/subadmin-StatusChange', 'StatusChange')->name('subadmin.StatusChange')->middleware('check.permission:Sub Admins,edit');
        Route::post('/admin/subadmin/toggle-status', 'toggleStatus')->name('admin.subadmin.toggleStatus');
        Route::get('/subadmin/subadmin_log_activity/{id}', 'SubAdminLog')->name('admin.subadmin.SubAdminLog');
    });

    // ############ Orders #################
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders',  'index')->name('order.index')->middleware('check.permission:Orders,view');
        Route::delete('/order-destroy/{id}',  'destroy')->name('order.destroy')->middleware('check.permission:Orders,delete');
        Route::post('/order/update-status/{id}', 'updateStatus')->name('order.updateStatus');
    });

    // ############ Notifications #################
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notification',  'index')->name('notification.index')->middleware('check.permission:Notifications,view');
        Route::post('/notification-store',  'store')->name('notification.store')->middleware('check.permission:Notifications,create');
        Route::delete('/notification-destroy/{id}',  'destroy')->name('notification.destroy')->middleware('check.permission:Notifications,delete');
        Route::delete('/notifications/delete-all', 'deleteAll')->name('notifications.deleteAll');
        Route::get('/get-users-by-type', 'getUsersByType');
    });

    // ############ Seo Routes #################
    // Route::controller(SeoController::class)->group(function () {
    //     Route::get('/seo', 'index')->name('seo.index');
    //     Route::get('/seo/{id}/edit', 'edit')->name('seo.edit');
    //     Route::post('/seo/{id}', 'update')->name('seo.update');
    //     Route::get('/admin/seo/page/{id}', 'getPage')->name('seo.page');
    // });

    // ############ Web Routes #################
    Route::controller(WebController::class)->group(function () {
        Route::get('/home-page', 'homepage')->name('web.homepage');
        Route::get('/about-page', 'aboutpage')->name('web.aboutpage');
        Route::get('/contact-page', 'contactpage')->name('web.contactpage');
    });

    // ############ Faq Routes #################
    Route::controller(FaqController::class)->group(function () {
        Route::get('faq', 'Faq')->middleware('check.permission:Faqs,view');
        Route::get('faq-edit/{id}', 'FaqsEdit')->name('faq.edit')->middleware('check.permission:Faqs,edit');
        Route::post('faq-update/{id}', 'FaqsUpdate')->middleware('check.permission:Faqs,edit');
        Route::get('faq-view', 'FaqView')->middleware('check.permission:Faqs,view');
        Route::get('faq-create', 'Faqscreateview')->middleware('check.permission:Faqs,create');
        Route::post('faq-store', 'Faqsstore')->middleware('check.permission:Faqs,create');
        Route::delete('faq-destroy/{id}', 'faqdelete')->name('faq.destroy');
        Route::post('/faqs/reorder', 'reorder')->name('faq.reorder');
    });

    // ############ Contact Us Routes #################
    Route::controller(ContactController::class)->group(function () {
        Route::get('/admin/contact-us', 'index')->name('contact.index')->middleware('check.permission:Contact us,view');
        Route::get('/admin/contact-us-create', 'create')->name('contact.create')->middleware('check.permission:Contact us,create');
        Route::post('/admin/contact-us-store', 'store')->name('contact.store')->middleware('check.permission:Contact us,create');
        Route::get('/admin/contact-us-edit/{id}', 'updateview')->name('contact.updateview')->middleware('check.permission:Contact us,edit');
        Route::post('/admin/contact-us-update/{id}', 'update')->name('contact.update');
    });

    // ############ About Us Routes #################
    Route::controller(SecurityController::class)->group(function () {
        Route::get('about-us', 'AboutUs')->middleware('check.permission:About us,view');
        Route::get('about-us-edit', 'AboutUsEdit')->middleware('check.permission:About us,edit');
        Route::post('about-us-update', 'AboutUsUpdate')->middleware('check.permission:About us,edit');
        Route::get('about-us-view', 'AboutUsView')->middleware('check.permission:About us,view');
    });

    // ############ Privacy Policy Routes #################
    Route::controller(SecurityController::class)->group(function () {
        Route::get('privacy-policy', 'PrivacyPolicy')->middleware('check.permission:Privacy & Policy,view');
        Route::get('privacy-policy-edit', 'PrivacyPolicyEdit')->middleware('check.permission:Privacy & Policy,edit');
        Route::post('privacy-policy-update', 'PrivacyPolicyUpdate');
        Route::get('privacy-policy-view', 'PrivacyPolicyView')->middleware('check.permission:Privacy & Policy,view');
    });

    // ############ Terms & Conditions Routes #################
     Route::controller(SecurityController::class)->group(function () {
        Route::get('term-condition', 'TermCondition')->middleware('check.permission:Terms & Conditions,view');
        Route::get('term-condition-edit', 'TermConditionEdit')->middleware('check.permission:Terms & Conditions,edit');
        Route::post('term-condition-update', 'TermConditionUpdate');
        Route::get('term-condition-view', 'TermConditionView')->middleware('check.permission:Terms & Conditions,view');
    });
});