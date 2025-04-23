<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Medicine;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_medicine');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medicine $medicine): bool
    {
        return $user->can('view_medicine');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medicine');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Medicine $medicine): bool
    {
        // dd($medicine);
        return $user->can('update_medicine');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Medicine $medicine): bool
    {
        // dd($medicine->consultations);
        return $user->can('delete_medicine') && $medicine->consultations->count() == 0;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_medicine');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Medicine $medicine): bool
    {
        return $user->can('force_delete_medicine');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_medicine');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Medicine $medicine): bool
    {
        return $user->can('restore_medicine');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_medicine');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Medicine $medicine): bool
    {
        return $user->can('replicate_medicine');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_medicine');
    }
}
