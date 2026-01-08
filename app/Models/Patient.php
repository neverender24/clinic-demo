<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
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

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function hospitalAdmissions(): HasMany
    {
        return $this->hasMany(HospitalAdmission::class);
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                    ->logAll();
    }
}
