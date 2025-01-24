<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $guarded = [];

    protected $casts = [
        'contact_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hmos(): BelongsToMany
    {
        return $this->belongsToMany(Hmo::class, 'patient_hmo')->withPivot(['date_registered', 'date_expiry']);
    }

    public function patientHmos(): HasMany
    {
        return $this->hasMany(PatientHmo::class);
    }
}
