<?php

namespace App\Filament\Pages;

use App\Models\Clinic;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('clinic_id')
                ->label('Clinic')
                ->options(['All' => 'All'] + Clinic::query()->pluck('name', 'id')->all())
                ->default('All')
                ->searchable(),
            Select::make('period')
                ->options([
                    'Daily' => 'Daily',
                    'Weekly' => 'Weekly',
                    'Monthly' => 'Monthly',
                    'Yearly' => 'Yearly',
                ])
                ->default('Monthly'),
            DatePicker::make('start_date')
                ->label('Start date'),
            DatePicker::make('end_date')
                ->label('End date'),
        ]);
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 12,
        ];
    }
}
