<?php

namespace App\Infolists\Components;

use Filament\Support\Enums\TextSize;
use Closure;
use Filament\Infolists\Components\Concerns\CanFormatState;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;

class PatientEntry extends TextEntry
{
    use CanFormatState;
    
    protected TextSize | string | Closure | null $size = null;
    
    protected string $view = 'infolists.components.patient-entry';

    public function size(TextSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): TextSize | string | null
    {
        return $this->evaluate($this->size, [
            'state' => $state,
        ]);
    }
}
