<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Schemas\Schema;
use App\Models\Clinic;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterClinic extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Clinic';
    }
 
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
