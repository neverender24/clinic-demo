<?php

namespace App\Filament\Resources\Medicines\Pages;

use App\Filament\Resources\Medicines\MedicineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicine extends CreateRecord
{
    protected static string $resource = MedicineResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
