<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Resources\MedicineResource\Pages\ListMedicines;
use App\Filament\Resources\MedicineResource\Pages;
use App\Filament\Resources\MedicineResource\RelationManagers;
use App\Models\Medicine;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    protected static string | \BackedEnum | null $navigationIcon = 'healthicons-o-medicines';

    protected static bool $isScopedToTenant = false;


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Generic Name')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('brand')
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
            ->modifyQueryUsing(fn(Builder $query) => $query->with('consultations'))
            ->defaultSort('name')
            ->columns([
                ToggleColumn::make('active')
                    ->offColor('danger'),
                TextColumn::make('name')
                    ->searchable()
                    ->html(),
                TextColumn::make('brand')
                    ->searchable()
                    ->html(),
                TextColumn::make('type')
                    ->html(),
                TextColumn::make('user.name')
                    ->label('Created By'),
                TextColumn::make('created_at')
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
                    ->mutateDataUsing(function($data) {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('lg'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicines::route('/'),
            // 'create' => Pages\CreateMedicine::route('/create'),
            // 'edit' => Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}
