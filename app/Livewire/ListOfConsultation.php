<?php

namespace App\Livewire;

use App\Filament\Resources\Consultations\Schemas\ConsultationInfolist;
use App\Models\Consultation;
use App\Trait\HasHistoryAction;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ListOfConsultation extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithForms;
    use HasHistoryAction;

    public ?array $data = [];

    #[Reactive]
    public $patient_id, $date;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Consultation::query()
                ->patientPreviousConsultations(patient_id: $this->patient_id, date: $this->date)
                ->latest('date')
            )
            ->columns([
                TextColumn::make('date')
                    ->label('Date of Consultation')
                    ->dateTime('F j, Y'),
                // TextColumn::make('charge')
                //     ->label('Charge')
                //     ->default(function ($record) {
                //         return Number::format($this->getTotal($record->fee, $record->follow_up_fees, $record->procedure_fee, $record->discount), 2);
                //     }),
            ])
            ->recordAction('select_history')
            ->recordActions([
                Action::make('select_history')
                    ->label('view')
                    ->modal()
                    ->modalWidth('4xl')
                    ->stickyModalFooter()
                    ->stickyModalHeader()
                    ->slideOver()
                    // ->schema([
                    //     Tabs::make('tabs')
                    //         ->dense()
                    //         ->gap(false)
                    //         ->extraAttributes([
                    //             'class' => 'patient-history',
                    //         ])
                    //         ->vertical()
                    //         ->tabs([
                    //             Tab::make('Consultation')
                    //                 ->dense()
                    //                 ->gap(false)
                    //                 ->schema([
                    //                     Section::make()
                    //                         ->columns(1)
                    //                         ->schema([
                    //                             Section::make()
                    //                                 ->schema([
                    //                                     TextEntry::make('chief_complaint')
                    //                                         ->label('Subjective')
                    //                                         ->formatStateUsing(fn ($state) => $this->renderToHtml($state))
                    //                                         ->html(),
                    //                                 ]),
                    //                             Section::make()
                    //                                 ->schema([
                    //                                     TextEntry::make('test_results')
                    //                                         ->label('Objective')
                    //                                         ->formatStateUsing(fn ($state) => $this->renderToHtml($state))
                    //                                         ->html(),
                    //                                 ])
                    //                                 ->hiddenLabel(false),
                    //                             Section::make()
                    //                                 ->schema([
                    //                                     TextEntry::make('diagnosis')
                    //                                         ->label('Assessment')
                    //                                         ->formatStateUsing(fn ($state) => $this->renderToHtml($state))
                    //                                         ->html(),
                    //                                 ])
                    //                                 ->hiddenLabel(false),
                    //                             Section::make()
                    //                                 ->schema([
                    //                                     TextEntry::make('management')
                    //                                         ->label('Plan')
                    //                                         ->formatStateUsing(fn ($state) => $this->renderToHtml($state))
                    //                                         ->html(),
                    //                                 ])
                    //                                 ->hiddenLabel(false),
                    //                         ]),
                    //                 ]),
                    //             Tab::make('prescription')
                    //                 ->badge(fn ($record) => $record->medicines->count())
                    //                 ->dense()
                    //                 ->gap(false)
                    //                 ->schema([
                    //                     RepeatableEntry::make('medicines')
                    //                         ->hiddenLabel()
                    //                         ->grid(2)
                    //                         ->dense()
                    //                         ->gap(false)
                    //                         ->schema([
                    //                             TextEntry::make('full_name_of_medicine')
                    //                                 ->formatStateUsing(fn ($state) => $state)
                    //                                 ->hiddenLabel()
                    //                                 ->html(),
                    //                             TextEntry::make('pivot.remarks')
                    //                                 ->hiddenLabel(),
                    //                             TextEntry::make('pivot.quantity')
                    //                                 ->formatStateUsing(fn ($state) => '#' . $state)
                    //                                 ->hiddenLabel(),
                    //                         ]),
                    //                 ]),
                    //             Tab::make('Other Attachments')
                    //                 ->dense()
                    //                 ->gap(false)
                    //                 ->schema([
                    //                     ImageEntry::make('attachments')
                    //                         ->imageWidth('100%')
                    //                         ->imageHeight('100%'),
                    //                 ]),
                    //         ]),
                    // ])
                    ->schema(fn(Schema $schema) => ConsultationInfolist::configure($schema))
                    ->modalHeading(fn ($record) => 'Consultation Details ' . Carbon::parse($record->date)->format('F j, Y'))
                    ->modalFooterActions(function (Action $action) {
                        return [
                            Action::make('copy_patient_details')
                                ->label('Copy Patient Record')
                                ->action(fn ($record) => $this->dispatch('copy-data', record: $record, type: 'patient-data'))
                                ->cancelParentActions()
                                ->color('indigo')
                                ->icon('healthicons-o-medical-records')
                                ->visible(fn () => auth()->user()->doctor()),
                            Action::make('copy_prescription')
                                ->label('Copy Prescription')
                                ->action(fn ($record) => $this->dispatch('copy-data', record: $record->medicines, type: 'prescription'))
                                ->cancelParentActions()
                                ->color('info')
                                ->icon('healthicons-o-prescription-document')
                                ->visible(fn () => auth()->user()->doctor()),
                            $action->getModalCancelAction(),
                        ];
                    }),
            ])
            ->recordActionsColumnLabel('Action')
            ->heading('Previous Consultations')
            ->paginated(true)
            ->paginationPageOptions([
                'All',
                3,
                4,
                5,
            ])
            ->defaultPaginationPageOption(4);
    }

    public function render()
    {
        return view('livewire.list-of-consultation');
    }
}