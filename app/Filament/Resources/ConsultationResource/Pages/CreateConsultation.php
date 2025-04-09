<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Consultation;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use App\Trait\HasHistoryAction;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Infolists\Components\PatientEntry;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action as FilamentAction;
use App\Filament\Resources\ConsultationResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;

class CreateConsultation extends CreateRecord implements HasTable
{
    use InteractsWithTable;
    use HasHistoryAction;

    protected static string $resource = ConsultationResource::class;

    public function getExtraBodyAttributes(): array
    {
        return [
            'id' => 'consultation-resource',
        ];
    }

    public function getFormActions(): array
    {
        return [
            FilamentAction::make('create')
                ->action('create'),
            FilamentAction::make('cancel')
                ->url(fn() => $this->previousUrl)
                ->color('gray')
        ];
    }
    
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
        return Consultation::currentConsultations($date)->max('queueing_number');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit.consultation', [$this->record->id]);
    } 

    public function table(Table $table): Table
    {
        return $table->query(fn() => Consultation::query()->patientPreviousConsultations(patient_id:$this->data['patient_id'], date: $this->data['date']))
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
                // ->modalContent(new HtmlString('test'))
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

}
