<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
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
        $data['clinic_id'] = Filament::getTenant()->id;

        return $data;
    }

}
