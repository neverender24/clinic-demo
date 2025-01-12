<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'healthicons-o-traumatism';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(45)
                            ->autocomplete(false)
                            ->autocapitalize('words'),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(45)
                            ->autocomplete(false)
                            ->autocapitalize('words'),
                        Forms\Components\TextInput::make('middle_name')
                            ->maxLength(45)
                            ->autocomplete(false)
                            ->autocapitalize('words'),
                        Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('birthday')
                                    ->required(),
                                Forms\Components\Select::make('sex')
                                    ->options([
                                        'M' => 'Male',
                                        'F' => 'Female'
                                    ]),
                            ])
                            ->columns(2),
                        Forms\Components\TagsInput::make('contact_details')
                            // ->separator(',')
                            ->hint('Can be a phone number, email, and/or any other contact detail'),
                    ])
            ])
            ->columns(1)
            ->extraAttributes(['class' => 'w-1/2']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sex')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_details')
                    ->searchable()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
