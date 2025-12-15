<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_consultation","view_any_consultation","create_consultation","update_consultation","restore_consultation","restore_any_consultation","replicate_consultation","reorder_consultation","delete_consultation","delete_any_consultation","force_delete_consultation","force_delete_any_consultation","view_hospital::admission","view_any_hospital::admission","create_hospital::admission","update_hospital::admission","restore_hospital::admission","restore_any_hospital::admission","replicate_hospital::admission","reorder_hospital::admission","delete_hospital::admission","delete_any_hospital::admission","force_delete_hospital::admission","force_delete_any_hospital::admission","view_medicine","view_any_medicine","create_medicine","update_medicine","restore_medicine","restore_any_medicine","replicate_medicine","reorder_medicine","delete_medicine","delete_any_medicine","force_delete_medicine","force_delete_any_medicine","view_patient","view_any_patient","create_patient","update_patient","restore_patient","restore_any_patient","replicate_patient","reorder_patient","delete_patient","delete_any_patient","force_delete_patient","force_delete_any_patient","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","add_management_consultation","add_diagnosis_consultation","add_chief_complaint_consultation","add_prescription_consultation","add_test_results_consultation","edit_as_doctor_consultation","add_followup_schedule_consultation","widget_MostPrescribedDrugs","widget_ConsultationChart","view_body::part","view_any_body::part","create_body::part","update_body::part","restore_body::part","restore_any_body::part","replicate_body::part","reorder_body::part","delete_body::part","delete_any_body::part","force_delete_body::part","force_delete_any_body::part","view_document","view_any_document","create_document","update_document","restore_document","restore_any_document","replicate_document","reorder_document","delete_document","delete_any_document","force_delete_document","force_delete_any_document","view_document::type","view_any_document::type","create_document::type","update_document::type","restore_document::type","restore_any_document::type","replicate_document::type","reorder_document::type","delete_document::type","delete_any_document::type","force_delete_document::type","force_delete_any_document::type","widget_HospitalAdmissionChart","widget_MedicineTableWidget"]},{"name":"secretary","guard_name":"web","permissions":["ViewAny:Consultation","View:Consultation","Create:Consultation","Update:Consultation","Delete:Consultation","Restore:Consultation","ViewAny:Medicine","View:Medicine","ViewAny:Patient","View:Patient","Update:Patient","Create:Patient","AddChiefComplaint:Consultation","AddTestResult:Consultation"]},{"name":"Doctor","guard_name":"web","permissions":["ViewAny:Consultation","View:Consultation","Create:Consultation","Update:Consultation","Delete:Consultation","Restore:Consultation","ViewAny:Medicine","View:Medicine","ViewAny:Patient","View:Patient","Update:Patient","Create:Patient","ViewAny:BodyPart","View:BodyPart","Create:BodyPart","Update:BodyPart","Delete:BodyPart","Restore:BodyPart","ForceDelete:BodyPart","ForceDeleteAny:BodyPart","RestoreAny:BodyPart","Replicate:BodyPart","Reorder:BodyPart","ForceDelete:Consultation","ForceDeleteAny:Consultation","RestoreAny:Consultation","Replicate:Consultation","Reorder:Consultation","AddManagement:Consultation","AddDiagnosis:Consultation","AddChiefComplaint:Consultation","AddPrescription:Consultation","AddTestResult:Consultation","AddFollowupSchedule:Consultation","ViewAny:HospitalAdmission","View:HospitalAdmission","Create:HospitalAdmission","Update:HospitalAdmission","Delete:HospitalAdmission","Restore:HospitalAdmission","ForceDelete:HospitalAdmission","ForceDeleteAny:HospitalAdmission","RestoreAny:HospitalAdmission","Replicate:HospitalAdmission","Reorder:HospitalAdmission","Create:Medicine","Update:Medicine","Delete:Medicine","Restore:Medicine","ForceDelete:Medicine","ForceDeleteAny:Medicine","RestoreAny:Medicine","Replicate:Medicine","Reorder:Medicine","Delete:Patient","Restore:Patient","ForceDelete:Patient","ForceDeleteAny:Patient","RestoreAny:Patient","Replicate:Patient","Reorder:Patient"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
