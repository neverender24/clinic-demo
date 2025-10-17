<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions;
use Illuminate\View\View;
use function Filament\authorize;
use Filament\Resources\Pages\EditRecord;

use App\Filament\Resources\ConsultationResource;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class EditConsultation extends EditRecord
{
    protected static string $resource = ConsultationResource::class;

    // protected static string $view = 'filament.consultations.list-records';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        // dd($this->record);
        $this->authorize('editRecord', $this->record);

        $this->form->fill($this->record->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),

        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Html2MediaAction::make('print_prescription')
                ->label('Prescription')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->content(function($record): View {
                    return view('consultations.print', [
                                'medicines' => $record->medicines->chunk(6),
                                'patient' => $record->patient,
                                'next_follow_up_schedule' => $record->next_follow_up_schedule?->format('F j, Y'),
                                'header_image' => $record->clinic->header_image,
                                'header_image1' => public_path("storage/{$record->clinic->header_image}"),
                            ]
                        );
                })
                // ->preview()
                ->orientation()
                ->format('a5')
                // ->pagebreak('section', ['css', 'legacy'])
                // ->margin([2, 2, 0, 2])
                ->modalWidth('2xl'),
            $this->getCancelFormAction(),
        ];
    }


    // protected function getViewData(): array
    // {
    //     return [
    //         'patient_id' => 'test'
    //     ];   
    // }
}
