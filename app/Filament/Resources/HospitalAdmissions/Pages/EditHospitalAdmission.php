<?php

namespace App\Filament\Resources\HospitalAdmissions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\HospitalAdmissions\HospitalAdmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHospitalAdmission extends EditRecord
{
    protected static string $resource = HospitalAdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
