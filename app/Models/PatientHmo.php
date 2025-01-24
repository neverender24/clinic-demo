<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientHmo extends Pivot
{
    protected $guarded = [];

    protected $table = 'patient_hmo';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function hmo(): BelongsTo
    {
        return $this->belongsTo(Hmo::class, 'hmo_id');
    }

}
