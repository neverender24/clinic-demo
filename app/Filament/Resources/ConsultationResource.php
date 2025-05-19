<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use App\Models\Medicine;
use Filament\Forms\Form;
use Illuminate\View\View;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Consultation;
use Livewire\Attributes\Url;
use App\Trait\HasStatusAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Actions\StaticAction;
use function Laravel\Prompts\form;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use App\Models\ConsultationMedicine;
use Filament\Forms\Components\Select;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ConsultationScope;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Livewire\Consultation\ListRecords;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Forms\Components\View as ComponentsView;
use App\Filament\Resources\ConsultationResource\Pages;
use App\Filament\Resources\ConsultationResource\Pages\CreateConsultation;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;
use Asmit\FilamentMention\Forms\Components\RichMentionEditor;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\ConsultationResource\RelationManagers;
use App\Filament\Resources\ConsultationResource\Pages\ListConsultations;
use App\Forms\Components\HistoryField;
use App\Models\HospitalAdmission;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Set;
use Filament\Support\Enums\MaxWidth;

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
            'edit_as_doctor',
            'add_followup_schedule'
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
                                        'xl' => '1'
                                    ]),
                                Forms\Components\Select::make('patient_id')
                                    ->label('Patient')
                                    ->relationship('patient', 'full_name')
                                    // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
                                    ->getOptionLabelsUsing(fn ($value) => Patient::find($value)->full_name)
                                    ->afterStateUpdated(function($livewire) {
                                        $livewire->getTable();
                                    }
                                    )
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
                                        'xl' => 3
                                    ])
                                    ,
                                Section::make()
                                    ->extraAttributes(['class' => 'mt-4'])
                                    ->schema([
                                        Forms\Components\RichEditor::make('chief_complaint')
                                        ->required()
                                        ->toolbarButtons(self::onlyAllowedToolbar())
                                        ->visible(fn() => auth()->user()->can('addChiefComplaint', static::$model)),
                                        Forms\Components\RichEditor::make('test_results')
                                            ->label('Medical Data')
                                            ->required()
                                            ->toolbarButtons(self::onlyAllowedToolbar())
                                            ->visible(fn() => auth()->user()->can('addTestResult', static::$model)),
                                        Forms\Components\RichEditor::make('diagnosis')
                                            ->required()
                                            ->toolbarButtons(self::onlyAllowedToolbar())
                                            ->columnSpanFull()
                                            ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin'])),
                                        Forms\Components\RichEditor::make('management')
                                            ->required()
                                            ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin']))
                                            ->columnSpan([
                                                'xl' => 'full'
                                            ]),
                                        DatePicker::make('next_follow_up_schedule')
                                            ->label('Patient\'s next follow up schedule (If necessary)')
                                            ->visible(fn($livewire) => auth()->user()->can('addFollowupSchedule', $livewire->record))
                                            // ->extraAttributes(['class' => 'mt-4'])
                                    ])
                                    ->columns([
                                        'xl' => 2
                                    ]),
                            ])
                            ->columns(4)
                            ->columnSpan(2),
                            Section::make('Prescriptions')
                                ->schema([
                                    Repeater::make('medicines')
                                        ->relationship('consultationMedicines')
                                        ->label('Prescription')
                                        ->addAction(function(StaticAction $action) {
                                            return $action
                                                        ->label('Add medicine to prescription')
                                                        ->icon('healthicons-o-medicines')
                                                        ->color('primary')
                                                        ->link()
                                                        ->size('lg');
                                        })
                                        ->reorderable()
                                        ->schema([
                                            Grid::make([
                                                'lg' => 4
                                            ])
                                            ->schema([
                                                Select::make('medicine_id')
                                                    ->label('Medicine')
                                                    ->relationship(
                                                        'medicine', 
                                                        'name',
                                                        modifyQueryUsing: fn(Builder $query) => $query->where('active', 1)
                                                    )
                                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name}".($record->brand ? ' - '."<b>{$record->brand}</b>" : ''))
                                                    ->allowHtml()
                                                    ->preload()
                                                    ->searchable(['brand', 'name'])
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
                                                    ->datalist(fn() => ConsultationMedicine::distinct('remarks')->pluck('remarks')->toArray())
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
                                        ->defaultItems(0)
                                        ->columnSpanFull()
                                ])
                                ->columnSpan(2)
                                ->visible(fn() => auth()->user()->hasRole('Doctor') || auth()->user()->superAdmin())

                    ])
                    ->columnSpan([
                        'default' => auth()->user()->doctor() ? 2 : 3,
                    ]),
                    Grid::make(1)
                        ->schema([
                            Placeholder::make('patient_history')
                                ->hiddenLabel()
                                ->content(fn(): View => view('forms.components.history-field'))
                                ->visible(fn() => auth()->user()->doctor())
                                ->dehydrated(false),
                            Placeholder::make('hospital_admission')
                                ->hiddenLabel()
                                ->content(fn($get) => view('filament.hospital_admission.history', data: ['hospital_admissions'=> HospitalAdmission::with('clinic')
                                                                                                                            ->where('patient_id', $get('patient_id'))->get()]))
                                ->visible(fn() => auth()->user()->doctor())
                                ->live()
                                ->dehydrated(false),
                            // HistoryField::make('hospital_admission')
                            //     // ->default(fn($get) => [$get('patient_id')])
                            //     // ->reactive()
                            //     ->dehydrated()
                        ])
                        ->columnSpan([
                            'default' => 1,
                        ])
                        ->visible(fn($operation) => $operation == 'create'),
            ])
            ->columns([
                'default' => 3,
                'lg' => 3
            ])
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
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScope(ConsultationScope::class)
                                                    ->with(['clinic', 'medicines', 'patient' => fn($query) => $query->withTrashed()])
            )
            ->defaultSort('queueing_number')
            ->columns([
                Tables\Columns\TextColumn::make('queueing_number')
                    ->label('Queue')
                    ->formatStateUsing(fn($state) => sprintf('%02d', $state))
                    ->sortable()
                    ->visible(fn($livewire) => $livewire->activeTab == 'current'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.full_name')
                    ->color(fn($record) => $record->patient->trashed() ? 'danger' : '')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    // ->formatStateUsing(function($record) {
                    //     dd($record);
                    // })
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
            ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->filters([
                Filter::make('date')
                    ->form([
                        Select::make('month')
                            ->options(function() {
                                $months = [];
                                foreach (range(1,12) as $key => $value) {
                                    $months[$key] = [
                                        'text' => Carbon::create()->day(1)->month($value)->format('F'),
                                        'value' => $value
                                    ];
                                }

                                return collect($months)->pluck('text', 'value');
                            })
                            ->visible(function($livewire) {
                                return $livewire->activeTab !== 'current';
                            }),
                        Select::make('year')
                            ->options(function() {
                                $years = [];
                                foreach (range(2024,now()->year) as $key => $value) {
                                    $years[$key] = [
                                        'value' => $value
                                    ];
                                }

                                return collect($years)->pluck('value', 'value');
                            })
                            ->visible(function($livewire) {
                                return $livewire->activeTab === 'all';
                            })
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['month'],
                                fn (Builder $query, $month): Builder => $query->whereMonth('date', $month),
                            )
                            ->when(
                                $data['year'],
                                fn (Builder $query, $year): Builder => $query->whereYear('date', $year),
                            );
                    })
                    // ->visible(function($livewire) {
                    //     return $livewire->activeTab !== 'current';
                    // })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn($record) => $record->status->value == 'Done'),
                Tables\Actions\DeleteAction::make()
                    ->disabled(fn($record) => $record->status->value == 'Done'),
                Tables\Actions\Action::make('edit_history')
                    ->visible(fn($record) => auth()->user()->can('edit_as_doctor_consultation'))
                    ->disabled(fn($record) => $record->status->value == 'Done')
                    ->label(fn() => auth()->user()->superAdmin() ? 'Edit as Doctor' : 'Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn($record) => route('filament.admin.resources.consultations.edit.consultation', [$record->clinic_id, $record->id])),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('medical_cert_form')
                        ->label('Medical Certificate Form')
                        ->color('primary')
                        ->icon('heroicon-s-document-text')
                        // ->visible(fn($record) => !filled($record->approximate_days) && !filled($record->estimated_date) && !filled($record->medical_cert_remarks))
                        ->form([
                            Forms\Components\Grid::make(2)
                                ->schema([

                                    Forms\Components\TextInput::make('approximate_days')
                                        ->label('Days of rest and recovery'),
                                    Forms\Components\DatePicker::make('estimated_date'),
                                ]),
                            Forms\Components\RichEditor::make('medical_cert_remarks')
                                ->label('Remarks'),
                        ])
                        ->fillForm(fn($record) => $record->toArray())
                        ->action(function($data, $record) {
                            try {
                                $record->update($data);
                                Notification::make()
                                ->success()
                                ->title('Success')
                                ->body('The changes have been saved');
                            } catch (\Throwable $th) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($th->getMessage());
                            }
                        }),
                    Tables\Actions\Action::make('admitting_order')
                        ->label('Admitting Order Form')
                        ->icon('heroicon-s-document-text')
                        ->form([
                            RichMentionEditor::make('admitting_order_data')
                            ->mentionsItems(function () {
                                return Patient::all()->map(function ($user) {
                                    return [
                                        'display_name' => $user->full_name,
                                        'name' => $user->full_name,
                                        'address' => $user->address,
                                        'avatar' => asset('images/user.svg'),
                                        'url' => 'admin/users/' . $user->id,
                                    ];
                                })->toArray();
                            })
                            ->default(fn($record) => transform($record->admitting_order_data, fn($value) => $value == '' || $value == null ? null: $value)
                                                            ?? '<p>&nbsp;To: <span class="text-underline">&nbsp; &nbsp; &nbsp; &nbsp;</span></p><p><br></p><p><br></p><p><br></p><p>&nbsp;- Please admit patient to <span class="text-underline"> &nbsp; &nbsp; &nbsp; &nbsp;</span>&nbsp;</p><p>&nbsp;- Secure consent to care&nbsp;</p><p>&nbsp;- Diet&nbsp;</p><p>&nbsp;- IVF&nbsp;</p><p>&nbsp;- Diagnostics:&nbsp;</p><p><br></p><p><br></p><p>&nbsp;- Medications:&nbsp;</p><p><br></p><p><br></p><p>&nbsp;- VS q4 and I &amp; O qShift&nbsp;</p><p>&nbsp;- Watchout for unusualities&nbsp;</p><p>&nbsp;- Kindly inform me once admitted&nbsp;</p><p>&nbsp;- Refer accordingly&nbsp;</p><p><br></p><p>- Special Instructions (if any):</p>')
                        ])
                        // ->fillForm(fn($record) => [
                        //     'admitting_order_data' => $record->admitting_order_data
                        // ])
                        ->action(function($data, $record) {
                        //    dd($data);
                            try {
                                //code...
                                $record->update($data);
                                Notification::make()
                                ->success()
                                ->title('Success')
                                ->body('The changes have been saved');
                            } catch (\Throwable $th) {
                                dd($th->getMessage());
                                Notification::make()
                                    ->title('Error')
                                    ->body($th->getMessage());
                            }
                        }),
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
                        ->preview()
                        // ->action(fn($data) => dd($data))
                        // ->visible(fn($record) => filled($record->approximate_days) && filled($record->estimated_date) && filled($record->medical_cert_remarks))
                        ->content(fn($record): View => view('consultations.medcert', [
                            'medicines' => $record->medicines,
                            'patient' => $record->patient,
                            'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
                            'header_image' => asset('storage/'.$record->clinic->medcert_header_image),
                            'watermark' => asset('storage/'.$record->clinic->watermarks),
                            'consultation_date' => $record->date?->format('F d, Y'),
                            'medical_cert_remarks' => $record->medical_cert_remarks,
                            'approximate_days' => $record->approximate_days,
                            'estimated_date' => $record->estimated_date ? $record->estimated_date->format('F j, Y') : '',
                            'diagnosis' => $record->diagnosis
                        ])),
                    Html2MediaAction::make('print_admitting_order')
                        ->label('Admitting Order')
                        ->color('success')
                        ->modalWidth(MaxWidth::MaxContent)
                        ->icon('heroicon-o-printer')
                        ->format('a5')
                        ->content(function($record): View {
                            // dd($record->admitting_order_data);
                            return view('consultations.admitting-order', [
                                'data' => $record->admitting_order_data,
                                'header_image' => $record->clinic->header_image,
                            ]);
                        })
                        ->preview()
                        // ->action(fn($data) => dd($data))
                        // ->visible(fn($record) => filled($record->approximate_days) && filled($record->estimated_date) && filled($record->medical_cert_remarks))
                        // ->content(fn($record): View => view('consultations.admitting-order', [
                        //     'data' => $record->admitting_order_data,
                        //     'header_image' => $record->clinic->header_image,
                        //     // 'patient' => $record->patient,
                        // ]))
                        ,
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
                        ->hidden(fn() => ! auth()->user()->doctor())
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
            // 'create' => Pages\CreateConsultation::route('/create'),
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
