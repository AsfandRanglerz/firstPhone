<?php

namespace App\Providers;

use App\Models\Faq;
 use Illuminate\Support\Facades\View;
use App\Models\Seo;
use App\Models\blog;
use App\Models\Role;
use App\Models\User;
use App\Models\AboutUs;
use App\Models\SubAdmin;
use App\Models\ContactUs;
use App\Models\Notification;
use App\Models\PrivacyPolicy;
use App\Models\TermCondition;
use App\Observers\ModelObserver;
use App\Models\TermsAndConditions;
use App\Models\UserRolePermission;
use App\Observers\SubAdminObserver;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\VendorRepository;
use Illuminate\Support\ServiceProvider;
use App\Services\SubAdminActivityService;
use App\Repositories\NotificationRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

use App\Repositories\Api\AuthRepository;
use App\Repositories\Api\Interfaces\AuthRepositoryInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */


    public function boot()
    {
        View::composer('*', function ($view) {
            $sideMenuPermissions = collect();

            if (Auth::guard('subadmin')->check()) {
                $user = Auth::guard('subadmin')->user();

                // Load roles from pivot
                $role = $user->roles()->first(); // assumes 1 role per subadmin

                if ($role) {
                    $roleId = $role->id;

                    $sideMenuPermissions = UserRolePermission::with(['permission', 'sideMenue'])
                        ->where('role_id', $roleId)
                        ->get()
                        ->groupBy(function ($item) {
                            return $item->sideMenue->name ?? 'undefined';
                        })
                        ->map(function ($items) {
                            return $items->pluck('permission.name');
                        });
                }
            }

            $view->with('sideMenuPermissions', $sideMenuPermissions);
        });
    }
}