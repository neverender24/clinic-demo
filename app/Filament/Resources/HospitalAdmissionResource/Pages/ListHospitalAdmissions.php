<?php

namespace App\Filament\Resources\HospitalAdmissionResource\Pages;

use App\Filament\Resources\HospitalAdmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHospitalAdmissions extends ListRecords
{
    protected static string $resource = HospitalAdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
