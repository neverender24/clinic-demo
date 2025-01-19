<?php

namespace App\Models;

use App\Enums\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Consultation extends Model
{
    protected $guarded = ['medicines'];

    protected function casts(): array
    {
        return [
            'status' => Status::class
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicines():BelongsToMany
    {
        return $this->belongsToMany(Medicine::class)->withPivot(['remarks']);
    }

    public function consultationMedicines(): HasMany
    {
        return $this->hasMany(ConsultationMedicine::class);
    }

    public function changeStatus()
    {
        $this->status = $this->status->value == 'Done' ? 'Pending' : 'Done';
        $this->save();
    }
}
