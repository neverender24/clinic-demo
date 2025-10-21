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
        return $authUser->can('ViewAny:HospitalAdmission');
    }

    public function view(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('View:HospitalAdmission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HospitalAdmission');
    }

    public function update(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('Update:HospitalAdmission');
    }

    public function delete(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('Delete:HospitalAdmission');
    }

    public function restore(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('Restore:HospitalAdmission');
    }

    public function forceDelete(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('ForceDelete:HospitalAdmission');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HospitalAdmission');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HospitalAdmission');
    }

    public function replicate(AuthUser $authUser, HospitalAdmission $hospitalAdmission): bool
    {
        return $authUser->can('Replicate:HospitalAdmission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HospitalAdmission');
    }

}