<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
use App\Models\Consultation;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateConsultation extends CreateRecord
{
    protected static string $resource = ConsultationResource::class;

    public function getExtraBodyAttributes(): array
    {
        return [
            'id' => 'consultation-resource',
        ];
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['queueing_number'] = $this->checkLastQueue($data['date']) + 1;   
        $data['clinic_id'] = Filament::getTenant()->id;

        return $data;
    }

    protected function checkLastQueue($date)
    {
        return Consultation::currentConsultations($date)->max('queueing_number');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    } 

}
