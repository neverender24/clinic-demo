<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Schemas\Schema;
use App\Models\Clinic;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;

class EditTenantProfile extends BaseEditTenantProfile
{
    use HasPageShield;
    
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
                    ->required(),
                FileUpload::make('medcert_header_image')
                    ->label('Header for Medcert')
                    ->required(),
                FileUpload::make('watermarks')
                    ->required()

                // ...
            ])
            ->extraAttributes([
                'class' => 'w-2/5'
            ]);
    }    
    
}
