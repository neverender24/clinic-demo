<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\HospitalAdmission;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HospitalAdmissionResource\Pages;
use App\Filament\Resources\HospitalAdmissionResource\RelationManagers;
use Filament\Forms\Components\Section;

class HospitalAdmissionResource extends Resource
{
    protected static ?string $model = HospitalAdmission::class;

    protected static ?string $navigationIcon = 'healthicons-o-admissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Grid::make()
                        ->schema([
                            Select::make('patient_id')
                                ->relationship('patient', 'full_name')
                                ->searchable(['full_name']),
                            Forms\Components\DatePicker::make('admission_date')
                                ->required(),
                            Forms\Components\DatePicker::make('discharge_date'),
                        ])
                        ->columns(3),
                    Grid::make()
                        ->schema([
                            Forms\Components\RichEditor::make('final_diagnosis'),
                            Forms\Components\RichEditor::make('remarks'),
                        ])
                        ->columns(2)
                ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('admission_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discharge_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clinic_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHospitalAdmissions::route('/'),
            'create' => Pages\CreateHospitalAdmission::route('/create'),
            'edit' => Pages\EditHospitalAdmission::route('/{record}/edit'),
        ];
    }
}
