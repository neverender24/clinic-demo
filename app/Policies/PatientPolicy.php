<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Patient;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_patient');
    }

    public function view(AuthUser $authUser, Patient $patient): bool
    {
        return $authUser->can('view_patient');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_patient');
    }

    public function update(AuthUser $authUser, Patient $patient): bool
    {
        return $authUser->can('update_patient');
    }

    public function delete(AuthUser $authUser, Patient $patient): bool
    {
        return $authUser->can('delete_patient');
    }

    public function restore(AuthUser $authUser, Patient $patient): bool
    {
        return $authUser->can('restore_patient');
    }

    public function forceDelete(AuthUser $authUser, Patient $patient): bool
    {
        return $authUser->can('force_delete_patient');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_patient');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_patient');
    }

    public function replicate(AuthUser $authUser, Patient $patient): bool
    {
        return $authUser->can('replicate_patient');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_patient');
    }

}