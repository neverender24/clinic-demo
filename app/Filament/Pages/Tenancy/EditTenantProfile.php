<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;

class EditTenantProfile extends BaseEditTenantProfile
{
    use HasPageShield;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        // Allow doctors (includes super_admin) to access
        if ($user && $user->doctor()) {
            return true;
        }

        // Fall back to Shield's default permission check
        $permission = static::getPagePermission();

        return $permission && $user
            ? $user->can($permission)
            : parent::canAccess();
    }
    
    // protected static string $view = 'filament.pages.tenancy.edit-tenant-profile';
    
    public static function getLabel(): string
    {
        return 'Edit Clinic';
    }

    // public static function isTenantSubscriptionRequired(Panel $panel): bool
    // {
    //     return tru;
    // }
 
    public function form(Schema $schema): Schema
    {
        // dd($this->record);
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                FileUpload::make('header_image')
                    ->required(false),
                FileUpload::make('medcert_header_image')
                    ->label('Header for Medcert')
                    ->required(false),
                FileUpload::make('watermarks')
                    ->required(false)

                // ...
            ])
            ->extraAttributes([
                'class' => 'w-2/5'
            ]);
    }    
    
}
