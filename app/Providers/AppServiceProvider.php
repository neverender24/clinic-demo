<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
         $this->app->bind(Activity::class, ActivityLog::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        //
       
    }
}
