<?php

namespace App\Filament\Resources\HospitalAdmissions\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\HospitalAdmissions\HospitalAdmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHospitalAdmissions extends ListRecords
{
    protected static string $resource = HospitalAdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
