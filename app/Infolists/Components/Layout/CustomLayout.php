<?php

namespace App\Infolists\Components\Layout;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Component;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class CustomLayout extends Component implements HasTable, HasForms
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected string $view = 'infolists.components.layout.custom-layout';

    public function mount(int | string $record): void
    {
        
        
        
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
