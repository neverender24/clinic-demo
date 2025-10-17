<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Throwable;
use Database\Factories\UserFactory;
use App\Trait\HasUserRole;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser, HasTenants, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUserRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'clinic_id',
        'email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }


    public function getTenants(Panel $panel): Collection
    {
        if ($this->doctor()) {
            return Clinic::all();
            // return $this->
        }
        return $this->clinics;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->doctor()) {
            return true;
        }
        return $this->clinics()->whereKey($tenant)->exists();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        // asset('storage/'.$record->clinic->watermarks)
        // dd(Filament::getTenant());
        try {
            return Filament::getTenant()->watermarks ? asset('storage/'.Filament::getTenant()->watermarks) : asset('images/user.svg');
        } catch (Throwable $th) {
            return asset('images/user.svg');
        }
    }
}
