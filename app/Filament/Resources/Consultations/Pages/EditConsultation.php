<?php

namespace App\Filament\Resources\Consultations\Pages;

use App\Filament\Resources\Consultations\ConsultationResource;
use App\Trait\HasHistoryAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\On;

use function Filament\authorize;

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

        } elseif ($type === 'prescription') {

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
            ->schema([
                Select::make('batch')
                    ->label('Select Prescription Batch')
                    ->options(function ($record) {
                        $batches = $record->medicines->pluck('pivot.batch')->unique()->sort()->values();
                        if ($batches->count() <= 1) {
                            return ['' => 'All Medicines'];
                        }
                        $options = ['' => 'All Medicines'];
                        foreach ($batches as $b) {
                            $options[$b] = "Prescription {$b}";
                        }
                        return $options;
                    })
                    ->default(''),
            ])
            ->modalSubmitActionLabel('Print')
            ->action(function ($data, $record, $livewire) {
                $params = [
                    'id' => $record->id,
                    'paper' => 'A5',
                ];
                if (!empty($data['batch'])) {
                    $params['batch'] = $data['batch'];
                }
                $url = route('pdf.new-tab', $params);
                $livewire->js("
                    const a = document.createElement('a');
                    a.href = '{$url}';
                    a.target = '_blank';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                ");
            });
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
                            ->label('Remarks'),
                    ]),
            ])
            ->fillForm(function ($record) {
                $data = $record;

                return [
                    // 'estimated_date' => $data->estimated_date,
                    // 'estimated_date_to' => $data->estimated_date_to,
                    // 'approximate_days' => $data->approximate_days,
                    // 'return_date' => $data->return_date,
                    'medical_cert_remarks' => $data->medical_cert_remarks,
                ];
            })
            ->modalSubmitActionLabel('Save & Print')
            ->action(function ($data, $record) {
                try {
                    $record->update($data);
                } catch (\Throwable $th) {
                    Notification::make()
                        ->danger()
                        ->title('Error')
                        ->body($th->getMessage())
                        ->send();
                }
            })
            ->after(function ($livewire, $record) {
                $paper = \App\Models\ClinicSetting::getMedCertSettings()['paper_size'] ?? 'letter';
                $url = route('pdf.a5.medcert', ['id' => $record->id, 'type' => 'Medical Certificate', 'paper' => $paper]);
                $livewire->js("
                                const a = document.createElement('a');
                                a.href = '{$url}';
                                a.target = '_blank';
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                            ");
            });
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
                        'Medical Abstract' => 'Medical Abstract',
                    ])
                    ->reactive(),
                \Filament\Forms\Components\Hidden::make('diagnosis'),
                Repeater::make('labRequests')
                    ->relationship()
                    ->collapsible()
                    ->schema([
                        CheckboxList::make('lab_tests')
                            ->label('Common Tests')
                            ->options([
                                'CBC' => 'CBC',
                                'Creatinine' => 'Creatinine',
                                'Sodium' => 'Sodium',
                                'Potassium' => 'Potassium',
                                'Calcium' => 'Calcium',
                                'Magnesium' => 'Magnesium',
                                'Uric acid' => 'Uric acid',
                                'Urine albumin-creatinine ratio' => 'Urine albumin-creatinine ratio',
                                'Urinalysis' => 'Urinalysis',
                                'FBS' => 'FBS',
                                'Lipid profile' => 'Lipid profile',
                                'HBA1C' => 'HBA1C',
                                'Anti-nuclear antibodies' => 'Anti-nuclear antibodies',
                                'ABG' => 'ABG',
                                'Ultrasound of whole abdomen with pre and post void scan' => 'Ultrasound of whole abdomen with pre and post void scan',
                                'Ultrasound KUB prostate' => 'Ultrasound KUB prostate',
                                'CT stonogram' => 'CT stonogram',
                                'CT-Scan of:' => 'CT-Scan of:',
                            ])
                            ->columns(3)
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateHydrated(function (CheckboxList $component, Get $get) {
                                $content = $get('content') ?? '';
                                $lines = array_filter(array_map('trim', explode("\n", $content)));
                                $options = array_keys($component->getOptions());
                                $component->state(array_values(array_intersect($lines, $options)));
                            })
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                $content = $get('content') ?? '';
                                $options = [
                                    'CBC', 'Creatinine', 'Sodium', 'Potassium', 'Calcium',
                                    'Magnesium', 'Uric acid', 'Urine albumin-creatinine ratio',
                                    'Urinalysis', 'FBS', 'Lipid profile', 'HBA1C',
                                    'Anti-nuclear antibodies', 'ABG',
                                    'Ultrasound of whole abdomen with pre and post void scan',
                                    'Ultrasound KUB prostate', 'CT stonogram', 'CT-Scan of:',
                                ];
                                $lines = array_map('trim', explode("\n", $content));
                                $preserved = array_filter($lines, fn ($line) => $line !== '' && !in_array($line, $options));
                                $merged = array_merge($preserved, $state ?? []);
                                $set('content', implode("\n", $merged));
                            })
                            ->disabled(fn () => ! auth()->user()->doctor()),
                        Textarea::make('content')
                            ->label('Content')
                            ->required()
                            ->rows(fn (Get $get) => max(3, substr_count($get('content') ?? '', "\n") + 1))
                            ->reactive()
                            ->default(fn (Get $get) => 'Diagnosis: '.strip_tags($get('../../diagnosis') ?? ''))
                            ->disabled(fn () => ! auth()->user()->doctor()),
                    ])
                    ->visible(fn (Get $get) => $get('type') == 'Laboratory Request')
                    ->deletable(fn () => auth()->user()->doctor())
                    ->addable(fn () => auth()->user()->doctor()),
                Textarea::make('referral_content')
                    ->label('Content')
                    ->visible(fn (Get $get) => $get('type') == 'Referral Form')
                    ->disabled(fn () => ! auth()->user()->doctor()),
                Textarea::make('medical_abstract')
                    ->label('Content')
                    ->autosize()
                    ->visible(fn (Get $get) => $get('type') == 'Medical Abstract')
                    ->disabled(fn () => ! auth()->user()->doctor()),
                Textarea::make('admitting_order_data')
                    ->visible(fn (Get $get) => $get('type') == 'Admitting Orders')
                    ->disabled(fn () => ! auth()->user()->doctor())
                    ->autosize(),
            ])
            ->fillForm(function ($record) {
                $record->admitting_order_data = transform($record->admitting_order_data, fn ($value) => $value == '' || $value == null ? null : $value)
                          ?? "To: ____________________\n\n- Please admit patient to ____________________\n- Secure consent to care\n- Diet:\n- IVF:\n- Diagnostics:\n\n- Medications:\n\n- VS q4 and I & O qShift\n- Watchout for unusualities\n- Kindly inform me once admitted\n- Refer accordingly\n\n- Special Instructions (if any):";
                $record->medical_abstract = $record->medical_abstract ?? 'Diagnosis: '.strip_tags($record->diagnosis);
                $record->referral_content = $record->referral_content ?? 'Diagnosis: '.strip_tags($record->diagnosis);

                return $record->toArray();
            })
            ->action(function ($data, $record) {
                if (auth()->user()->doctor()) {
                    // code...
                    $type_field = match ($data['type']) {
                        'Admitting Orders' => 'admitting_order_data',
                        'Laboratory Request' => 'lab_request_content',
                        'Referral Form' => 'referral_content',
                        'Medical Abstract' => 'medical_abstract',
                    };

                    try {
                        if ($type_field !== 'lab_request_content') {
                            // code...
                            $record->update([
                                $type_field => $data[$type_field],
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
            ->modalSubmitActionLabel('Save & Print')
            ->after(function ($livewire, $record, $data) {
                $url = route('pdf.new-tab', [
                    'id' => $record->id,
                    'paper' => 'A5',
                    'type' => $data['type'],
                ]);

                $livewire->js("
                                const a = document.createElement('a');
                                a.href = '{$url}';
                                a.target = '_blank';
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                            ");
            });
    }
    // protected function getViewData(): array
    // {
    //     return [
    //         'patient_id' => 'test'
    //     ];
    // }
}
