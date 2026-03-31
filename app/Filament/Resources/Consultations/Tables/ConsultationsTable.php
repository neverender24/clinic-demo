<?php

namespace App\Filament\Resources\Consultations\Tables;

use App\Filament\Resources\Medicines\MedicineResource;
use App\Models\ClinicSetting;
use App\Models\ConsultationMedicine;
use App\Models\Medicine;
use App\Models\Scopes\ConsultationScope;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Throwable;

class ConsultationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->withoutGlobalScope(ConsultationScope::class)
                    ->with(['clinic', 'medicines', 'patient' => fn ($query) => $query->withTrashed()])
                    ->orderBy('date', 'desc')
                    ->orderBy('status', 'desc')
                    ->orderBy('queueing_number', 'asc')
            )
            // ->defaultSort('queueing_number')
            ->columns([
                TextColumn::make('queueing_number')
                    ->label('Queue')
                    ->formatStateUsing(fn ($state) => sprintf('%02d', $state))
                    ->sortable()
                    ->visible(fn ($livewire) => $livewire->activeTab == 'current'),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('patient.full_name')
                    ->color(fn ($record) => $record->patient->trashed() ? 'danger' : '')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')
                    ->options([
                        'Done' => 'Done',
                        'Pending' => 'Pending',
                    ])
                    ->selectablePlaceholder(false)
                    ->width(10),
                \Filament\Tables\Columns\IconColumn::make('is_dialysis')
                    ->label('Dialysis')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fee')
                    ->summarize(
                        Sum::make()
                            ->label('Total')
                            ->money('PHP')
                    ),

            ])
            ->recordClasses([
                'gap-0',
            ])
            ->paginationMode(PaginationMode::Simple)
            // ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->filters([
                Filter::make('is_dialysis')
                    ->label('Dialysis Only')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_dialysis', true)),
                Filter::make('date')
                    ->schema([
                        Select::make('month')
                            ->options(function () {
                                $months = [];
                                foreach (range(1, 12) as $key => $value) {
                                    $months[$key] = [
                                        'text' => Carbon::create()->day(1)->month($value)->format('F'),
                                        'value' => $value,
                                    ];
                                }

                                return collect($months)->pluck('text', 'value');
                            })
                            ->visible(function ($livewire) {
                                return $livewire->activeTab !== 'current';
                            }),
                        Select::make('year')
                            ->options(function () {
                                $years = [];
                                foreach (range(2024, now()->year) as $key => $value) {
                                    $years[$key] = [
                                        'value' => $value,
                                    ];
                                }

                                return collect($years)->pluck('value', 'value');
                            })
                            ->visible(function ($livewire) {
                                return $livewire->activeTab === 'all';
                            }),
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
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('encounter')
                    ->label('Encounter')
                    ->color('primary')
                    ->icon(Heroicon::PencilSquare)
                    ->url(fn ($record) => route('filament.admin.resources.consultations.edit', [filament()->getTenant()->id, $record]))
                    ->disabled(fn ($record) => $record->status->value == 'Done'),
                // EditAction::make()
                //     ->label(fn () => request()->user()->doctor() ? 'Encounter' : 'Edit')
                //     ->disabled(fn ($record) => $record->status->value == 'Done'),
                ActionGroup::make([
                    Action::make('custom_docs')
                            ->icon('heroicon-s-document-text')
                            ->url(fn($record) => route('filament.admin.resources.consultations.custom.doc', [filament()->getTenant()->id, $record->id])),
                    Action::make('clinical_orders')
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
                            \Filament\Forms\Components\Hidden::make('diagnosis')
                            // ->default(fn($record) => dd($record))
                            ,
                            Repeater::make('labRequests')
                                ->relationship()
                                ->schema([
                                    CheckboxList::make('lab_tests')
                                        ->label('Common Tests')
                                        ->options(fn () => ClinicSetting::getLabTests())
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
                                            $options = array_keys(ClinicSetting::getLabTests());
                                            $lines = array_map('trim', explode("\n", $content));
                                            $preserved = array_filter($lines, fn ($line) => $line !== '' && ! in_array($line, $options));
                                            $merged = array_merge($preserved, $state ?? []);
                                            $set('content', implode("\n", $merged));
                                        })
                                        ->disabled(fn () => ! request()->user()->doctor()),
                                    Textarea::make('content')
                                        ->label('Content')
                                        ->required()
                                        ->rows(fn (Get $get) => max(3, substr_count($get('content') ?? '', "\n") + 1))
                                        ->reactive()
                                        ->default(fn ($record, $get) => 'Diagnosis: '.strip_tags($get('../../diagnosis') ?? ''))
                                        ->disabled(fn () => ! request()->user()->doctor()),
                                ])
                                ->visible(fn (Get $get) => $get('type') == 'Laboratory Request')
                                ->deletable(fn () => request()->user()->doctor())
                                ->addable(fn () => request()->user()->doctor()),
                            Textarea::make('referral_content')
                                ->label('Content')
                                ->autosize()
                                ->visible(fn (Get $get) => $get('type') == 'Referral Form')
                                ->disabled(fn () => ! request()->user()->doctor()),
                            Textarea::make('medical_abstract')
                                ->label('Content')
                                ->autosize()
                                ->visible(fn (Get $get) => $get('type') == 'Medical Abstract')
                                ->disabled(fn () => ! request()->user()->doctor()),
                            Textarea::make('admitting_order_data')
                                ->visible(fn (Get $get) => $get('type') == 'Admitting Orders')
                                ->disabled(fn () => ! request()->user()->doctor())
                                ->autosize(),
                            // ->default(fn($record) => )
                        ])
                        ->fillForm(function ($record) {
                            $record->admitting_order_data = transform($record->admitting_order_data, fn ($value) => $value == '' || $value == null ? null : $value)
                                    ?? "To: ____________________\n\n- Please admit patient to ____________________\n- Secure consent to care\n- Diet:\n- IVF:\n- Diagnostics:\n\n- Medications:\n\n- VS q4 and I & O qShift\n- Watchout for unusualities\n- Kindly inform me once admitted\n- Refer accordingly\n\n- Special Instructions (if any):";
                            $record->medical_abstract = $record->medical_abstract ?? 'Diagnosis: '.strip_tags($record->diagnosis);
                            $record->referral_content = $record->referral_content ?? 'Diagnosis: '.strip_tags($record->diagnosis);

                            return $record->toArray();
                        })
                        ->action(function ($data, $record) {
                            if (request()->user()->doctor()) {
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
                                } catch (Throwable $th) {
                                    dd($th->getMessage());
                                    Notification::make()
                                        ->title('Error')
                                        ->body($th->getMessage());
                                }
                            }
                        })
                        ->modalSubmitAction(fn (Action $action) => $action->label('Print')->color('success'))

                        ->after(function ($livewire, $record, $data) {
                            $livewire->js("window.open('".route('pdf.new-tab', [
                                'id' => $record->id,
                                'paper' => 'A5',
                                'type' => $data['type'],
                            ])."', '_blank')");
                        }),
                    Action::make('medical_cert_form')
                        ->label('Medical Certificate')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        // ->visible(fn($record) => !filled($record->approximate_days) && !filled($record->estimated_date) && !filled($record->medical_cert_remarks))
                        ->form([
                            Fieldset::make('Date of rest')
                                ->schema([
                                    DatePicker::make('estimated_date')
                                        ->label('From'),
                                    DatePicker::make('estimated_date_to')
                                        ->label('To')
                                        ->validationAttribute('End of date to rest'),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('approximate_days')
                                        ->label('Days of rest and recovery')
                                        ->numeric(),
                                    DatePicker::make('return_date')
                                        ->afterOrEqual('estimated_date_to')
                                        ->validationMessages([
                                            'after_or_equal' => 'The value must be after the \'Date to\' in the Date of Rest.'
                                        ]),
                                ]),
                            RichEditor::make('medical_cert_remarks')
                                ->label('Remarks'),
                        ])
                        ->fillForm(fn($record) => $record->toArray())
                        ->modalSubmitActionLabel('Print')
                        ->action(function ($data, $record) {
                            DB::beginTransaction();
                            try {
                                $record->update($data);
                                DB::commit();
                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('The changes have been saved');
                            } catch (\Throwable $th) {
                                DB::rollBack();
                                Notification::make()
                                    ->title('Error')
                                    ->body($th->getMessage());
                            }
                        })
                        ->after(function ($livewire, $record) {
                            $paper = \App\Models\ClinicSetting::getMedCertSettings()['paper_size'] ?? 'letter';
                            $livewire->js("window.open('".route('pdf.a5.medcert', ['id' => $record->id, 'type' => 'Medical Certificate', 'paper' => $paper])."', '_blank')");
                        }),
                    Action::make('prescription')
                        ->label('Print Prescription')
                        ->color('success')
                        ->icon('heroicon-o-printer')
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
                        ->action(function ($data, $record, $livewire) {
                            $params = [
                                'id' => $record->id,
                                'paper' => 'A5',
                            ];
                            if (!empty($data['batch'])) {
                                $params['batch'] = $data['batch'];
                            }
                            $livewire->js("window.open('".route('pdf.new-tab', $params)."', '_blank')");
                        }),
                    Action::make('create_prescription')
                        ->label('Create New Prescription')
                        ->color('primary')
                        ->icon('heroicon-o-plus-circle')
                        ->schema([
                            Repeater::make('medicines')
                                ->label('Medicines')
                                ->schema([
                                    Select::make('medicine_id')
                                        ->label('Medicine')
                                        ->searchable()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return Medicine::where(function ($q) use ($search) {
                                                $q->where('brand', 'like', "%{$search}%")
                                                    ->orWhere('name', 'like', "%{$search}%");
                                            })
                                                ->where('active', 1)
                                                ->limit(20)
                                                ->get()
                                                ->map(fn ($item) => [
                                                    'id' => $item->id,
                                                    'name' => "<div class='flex items-center gap-2'><div>$item->name</div><div class='text-indigo-500'>($item->brand)</div></div>",
                                                ])
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        })
                                        ->getOptionLabelUsing(function ($value) {
                                            $med = Medicine::find($value);
                                            return $med ? "{$med->name}" . ($med->brand ? " - <b>{$med->brand}</b>" : '') : '';
                                        })
                                        ->allowHtml()
                                        ->required()
                                        ->createOptionForm(function (Schema $schema) {
                                            return MedicineResource::form($schema)->extraAttributes(['class' => 'w-full']);
                                        })
                                        ->createOptionAction(function (Action $action) {
                                            return $action
                                                ->modalHeading('Add Medicine')
                                                ->mutateDataUsing(function (array $data) {
                                                    $data['user_id'] = auth()->id();
                                                    return $data;
                                                });
                                        }),
                                    TextInput::make('remarks')
                                        ->datalist(fn () => ConsultationMedicine::distinct('remarks')->pluck('remarks')->toArray())
                                        ->required(),
                                    TextInput::make('quantity')
                                        ->required()
                                        ->numeric(),
                                ])
                                ->table([
                                    TableColumn::make('Generic'),
                                    TableColumn::make('Instruction'),
                                    TableColumn::make('Qty')
                                        ->width('100px'),
                                ])
                                ->defaultItems(1)
                                ->reorderable()
                                ->required(),
                        ])
                        ->modalHeading('Create New Prescription')
                        ->modalWidth('7xl')
                        ->closeModalByClickingAway(false)
                        ->extraModalWindowAttributes(['style' => 'min-height: 500px; overflow: visible;'])
                        ->modalSubmitActionLabel('Save & Print')
                        ->action(function ($data, $record, $livewire) {
                            $nextBatch = ($record->medicines()->max('consultation_medicine.batch') ?? 0) + 1;

                            foreach ($data['medicines'] as $sort => $med) {
                                $record->medicines()->attach($med['medicine_id'], [
                                    'remarks' => $med['remarks'],
                                    'quantity' => $med['quantity'],
                                    'sort' => $sort,
                                    'batch' => $nextBatch,
                                ]);
                            }

                            Notification::make()
                                ->success()
                                ->title('Prescription saved')
                                ->body("Batch #{$nextBatch} created with " . count($data['medicines']) . " medicine(s).")
                                ->send();

                            $livewire->js("window.open('".route('pdf.new-tab', [
                                'id' => $record->id,
                                'paper' => 'A5',
                                'batch' => $nextBatch,
                            ])."', '_blank')");
                        }),
                    DeleteAction::make()
                        ->disabled(fn ($record) => $record->status->value == 'Done'),
                ]),
            ]);
    }

    protected static function pay($data, $record)
    {
        $record->otherPayments()->create($data);
    }

    protected static function medcertUpdate($data, $record)
    {
        $data = collect($data)->only(
            'estimated_date',
            'estimated_date_to',
            'approximate_days',
            'return_date',
            'medical_cert_remarks'
        );

        $record->update($data->toArray());
    }
}
