<?php

namespace App\Trait\Dashboard;

trait HasAgeDistributationColumn
{
    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 1,
            'lg' => 6,
            'xl' => 3,
        ];
    }
}
