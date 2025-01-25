<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;

class EditTenantProfile extends BaseEditTenantProfile
{
    protected static string $view = 'filament.pages.tenancy.edit-tenant-profile';
    
    public static function getLabel(): string
    {
        return 'Clinic';
    }
 
    public function form(Form $form): Form
    {
        // dd($this->record);
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ])
            ->extraAttributes([
                'class' => 'w-2/5'
            ]);
    }
}
