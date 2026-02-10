<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(45)
                            ->autocomplete(false)
                            ->autocapitalize('words'),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(45)
                            ->autocomplete(false)
                            ->autocapitalize('words'),
                        TextInput::make('middle_name')
                            ->maxLength(45)
                            ->autocomplete(false)
                            ->autocapitalize('words'),
                        Grid::make()
                            ->schema([
                                DatePicker::make('birthday')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->native(true),
                                Select::make('sex')
                                    ->options([
                                        'M' => 'Male',
                                        'F' => 'Female'
                                    ])
                                    ->required(),
                                Select::make('civil_status')
                                    ->columnSpanFull()
                                    ->required()
                                    ->options([
                                         'Child' => 'Child', 
                                         'Single' => 'Single', 
                                         'Married' => 'Married', 
                                         'Widow' => 'Widow',
                                         'Widower' => 'Widower',
                                    ]),
                            ])
                            ->columns(2),
                        TagsInput::make('contact_details')
                            // ->separator(',')
                            ->hint('Can be a phone number, email, and/or any other contact detail'),
                        TextInput::make('address')
                            ->required(),
                        TextInput::make('occupation')
                            // ->required()
                            // ->separator(',')
                            ,

                        TagsInput::make('allergies')->separator(','),
                        TagsInput::make('surgeries')->separator(','),
                        TagsInput::make('medical_conditions')
                            ->separator(',')
                            ->suggestions([
                                'Hypertension',
                                'Diabetes Mellitus',
                                'Diabetes Mellitus Type 1',
                                'Diabetes Mellitus Type 2',
                                'Stroke',
                                'Heart Disease',
                                'Coronary Artery Disease',
                                'Chronic Kidney Disease',
                                'Asthma',
                                'COPD',
                                'Thyroid Disease',
                                'Hyperthyroidism',
                                'Hypothyroidism',
                                'Cancer',
                                'Arthritis',
                                'Epilepsy',
                                'Hepatitis',
                                'HIV/AIDS',
                                'Tuberculosis',
                                'Anemia',
                                'Gout',
                                'Psoriasis',
                                'Lupus',
                            ]),
                        TagsInput::make('medications')->separator(','), 
                        Select::make('smoker_type')
                                ->options([
                                    'Smoker' => 'Smoker',
                                    'Non-smoker' => 'Non-smoker',
                                    'Vaper' => 'Vaper',
                                    'Quitter' => 'Quitter',
                                ]),
                        Repeater::make('patientHmos')
                            ->label('Patient HMOs')
                            // ->addActionLabel('Click here to add HMO')
                            // ->addActionAlignment(Alignment::Left)
                            ->addAction(function(Action $action) {
                                return $action
                                            ->label('Click here to add HMO')
                                            ->icon('healthicons-o-social-work')
                                            ->color('primary')
                                            ->link()
                                            ->size('lg');
                            })
                            ->relationship()
                            ->schema([
                                Select::make('hmo_id')
                                    ->label('Name')
                                    ->required()
                                    ->relationship('hmo', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                    ])
                                    ->columnSpanFull(),
                                DatePicker::make('date_registered')
                                    ->label('Registered Date'),
                                DatePicker::make('date_expiry')
                                    ->label('Expired Date'),
                            ])
                            ->defaultItems(0)
                            ->columns(2)
                    ])
            ])
            ->columns(1)
            ->extraAttributes(['class' => 'w-1/2']);
    }
}
