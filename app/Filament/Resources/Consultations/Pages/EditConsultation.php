<?php

namespace App\Filament\Resources\Consultations\Pages;

use Filament\Actions;
use Illuminate\View\View;
use Filament\Actions\Action;
use App\Trait\HasHistoryAction;
use function Filament\authorize;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use App\Filament\Resources\Consultations\ConsultationResource;
use Livewire\Attributes\On;

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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
             Action::make('prescription')
                        ->color(fn($record) => $record->medicines->count() < 1 ? 'danger' : 'success')
                        ->icon('heroicon-m-printer')
                        ->url(fn($record) => route('prescription.print', [$record->id]), shouldOpenInNewTab: true)

        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
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
