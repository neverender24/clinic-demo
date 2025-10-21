<?php

namespace App\Filament\Resources\Consultations\Pages;

use Throwable;
use Carbon\Carbon;
use Illuminate\View\View;
use Filament\Tables\Table;
use App\Models\Consultation;
use Filament\Schemas\Schema;
use App\Trait\HasHistoryAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use App\Models\ConsultationMedicine;
use Filament\Support\Enums\TextSize;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Models\Scopes\ConsultationScope;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Fieldset;
use App\Infolists\Components\PatientEntry;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Filament\Resources\Patients\PatientResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use App\Filament\Resources\Consultations\ConsultationResource;
use App\Filament\Resources\Consultations\Schemas\ConsultationForm;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class EditWithHistory extends Page implements HasForms, HasTable, HasInfolists
{
    protected static string $resource = ConsultationResource::class;

    protected string $view = 'filament.resources.consultation-resource.pages.edit-with-history';

    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;
    use InteractsWithInfolists;
    use HasHistoryAction;
    use InteractsWithFormActions;
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

    public function getTitle(): string|Htmlable
    {
        return 'Edit Consultation';
    }

    public function mount(int | string $record): void
    {
        
        $this->record = Consultation::with('consultationMedicines')->findOrFail($record)->load('medicines');

      
        $this->historyData = $this->record;

        // dd(auth()->user());

        $this->authorize('edit_as_doctor_consultation', [$this->record]);

        $this->patient_id = $this->record->patient_id;

        // $this->record->medicines = $this->record->consultationMedicines->map(fn($item) => [
        //                                     "consultation_id" => $item->consultation_id,
        //                                     "medicine_id" => $item->medicine_id,
        //                                     "remarks" => $item->remarks,
        //                                     "quantity" => $item->quantity,
        //                                     "name" => $item->name,
        //                                     "brand" => $item->brand
        //                                 ]);

        $meds = $this->record->consultationMedicines->map(fn($item) => [
                                                "consultation_id" => $item->consultation_id,
                                                "medicine_id" => $item->medicine_id,
                                                "remarks" => $item->remarks,
                                                "quantity" => $item->quantity
                                            ]);
        $data = collect($this->record)->except('consultation_medicines', 'medicines');

        $formData = array_merge($data->toArray(), ['medicines' => $meds->toArray() ?? []]);
    
        $this->form->fill($formData);
        

    }

    public function form(Schema $schema): Schema
    {
        return ConsultationForm::configure($schema)
            ->columns(1)
            ->statePath('data')
            ->model(Consultation::class);
    }

    public function form2(Schema $schema): Schema
    {
        return $schema
                    ->components([
                        RichEditor::make('test')
                    ]);
    }

    public function table(Table $table): Table
    {
        return $table
                ->query(fn() => Consultation::query()
                                    ->patientPreviousConsultations(patient_id:$this->patient_id, date: $this->data['date'])
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
                    ActionsAction::make('select_history')
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
                            ActionsAction::make('copy_patient_details')
                                ->label('Copy Patient Record')
                                ->action(fn($record) => $this->selectHistory($record))
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
                                        ActionsAction::make('Copy')
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

    

    public function submit()
    {
        $this->validate();

        DB::transaction(function() {
            // dd($this->data);
            try {
                $this->record->update($this->data);
                $this->record->medicines()->detach();
                $this->record->medicines()->sync($this->data['medicines']);

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->send();

                $this->dispatch('refresh');

                redirect()->to($this->getResource()::getUrl('edit.consultation', ['record' => $this->record]));
                // redirect()->to($this->getResource()::getUrl('index'));

            } catch (Throwable $th) {
                Notification::make()
                    ->title('Error')
                    ->body($th->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            // Html2MediaAction::make('print_prescription')
            //     ->label('Prescription')
            //     ->color('success')
            //     ->icon('heroicon-o-printer')
            //     ->extraAttributes([
            //         'class' => 'hidden'
            //     ])
            //     ->content(function($record): View {
            //         // dd($record);
            //         return view('consultations.print', [
            //                     'medicines' => $record->medicines->chunk(6),
            //                     'patient' => $record->patient,
            //                     'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
            //                     'header_image' => $record->clinic->header_image,
            //                     'header_image1' => public_path("storage/{$record->clinic->header_image}"),
            //                 ]
            //             );
            //     })

            //     // ->preview()
            //     ->orientation()
            //     ->format('a5')
            //     // ->pagebreak('section', ['css', 'legacy'])
            //     // ->margin([2, 2, 0, 2])
            //     ->modalWidth('2xl'),
                DeleteAction::make()
                    ->successRedirectUrl(route('filament.admin.resources.consultations.index', [1])),
        ];
    }

    public function getPrintAction()
    {
        return Html2MediaAction::make('print_prescription')
                ->label('Prescription')
                ->color('success')
                // ->content(function(): View {
                //     $record = $this->record;
                //     return view('consultations.print', [
                //                 'medicines' => $record->medicines->chunk(6),
                //                 'patient' => $record->patient,
                //                 'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
                //                 'header_image' => $record->clinic->header_image,
                //                 'header_image1' => public_path("storage/{$record->clinic->header_image}"),
                //             ]
                //         );
                // })
                // ->preview()
                ->orientation()
                ->format('a5')
                // ->pagebreak('section', ['css', 'legacy'])
                // ->margin([2, 2, 0, 2])
                ->modalWidth('2xl');
    }

    protected function renderToHtml($content): string 
    {
        return RichContentRenderer::make($content)->toHtml();
    }

}
