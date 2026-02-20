<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Medicine;
use Filament\Tables\Table;
use App\Trait\Dashboard\HasDashboardSettings;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Dom\Text;

class MedicineTableWidget extends BaseWidget
{
    use HasDashboardSettings;

    // use HasWidgetShield;
    protected int | string | array $columnSpan = 'full';

    public function getColumnSpan(): int | string | array
    {
        return ['default' => 'full'];
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Medicine::take(10)
            )
            ->columns([
                TextColumn::make("name")->searchable(),
                TextColumn::make("brand")->searchable(),
                TextColumn::make("consultations_count")->counts('consultations')->sortable(),
            ])->defaultSort('consultations_count', 'desc');
    }
}
