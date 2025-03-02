<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalAdmission extends Model
{
    protected $guarded = [];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeWithPeriod(Builder $query, $period)
    {
        if($period == 'Weekly' || $period == 'Daily')
        {
            $query->whereMonth('admission_date', now()->month)
                ->whereYear('admission_date', now()->year);
        }
    }
}
