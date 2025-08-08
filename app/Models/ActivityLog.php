<?php

namespace App\Models;

use Filament\Facades\Filament;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Activity
{
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $activity) {
            // ✅ Automatically assign the current tenant (clinic)
            if (Filament::getTenant()) {
                $activity->clinic_id = Filament::getTenant()->id;
            }
        });
    }

}
