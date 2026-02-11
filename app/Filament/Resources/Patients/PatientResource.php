<?php

namespace App\Filament\Resources\Patients;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use App\Models\Scopes\TenantScope;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use App\Models\Scopes\ConsultationScope;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\Patients\Pages\EditPatient;
use App\Filament\Resources\Patients\Pages\ListPatients;
use App\Filament\Resources\Patients\Pages\CreatePatient;
use App\Filament\Resources\Patients\Schemas\PatientForm;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\Patients\RelationManagers\ConsultationsRelationManager;
use App\Filament\Resources\Patients\RelationManagers\HospitalAdmissionsRelationManager;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static bool $isScopedToTenant = false;

    protected static string | \BackedEnum | null $navigationIcon = 'healthicons-o-traumatism';

    /**
     * Get the base form fields (without relationship-based repeaters)
     * Used for createOptionForm/editOptionForm in Select components
     */
    

    public static function form(Schema $schema): Schema
    {
        return PatientForm::configure($schema)
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
                TextColumn::make('smoker_type')
                    ->label('Smoker')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Smoker' => 'danger',
                        'Non-smoker' => 'success',
                        'Vaper' => 'warning',
                        'Quitter' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('medical_conditions')
                    ->label('Medical Conditions')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('medications')
                    ->label('Medications')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('allergies')
                    ->label('Allergies')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('surgeries')
                    ->label('Surgeries')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->deferFilters(false)
            ->filters([
                TrashedFilter::make(),
                Filter::make('is_dialysis')
                    ->label('Dialysis Patient')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'consultations',
                        fn (Builder $query) => $query->where('is_dialysis', true)
                    )),
                SelectFilter::make('medical_conditions')
                    ->label('Medical Condition')
                    ->options([
                        'Hypertension' => 'Hypertension',
                        'Diabetes Mellitus' => 'Diabetes Mellitus',
                        'Diabetes Mellitus Type 1' => 'Diabetes Mellitus Type 1',
                        'Diabetes Mellitus Type 2' => 'Diabetes Mellitus Type 2',
                        'Stroke' => 'Stroke',
                        'Heart Disease' => 'Heart Disease',
                        'Coronary Artery Disease' => 'Coronary Artery Disease',
                        'Chronic Kidney Disease' => 'Chronic Kidney Disease',
                        'Asthma' => 'Asthma',
                        'COPD' => 'COPD',
                        'Thyroid Disease' => 'Thyroid Disease',
                        'Hyperthyroidism' => 'Hyperthyroidism',
                        'Hypothyroidism' => 'Hypothyroidism',
                        'Cancer' => 'Cancer',
                        'Arthritis' => 'Arthritis',
                        'Epilepsy' => 'Epilepsy',
                        'Hepatitis' => 'Hepatitis',
                        'HIV/AIDS' => 'HIV/AIDS',
                        'Tuberculosis' => 'Tuberculosis',
                        'Anemia' => 'Anemia',
                        'Gout' => 'Gout',
                        'Psoriasis' => 'Psoriasis',
                        'Lupus' => 'Lupus',
                    ])
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $value): Builder => $query->whereJsonContains('medical_conditions', $value)
                    )),
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
