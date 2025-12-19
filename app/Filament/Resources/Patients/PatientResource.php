<?php

namespace App\Filament\Resources\Patients;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Repeater;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Patients\Pages\ListPatients;
use App\Filament\Resources\Patients\Pages\CreatePatient;
use App\Filament\Resources\Patients\Pages\EditPatient;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Actions;
use App\Models\Scopes\ConsultationScope;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\Patients\RelationManagers\ConsultationsRelationManager;
use App\Filament\Resources\Patients\RelationManagers\HospitalAdmissionsRelationManager;
use App\Models\Scopes\TenantScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static bool $isScopedToTenant = false;

    protected static string | \BackedEnum | null $navigationIcon = 'healthicons-o-traumatism';

    /**
     * Get the base form fields (without relationship-based repeaters)
     * Used for createOptionForm/editOptionForm in Select components
     */
    public static function getFormSchema(): array
    {
        return [
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
                        ->hint('Can be a phone number, email, and/or any other contact detail'),
                    TextInput::make('address')
                        ->required(),
                    TextInput::make('occupation'),
                    TagsInput::make('allergies')->separator(','),
                    TagsInput::make('surgeries')->separator(','),
                ])
        ];
    }

    /**
     * Get the full form schema including relationship-based repeaters
     * Used for the main resource form
     */
    public static function getFullFormSchema(): array
    {
        return [
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
                        ->hint('Can be a phone number, email, and/or any other contact detail'),
                    TextInput::make('address')
                        ->required(),
                    TextInput::make('occupation'),
                    TagsInput::make('allergies')->separator(','),
                    TagsInput::make('surgeries')->separator(','),
                    Repeater::make('patientHmos')
                        ->label('Patient HMOs')
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
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::getFullFormSchema())
            ->columns(1)
            ->extraAttributes(['class' => 'w-1/2']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with([
                'consultations' => fn($q) => $q->withoutGlobalScopes([TenantScope::class])
            ]))
            ->defaultSort('last_name', )
            ->columns([
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('birthday')
                    ->date()
                    ->sortable(),
                TextColumn::make('sex')
                    ->searchable(),
                TextColumn::make('contact_details')
                    ->searchable()
                    ->listWithLineBreaks(),
                TextColumn::make('address'),
                TextColumn::make('hmos')
                    // ->searchable()
                    ->badge()
                    // ->color(fn($livewire) => dd($livewire))
                    ->color(fn($state) => now()->gte(Carbon::parse($state->pivot?->date_expiry)) ? 'danger' : 'success')
                    ->formatStateUsing(fn($state) => $state->name)
                    // ->colors(fn($record) => dd($record))
                    // ->color(fn($record) => dd($record->pivot))
                    ,
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn($record) => $record->consultations->count() > 0),
                Action::make('delete_disable')
                    ->label('Delete')
                    ->disabled()
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->visible(fn($record) => $record->consultations->count() > 0)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ConsultationsRelationManager::class,
            HospitalAdmissionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'create' => CreatePatient::route('/create'),
            'edit' => EditPatient::route('/{record}/edit'),
        ];
    }
}
