<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Tables\Table;
use App\Trait\Dashboard\HasDashboardSettings;
use App\Trait\Dashboard\InteractsWithDashboardFilters;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class MedicineTableWidget extends BaseWidget
{
    use HasDashboardSettings;
    use InteractsWithDashboardFilters;

    protected int | string | array $columnSpan = 'full';

    public function getColumnSpan(): int | string | array
    {
        return ['default' => 'full'];
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Medicine::query()
                    ->withCount([
                        'consultations as filtered_consultations_count' => fn ($query) => $this->applyDashboardConsultationFilters($query),
                    ])
                    ->whereHas('consultations', fn ($query) => $this->applyDashboardConsultationFilters($query))
            )
            ->columns([
                TextColumn::make("name")->searchable(),
                TextColumn::make("brand")->searchable(),
                TextColumn::make('filtered_consultations_count')
                    ->label('Consultations')
                    ->sortable(),
            ])
            ->defaultSort('filtered_consultations_count', 'desc');
    }
}
