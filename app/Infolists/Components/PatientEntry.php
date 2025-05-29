<?php

namespace App\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Concerns\CanFormatState;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;

class PatientEntry extends TextEntry
{
    use CanFormatState;
    
    protected TextEntrySize | string | Closure | null $size = null;
    
    protected string $view = 'infolists.components.patient-entry';

    public function size(TextEntrySize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): TextEntrySize | string | null
    {
        return $this->evaluate($this->size, [
            'state' => $state,
        ]);
    }
}
