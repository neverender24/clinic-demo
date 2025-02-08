<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Filament\Resources\ConsultationResource;
use Filament\Resources\Pages\Page;

class CustomListConsultations extends Page
{
    protected static string $resource = ConsultationResource::class;

    protected static string $view = 'filament.resources.consultation-resource.pages.custom-list-consultations';

    public function getTable()
    {
        
    }
}
