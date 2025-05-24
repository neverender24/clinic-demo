<?php

namespace App\Filament\Resources\HospitalAdmissionResource\Pages;

use App\Filament\Resources\HospitalAdmissionResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateHospitalAdmission extends CreateRecord
{
    protected static string $resource = HospitalAdmissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $data['clinic_id'] = Filament::getTenant()->id;

        return $data;
    }
}
