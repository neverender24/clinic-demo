<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use App\Models\Clinic;
use Filament\PanelProvider;
use Filament\Actions\Action;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use App\Filament\Pages\Auth\Login;
use Filament\Support\Colors\Color;
use Filament\Enums\UserMenuPosition;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Filament\FontProviders\LocalFontProvider;
use App\Filament\Pages\Tenancy\RegisterClinic;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\ActivityLogResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Resources\Patients\PatientResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\Medicines\MedicineResource;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\Consultations\ConsultationResource;

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
            ->tenantMenu(fn () => true)
            ->tenantProfile(EditTenantProfile::class)
            ->tenantMenuItems([
                'profile' => fn(Action $action) => $action->label('Edit Clinic'),
                'register' => fn(Action $action) => $action->label('Register Clinic')->openUrlInNewTab(),
                // ...
            ])
            // ->login(Login::class)
            // ->darkMode(false)
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
            ->userMenu(position: UserMenuPosition::Sidebar)
            ->globalSearch(false)
            ->topbar(false)
            ->sidebarCollapsibleOnDesktop(true)
            ->sidebarFullyCollapsibleOnDesktop(false)
            ->plugins([
                \Javarex\DdoLogin\LoginDdoPlugin::make(),
                FilamentShieldPlugin::make()
                    ->scopeToTenant(false),
                FilamentApexChartsPlugin::make(),
                // ActivitylogPlugin::make()
                //     ->label('Log')
                //     ->pluralLabel('Logs')
                //     ->navigationGroup('Administration'),

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
            ->sidebarWidth('16rem')
            ->unsavedChangesAlerts()
            ->font(
                'Inter',
                url: asset('css/fonts/fonts.css'),
                provider: LocalFontProvider::class,
            )
            // ->spa()
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->navigationItems([
                NavigationItem::make('Backup')
                    ->url(fn () => route('backup.run'))
                    ->icon('heroicon-o-circle-stack')
                    ->group('Administration')
                    ->visible(fn() => auth()->user()->doctor())
                    ->sort(3),
            ]);
    }
}
