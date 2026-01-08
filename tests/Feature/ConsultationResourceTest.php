<?php

use App\Models\User;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Consultation;
use App\Enums\Enums\Status;
use App\Filament\Resources\Consultations\ConsultationResource;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Disable activity logging for tests
    activity()->disableLogging();

    $this->clinic = Clinic::factory()->create();
    $this->user = User::factory()->create([
        'clinic_id' => $this->clinic->id,
    ]);

    actingAs($this->user);

    // Set the Filament tenant for TenantScope
    Filament::setTenant($this->clinic);
});

afterEach(function () {
    // Re-enable activity logging after tests
    activity()->enableLogging();
});

describe('Consultation Model', function () {
    it('can create a consultation', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
            'chief_complaint' => 'Headache',
            'diagnosis' => 'Migraine',
        ]);

        expect($consultation)->toBeInstanceOf(Consultation::class)
            ->and($consultation->chief_complaint)->toBe('Headache')
            ->and($consultation->diagnosis)->toBe('Migraine');
    });

    it('belongs to a patient', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect($consultation->patient)->toBeInstanceOf(Patient::class)
            ->and($consultation->patient->id)->toBe($patient->id);
    });

    it('belongs to a clinic', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect($consultation->clinic)->toBeInstanceOf(Clinic::class)
            ->and($consultation->clinic->id)->toBe($this->clinic->id);
    });

    it('can change status', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->pending()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect($consultation->status)->toBe(Status::Pending);

        $consultation->changeStatus();

        expect($consultation->fresh()->status)->toBe(Status::Done);
    });

    it('can scope to current consultations', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create consultation for today
        $todayConsultation = Consultation::factory()->today()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        // Create consultation for yesterday
        Consultation::factory()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
            'date' => now()->subDay(),
        ]);

        $currentConsultations = Consultation::withoutGlobalScopes()
            ->where('clinic_id', $this->clinic->id)
            ->currentConsultations()
            ->get();

        expect($currentConsultations)->toHaveCount(1)
            ->and($currentConsultations->first()->id)->toBe($todayConsultation->id);
    });
});

describe('ConsultationResource', function () {
    it('shows correct navigation badge count for pending consultations', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Consultation::factory()->count(3)->pending()->today()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        Consultation::factory()->count(2)->done()->today()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect(ConsultationResource::getNavigationBadge())->toBe('3');
    });

    it('returns null badge when no pending consultations', function () {
        expect(ConsultationResource::getNavigationBadge())->toBeNull();
    });

    it('has correct permission prefixes', function () {
        $permissions = ConsultationResource::getPermissionPrefixes();

        expect($permissions)->toContain('view')
            ->and($permissions)->toContain('create')
            ->and($permissions)->toContain('update')
            ->and($permissions)->toContain('delete')
            ->and($permissions)->toContain('add_prescription')
            ->and($permissions)->toContain('add_diagnosis');
    });
});

describe('Patient Factory', function () {
    it('creates valid patient', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        expect($patient)->toBeInstanceOf(Patient::class)
            ->and($patient->first_name)->not->toBeEmpty()
            ->and($patient->last_name)->not->toBeEmpty()
            ->and($patient->sex)->toBeIn(['M', 'F']);
    });

    it('creates male patient', function () {
        $patient = Patient::factory()->male()->create([
            'user_id' => $this->user->id,
        ]);

        expect($patient->sex)->toBe('M');
    });

    it('creates female patient', function () {
        $patient = Patient::factory()->female()->create([
            'user_id' => $this->user->id,
        ]);

        expect($patient->sex)->toBe('F');
    });
});

describe('Consultation Factory', function () {
    it('creates pending consultation', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->pending()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect($consultation->status)->toBe(Status::Pending);
    });

    it('creates done consultation', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->done()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect($consultation->status)->toBe(Status::Done);
    });

    it('creates consultation for today', function () {
        $patient = Patient::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $consultation = Consultation::factory()->today()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
        ]);

        expect($consultation->date->toDateString())->toBe(now()->toDateString());
    });
});
