<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterClinic extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Clinic';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('location'),
            ]);
    }
 
    protected function handleRegistration(array $data): Clinic
    {
        $team = Clinic::create($data);
 
        $team->users()->attach([auth()->user(), 2]);
 
        return $team;
    }
}
