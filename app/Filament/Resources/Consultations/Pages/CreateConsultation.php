<?php

namespace App\Filament\Resources\Consultations\Pages;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\TextSize;
use Filament\Schemas\Components\Fieldset;
use Filament\Actions;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Consultation;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use App\Trait\HasHistoryAction;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Infolists\Components\PatientEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action as FilamentAction;
use App\Filament\Resources\Consultations\ConsultationResource;
use App\Models\HospitalAdmission;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;

class CreateConsultation extends CreateRecord 
{
    // use InteractsWithTable;
    use HasHistoryAction;

    protected static string $resource = ConsultationResource::class;

    public $hospital_admission = 'test';

    public function getExtraBodyAttributes(): array
    {
        return [
            'id' => 'consultation-resource',
        ];
    }

    #[On('copy-data')]
    public function copyPatientData($record, $type)
    {
        if ($type === 'patient-data') {
            
            $this->selectHistory(fluent($record));

        } else if($type === 'prescription') {

            $this->copyPrescription(collect($record));

        }
    }
   

    #[On('update-from-admission')]
    public function copyData($data, $field = "all")
    {
        $data['test_results'] = $data['remarks'];
        $data['diagnosis'] = $data['final_diagnosis'];
        if ($field == 'all') {
            # code...
            $this->data['diagnosis'] .= $data['final_diagnosis'];
            $this->data['test_results'] .= $data['remarks'];
        } else {
            $this->data[$field] .= $data[$field];
        }

        Notification::make()
            ->success()
            ->title('Copied')
            ->body('Please click cancel to close the dialog box')
            ->icon('heroicon-o-check')
            ->send();

        if ($field == 'all') {
            $this->dispatch('modal-close');
        }
    }

    // public function getFormActions(): array
    // {
    //     return [
    //         FilamentAction::make('create')
    //             ->action('create'),
    //         FilamentAction::make('cancel')
    //             ->url(fn() => $this->previousUrl)
    //             ->color('gray')
    //     ];
    // }
    
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['queueing_number'] = $this->checkLastQueue($data['date']) + 1;   
        $data['clinic_id'] = Filament::getTenant()->id;

        return $data;
    }

    // public function create(bool $another = false): void
    // {
    //     dd('test');
    // }
    
    protected function checkLastQueue($date)
    {
        return Consultation::whereDate('date', $date)->max('queueing_number');
    }

    protected function getRedirectUrl(): string
    {
        // if (auth()->user()->doctor()) {
        //     return $this->getResource()::getUrl('edit.consultation', [$this->record->id]);
        // } else {
        //     return $this->getResource()::getUrl('edit', [$this->record->id]);
        // }
        return $this->getResource()::getUrl('edit', [$this->record->id]);
    } 

    public function table(Table $table): Table
    {
        return $table->query(fn() => Consultation::query()
                                        ->patientPreviousConsultations(patient_id:$this->data['patient_id'], date: $this->data['date'])
                                        ->latest('date'))
        ->columns([
            TextColumn::make('date')
                ->label('Date of Consultation')
                ->dateTime('F j, Y')
        ])
        ->recordActions([
            // Action::make('select2')
            //     ->action(function($record) {
            //         $this->historyData = $record;
            //         $this->dispatch('open-modal', id: 'history-modal');
            //     }),
            FilamentAction::make('select_history')
                ->label('view')
                ->modal()
                ->modalWidth('7xl')
                ->schema([
                    Grid::make(5)
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make(columns: 1)
                                    ->schema([
                                        TextEntry::make('patient.full_name')
                                            ->size(TextSize::Large)
                                            ->weight(FontWeight::ExtraBold)
                                            ->icon('healthicons-o-traumatism')
                                            ->iconColor('white')
                                        // PatientEntry::make('patient.full_name')
                                        //     ->inlineLabel(false)
                                        //     ->hiddenLabel()
                                        //     ->size(TextSize::Large)
                                        //     ->icon('healthicons-o-traumatism')
                                        //     ->iconColor('white')
                                        //     ->formatStateUsing(fn($state) => strtoupper($state)),
                                    ]),
                                Grid::make()
                                    ->extraAttributes(['class' => 'border-t-2'])
                                    ->schema([
                                        // RichContentRenderer::make()
                                        // TextEntry::make('test_results')
                                        //     ->html()
                                        //     ->columnSpanFull(),
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
                                        TextEntry::make('active')
                                            ->hiddenLabel()
                                            // ->icon(fn($state) => match ($state) {
                                            //     1 => 'heroicon-o-check-badge',
                                            //     0 => 'heroicon-o-x-mark',
                                            // })
                                            ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive')
                                            ->color(fn($state) => $state ? 'primary' : 'danger')
                                            ->iconColor(fn($state) => $state ? 'primary' : 'danger')
                                            ->icon(fn($state) => $state ? 'heroicon-o-check-badge' : 'heroicon-o-x-mark')
                                            // ->trueIcon()
                                            // ->falseIcon()
                                            ,
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
                // ->modalContent(new HtmlString('test'))
                ->modalHeading(fn($record) => 'Consultation Details '.Carbon::parse($record->date)->format('F j, Y'))
                ->modalFooterActions([
                    FilamentAction::make('copy_patient_details')
                        ->label('Copy Patient Record')
                        ->action(fn($record) => $this->selectHistory($record))
                        ->cancelParentActions()
                        ->color('indigo')
                        ->icon('healthicons-o-medical-records'),
                    FilamentAction::make('copy_prescription')
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

}
