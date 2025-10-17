<?php

namespace App\Filament\Resources\Consultations\Tables;

use App\Models\Patient;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\Width;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Fieldset;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ConsultationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['clinic', 'medicines', 'patient' => fn($query) => $query->withTrashed()])
            )
            ->defaultSort('queueing_number')
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
                TextColumn::make('status')
                    ->searchable()
                    // ->formatStateUsing(function($record) {
                    //     dd($record);
                    // })
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginationPageOptions([5, 10, 15, 20, 50, 100])
            ->filters([
                Filter::make('date')
                    ->schema([
                        Select::make('month')
                            ->options(function() {
                                $months = [];
                                foreach (range(1,12) as $key => $value) {
                                    $months[$key] = [
                                        'text' => Carbon::create()->day(1)->month($value)->format('F'),
                                        'value' => $value
                                    ];
                                }

                                return collect($months)->pluck('text', 'value');
                            })
                            ->visible(function($livewire) {
                                return $livewire->activeTab !== 'current';
                            }),
                        Select::make('year')
                            ->options(function() {
                                $years = [];
                                foreach (range(2024,now()->year) as $key => $value) {
                                    $years[$key] = [
                                        'value' => $value
                                    ];
                                }

                                return collect($years)->pluck('value', 'value');
                            })
                            ->visible(function($livewire) {
                                return $livewire->activeTab === 'all';
                            })
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
                    })
                    // ->visible(function($livewire) {
                    //     return $livewire->activeTab !== 'current';
                    // })
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn($record) => $record->status->value == 'Done'),
                DeleteAction::make()
                    ->disabled(fn($record) => $record->status->value == 'Done'),
                Action::make('edit_history')
                    ->visible(fn($record) => auth()->user()->can('edit_as_doctor_consultation'))
                    ->disabled(fn($record) => $record->status->value == 'Done')
                    ->label(fn() => auth()->user()->superAdmin() ? 'Edit as Doctor' : 'Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn($record) => route('filament.admin.resources.consultations.edit.consultation', [$record->clinic_id, $record->id])),
                ActionGroup::make([
                    Action::make('medical_cert_form')
                        ->label('Medical Certificate Form')
                        ->color('primary')
                        ->icon('heroicon-s-document-text')
                        // ->visible(fn($record) => !filled($record->approximate_days) && !filled($record->estimated_date) && !filled($record->medical_cert_remarks))
                        ->schema([
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
                        ->action(function($data, $record) {
                            // dd($data);
                            DB::beginTransaction();
                            try {
                                $record->update($data);
                                DB::commit();
                                Notification::make()
                                ->success()
                                ->title('Success')
                                ->body('The changes have been saved');
                            } catch (Throwable $th) {
                                DB::rollBack();
                                Notification::make()
                                    ->title('Error')
                                    ->body($th->getMessage());
                            }
                        })
                        ->after(fn($livewire) =>  $livewire->dispatch('refreshTable')),
                    Action::make('admitting_order')
                        ->label('Admitting Order Form')
                        ->icon('heroicon-s-document-text')
                        ->schema([
                            RichEditor::make('admitting_order_data')
                            // ->mentionsItems(function () {
                            //     return Patient::all()->map(function ($user) {
                            //         return [
                            //             'display_name' => $user->full_name,
                            //             'name' => $user->full_name,
                            //             'address' => $user->address,
                            //             'avatar' => asset('images/user.svg'),
                            //             'url' => 'admin/users/' . $user->id,
                            //         ];
                            //     })->toArray();
                            // })
                            ->default(fn($record) => transform($record->admitting_order_data, fn($value) => $value == '' || $value == null ? null: $value)
                                                            ?? '<p>&nbsp;To: <span class="text-underline">&nbsp; &nbsp; &nbsp; &nbsp;</span></p><p><br></p><p><br></p><p><br></p><p>&nbsp;- Please admit patient to <span class="text-underline"> &nbsp; &nbsp; &nbsp; &nbsp;</span>&nbsp;</p><p>&nbsp;- Secure consent to care&nbsp;</p><p>&nbsp;- Diet&nbsp;</p><p>&nbsp;- IVF&nbsp;</p><p>&nbsp;- Diagnostics:&nbsp;</p><p><br></p><p><br></p><p>&nbsp;- Medications:&nbsp;</p><p><br></p><p><br></p><p>&nbsp;- VS q4 and I &amp; O qShift&nbsp;</p><p>&nbsp;- Watchout for unusualities&nbsp;</p><p>&nbsp;- Kindly inform me once admitted&nbsp;</p><p>&nbsp;- Refer accordingly&nbsp;</p><p><br></p><p>- Special Instructions (if any):</p>')
                        ])
                        // ->fillForm(fn($record) => [
                        //     'admitting_order_data' => $record->admitting_order_data
                        // ])
                        ->action(function($data, $record, $livewire) {
                            DB::beginTransaction();
                            try {
                                //code...
                                $record->update($data);
                                DB::commit();
                                Notification::make()
                                ->success()
                                ->title('Success')
                                ->body('The changes have been saved');
                            } catch (Throwable $th) {
                                DB::rollBack();
                                dd($th->getMessage());
                                Notification::make()
                                    ->title('Error')
                                    ->body($th->getMessage());
                            }
                        }),
                        Action::make('custom_docs')
                            ->icon('heroicon-s-document-text')
                            ->url(fn($record) => static::getUrl('custom.doc', [$record])),
                    // Html2MediaAction::make('print_prescription')
                    //     ->label('Prescription')
                    //     ->color('success')
                    //     ->icon('heroicon-o-printer')
                    //     ->content(function($record): View {
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
                    // Html2MediaAction::make('print_medcert')
                    //     ->label('Medical Certificate')
                    //     ->color('success')
                    //     ->icon('heroicon-o-printer')
                    //     ->format(format: 'letter')
                    //     // ->preview()
                    //     // ->action(fn($data) => dd($data))
                    //     // ->visible(fn($record) => filled($record->approximate_days) && filled($record->estimated_date) && filled($record->medical_cert_remarks))
                    //     ->content(fn($record): View => view('consultations.medcert', [
                    //         'medicines' => $record->medicines,
                    //         'patient' => $record->patient,
                    //         'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
                    //         'header_image' => asset('storage/'.$record->clinic->medcert_header_image),
                    //         'watermark' => asset('storage/'.$record->clinic->watermarks),
                    //         'consultation_date' => $record->date?->format('F d, Y'),
                    //         'medical_cert_remarks' => $record->medical_cert_remarks,
                    //         'approximate_days_in_word' => Number::spell(intval($record->approximate_days)),
                    //         'approximate_days' => transform($record->approximate_days, fn($value) => "($value)", 'N/A'),
                    //         'estimated_date' => $record->estimated_date ? $record->estimated_date->format('F j, Y') : '',
                    //         'estimated_date_to' => $record->estimated_date_to ? Carbon::parse($record->estimated_date_to)->format('F j, Y') : null,
                    //         'return_date' => $record->return_date ? Carbon::parse($record->return_date)->format('F j, Y') : null,
                    //         'diagnosis' => $record->diagnosis,
                    //         'chief_complaint' => str_replace(['</p>', '<p>'], '', $record->chief_complaint)
                    //     ])),
                    // Html2MediaAction::make('print_admitting_order')
                    //     ->label('Admitting Order')
                    //     ->color('success')
                    //     ->modalWidth(Width::MaxContent)
                    //     ->icon('heroicon-o-printer')
                    //     ->format('a5')
                    //     ->content(function($record): View {
                    //         // dd($record->admitting_order_data);
                    //         return view('consultations.admitting-order', [
                    //             'data' => $record->admitting_order_data,
                    //             'header_image' => $record->clinic->header_image,
                    //             'patient' => $record->patient,
                    //         ]);
                    //     })
                    //     ->preview(),
                    // Tables\Actions\Action::make('print_prescription1')
                    //     ->label('Prescription')
                    //     ->hidden(false)
                    //     ->color('success')
                    //     ->icon('heroicon-o-printer')
                    //     ->modalContent(function($record): View {
                    //         return view('consultations.print', [
                    //             'medicines' => $record->medicines,
                    //             'patient' => $record->patient,
                    //             'next_follow_up_schedule' => $record->next_follow_up_schedule->format('F j, Y')
                    //         ]);
                    //     })
                    //     ->modalWidth('2xl')
                    //     ->modalSubmitAction(false)
                    //     ->modalCancelAction(false)
                    //     // ->modalFooterActions(function(Action))
                    //     ->slideOver(),
                    // Tables\Actions\Action::make('med_cert')
                    //     ->label('Medical Certification')
                    //     ->hidden(false)
                    //     ->color('success')
                    //     ->icon('heroicon-o-printer')
                    //     ->modalContent(function($record): View {
                    //         return view('consultations.print', [
                    //             'medicines' => $record->medicines,
                    //             'patient' => $record->patient,
                    //             'next_follow_up_schedule' => $record->next_follow_up_schedule->format('F j, Y')
                    //         ]);
                    //     })
                    //     ->modalWidth('7xl')
                    //     ->slideOver(),
                    Action::make('status')
                        ->label(fn($record) => static::statusLabel($record))
                        ->color(fn($record) => static::statusColor($record))
                        ->icon(fn($record) => static::statusIcon($record))
                        ->action(fn($record) => $record->changeStatus())
                        ->hidden(fn() => ! auth()->user()->doctor())
                        ->requiresConfirmation()
                ])
            ])
            ;
    }
}
