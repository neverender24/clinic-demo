<?php

namespace App\Filament\Resources\HospitalAdmissions;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\HospitalAdmissions\Pages\ListHospitalAdmissions;
use App\Filament\Resources\HospitalAdmissions\Pages\CreateHospitalAdmission;
use App\Filament\Resources\HospitalAdmissions\Pages\EditHospitalAdmission;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\HospitalAdmission;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HospitalAdmissionResource\Pages;
use App\Filament\Resources\HospitalAdmissionResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Illuminate\View\View;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

class HospitalAdmissionResource extends Resource
{
    protected static ?string $model = HospitalAdmission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'healthicons-o-admissions';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    Grid::make()
                        ->schema([
                            Select::make('patient_id')
                                ->relationship('patient', 'full_name')
                                ->preload()
                                ->searchable(['full_name'])
                                ->createOptionForm(function (Schema $schema) {
                                    return PatientResource::form($schema)->extraAttributes(['class' => 'w-full']);
                                })
                                ->createOptionAction(function(Action $action) {
                                    return $action
                                        ->modalWidth('xl')
                                        ->modalHeading('Create Patient')
                                        ->mutateDataUsing(function(array $data) {
                                            $data['user_id'] = auth()->id();
                                            return $data;
                                        });
                                }),
                            TextInput::make('hospital')
                                ->required()
                                ->datalist(fn($model) => $model::query()->select('hospital')->distinct()->get()->pluck('hospital')),
                            DatePicker::make('admission_date')
                                ->required(),
                            DatePicker::make('discharge_date'),
                        ])
                        ->columns(2),
                    Grid::make()
                        ->schema([
                            RichEditor::make('final_diagnosis')
                                ->toolbarButtons(self::onlyAllowedToolbar()),
                            RichEditor::make('remarks')
                                ->toolbarButtons(self::onlyAllowedToolbar()),
                        ])
                        ->columns(2)
                ]),
                
            ]);
    }
    
    protected static function onlyAllowedToolbar(): array
    {
        return [
            'bold',
            'bulletList',
            'italic',
            'orderedList',
            'redo',
            'underline',
            'undo',
            'attachFiles'
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hospital')
                    ->sortable(),
                TextColumn::make('admission_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('discharge_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('patient.full_name')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('user_id')
                //     ->numeric()
                //     ->sortable(),
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
            ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->recordActions([
                EditAction::make(),
                
                // Html2MediaAction::make('print_admitting_order')
                //     ->icon('heroicon-o-printer')
                //     ->color('success')
                //     ->label('Admitting Order')
                //     ->content(function($record): View {
                //         return view('consultations.admitting-order', [
                //             'header_image' => $record->clinic->header_image,
                //             'patient' => $record->patient,
                //             'hospital' => $record->hospital
                //         ]);
                //     })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListHospitalAdmissions::route('/'),
            'create' => CreateHospitalAdmission::route('/create'),
            'edit' => EditHospitalAdmission::route('/{record}/edit'),
        ];
    }
}
