<?php

namespace App\Filament\Resources\Consultations\Tables;

use Throwable;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Models\Scopes\ConsultationScope;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\PaginationMode;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Utilities\Get;

class ConsultationsTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query->withoutGlobalScope(ConsultationScope::class)
                    ->with(['clinic', 'medicines', 'patient' => fn($query) => $query->withTrashed()])
                    ->orderBy('date', 'desc')
                    ->orderBy('status', 'desc')
                    ->orderBy('queueing_number', 'asc')
            )
            // ->defaultSort('queueing_number')
            ->columns([
                TextColumn::make('queueing_number')
                    ->label('Queue')
                    ->formatStateUsing(fn($state) => sprintf('%02d', $state))
                    ->sortable()
                    ->visible(fn($livewire) => $livewire->activeTab == 'current'),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('patient.full_name')
                    ->color(fn($record) => $record->patient->trashed() ? 'danger' : '')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')
                    ->options([
                        'Done' => 'Done',
                        'Pending' => 'Pending'
                    ])
                    ->selectablePlaceholder(false)
                    ->width(10),
                // Tables\Columns\ToggleColumn::make('status')
                //     ->searchable()
                //     // ->formatStateUsing(function($record) {
                //     //     dd($record);
                //     // })
                //     ->sortable()
                //     ->badge(),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses([
                'gap-0'
            ])
            ->paginationMode(PaginationMode::Simple)
            // ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->filters([
                Filter::make('date')
                    ->schema([
                        Select::make('month')
                            ->options(function () {
                                $months = [];
                                foreach (range(1, 12) as $key => $value) {
                                    $months[$key] = [
                                        'text' => Carbon::create()->day(1)->month($value)->format('F'),
                                        'value' => $value
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
                                        'value' => $value
                                    ];
                                }

                                return collect($years)->pluck('value', 'value');
                            })
                            ->visible(function ($livewire) {
                                return $livewire->activeTab === 'all';
                            })
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['month'],
                                fn(Builder $query, $month): Builder => $query->whereMonth('date', $month),
                            )
                            ->when(
                                $data['year'],
                                fn(Builder $query, $year): Builder => $query->whereYear('date', $year),
                            );
                    })
                // ->visible(function($livewire) {
                //     return $livewire->activeTab !== 'current';
                // })
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->label(fn() => auth()->user()->doctor() ? 'Encounter' : 'Edit')
                    ->disabled(fn($record) => $record->status->value == 'Done'),
                // Action::make('edit_history')
                //     ->visible(fn($record) => auth()->user()->can('edit_as_doctor_consultation'))
                //     ->disabled(fn($record) => $record->status->value == 'Done')
                //     ->label(fn() => auth()->user()->superAdmin() ? 'Edit as Doctor' : 'Encounter')
                //     ->icon('heroicon-m-pencil-square')
                //     ->url(fn($record) => route('filament.admin.resources.consultations.edit.consultation', [$record->clinic_id, $record->id])),
                ActionGroup::make([
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
                                // ->default(fn($record) => )
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
                                } catch (Throwable $th) {
                                    dd($th->getMessage());
                                    Notification::make()
                                        ->title('Error')
                                        ->body($th->getMessage());
                                }
                            }
                        })
                        ->modalSubmitAction(fn(Action $action) => $action->label('Print')->color('success'))
                        // ->extraModalFooterActions(function ($action, $arguments, $data) {
                        //     return [
                        //         $action->make('save')
                        //             ->visible(fn() => auth()->user()->doctor())
                        //             ->action(function ($record, $mountedActions) use ($arguments, $data){
                        //                 $data = $mountedActions[0]->getRawData();
                                       
                        //                 try {
                        //                     // This is to get array of admitting_order_data from livewire

                        //                     $type_field = match ($data['type']) {
                        //                         'Admitting Orders' => 'admitting_order_data',
                        //                         'Laboratory Request' => 'lab_request_content',
                        //                         'Referral Form' => 'referral_content',
                        //                     };

                        //                     if ($type_field == 'lab_request_content') {
                                                
                        //                         $result = collect($data['labRequests'])->map(fn ($item) => [
                        //                             'content' => $item['content'] ?? null,
                        //                         ])->values()->toArray();
                                                
                        //                         $record->labRequests()->delete();

                        //                         $record->labRequests()->createMany($result);
                        //                     } else {

                        //                         $record->update([
                        //                             $type_field => $data[$type_field]
                        //                         ]);
                                                
                        //                     }


                        //                     Notification::make()
                        //                         ->success()
                        //                         ->title('Success')
                        //                         ->body('The changes have been saved');
                        //                 } catch (Throwable $th) {
                        //                     dd($th->getMessage());
                        //                     Notification::make()
                        //                         ->title('Error')
                        //                         ->body($th->getMessage());
                        //                 }
                        //             })
                        //             ->cancelParentActions()
                        //     ];
                        // })
                        ->after(function ($livewire, $record, $data) {
                            $livewire->js("window.open('" . route('pdf.new-tab', [
                                'id' => $record->id,
                                'paper' => 'A5',
                                'type' => $data['type']
                            ]) . "', '_blank')");
                        }),
                    Action::make('medical_cert_form')
                        ->label('Medical Certificate')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        // ->visible(fn($record) => !filled($record->approximate_days) && !filled($record->estimated_date) && !filled($record->medical_cert_remarks))
                        ->schema([
                            Section::make()
                                ->schema([
                                    Textarea::make('medical_cert_remarks')
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
                        // ->extraModalFooterActions(fn(Action $action, $data) => [
                        //     $action->make('save')
                        //         ->action(function ($record, array $mountedActions) use($data, $action){
                                    
                        //             try {
                        //                 // This is to get array of admitting_order_data from livewire

                        //                 $data = $data = $mountedActions[0]->getRawData();

                        //                 static::medcertUpdate($data, $record);
                        //                 if ($data['fee']) {

                        //                     $data['patient_id'] = $record->patient_id;

                        //                     $data['type_of_payment'] = 'Medical Certificate';

                        //                     $data['amount'] = $data['fee'];

                        //                     $record->otherPayments()->create($data);
                        //                 }
                        //                 Notification::make()
                        //                     ->success()
                        //                     ->title('Success')
                        //                     ->body('The changes have been saved')
                        //                     ->send();
                        //             } catch (Throwable $th) {
                        //                 dd($th->getMessage());
                        //                 Notification::make()
                        //                     ->title('Error')
                        //                     ->body($th->getMessage())
                        //                     ->send();
                        //             }
                        //         })
                        //         ->cancelParentActions()
                        //     ])
                        ->modalSubmitActionLabel('Print')
                        ->action(function ($data, $record) {
                            // dd($data);
                            
                            try {
                                
                                $record->update($data);
                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('The changes have been saved');
                                
                            } catch (Throwable $th) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($th->getMessage());
                            }
                        })
                        ->after(fn($livewire, $record) =>  $livewire->js("window.open('".route('pdf.a5.medcert', ['id' => $record->id, 'type' => 'Medical Certificate', 'paper' => 'A5'])."', '_blank')")),
                    // Action::make('lab_requests')
                    //     ->url(fn($record) => route('filament.admin.resources.consultations.labRequest', [Filament::getTenant()->id, $record->id])),
                    Action::make('prescription')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => route('pdf.new-tab', [
                            'id' => $record->id,
                            'paper' => 'A5'
                        ]), shouldOpenInNewTab: true),
                    DeleteAction::make()
                        ->disabled(fn($record) => $record->status->value == 'Done'),
                ])
            ])
        ;
    }

    // public static function paymentSchema(): array
    // {
    //     return [
    //         TextInput::make('fee')
    //             ->numeric()
    //             ->afterStateUpdated(fn($set, $get) => self::computeTotalCharge($get, $set))
    //             ->live(debounce: 500),
    //         TextInput::make('discount')
    //             ->numeric()
    //             ->afterStateUpdated(fn($set, $get) => self::computeTotalCharge($get, $set))
    //             ->live(debounce: 500),
    //         TextInput::make('Total')
    //             ->afterStateHydrated(fn($set, $get) => self::computeTotalCharge($get, $set))
    //             ->disabled(),
    //     ];
    // }

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
