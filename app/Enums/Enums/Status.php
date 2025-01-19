<?php

namespace App\Enums\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel, HasIcon, HasColor
{
    case Pending = 'Pending';
    case Done = 'Done';

    public function getLabel(): ?string
    {
        return $this->name;
        
        // or
    
        return match ($this) {
            self::Pending => 'Pending',
            self::Done => 'Done',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Done => 'heroicon-o-check',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'primary',
            self::Done => 'success',
        };
    }
}
