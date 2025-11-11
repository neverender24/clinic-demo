<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Consultation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        dd($authUser->can('view_any_consultation'));
        return $authUser->can('ViewAny:Consultation');
    }

    public function view(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('View:Consultation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Consultation');
    }

    public function update(AuthUser $authUser, Consultation $consultation): bool
    {
        // dd($authUser->can('Update:Consultation'));
        return $authUser->can('Update:Consultation') || true;
    }

    public function delete(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('Delete:Consultation');
    }

    public function restore(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('Restore:Consultation');
    }

    public function forceDelete(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('ForceDelete:Consultation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Consultation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Consultation');
    }

    public function replicate(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('Replicate:Consultation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Consultation');
    }

          /**
     * Customized permissions.
     */
    public function addManagement(AuthUser $user): bool
    {
        return $user->can('add_management_consultation');
    }

    public function addDiagnosis(AuthUser $user): bool
    {
        return $user->can('add_diagnosis_consultation');
    }

    public function addChiefComplaint(AuthUser $user): bool
    {
        return $user->can('add_chief_complaint_consultation');
    }

    public function addPrescription(AuthUser $user): bool
    {
        return $user->can('add_prescription_consultation');
    }

    public function addTestResult(AuthUser $user): bool
    {
        return $user->can('add_test_results_consultation');
    }

    public function editAsDoctor(AuthUser $user)
    {
        // dd($user->hasPermissionTo());
        return $user->can('edit_as_doctor_consultation');
    }

    public function editRecord(AuthUser $user, Consultation $consultation): bool
    {
        return ($consultation->status->value !== 'Done' || $user->doctor());
    }

    public function addFollowupSchedule(AuthUser $user, Consultation $consultation): bool
    {
        return $user->can('add_followup_schedule_consultation');
    }

}