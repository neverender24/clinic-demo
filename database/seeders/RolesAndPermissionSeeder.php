<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            Role::upsert($this->roles(), ['id', 'name'],  ['name']);
            Permission::upsert($this->permissions(), ['id', 'name'],  ['name']);
            DB::table('role_has_permissions')->insert($this->rolesPermissions());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    protected function roles(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'super_admin',
                'guard_name' => 'web',
            ],
            [
                'id' => 2,
                'name' => 'secretary',
                'guard_name' => 'web',
            ],
            [
                'id' => 3,
                'name' => 'Doctor',
                'guard_name' => 'web',
            ],
        ];
    }

    protected function rolesPermissions(): array
    {
        return [
            [
                'permission_id' => 1,
                'role_id' => 1,
            ],
            [
                'permission_id' => 2,
                'role_id' => 1,
            ],
            [
                'permission_id' => 3,
                'role_id' => 1,
            ],
            [
                'permission_id' => 4,
                'role_id' => 1,
            ],
            [
                'permission_id' => 5,
                'role_id' => 1,
            ],
            [
                'permission_id' => 6,
                'role_id' => 1,
            ],
            [
                'permission_id' => 7,
                'role_id' => 1,
            ],
            [
                'permission_id' => 8,
                'role_id' => 1,
            ],
            [
                'permission_id' => 9,
                'role_id' => 1,
            ],
            [
                'permission_id' => 10,
                'role_id' => 1,
            ],
            [
                'permission_id' => 11,
                'role_id' => 1,
            ],
            [
                'permission_id' => 12,
                'role_id' => 1,
            ],
            [
                'permission_id' => 13,
                'role_id' => 1,
            ],
            [
                'permission_id' => 14,
                'role_id' => 1,
            ],
            [
                'permission_id' => 15,
                'role_id' => 1,
            ],
            [
                'permission_id' => 16,
                'role_id' => 1,
            ],
            [
                'permission_id' => 17,
                'role_id' => 1,
            ],
            [
                'permission_id' => 18,
                'role_id' => 1,
            ],
            [
                'permission_id' => 19,
                'role_id' => 1,
            ],
            [
                'permission_id' => 20,
                'role_id' => 1,
            ],
            [
                'permission_id' => 21,
                'role_id' => 1,
            ],
            [
                'permission_id' => 22,
                'role_id' => 1,
            ],
            [
                'permission_id' => 23,
                'role_id' => 1,
            ],
            [
                'permission_id' => 24,
                'role_id' => 1,
            ],
            [
                'permission_id' => 25,
                'role_id' => 1,
            ],
            [
                'permission_id' => 26,
                'role_id' => 1,
            ],
            [
                'permission_id' => 27,
                'role_id' => 1,
            ],
            [
                'permission_id' => 28,
                'role_id' => 1,
            ],
            [
                'permission_id' => 29,
                'role_id' => 1,
            ],
            [
                'permission_id' => 30,
                'role_id' => 1,
            ],
            [
                'permission_id' => 31,
                'role_id' => 1,
            ],
            [
                'permission_id' => 32,
                'role_id' => 1,
            ],
            [
                'permission_id' => 33,
                'role_id' => 1,
            ],
            [
                'permission_id' => 34,
                'role_id' => 1,
            ],
            [
                'permission_id' => 35,
                'role_id' => 1,
            ],
            [
                'permission_id' => 36,
                'role_id' => 1,
            ],
            [
                'permission_id' => 37,
                'role_id' => 1,
            ],
            [
                'permission_id' => 38,
                'role_id' => 1,
            ],
            [
                'permission_id' => 39,
                'role_id' => 1,
            ],
            [
                'permission_id' => 40,
                'role_id' => 1,
            ],
            [
                'permission_id' => 41,
                'role_id' => 1,
            ],
            [
                'permission_id' => 42,
                'role_id' => 1,
            ],
            [
                'permission_id' => 43,
                'role_id' => 1,
            ],
            [
                'permission_id' => 44,
                'role_id' => 1,
            ],
            [
                'permission_id' => 45,
                'role_id' => 1,
            ],
            [
                'permission_id' => 46,
                'role_id' => 1,
            ],
            [
                'permission_id' => 47,
                'role_id' => 1,
            ],
            [
                'permission_id' => 48,
                'role_id' => 1,
            ],
            [
                'permission_id' => 49,
                'role_id' => 1,
            ],
            [
                'permission_id' => 50,
                'role_id' => 1,
            ],
            [
                'permission_id' => 51,
                'role_id' => 1,
            ],
            [
                'permission_id' => 52,
                'role_id' => 1,
            ],
            [
                'permission_id' => 53,
                'role_id' => 1,
            ],
            [
                'permission_id' => 54,
                'role_id' => 1,
            ],
            [
                'permission_id' => 55,
                'role_id' => 1,
            ],
            [
                'permission_id' => 56,
                'role_id' => 1,
            ],
            [
                'permission_id' => 57,
                'role_id' => 1,
            ],
            [
                'permission_id' => 58,
                'role_id' => 1,
            ],
            [
                'permission_id' => 59,
                'role_id' => 1,
            ],
            [
                'permission_id' => 60,
                'role_id' => 1,
            ],
            [
                'permission_id' => 61,
                'role_id' => 1,
            ],
            [
                'permission_id' => 62,
                'role_id' => 1,
            ],
            [
                'permission_id' => 63,
                'role_id' => 1,
            ],
            [
                'permission_id' => 64,
                'role_id' => 1,
            ],
            [
                'permission_id' => 65,
                'role_id' => 1,
            ],
            [
                'permission_id' => 66,
                'role_id' => 1,
            ],
            [
                'permission_id' => 67,
                'role_id' => 1,
            ],
            [
                'permission_id' => 68,
                'role_id' => 1,
            ],
            [
                'permission_id' => 69,
                'role_id' => 1,
            ],
            [
                'permission_id' => 70,
                'role_id' => 1,
            ],
            [
                'permission_id' => 71,
                'role_id' => 1,
            ],
            [
                'permission_id' => 72,
                'role_id' => 1,
            ],
            [
                'permission_id' => 73,
                'role_id' => 1,
            ],
            [
                'permission_id' => 74,
                'role_id' => 1,
            ],
            [
                'permission_id' => 75,
                'role_id' => 1,
            ],
            [
                'permission_id' => 7,
                'role_id' => 2,
            ],
            [
                'permission_id' => 8,
                'role_id' => 2,
            ],
            [
                'permission_id' => 9,
                'role_id' => 2,
            ],
            [
                'permission_id' => 10,
                'role_id' => 2,
            ],
            [
                'permission_id' => 15,
                'role_id' => 2,
            ],
            [
                'permission_id' => 16,
                'role_id' => 2,
            ],
            [
                'permission_id' => 69,
                'role_id' => 2,
            ],
            [
                'permission_id' => 71,
                'role_id' => 2,
            ],
            [
                'permission_id' => 7,
                'role_id' => 3,
            ],
            [
                'permission_id' => 8,
                'role_id' => 3,
            ],
            [
                'permission_id' => 9,
                'role_id' => 3,
            ],
            [
                'permission_id' => 11,
                'role_id' => 3,
            ],
            [
                'permission_id' => 12,
                'role_id' => 3,
            ],
            [
                'permission_id' => 13,
                'role_id' => 3,
            ],
            [
                'permission_id' => 14,
                'role_id' => 3,
            ],
            [
                'permission_id' => 15,
                'role_id' => 3,
            ],
            [
                'permission_id' => 16,
                'role_id' => 3,
            ],
            [
                'permission_id' => 17,
                'role_id' => 3,
            ],
            [
                'permission_id' => 18,
                'role_id' => 3,
            ],
            [
                'permission_id' => 19,
                'role_id' => 3,
            ],
            [
                'permission_id' => 20,
                'role_id' => 3,
            ],
            [
                'permission_id' => 21,
                'role_id' => 3,
            ],
            [
                'permission_id' => 22,
                'role_id' => 3,
            ],
            [
                'permission_id' => 23,
                'role_id' => 3,
            ],
            [
                'permission_id' => 24,
                'role_id' => 3,
            ],
            [
                'permission_id' => 25,
                'role_id' => 3,
            ],
            [
                'permission_id' => 26,
                'role_id' => 3,
            ],
            [
                'permission_id' => 27,
                'role_id' => 3,
            ],
            [
                'permission_id' => 28,
                'role_id' => 3,
            ],
            [
                'permission_id' => 29,
                'role_id' => 3,
            ],
            [
                'permission_id' => 30,
                'role_id' => 3,
            ],
            [
                'permission_id' => 31,
                'role_id' => 3,
            ],
            [
                'permission_id' => 32,
                'role_id' => 3,
            ],
            [
                'permission_id' => 33,
                'role_id' => 3,
            ],
            [
                'permission_id' => 34,
                'role_id' => 3,
            ],
            [
                'permission_id' => 35,
                'role_id' => 3,
            ],
            [
                'permission_id' => 36,
                'role_id' => 3,
            ],
            [
                'permission_id' => 37,
                'role_id' => 3,
            ],
            [
                'permission_id' => 38,
                'role_id' => 3,
            ],
            [
                'permission_id' => 39,
                'role_id' => 3,
            ],
            [
                'permission_id' => 40,
                'role_id' => 3,
            ],
            [
                'permission_id' => 41,
                'role_id' => 3,
            ],
            [
                'permission_id' => 42,
                'role_id' => 3,
            ],
            [
                'permission_id' => 43,
                'role_id' => 3,
            ],
            [
                'permission_id' => 44,
                'role_id' => 3,
            ],
            [
                'permission_id' => 45,
                'role_id' => 3,
            ],
            [
                'permission_id' => 46,
                'role_id' => 3,
            ],
            [
                'permission_id' => 47,
                'role_id' => 3,
            ],
            [
                'permission_id' => 48,
                'role_id' => 3,
            ],
            [
                'permission_id' => 49,
                'role_id' => 3,
            ],
            [
                'permission_id' => 50,
                'role_id' => 3,
            ],
            [
                'permission_id' => 51,
                'role_id' => 3,
            ],
            [
                'permission_id' => 52,
                'role_id' => 3,
            ],
            [
                'permission_id' => 53,
                'role_id' => 3,
            ],
            [
                'permission_id' => 54,
                'role_id' => 3,
            ],
            [
                'permission_id' => 55,
                'role_id' => 3,
            ],
            [
                'permission_id' => 56,
                'role_id' => 3,
            ],
            [
                'permission_id' => 57,
                'role_id' => 3,
            ],
            [
                'permission_id' => 58,
                'role_id' => 3,
            ],
            [
                'permission_id' => 59,
                'role_id' => 3,
            ],
            [
                'permission_id' => 60,
                'role_id' => 3,
            ],
            [
                'permission_id' => 61,
                'role_id' => 3,
            ],
            [
                'permission_id' => 62,
                'role_id' => 3,
            ],
            [
                'permission_id' => 63,
                'role_id' => 3,
            ],
            [
                'permission_id' => 64,
                'role_id' => 3,
            ],
            [
                'permission_id' => 65,
                'role_id' => 3,
            ],
            [
                'permission_id' => 66,
                'role_id' => 3,
            ],
            [
                'permission_id' => 67,
                'role_id' => 3,
            ],
            [
                'permission_id' => 68,
                'role_id' => 3,
            ],
            [
                'permission_id' => 69,
                'role_id' => 3,
            ],
            [
                'permission_id' => 70,
                'role_id' => 3,
            ],
            [
                'permission_id' => 71,
                'role_id' => 3,
            ],
            [
                'permission_id' => 72,
                'role_id' => 3,
            ],
            [
                'permission_id' => 73,
                'role_id' => 3,
            ],
        ];
    }

    protected function permissions(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'view_role',
                'guard_name' => 'web',
            ],
            [
                'id' => 2,
                'name' => 'view_any_role',
                'guard_name' => 'web',
            ],
            [
                'id' => 3,
                'name' => 'create_role',
                'guard_name' => 'web',
            ],
            [
                'id' => 4,
                'name' => 'update_role',
                'guard_name' => 'web',
            ],
            [
                'id' => 5,
                'name' => 'delete_role',
                'guard_name' => 'web',
            ],
            [
                'id' => 6,
                'name' => 'delete_any_role',
                'guard_name' => 'web',
            ],
            [
                'id' => 7,
                'name' => 'view_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 8,
                'name' => 'view_any_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 9,
                'name' => 'create_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 10,
                'name' => 'update_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 11,
                'name' => 'restore_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 12,
                'name' => 'restore_any_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 13,
                'name' => 'replicate_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 14,
                'name' => 'reorder_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 15,
                'name' => 'delete_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 16,
                'name' => 'delete_any_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 17,
                'name' => 'force_delete_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 18,
                'name' => 'force_delete_any_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 19,
                'name' => 'view_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 20,
                'name' => 'view_any_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 21,
                'name' => 'create_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 22,
                'name' => 'update_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 23,
                'name' => 'restore_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 24,
                'name' => 'restore_any_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 25,
                'name' => 'replicate_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 26,
                'name' => 'reorder_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 27,
                'name' => 'delete_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 28,
                'name' => 'delete_any_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 29,
                'name' => 'force_delete_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 30,
                'name' => 'force_delete_any_hospital::admission',
                'guard_name' => 'web',
            ],
            [
                'id' => 31,
                'name' => 'view_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 32,
                'name' => 'view_any_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 33,
                'name' => 'create_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 34,
                'name' => 'update_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 35,
                'name' => 'restore_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 36,
                'name' => 'restore_any_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 37,
                'name' => 'replicate_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 38,
                'name' => 'reorder_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 39,
                'name' => 'delete_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 40,
                'name' => 'delete_any_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 41,
                'name' => 'force_delete_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 42,
                'name' => 'force_delete_any_medicine',
                'guard_name' => 'web',
            ],
            [
                'id' => 43,
                'name' => 'view_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 44,
                'name' => 'view_any_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 45,
                'name' => 'create_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 46,
                'name' => 'update_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 47,
                'name' => 'restore_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 48,
                'name' => 'restore_any_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 49,
                'name' => 'replicate_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 50,
                'name' => 'reorder_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 51,
                'name' => 'delete_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 52,
                'name' => 'delete_any_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 53,
                'name' => 'force_delete_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 54,
                'name' => 'force_delete_any_patient',
                'guard_name' => 'web',
            ],
            [
                'id' => 55,
                'name' => 'view_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 56,
                'name' => 'view_any_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 57,
                'name' => 'create_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 58,
                'name' => 'update_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 59,
                'name' => 'restore_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 60,
                'name' => 'restore_any_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 61,
                'name' => 'replicate_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 62,
                'name' => 'reorder_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 63,
                'name' => 'delete_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 64,
                'name' => 'delete_any_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 65,
                'name' => 'force_delete_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 66,
                'name' => 'force_delete_any_user',
                'guard_name' => 'web',
            ],
            [
                'id' => 67,
                'name' => 'add_management_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 68,
                'name' => 'add_diagnosis_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 69,
                'name' => 'add_chief_complaint_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 70,
                'name' => 'add_prescription_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 71,
                'name' => 'add_test_results_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 72,
                'name' => 'edit_as_doctor_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 73,
                'name' => 'add_followup_schedule_consultation',
                'guard_name' => 'web',
            ],
            [
                'id' => 74,
                'name' => 'widget_MostPrescribedDrugs',
                'guard_name' => 'web',
            ],
            [
                'id' => 75,
                'name' => 'widget_ConsultationChart',
                'guard_name' => 'web',
            ],
        ];
    }
}
