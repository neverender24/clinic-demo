<?php

namespace App\Models;

use App\Enums\Enums\Status;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

#[ScopedBy([TenantScope::class])]
class Consultation extends Model
{
    protected $guarded = ['medicines'];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'next_follow_up_schedule' => 'date',
            'date' => 'date'
        ];
    }

    // public function date(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn($value) => Carbon::parse($value)->format('F j, Y')
    //     );
    // }

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
        return $this->belongsToMany(Medicine::class)->withPivot(['remarks', 'quantity']);
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
