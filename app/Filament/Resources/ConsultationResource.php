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
use App\Models\Medicine;
use App\Trait\HasStatusAction;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

class ConsultationResource extends Resource implements HasShieldPermissions
{
    use HasStatusAction;

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
            'edit_as_doctor'
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
                                    ->default(now())
                                    ->required()
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => '1'
                                    ]),
                                Forms\Components\Select::make('patient_id')
                                    ->label('Patient')
                                    ->relationship('patient', 'full_name')
                                    // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
                                    ->getOptionLabelsUsing(fn ($value) => Patient::find($value)->full_name)
                                    ->preload()
                                    ->searchable()
                                    
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
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => '3'
                                    ]),
                                Section::make()
                                    ->extraAttributes(['class' => 'mt-4'])
                                    ->schema([
                                        Forms\Components\RichEditor::make('test_results')
                                            ->label('Medical Data')
                                            ->required()
                                            ->toolbarButtons(self::onlyAllowedToolbar())
                                            // ->columnSpan(2)
                                            // ->columnSpan(function() {
                                            //     if (auth()->user()->doctor()) {
                                            //         return 'full';
                                            //     }
                                            //     return '2';
                                            // })
                                            ->visible(fn() => auth()->user()->can('addTestResult', static::$model)),
                                        Forms\Components\RichEditor::make('chief_complaint')
                                                ->required()
                                                ->toolbarButtons(self::onlyAllowedToolbar())
                                                // ->columnSpan(2)
                                                // ->columnSpanFull()
                                                // ->columnSpan(function() {
                                                //     if (auth()->user()->doctor()) {
                                                //         return 'full';
                                                //     }
                                                //     return '2';
                                                // })
                                                ->visible(fn() => auth()->user()->can('addChiefComplaint', static::$model)),
                                        Forms\Components\RichEditor::make('diagnosis')
                                            ->required()
                                            ->toolbarButtons(self::onlyAllowedToolbar())
                                            ->columnSpanFull()
                                            ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin'])),
                                        Forms\Components\TextInput::make('management')
                                            ->required()
                                            // ->toolbarButtons(self::onlyAllowedToolbar())
                                            ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin']))
                                            ->columnSpan([
                                                'lg' => 1,
                                                'xl' =>1
                                            ])
                                            ,
                                        DatePicker::make('next_follow_up_schedule')
                                            ->label('Patient\'s next follow up schedule (If necessary)')
                                            ->columnSpan([
                                                'lg' => 1,
                                                'xl' =>1
                                            ])
                                    ])
                                    // ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin']))
                                    ->columns([
                                        'lg' => 1,
                                        'xl' => '2'
                                    ]),
                            ])
                            ->columns([
                                'default' => 4
                            ])
                            ->columnSpan(2),
                            Section::make('Prescriptions')
                                ->schema([
                                    Repeater::make('medicines')
                                        ->relationship('consultationMedicines')
                                        ->label('Prescription')
                                        ->reorderable()
                                        ->schema([
                                            Grid::make([
                                                'lg' => 4
                                            ])
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
                                                    })
                                                    ->editOptionForm(function (Form $form) {
                                                        return MedicineResource::form($form)->extraAttributes(['class' => 'w-full']);
                                                    })
                                                    ->editOptionAction(function(Action $action, $state) {
                                                        return $action
                                                                ->visible(fn($state) => Medicine::with('consultations')->find($state)->consultations->isEmpty());
                                                    })
                                                    ->columnSpan([
                                                        'lg' => 'full',  
                                                    ]),
                                                TextInput::make('remarks')
                                                    ->required()
                                                    ->columnSpan([
                                                        'md' => 'full',
                                                        'lg' => 3
                                                    ]),
                                                TextInput::make('quantity')
                                                    ->required()
                                                    // ->columnSpan([
                                                    //     'lg' => 4,
                                                    //     'xl' => 1
                                                    // ]),
                                            ])
                                        ])
                                        ->grid([
                                            'xl' => 2
                                        ])
                                        ->hiddenLabel()
                                        // ->label('Prescription')
                                        ->columns(1)
                                        ->defaultItems(1)
                                        ->columnSpanFull()
                                ])
                                ->columnSpan(2)
                                ->visible(fn() => auth()->user()->hasRole('Doctor') || auth()->user()->superAdmin())
                                
                    ])
                    ->columnSpan(2)
                    // ->columnSpan(function($operation) {
                    //     if($operation == 'create') {
                    //         return 2;
                    //     }
                    //     return 1;
                    // })
                    ,
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
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['medicines', 'patient']))
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge(),
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
                Tables\Actions\EditAction::make()
                    ->disabled(fn($record) => $record->status->value == 'Done'),
                Tables\Actions\Action::make('edit_history')
                    ->visible(fn($record) => auth()->user()->can('edit_as_doctor_consultation'))
                    ->disabled(fn($record) => $record->status->value == 'Done')
                    ->label(fn() => auth()->user()->superAdmin() ? 'Edit as Doctor' : 'Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn($record) => route('filament.admin.resources.consultations.edit.consultation', [$record->clinic_id, $record->id])),
                Tables\Actions\ActionGroup::make([
                    Html2MediaAction::make('print_prescription')
                        ->label('Prescription')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        ->content(function($record): View { 
                            return view('consultations.print', [
                                        'medicines' => $record->medicines->chunk(6),
                                        'patient' => $record->patient,
                                        'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
                                        'header_image' => $record->clinic->header_image,
                                        'header_image1' => public_path("storage/{$record->clinic->header_image}"),
                                    ]
                                );
                        })
                        // ->preview()
                        ->orientation()
                        ->format('a5')
                        // ->pagebreak('section', ['css', 'legacy'])
                        // ->margin([2, 2, 0, 2])
                        ->modalWidth('2xl'),
                    Html2MediaAction::make('print_medcert')
                        ->label('Medical Certificate')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        ->format(format: 'letter')
                        ->content(fn($record): View => view('consultations.medcert', [
                            'medicines' => $record->medicines,
                            'patient' => $record->patient,
                            'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
                            'header_image' => asset('storage/'.$record->clinic->header_image),
                            'watermark' => asset('storage/'.$record->clinic->watermarks),
                            'consultation_date' => $record->date?->format('F d, Y')
                        ])),
                    // Tables\Actions\Action::make('print_prescription1')
                    //     ->label('Prescription')
                    //     ->hidden(false)
                    //     ->color('success')
                    //     ->icon('heroicon-o-printer')
                    //     ->modalContent(function($record): View {
                    //         return view('consultations.print', [
                    //             'medicines' => $record->medicines,
                    //             'patient' => $record->patient,
                    //             'next_follow_up_schedule' => $record->next_follow_up_schedule->format('F j, Y')
                    //         ]);
                    //     })
                    //     ->modalWidth('2xl')
                    //     ->modalSubmitAction(false)
                    //     ->modalCancelAction(false)
                    //     // ->modalFooterActions(function(Action))
                    //     ->slideOver(),
                    // Tables\Actions\Action::make('med_cert')
                    //     ->label('Medical Certification')
                    //     ->hidden(false)
                    //     ->color('success')
                    //     ->icon('heroicon-o-printer')
                    //     ->modalContent(function($record): View {
                    //         return view('consultations.print', [
                    //             'medicines' => $record->medicines,
                    //             'patient' => $record->patient,
                    //             'next_follow_up_schedule' => $record->next_follow_up_schedule->format('F j, Y')
                    //         ]);
                    //     })
                    //     ->modalWidth('7xl')
                    //     ->slideOver(),
                    Tables\Actions\Action::make('status')
                        ->label(fn($record) => static::statusLabel($record))
                        ->color(fn($record) => static::statusColor($record))
                        ->icon(fn($record) => static::statusIcon($record))
                        ->action(fn($record) => $record->changeStatus())
                        ->requiresConfirmation()
                ])
            ])
            ;
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
            // 'index' => Pages\CustomListConsultations::route('/'),
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
            'edit.consultation' => Pages\EditWithHistory::route('/{record}/edit-consultation'),
        ];
    }

    // additional methods

    public static function getNavigationBadge(): ?string
    {
        return transform(static::getModel()::query()->where('status', 'Pending')->count(), fn($value) => $value > 0 ? $value : null);
    }
}
