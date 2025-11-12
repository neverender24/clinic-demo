<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HospitalAdmission;
use Illuminate\Auth\Access\HandlesAuthorization;

class HospitalAdmissionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_hospital::admission');
    }

    public function view(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('view_hospital::admission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_hospital::admission');
    }

    public function update(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('update_hospital::admission');
    }

    public function delete(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('delete_hospital::admission');
    }

    public function restore(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('restore_hospital::admission');
    }

    public function forceDelete(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('force_delete_hospital::admission');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_hospital::admission');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_hospital::admission');
    }

    public function replicate(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('replicate_hospital::admission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_hospital::admission');
    }

}