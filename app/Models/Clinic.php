<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
    protected $fillable = [
        'name',
        'location',
        'header_image',
        'medcert_header_image',
        'watermarks'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function hospitalAdmissions(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
}
