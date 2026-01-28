<?php

namespace App\Models;

use App\Enums\Enums\Status;
use App\Models\Scopes\ConsultationScope;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

#[ScopedBy([TenantScope::class])]
class Consultation extends Model
{
    use HasFactory;

    protected $guarded = ['medicines'];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'next_follow_up_schedule' => 'date',
            'date' => 'date',
            'estimated_date' => 'date',
            'attachments' => 'array'
           
        ];
    }

    // public function date(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn($value) => Carbon::parse($value)->format('F j, Y')
    //     );
    // }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class);
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
        return $this->belongsToMany(Medicine::class)->withPivot(['id', 'remarks', 'quantity', 'sort'])->orderByPivot('sort');
    }

    public function consultationMedicines(): HasMany
    {
        return $this->hasMany(ConsultationMedicine::class);
    }

    public function customDocs(): HasMany
    {
        return $this->hasMany(CustomDoc::class);
    }

    public function changeStatus()
    {
        $this->status = $this->status->value == 'Done' ? 'Pending' : 'Done';
        $this->save();
    }

    protected function scopeCurrentConsultations(Builder $query)
    {
        $query->where('date', now()->toDateString());
    }

    protected function scopePatientPreviousConsultations(Builder $query, $patient_id, $date)
    {
        $query->where('patient_id', $patient_id)->whereDate('date', '<', $date);
    }

    public function scopeWithPeriod(Builder $query, $period)
    {
        if($period == 'Weekly' || $period == 'Daily')
        {
            $query->whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
        }
    }
}
