<?php

namespace App\Livewire;

use App\Models\Patient;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Consultation;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use App\Trait\HasHistoryAction;
use Livewire\Attributes\Reactive;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\TextSize;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Fieldset;
use App\Infolists\Components\PatientEntry;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Filament\Resources\Patients\PatientResource;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use App\Filament\Resources\Consultations\ConsultationResource;

class ListOfConsultation extends Component implements HasTable, HasForms, HasActions
{

    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;
    use HasHistoryAction;

    public ?array $data = [];

    #[Reactive]
    public $patient_id;

    public function mount(): void
    {
        // $this->record = $this->resolveR
        // dd($this);
        
    }

    // #[On('refresh_table')]
    // public function refreshTable(): void
    // {
    //     dd($this->patient_id);
    //     // $this->resetTable();
    // }

    public function table(Table $table): Table
    {

        // dd($this);
        return $table
            ->query(function () {
                return Consultation::query()->patientPreviousConsultations($this->patient_id, now()->addDay()->toDateString());
            })
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Date of Consultation')
                    ->description(function($record) {
                        return $record->created_at->format('H:i:s');
                    })
                    ->dateTime('F j, Y')
            ])
            ->recordActions([
                // Action::make('select2')
                //     ->action(function($record) {
                //         $this->historyData = $record;
                //         $this->dispatch('open-modal', id: 'history-modal');
                //     }),
                ActionsAction::make('select_history')
                    ->label('view')
                    ->modal()
                    ->modalWidth('7xl')
                    ->schema([
                        ComponentsGrid::make(5)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        ComponentsGrid::make(columns: 1)
                                            ->schema([
                                                TextEntry::make('patient.full_name')
                                                    ->inlineLabel(false)
                                                    ->hiddenLabel()
                                                    ->size(TextSize::Large)
                                                    // ->icon('healthicons-o-traumatism')
                                                    ->iconColor('white')
                                                    ->formatStateUsing(fn($state) => strtoupper($state)),
                                            ]),
                                        ComponentsGrid::make()
                                            ->extraAttributes(['class' => 'border-t-2'])
                                            ->schema([
                                                TextEntry::make('test_results')
                                                    ->html()
                                                    ->columnSpanFull(),
                                                Fieldset::make('Chief Complaint')
                                                    ->schema([
                                                        TextEntry::make('chief_complaint')
                                                            ->html()
                                                            ->hiddenLabel(),
                                                    ])
                                                    ->columnSpan(1),
                                                Fieldset::make('Diagnosis')
                                                    ->schema([
                                                        TextEntry::make('diagnosis')
                                                            ->html()
                                                            ->hiddenLabel(),
                                                    ])
                                                    ->columnSpan(1),
                                                Fieldset::make('Management')
                                                    ->schema([
                                                        TextEntry::make('management')
                                                            ->html()
                                                            ->hiddenLabel()
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpan(3),
                                Section::make('Prescription')
                                    ->schema([
                                        RepeatableEntry::make('medicines')
                                            ->hiddenLabel()
                                            ->schema([
                                                TextEntry::make('full_name_of_medicine')
                                                    ->formatStateUsing(function ($state) {
                                                        return $state;
                                                    })
                                                    ->hiddenLabel()
                                                    ->html(),
                                                TextEntry::make('pivot.remarks')
                                                    ->hiddenLabel(),
                                                TextEntry::make('pivot.quantity')
                                                    ->formatStateUsing(fn($state) => '#' . $state)
                                                    ->hiddenLabel()
                                            ])
                                    ])
                                    ->columnSpan(2)
                            ])
                    ])
                    // ->modalContent(new HtmlString('test'))
                    ->modalHeading(fn($record) => 'Consultation Details ' . Carbon::parse($record->date)->format('F j, Y'))
                    ->modalFooterActions([
                        ActionsAction::make('copy_patient_details')
                            ->label('Copy Patient Record')
                            ->action(fn($record) => $this->dispatch('testing-event'))
                            ->cancelParentActions()
                            ->color('indigo')
                            ->icon('healthicons-o-medical-records'),
                        ActionsAction::make('copy_prescription')
                            ->label('Copy Prescription')
                            ->action(fn($record) => $this->copyPrescription($record->medicines))
                            ->cancelParentActions()
                            ->color('info')
                            ->icon('healthicons-o-prescription-document')
                    ])

                // ->action(fn($record) => $this->selectHistory($record))
            ])
            ->recordActionsColumnLabel('Action')
            ->heading('Previous Consultations')
            ->paginated(false);
    }

    public function HistoryInfolist(Schema $schema): Schema
    {
        // dd($this->record);
        return $schema
            ->record($this->historyData->load('medicines'))
            ->components([
                Grid::make(5)
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make(columns: 1)
                                    ->schema([
                                        PatientEntry::make('patient.full_name')
                                            ->inlineLabel(false)
                                            ->hiddenLabel()
                                            ->size(TextSize::Large)
                                            ->icon('healthicons-o-traumatism')
                                            ->iconColor('white')
                                            ->formatStateUsing(fn($state) => strtoupper($state)),
                                    ]),
                                Grid::make()
                                    ->extraAttributes(['class' => 'border-t-2'])
                                    ->schema([
                                        TextEntry::make('test_results')
                                            ->html()
                                            ->columnSpanFull(),
                                        Fieldset::make('Chief Complaint')
                                            ->schema([
                                                TextEntry::make('chief_complaint')
                                                    ->html()
                                                    ->hiddenLabel(),
                                            ])
                                            ->columnSpan(1),
                                        Fieldset::make('Diagnosis')
                                            ->schema([
                                                TextEntry::make('diagnosis')
                                                    ->html()
                                                    ->hiddenLabel(),
                                            ])
                                            ->columnSpan(1),
                                        Fieldset::make('Management')
                                            ->schema([
                                                TextEntry::make('management')
                                                    ->html()
                                                    ->hiddenLabel()
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(3),
                        Section::make('Prescription')
                            ->schema([
                                RepeatableEntry::make('medicines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('full_name_of_medicine')
                                            ->formatStateUsing(function ($state) {
                                                return $state;
                                            })
                                            ->hiddenLabel()
                                            ->html(),
                                        TextEntry::make('pivot.remarks')
                                            ->hiddenLabel(),
                                        TextEntry::make('pivot.quantity')
                                            ->formatStateUsing(fn($state) => '#' . $state)
                                            ->hiddenLabel()
                                    ])
                            ])
                            ->headerActions([
                                ActionsAction::make('Copy')
                                    ->action(fn($record) => $this->copyPrescription($record->medicines))
                            ])
                            ->columnSpan(2)
                    ])
            ]);
    }

    public function form(Schema $schema): Schema
    {
        // return $form
        //     ->schema([
        //         DatePicker::make('date')
        //                 ->required(),
        //                 Select::make('patient_id')
        //                             ->label('Patient')
        //                             // ->getSearchResultsUsing(fn (string $search) => Patient::query()->where('full_name', 'like', "%$search%")->pluck('full_name', 'id'))
        //                             ->getOptionLabelsUsing(fn ($value) => Patient::find($value)->full_name)
        //                             ->preload()
        //                             ->searchable()
        //                             ->relationship('patient', 'full_name')
        //                             ->createOptionForm(function (Form $form) {
        //                                 return PatientResource::form($form)->extraAttributes(['class' => 'w-full']);
        //                             })
        //                             ->createOptionAction(function(Action $action) {
        //                                 return $action
        //                                     ->modalWidth('xl')
        //                                     ->modalHeading('Create Patient')
        //                                     ->mutateFormDataUsing(function(array $data) {
        //                                         $data['user_id'] = auth()->id();
        //                                         return $data;
        //                                     });
        //                             })
        //                             ->live()
        //                             ->required()
        //                             ->columnSpan(3)
        //     ])
        //     ->model(Consultation::class)
        //     ->statePath('data')
        //     ;
        return ConsultationResource::form($schema)
                ->model(Consultation::class)
                ->statePath('data');
    }

    public function render()
    {
        return view('livewire.list-of-consultation');
    }
}
