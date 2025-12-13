<?php

namespace App\Filament\Resources\HospitalAdmissions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HospitalAdmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hospital'),
                DatePicker::make('admission_date')
                    ->required(),
                DatePicker::make('discharge_date'),
                Textarea::make('final_diagnosis')
                    ->columnSpanFull(),
                Textarea::make('remarks')
                    ->columnSpanFull(),
                Select::make('clinic_id')
                    ->relationship('clinic', 'name')
                    ->required(),
                Select::make('patient_id')
                    ->relationship('patient', 'id')
                    ->required(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
