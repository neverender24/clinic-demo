<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use App\Models\ConsultationMedicine;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Infolists\Components\PatientEntry;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\PatientResource;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class EditWithHistory extends Page implements HasForms, HasTable, HasInfolists
{
    protected static string $resource = ConsultationResource::class;

    protected static string $view = 'filament.resources.consultation-resource.pages.edit-with-history';

    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;
    use InteractsWithInfolists;
    // use HasPageShield;

    public $patient_id;

    public Consultation $consultation;

    public $data;

    public $modal_open = false;

    public Consultation $historyData;

    protected static function getPermissionName(): string
    {
        return 'edit_as_doctor';
    }


    public function mount(int | string $record): void
    {
        
        $this->record = $this->resolveRecord($record)->load('consultationMedicines');

        $this->historyData = $this->record;

        // dd(auth()->user());

        $this->authorize('edit_as_doctor_consultation', [$this->record]);
        
        $this->patient_id = $this->record->patient_id;

        $this->record->medicines = $this->record->consultationMedicines->map(fn($item) => [
                                            "consultation_id" => $item->consultation_id,
                                            "medicine_id" => $item->medicine_id,
                                            "remarks" => $item->remarks,
                                            "quantity" => $item->quantity
                                        ]);
    
        $this->form->fill(collect($this->record)->except('consultation_medicines')->toArray());
        
    }

    public function form(Form $form): Form
    {
        return ConsultationResource::form($form)
            ->columns(1)
            ->statePath('data')
            ->model(Consultation::class);
    }
    
    public function table(Table $table): Table
    {   
        return $table
                ->query(fn() => Consultation::query()
                                ->where('patient_id', $this->patient_id)
                                ->whereDate('date', '<', $this->data['date'])
                )
                ->columns([
                    TextColumn::make('date')
                        ->label('Date of Consultation')
                        ->dateTime('F j, Y')
                ])
                ->actions([
                    // Action::make('select2')
                    //     ->action(function($record) {
                    //         $this->historyData = $record;
                    //         $this->dispatch('open-modal', id: 'history-modal');
                    //     }),
                    Action::make('select_history')
                        ->label('view')
                        ->modal()
                        ->modalWidth('7xl')
                        ->infolist([
                            Grid::make(5)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make(columns: 1)
                                            ->schema([
                                                PatientEntry::make('patient.full_name')
                                                    ->inlineLabel(false)
                                                    ->hiddenLabel()
                                                    ->size(TextEntry\TextEntrySize::Large)
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
                                                ->formatStateUsing(function($state){
                                                    return $state;
                                                })
                                                ->hiddenLabel()
                                                ->html(),
                                                TextEntry::make('pivot.remarks')
                                                ->hiddenLabel(),
                                                TextEntry::make('pivot.quantity')
                                                ->formatStateUsing(fn($state) => '#'.$state)
                                                ->hiddenLabel()
                                            ])
                                        ])
                                        ->columnSpan(2)
                            ])
                        ])
                        ->modalContent(new HtmlString('test'))
                        ->modalHeading(fn($record) => 'Consultation Details '.Carbon::parse($record->date)->format('F j, Y'))
                        ->modalFooterActions([
                            Action::make('copy_patient_details')
                                ->label('Copy Patient Record')
                                ->action(fn($record) => $this->selectHistory($record))
                                ->cancelParentActions()
                                ->color('indigo')
                                ->icon('healthicons-o-medical-records'),
                            Action::make('copy_prescription')
                                ->label('Copy Prescription')
                                ->action(fn($record) => $this->copyPrescription($record->medicines))
                                ->cancelParentActions()
                                ->color('info')
                                ->icon('healthicons-o-prescription-document')
                        ])

                        // ->action(fn($record) => $this->selectHistory($record))
                ])
                ->actionsColumnLabel('Action')
                ->heading('Previous Consultations')
                ->paginated(false);
    }

    public function HistoryInfolist(Infolist $infolist): Infolist
    {
        // dd($this->record);
        return $infolist
                ->record($this->historyData->load('medicines'))
                ->schema([
                    Grid::make(5)
                        ->schema([
                            Section::make()
                                ->schema([
                                    Grid::make(columns: 1)
                                        ->schema([
                                            PatientEntry::make('patient.full_name')
                                                ->inlineLabel(false)
                                                ->hiddenLabel()
                                                ->size(TextEntry\TextEntrySize::Large)
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
                                            ->formatStateUsing(function($state){
                                                return $state;
                                            })
                                            ->hiddenLabel()
                                            ->html(),
                                            TextEntry::make('pivot.remarks')
                                            ->hiddenLabel(),
                                            TextEntry::make('pivot.quantity')
                                            ->formatStateUsing(fn($state) => '#'.$state)
                                            ->hiddenLabel()
                                        ])
                                    ])
                                    ->headerActions([
                                        Action::make('Copy')
                                            ->action(fn($record) => $this->copyPrescription($record->medicines))
                                    ])
                                    ->columnSpan(2)
                        ])
                    ]);
    }

    // public function prescriptionTable(Table $table): Table
    // {
    //     return $table
    //             ->query(fn() => ConsultationMedicine::with('medicine')->where('consultation_id', $this->historyData->id))
    //             ->columns([
    //                 TextColumn::make('medicine.full_name_of_medicine')
    //             ]);
    // }

    protected function selectHistory($record)
    {
        // $record = collect($record)->except('date');

        // $record->date = $this->record->date; 

        // $record->id = $this->record->id; 

        // dd($this->record);
 

        // dd($record->chief_complaint);
        $this->data['test_results'].=$record->test_results;
        $this->data['chief_complaint'].=$record->chief_complaint;
        $this->data['diagnosis'].=$record->diagnosis;
        $this->data['management'].=$record->management;
        // $this->data = $record->toArray();

        // dd($this->data);
        
    }

    protected function copyPrescription($record)
    {
        $previous_meds = $record->map(fn($item) => collect($item->pivot)->except('consultation_id'))->values()->toArray();

        $new_meds = array_merge($this->data['medicines'], $previous_meds);

        $this->data['medicines'] = $new_meds;

    }

    public function submit()
    {
        $this->validate();

        DB::transaction(function() {
            try {
                $this->record->update($this->data);

                $this->record->medicines()->sync($this->data['medicines']);

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->send();

            } catch (\Throwable $th) {
                Notification::make()
                    ->title('Error')
                    ->body($th->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }

    public function cancelAction()
    {
        
    }
}
