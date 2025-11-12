<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Medicine;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicinePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_medicine');
    }

    public function view(AuthUser $authUser, Medicine $medicine): bool
    {
        return $authUser->can('view_medicine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_medicine');
    }

    public function update(AuthUser $authUser, Medicine $medicine): bool
    {
        return $authUser->can('update_medicine');
    }

    public function delete(AuthUser $authUser, Medicine $medicine): bool
    {
        return $authUser->can('delete_medicine');
    }

    public function restore(AuthUser $authUser, Medicine $medicine): bool
    {
        return $authUser->can('restore_medicine');
    }

    public function forceDelete(AuthUser $authUser, Medicine $medicine): bool
    {
        return $authUser->can('force_delete_medicine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_medicine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_medicine');
    }

    public function replicate(AuthUser $authUser, Medicine $medicine): bool
    {
        return $authUser->can('replicate_medicine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_medicine');
    }

}