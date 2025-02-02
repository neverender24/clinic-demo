<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
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

    // public static function isTenantSubscriptionRequired(Panel $panel): bool
    // {
    //     return tru;
    // }
 
    public function form(Form $form): Form
    {
        // dd($this->record);
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                FileUpload::make('header_image')
                    ->required()

                // ...
            ])
            ->extraAttributes([
                'class' => 'w-2/5'
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form')
        ];
    }
}
