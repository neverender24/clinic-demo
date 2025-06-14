<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medicine extends Model
{
    use SoftDeletes; 
    use LogsActivity;
    protected $fillable = [
        'name',
        'user_id',
        'brand',
        'type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultations(): BelongsToMany
    {
        return $this->belongsToMany(Consultation::class, 'consultation_medicine')->withoutGlobalScopes();
    }

    public function medfullname(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['name'] . ' ' . $attributes['brand'],
        );
    }
   
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                    ->logAll();
    }
}
