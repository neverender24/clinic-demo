<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;
    protected static ?string $title = 'Dashboard';

    public function getColumns(): int | array
    {
        dd(auth()->user()->roles->load('permissions'));
        return 12;
    }
}