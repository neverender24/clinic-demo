<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Actions\CreateAction;
use App\Filament\Resources\HospitalAdmissions\HospitalAdmissionResource;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HospitalAdmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'hospitalAdmissions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hospital')
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
                CreateAction::make(),
            ])
            ->recordActions(HospitalAdmissionResource::table($table)->getActions());
    }
}
