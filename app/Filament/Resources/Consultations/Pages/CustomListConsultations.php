<?php

namespace App\Filament\Resources\Consultations\Pages;

use App\Filament\Resources\Consultations\ConsultationResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;

class CustomListConsultations extends Page
{
    protected static string $resource = ConsultationResource::class;

    protected string $view = 'filament.resources.consultation-resource.pages.custom-list-consultations';

    public $consultation;

    protected function getViewData(): array
    {
        return [
            'consultations' => static::$resource::getModel()::with('patient')
                                    ->get()
                                    ->each(function($item) {
                                        $item->date_consult = $item->date->format('M j, Y');
                                    }),

            'tenant' => Filament::getTenant()->id
        ];
    }
}
