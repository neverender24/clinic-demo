<?php

namespace App\Trait\Dashboard;

trait HasAgeDistributationColumn
{
    public function getColumnSpan(): int|string|array
    {
        return [
            'xl' => 3,
            'default' => 6,
        ];
    }
}
