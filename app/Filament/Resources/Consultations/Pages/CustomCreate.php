<?php

namespace App\Filament\Resources\Consultations\Pages;

use App\Filament\Resources\Consultations\ConsultationResource;
use Filament\Resources\Pages\Page;

class CustomCreate extends Page
{
    protected static string $resource = ConsultationResource::class;

    protected string $view = 'filament.resources.consultation-resource.pages.custom-create';
}
