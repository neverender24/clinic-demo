<?php

namespace App\Trait\Dashboard;

use App\Models\ClinicSetting;
use Illuminate\Support\Str;

trait HasDashboardSettings
{
    public static function canView(): bool
    {
        return ClinicSetting::isWidgetVisible(
            Str::snake(class_basename(static::class))
        );
    }

    public static function getSort(): int
    {
        return ClinicSetting::getWidgetSort(
            Str::snake(class_basename(static::class))
        );
    }
}
