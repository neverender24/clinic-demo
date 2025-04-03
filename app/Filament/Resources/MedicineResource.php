<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicineResource\Pages;
use App\Filament\Resources\MedicineResource\RelationManagers;
use App\Models\Medicine;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    protected static ?string $navigationIcon = 'healthicons-o-medicines';

    protected static bool $isScopedToTenant = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Generic Name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('brand')
                    ->label('Brand Name')
                   // ->required()
                    ->columnSpanFull(),
                // Forms\Components\TextInput::make('type')
                //     ->label('Preparation')
                //     ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->html(),
                Tables\Columns\TextColumn::make('brand')
                    ->html(),
                Tables\Columns\TextColumn::make('type')
                    ->html(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('F j, Y'),
            ])
            // ->paginated(false)
            ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('lg')
                    ->mutateFormDataUsing(function($data) {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('lg'),
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
            'index' => Pages\ListMedicines::route('/'),
            // 'create' => Pages\CreateMedicine::route('/create'),
            // 'edit' => Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}
