<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use Filament\Forms\Form;
use Illuminate\View\View;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Consultation;
use Livewire\Attributes\Url;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Livewire\Consultation\ListRecords;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Forms\Components\View as ComponentsView;
use App\Filament\Resources\ConsultationResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\ConsultationResource\RelationManagers;
use App\Filament\Resources\ConsultationResource\Pages\ListConsultations;

class ConsultationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Consultation::class;

    protected static ?string $navigationIcon = 'healthicons-o-telemedicine';

    protected static bool $shouldRegisterNavigation = true;

    public static function  getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'add_management',
            'add_diagnosis',
            'add_chief_complaint',
            'add_prescription',
            'add_test_results',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->required(),
                                Forms\Components\Select::make('patient_id')
                                    ->label('Patient')
                                    // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
                                    ->getOptionLabelsUsing(fn ($value) => Patient::find($value)->full_name)
                                    ->preload()
                                    ->searchable()
                                    ->relationship('patient', 'full_name')
                                    ->createOptionForm(function (Form $form) {
                                        return PatientResource::form($form)->extraAttributes(['class' => 'w-full']);
                                    })
                                    ->createOptionAction(function(Action $action) {
                                        return $action
                                            ->modalWidth('xl')
                                            ->modalHeading('Create Patient')
                                            ->mutateFormDataUsing(function(array $data) {
                                                $data['user_id'] = auth()->id();
                                                return $data;
                                            });
                                    })
                                    ->live()
                                    ->required()
                                    ->columnSpan(3),
                                Forms\Components\MarkdownEditor::make('test_results')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'orderedList',
                                        'redo',
                                        'underline',
                                        'undo',
                                    ])
                                    ->columnSpanFull()
                                    ->visible(fn() => auth()->user()->can('addTestResult', static::$model))
                                    ,
                                Grid::make()
                                    ->schema([
                                        Forms\Components\MarkdownEditor::make('chief_complaint')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'orderedList',
                                                'redo',
                                                'underline',
                                                'undo',
                                            ])
                                            // ->columnSpanFull()
                                            ->columnSpan(2)
                                            ,
                                       
                                        Forms\Components\MarkdownEditor::make('diagnosis')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'orderedList',
                                                'redo',
                                                'underline',
                                                'undo',
                                            ])
                                            ->columnSpan(2),
                                        Forms\Components\MarkdownEditor::make('management')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'orderedList',
                                                'redo',
                                                'underline',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin']))
                                    ->columns(4),
                            ])
                            ->columns(4)
                            ->columnSpan(2),
                            Section::make('Prescriptions')
                                ->schema([
                                    Repeater::make('medicines')
                                        ->relationship('consultationMedicines')
                                        ->schema([
                                            Select::make('medicine_id')
                                                ->label('Medicine')
                                                ->relationship('medicine', 'full_name_of_medicine')
                                                // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - <b>{$record->brand}</b>")
                                                ->allowHtml()
                                                ->preload()
                                                ->searchable()
                                                ->required()
                                                ->createOptionForm(function (Form $form) {
                                                    return MedicineResource::form($form)->extraAttributes(['class' => 'w-full']);
                                                })
                                                ->createOptionAction(function(Action $action) {
                                                    return $action
                                                        ->modalHeading('Add Medicine')
                                                        ->mutateFormDataUsing(function(array $data) {
                                                            $data['user_id'] = auth()->id();
                                                            return $data;
                                                        });
                                                }),
                                            TextInput::make('remarks')
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->label('Prescription')
                                        ->defaultItems(1)
                                ])
                                ->visible(fn() => auth()->user()->hasRole('Doctor'))
                                
                    ])
                    ->columnSpan(function($operation) {
                        if($operation == 'create') {
                            return 2;
                        }
                        return 1;
                    }),
                // Grid::make('')
                //     ->schema([
                //         Livewire::make(ListRecords::class, data: fn($record) => ['patient_id' => $record->patient_id])
                //     ])
                //     ->visible(fn($operation) => $operation  == 'edit' )
                //     ->columnSpan(1)
            ])
            ->columns(2)
            ->extraAttributes(['class' => '', 'id' => 'consutation-form']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['medicines', 'patient']))
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.full_name')
                    ->numeric()
                    ->searchable()
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
                ActionsAction::make('print prescription')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->modalContent(function($record): View {
                        return view('consultations.print', [
                            'medicines' => $record->medicines,
                            'patient' => $record->patient,
                        ]);
                    })
                    ->slideOver(),
            ])
            ;
    }

    #[On('selected-history')]
    public function selectHistory()
    {
        dd('testing history12');
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
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditWithHistory::route('/{record}/edit'),
        ];
    }

    // additional methods

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
