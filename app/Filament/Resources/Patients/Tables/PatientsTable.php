<?php

namespace App\Filament\Resources\Patients\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Filament\Actions\EditAction;
use App\Models\Scopes\TenantScope;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;

class PatientsTable
{
    public static function configure(Table $table): Table
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
}
