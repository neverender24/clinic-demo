<?php

namespace App\Filament\Resources\Consultations\Schemas;

use App\Models\Patient;
use App\Models\Medicine;
use Illuminate\View\View;
use App\Enums\Enums\Status;
use App\Models\Consultation;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\HospitalAdmission;
use App\Models\ConsultationMedicine;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use App\Forms\Components\HistoryField;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\Patients\PatientResource;
use App\Filament\Resources\Medicines\MedicineResource;
use Filament\Schemas\Components\View as ComponentsView;

class ConsultationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                DatePicker::make('date')
                                    ->default(now())
                                    ->required()
                                    ->columnSpan([
                                        'default' => 'full',
                                        'xl' => '1'
                                    ]),
                                Select::make('patient_id')
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
                                        RichEditor::make('chief_complaint')
                                        ->required()
                                        ->toolbarButtons(self::onlyAllowedToolbar())
                                        ->fileAttachmentsDirectory('chief-complaint/'.now()->format('m-y'))
                                        ->visible(fn() => auth()->user()->can('addChiefComplaint', Consultation::class)),
                                        RichEditor::make('test_results')
                                            ->label('Medical Data')
                                            ->required()
                                            ->toolbarButtons(self::onlyAllowedToolbar())
                                            ->fileAttachmentsDirectory('test-results/'.now()->format('m-y'))
                                            ->visible(fn() => auth()->user()->can('addTestResult', Consultation::class)),
                                        RichEditor::make('diagnosis')
                                            ->required()
                                            ->toolbarButtons(self::onlyAllowedToolbar())
                                            ->columnSpanFull()
                                            ->fileAttachmentsDirectory('diagnosis/'.now()->format('m-y'))
                                            ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin'])),
                                        RichEditor::make('management')
                                            ->required()
                                            ->visible(fn() => auth()->user()->hasAnyRole(['Doctor', 'super_admin']))
                                            ->fileAttachmentsDirectory('management/'.now()->format('m-y'))
                                            ->columnSpan([
                                                'xl' => 'full'
                                            ]),
                                        DatePicker::make('next_follow_up_schedule')
                                            ->label('Patient\'s next follow up schedule (If necessary)')
                                            ->visible(fn($livewire) => auth()->user()->can('addFollowupSchedule', $livewire->record))
                                            // ->extraAttributes(['class' => 'mt-4'])
                                    ])
                                    ->columnSpanFull()
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
                                        ->addAction(function(Action $action) {
                                            return $action
                                                        ->label('Add medicine to prescription')
                                                        ->icon('healthicons-o-medicines')
                                                        ->color('primary')
                                                        ->link()
                                                        ->size('lg');
                                        })
                                        ->reorderable()
                                        ->default(fn ($state) => is_array($state) ? $state : [])
                                        // ->reorderable()
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
                                                        // modifyQueryUsing: fn(Builder $query) => $query->where('active', 1)
                                                    )
                                                    // ->preload()
                                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name}".($record->brand ? ' - '."<b>{$record->brand}</b>" : ''))
                                                    ->allowHtml()
                                                    // ->preload()
                                                    ->searchable(['name', 'brand'])
                                                    // ->searchable(function (Builder $query, $search): Builder {
                                                    //     return $query
                                                    //         ->where('brand', 'like', "%{$search}%")
                                                    //             ->orWhere('name', 'like', "%{$search}%");
                                                            
                                                    // })
                                                    ->getSearchResultsUsing(function (string $search) {
                                                        // dd($search);
                                                        return Medicine::where(function($q) use ($search) {
                                                            $q->where('brand', 'like', "%{$search}%")
                                                               ->orWhere('name', 'like', "%{$search}%");
                                                        })
                                                        ->where('active', 1)
                                                        ->limit(20)
                                                        ->get()
                                                        ->map(fn($item) => [
                                                            'id' => $item->id,
                                                            'name' => "
                                                                <div class='flex flex-col'>
                                                                    <div>$item->name</div>
                                                                    <div class='text-indigo-500'>$item->brand</div>
                                                                </div>
                                                            "
                                                        ])
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                    })
                                                    ->allowHtml()
                                                    ->required()
                                                    ->createOptionForm(function (Schema $schema) {
                                                        return MedicineResource::form($schema)->extraAttributes(['class' => 'w-full']);
                                                    })
                                                    ->createOptionAction(function(Action $action) {
                                                        return $action
                                                            ->modalHeading('Add Medicine')
                                                            ->mutateDataUsing(function(array $data) {
                                                                $data['user_id'] = auth()->id();
                                                                return $data;
                                                            });
                                                    })
                                                    ->editOptionForm(function (Schema $schema) {
                                                        return MedicineResource::form($schema)->extraAttributes(['class' => 'w-full']);
                                                    })
                                                    ->editOptionAction(function(Action $action, $state) {
                                                        Medicine::with('consultations')->find($state);
                                                        return $action
                                                                ->visible(fn($state) => Medicine::with('consultations')->find($state)?->consultations->isEmpty());
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
                            ComponentsView::make('patient_history')
                                ->view('forms.components.history-field')
                                // ->hiddenLabel()
                                // ->content(fn(): View => view('forms.components.history-field'))
                                ->visible(fn() => auth()->user()->doctor())
                                // ->dehydrated(false)
                                ,
                            ComponentsView::make('hospital_admission')
                                // ->hiddenLabel()
                                ->view(
                                    'filament.hospital_admission.history',
                                    fn($get) => [
                                        'hospital_admissions'=> HospitalAdmission::with('clinic')
                                                                ->where('patient_id', $get('patient_id'))->get()
                                    ]
                                )
                                // ->content(fn($get) => view('filament.hospital_admission.history', data: ['hospital_admissions'=> HospitalAdmission::with('clinic')
                                //                                                                                             ->where('patient_id', $get('patient_id'))->get()]))
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

}
