<?php

namespace App\Filament\Resources\Consultations\Tables;

use App\Models\Patient;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Trait\HasStatusAction;
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
use Filament\Tables\Columns\SelectColumn;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Filament\Resources\Consultations\ConsultationResource;

class ConsultationsTable
{
    use HasStatusAction;
    
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
                SelectColumn::make('status')
                    ->options([
                        'Done' => 'Done',
                        'Pending' => 'Pending'
                    ])
                    ->selectablePlaceholder(false)
                    ->width(10),
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
                    Action::make('prescription')
                        ->color(fn($record) => $record->medicines->count() < 1 ? 'danger' : 'success')
                        ->icon('heroicon-m-printer')
                        ->url(fn($record) => route('prescription.print', [$record->id]), shouldOpenInNewTab: true)
                        ,
                    Action::make('medical_cert_form')
                        ->label('Medical Certificate Form')
                         ->color('success')
                        ->icon('heroicon-s-printer')
                        // ->visible(fn($record) => !filled($record->approximate_days) && !filled($record->estimated_date) && !filled($record->medical_cert_remarks))
                        ->schema([  
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
                         ->after(function($livewire, $record) {
                            $livewire->js("window.open('".route('medical.medcert', [$record->id])."', '_blank')");
                        }),
                    Action::make('admitting_order')
                        ->label('Admitting Order')
                        ->color('success')
                        ->icon('heroicon-s-printer')
                        ->closeModalByClickingAway(false)
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
                        ->action(function($data, $record) {
                        //    dd($data);
                            try {
                                //code...
                                $record->update($data);
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
                        })
                        ->after(function($livewire, $record) {
                              $livewire->js("window.open('".route('prescription.admitting.order', $record)."', '_blank')");
                        }),
                        Action::make('custom_docs')
                            ->icon('heroicon-s-document-text')
                            ->url(fn($record) => ConsultationResource::getUrl('custom.doc', [$record])),
                    // Action::make('status')
                    //     ->label(fn($record) => static::statusLabel($record))
                    //     ->color(fn($record) => static::statusColor($record))
                    //     ->icon(fn($record) => static::statusIcon($record))
                    //     ->action(fn($record) => $record->changeStatus())
                    //     ->hidden(fn() => ! auth()->user()->doctor())
                    //     ->requiresConfirmation()
                ])
            ])
            ;
    }
}
