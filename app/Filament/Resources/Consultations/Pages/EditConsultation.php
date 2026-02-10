<?php

namespace App\Filament\Resources\Consultations\Pages;

use Filament\Actions;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use App\Trait\HasHistoryAction;

use function Filament\authorize;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use App\Filament\Resources\Consultations\ConsultationResource;

class EditConsultation extends EditRecord
{

    use HasHistoryAction;
    
    protected static string $resource = ConsultationResource::class;

    // protected static string $view = 'filament.consultations.list-records';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        // dd($this->record);
        // $this->authorize('editRecord', $this->record);

        $this->form->fill($this->record->toArray());
        
    }

    #[On('testing-event')]
    public function testing()
    {
        $this->data['chief_complaint'] = '\n testing ni';
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
    
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
             $this->printPrescriptionAction(),

        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->printPrescriptionAction(),
            $this->printMedcertAction(),
            $this->printClinicalAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function printPrescriptionAction(): Action
    {
        return Action::make('prescription')
                ->color('success')
                ->icon(Heroicon::OutlinedPrinter)
                ->url(fn($record) => route('pdf.new-tab', [
                    'id' => $record->id,
                    'paper' => 'A5'
                ]), shouldOpenInNewTab: true);
    }

    protected function printMedcertAction(): Action 
    {
        return Action::make('medical_cert_form')
                        ->label('Medical Certificate')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        // ->visible(fn($record) => !filled($record->approximate_days) && !filled($record->estimated_date) && !filled($record->medical_cert_remarks))
                        ->schema([
                            Section::make()
                                ->schema([
                                    Textarea::make('medical_cert_remarks')
                                        ->label('Remarks')
                                ])
                        ])
                        ->fillForm(function($record) {
                            $data = $record;
                            return [
                                // 'estimated_date' => $data->estimated_date,
                                // 'estimated_date_to' => $data->estimated_date_to,
                                // 'approximate_days' => $data->approximate_days,
                                // 'return_date' => $data->return_date,
                                'medical_cert_remarks' => $data->medical_cert_remarks 
                            ];
                        })
                        ->modalSubmitActionLabel('Print')
                        ->action(function ($data, $record) {
                            // dd($data);
                            
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
                        })
                        ->after(fn($livewire, $record) =>  $livewire->js("window.open('".route('pdf.a5.medcert', ['id' => $record->id, 'type' => 'Medical Certificate', 'paper' => 'A5'])."', '_blank')"));
    }

    protected function printClinicalAction(): Action
    {
        return Action::make('clinical_orders')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        ->closeModalByClickingAway(false)
                        ->schema([
                            Select::make('type')
                                ->options([
                                    'Admitting Orders' => 'Admitting Orders',
                                    'Laboratory Request' => 'Laboratory Request',
                                    'Referral Form' => 'Referral Form',
                                    'Medical Abstract' => 'Medical Abstract'
                                ])
                                ->reactive(),
                            Repeater::make('labRequests')
                                ->relationship()
                                ->simple(
                                    Textarea::make('content')
                                        ->label('Content')
                                        ->required()
                                        ->disabled(fn() => ! auth()->user()->doctor())
                                )
                                ->visible(fn(Get $get) => $get('type') == 'Laboratory Request')
                                ->deletable(fn() => auth()->user()->doctor())
                                ->addable(fn() => auth()->user()->doctor()),
                            Textarea::make('referral_content')
                                ->label('Content')
                                ->visible(fn(Get $get) => $get('type') == 'Referral Form')
                                ->disabled(fn() => ! auth()->user()->doctor()),
                            Textarea::make('medical_abstract')
                                ->label('Content')
                                ->autosize()
                                ->visible(fn(Get $get) => $get('type') == 'Medical Abstract')
                                ->disabled(fn() => ! auth()->user()->doctor()),
                            Textarea::make('admitting_order_data')
                                ->visible(fn(Get $get) => $get('type') == 'Admitting Orders')
                                ->disabled(fn() => ! auth()->user()->doctor())
                                ->autosize()
                        ])
                        ->fillForm(function ($record) {
                          $record->admitting_order_data = transform($record->admitting_order_data, fn($value) => $value == '' || $value == null ? null : $value)
                                    ?? "To: ____________________\n\n- Please admit patient to ____________________\n- Secure consent to care\n- Diet:\n- IVF:\n- Diagnostics:\n\n- Medications:\n\n- VS q4 and I & O qShift\n- Watchout for unusualities\n- Kindly inform me once admitted\n- Refer accordingly\n\n- Special Instructions (if any):";
                            $record->medical_abstract = $record->medical_abstract ?? "Diagnosis: ".strip_tags($record->diagnosis);
                                    return $record->toArray();
                        })
                        ->action(function ($data, $record) {
                           if (auth()->user()->doctor()) {
                                # code...
                                $type_field = match ($data['type']) {
                                    'Admitting Orders' => 'admitting_order_data',
                                    'Laboratory Request' => 'lab_request_content',
                                    'Referral Form' => 'referral_content',
                                    'Medical Abstract' => 'medical_abstract',
                                };
    
                                try {
                                    if ($type_field !== 'lab_request_content') {
                                        # code...
                                        $record->update([
                                            $type_field => $data[$type_field]
                                        ]);
                                    }
    
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
                            }
                        })
                        ->modalSubmitAction(fn(Action $action) => $action->label('Print')->color('success'))
                        ->after(function ($livewire, $record, $data) {
                            $livewire->js("window.open('" . route('pdf.new-tab', [
                                'id' => $record->id,
                                'paper' => 'A5',
                                'type' => $data['type']
                            ]) . "', '_blank')");
                        });
    }
    // protected function getViewData(): array
    // {
    //     return [
    //         'patient_id' => 'test'
    //     ];   
    // }
}
