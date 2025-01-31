<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use function Filament\authorize;

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
            Actions\DeleteAction::make(),
        ];
    }


    // protected function getViewData(): array
    // {
    //     return [
    //         'patient_id' => 'test'
    //     ];   
    // }
}
