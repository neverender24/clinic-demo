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
        return $authUser->can('view_any_consultation');
    }

    public function view(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('view_consultation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_consultation');
    }

    public function update(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('update_consultation');
    }

    public function delete(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('delete_consultation');
    }

    public function restore(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('restore_consultation');
    }

    public function forceDelete(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('force_delete_consultation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_consultation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_consultation');
    }

    public function replicate(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('replicate_consultation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_consultation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_consultation');
    }

    public function addManagement(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('add_management_consultation');
    }

    public function addDiagnosis(AuthUser $authUser, Consultation $consultation): bool
    {
        return $authUser->can('add_diagnosis_consultation');
    }

    public function addChiefComplaint(AuthUser $authUser): bool
    {
        return $authUser->can('add_chief_complaint_consultation');
    }

    public function addPrescription(AuthUser $authUser): bool
    {
        return $authUser->can('add_prescription_consultation');
    }

    public function addTestResults(AuthUser $authUser): bool
    {
        return $authUser->can('add_test_results_consultation');
    }

    public function editAsDoctor(AuthUser $authUser): bool
    {
        return $authUser->can('edit_as_doctor_consultation');
    }

    public function addFollowupSchedule(AuthUser $authUser): bool
    {
        return $authUser->can('add_followup_schedule_consultation');
    }

}