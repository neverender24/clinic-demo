<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ConsultationResource;
use App\Models\Scopes\ConsultationScope;

class ListConsultations extends ListRecords
{
    protected static string $resource = ConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    public function getTabs(): array
    {
        return [
            'current' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->with(['medicines', 'patient'])->where('date', now()->format('Y-m-d'))),
            'current_year' => Tab::make()
                ->label(now()->year." Consultations")
                ->modifyQueryUsing(fn (Builder $query) => $query->with(['medicines', 'patient'])->whereYear('date', now()->year)),
            'all' => Tab::make()
                        ->modifyQueryUsing(fn (Builder $query) => $query->with([
                                'medicines',
                                'patient'
                            ])
                            // ->whereHas('patient', fn($query) => $query->withTrashed())
                        ),
        ];
    }
}
