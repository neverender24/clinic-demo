<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Consultations\ConsultationResource;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsultationsRelationManager extends RelationManager
{
    protected static string $relationship = 'consultations';

    public function form(Schema $schema): Schema
    {
        return ConsultationResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns(ConsultationResource::table($table)->getColumns())
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions(ConsultationResource::table($table)->getActions());
    }
}
