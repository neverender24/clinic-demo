<?php

namespace App\Trait;

use Spatie\Permission\Traits\HasRoles;

trait HasUserRole
{
    use HasRoles;
    

    public function superAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function doctor(): bool
    {
        return $this->hasRole('Doctor') || $this->superAdmin();
    }
}
