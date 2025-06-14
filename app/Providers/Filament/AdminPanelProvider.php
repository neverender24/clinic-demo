<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use App\Models\Clinic;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use App\Filament\Pages\Auth\Login;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Resources\PatientResource;
use Rmsramos\Activitylog\ActivitylogPlugin;
use App\Filament\Resources\MedicineResource;
use Filament\FontProviders\LocalFontProvider;
use App\Filament\Pages\Tenancy\RegisterClinic;
use App\Filament\Resources\ActivityLogResource;
use Illuminate\Session\Middleware\StartSession;
use App\Filament\Resources\ConsultationResource;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->tenant(Clinic::class)
            ->tenantRegistration(RegisterClinic::class)
            ->tenantMenu(fn () => auth()->user()->can('canManageTenant', User::class))
            ->tenantMenuItems([
                'profile' => MenuItem::make()->label('Edit Clinic'),
                'register' => MenuItem::make()->label('Register Clinic'),
                // ...
            ])
            ->tenantProfile(EditTenantProfile::class)
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
                'red' => Color::Red,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->sidebarFullyCollapsibleOnDesktop()
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentApexChartsPlugin::make(),
                ActivitylogPlugin::make()
                    ->label('Log')
                    ->pluralLabel('Logs')
                    ->navigationGroup('Administration'),

            ])
            // ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            //     return $builder->items([
            //         // NavigationItem::make('Dashboard')
            //         //     ->icon('heroicon-o-home')
            //         //     ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
            //         //     ->url(fn (): string => Dashboard::getUrl()),
            //         ...Dashboard::getNavigationItems(),
            //         ...UserResource::getNavigationItems(),
            //         ...MedicineResource::getNavigationItems(),
            //         ...ConsultationResource::getNavigationItems(),
            //         ...PatientResource::getNavigationItems(),
            //     ]);
            // })
            ->authMiddleware([
                Authenticate::class,
            ])
            ->unsavedChangesAlerts()
            ->font(
                'Inter',
                url: asset('css/fonts/fonts.css'),
                provider: LocalFontProvider::class,
            )
            // ->spa()
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
