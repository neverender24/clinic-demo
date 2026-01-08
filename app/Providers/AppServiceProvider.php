<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Filament\Pages\Page;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Filament\Widgets\Widget;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\PermissionRegistrar;
use Filament\Support\Facades\FilamentAsset;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

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
        // Gate::before(function (User $user, string $ability) {
        //     if ($user->hasRole('super_admin')) {
        //         return true;
        //     }
        // });

        // app(PermissionRegistrar::class)
        //     ->setPermissionClass(Permission::class)
        //     ->setRoleClass(Role::class);

        // //

        // FilamentShield::buildPermissionKeyUsing(
        //     function (string $entity, string $affix, string $subject, string $case, string $separator) {
        //         return match(true) {
        //             # if `configurePermissionIdentifierUsing()` was used previously, then this needs to be adjusted accordingly
        //             is_subclass_of($entity, Resource::class) => Str::of($affix)
        //                 ->snake()
        //                 ->append('_')
        //                 ->append(
        //                     Str::of($entity)
        //                         ->afterLast('\\')
        //                         ->beforeLast('Resource')
        //                         ->replace('\\', '')
        //                         ->snake()
        //                         ->replace('_', '::')
        //                 )
        //                 ->toString(),
        //             is_subclass_of($entity, Page::class) => Str::of('page_')
        //                 ->append(class_basename($entity))
        //                 ->toString(),
        //             is_subclass_of($entity, Widget::class) => Str::of('widget_')
        //                 ->append(class_basename($entity))
        //                 ->toString()
        //             };
        //     });
    }
}
