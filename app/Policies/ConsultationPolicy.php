<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Consultation;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_consultation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Consultation $consultation): bool
    {
        return $user->can('view_consultation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_consultation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Consultation $consultation): bool
    {
        return $user->can('update_consultation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->can('delete_consultation');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_consultation');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Consultation $consultation): bool
    {
        return $user->can('force_delete_consultation');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_consultation');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Consultation $consultation): bool
    {
        return $user->can('restore_consultation');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_consultation');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Consultation $consultation): bool
    {
        return $user->can('replicate_consultation');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_consultation');
    }

        /**
     * Customized permissions.
     */
    public function addManagement(User $user): bool
    {
        return $user->can('add_management_consultation');
    }

    public function addDiagnosis(User $user): bool
    {
        return $user->can('add_diagnosis_consultation');
    }

    public function addChiefComplaint(User $user): bool
    {
        return $user->can('add_chief_complaint_consultation');
    }

    public function addPrescription(User $user): bool
    {
        return $user->can('add_prescription_consultation');
    }

    public function addTestResult(User $user): bool
    {
        return $user->can('add_test_results_consultation');
    }

    public function editAsDoctor(User $user)
    {
        // dd($user->hasPermissionTo());
        return $user->can('edit_as_doctor_consultation');
    }

    public function editRecord(User $user, Consultation $consultation): bool
    {
        return ($consultation->status->value !== 'Done' || $user->doctor());
    }

    public function addFollowupSchedule(User $user, Consultation $consultation): bool
    {
        return $user->can('add_followup_schedule_consultation');
    }
}
