<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Filament\Resources\HospitalAdmissionResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HospitalAdmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'hospitalAdmissions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hospital')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('hospital')
            ->columns(HospitalAdmissionResource::table($table)->getColumns())
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions(HospitalAdmissionResource::table($table)->getActions());
    }
}
