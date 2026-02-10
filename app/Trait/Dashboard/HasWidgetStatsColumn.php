<?php

namespace App\Trait\Dashboard;

trait HasWidgetStatsColumn
{
    protected function getColumns(): int|array|null
    {
        return [
            'xl' => 4,
            'default' => 2,
        ];
    }
}
