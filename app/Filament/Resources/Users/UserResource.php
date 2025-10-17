<?php

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255),
                Select::make('registeredClinics')
                    ->relationship(name: 'clinics', titleAttribute: 'name')
                    ->multiple()
                    ->preload(),
                TextInput::make('username')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->dehydrated(fn($operation, $state) => $operation === 'create' || ($state !== null && strtolower($operation) === 'edit'))
                    ->password()
                    ->required(fn($operation) => strtolower($operation) === 'create')
                    ->maxLength(255),
                CheckboxList::make('roles')
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function($query) {
                            if (!auth()->user()->superAdmin()) {
                                return $query->whereNotIn('name', ['Doctor', 'super_admin']);
                            }
                        }
                    )
                    ->required()
                    // ->saveRelationshipsUsing(function (Model $record, $state) {
                    //      $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
                    // })
                   ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('clinic.name')
                    ->searchable()
                    ->label('Assigned Clinic'),
                TextColumn::make('roles.name')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
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
            ->recordActions([
                Impersonate::make()
                ->redirectTo(function($record) {
                    $clinic = $record->load('clinics')->clinics->firstWhere('id',Filament::getTenant())?->id ?? $record->load('clinics')->clinics->first()->id;
                    return route('filament.admin.resources.consultations.index', [$clinic]);
                })
                ->button()
                // ->icon('')
                ->label('Login'),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
