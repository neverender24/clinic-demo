<?php

// declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Consultation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
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
        return $authUser->can('Update:Consultation');
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

    public function addManagement(AuthUser $authUser): bool
    {
        return $authUser->can('AddManagement:Consultation');
    }

    public function addDiagnosis(AuthUser $authUser): bool
    {
        return $authUser->can('AddDiagnosis:Consultation');
    }

    public function addChiefComplaint(AuthUser $authUser): bool
    {
        return $authUser->can('AddChiefComplaint:Consultation');
    }

    public function addPrescription(AuthUser $authUser): bool
    {
        return $authUser->can('AddPrescription:Consultation');
    }

    public function addTestResult(AuthUser $authUser): bool
    {
        return $authUser->can('AddTestResult:Consultation');
    }

    public function addFollowupSchedule(AuthUser $authUser): bool
    {
        return $authUser->can('AddFollowupSchedule:Consultation');
    }

    public function addVitalSign(AuthUser $authUser): bool
    {
        // dd('test');
        return $authUser->can('AddVitalSign:Consultation');
    }

}