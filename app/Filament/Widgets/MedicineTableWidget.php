<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Medicine;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Dom\Text;

class MedicineTableWidget extends BaseWidget
{

    // use HasWidgetShield;
    protected static ?int $sort = 4;
protected int | string | array $columnSpan = 'full';
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
