<?php

declare(strict_types=1);

namespace Modules\Authentication\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authentication\Enums\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Seeds every permission the application understands and attaches them to
 * the default roles. Permission names follow the `{module}.{action}` pattern.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * @return array<string, string[]>
     */
    public static function permissions(): array
    {
        $crud = static fn (string $module): array => [
            $module . '.view',
            $module . '.create',
            $module . '.update',
            $module . '.delete',
        ];

        return [
            ...$crud('patients'),
            ...$crud('appointments'),
            ...$crud('queue'),
            ...$crud('visits'),
            'visits.complete',
            ...$crud('vitals'),
            ...$crud('diagnoses'),
            ...$crud('prescriptions'),
            'prescriptions.print',
            ...$crud('medicines'),
            ...$crud('lab_tests'),
            'lab_tests.manage_results',
            ...$crud('documents'),
            'documents.download',
            ...$crud('medical_records'),
            ...$crud('billing'),
            ...$crud('payments'),
            ...$crud('expenses'),
            ...$crud('staff'),
            'reports.view',
            'audit_logs.view',
            'settings.view',
            'settings.update',
            'notifications.view',
            'notifications.send',
            'dashboard.view',
            'patients.view_history',
        ];
    }

    /**
     * @return array<string, string[]>
     */
    public static function rolePermissions(): array
    {
        $all = self::permissions();

        return [
            Role::SuperAdmin->value => $all,
            Role::ClinicAdmin->value => $all,
            Role::Doctor->value => [
                'dashboard.view',
                'patients.view',
                'patients.create',
                'patients.update',
                'patients.view_history',
                'appointments.view',
                'appointments.create',
                'appointments.update',
                'queue.view',
                'queue.create',
                'visits.view',
                'visits.create',
                'visits.update',
                'visits.complete',
                'vitals.view',
                'vitals.create',
                'vitals.update',
                'diagnoses.view',
                'diagnoses.create',
                'diagnoses.update',
                'diagnoses.delete',
                'prescriptions.view',
                'prescriptions.create',
                'prescriptions.update',
                'prescriptions.delete',
                'prescriptions.print',
                'medicines.view',
                'lab_tests.view',
                'lab_tests.create',
                'lab_tests.update',
                'documents.view',
                'documents.create',
                'documents.download',
                'medical_records.view',
                'medical_records.create',
                'medical_records.update',
                'notifications.view',
            ],
            Role::Receptionist->value => [
                'dashboard.view',
                'patients.view',
                'patients.create',
                'patients.update',
                'patients.view_history',
                'appointments.view',
                'appointments.create',
                'appointments.update',
                'appointments.delete',
                'queue.view',
                'queue.create',
                'queue.update',
                'billing.view',
                'billing.create',
                'payments.view',
                'payments.create',
            ],
            Role::Nurse->value => [
                'dashboard.view',
                'patients.view',
                'appointments.view',
                'queue.view',
                'queue.update',
                'visits.view',
                'visits.create',
                'visits.update',
                'vitals.view',
                'vitals.create',
                'vitals.update',
            ],
            Role::Pharmacist->value => [
                'dashboard.view',
                'medicines.view',
                'medicines.create',
                'medicines.update',
                'prescriptions.view',
                'prescriptions.update',
            ],
            Role::Accountant->value => [
                'dashboard.view',
                'billing.view',
                'billing.create',
                'billing.update',
                'payments.view',
                'payments.create',
                'payments.delete',
                'expenses.view',
                'expenses.create',
                'expenses.update',
                'reports.view',
            ],
            Role::LabTechnician->value => [
                'dashboard.view',
                'lab_tests.view',
                'lab_tests.update',
                'lab_tests.manage_results',
                'documents.view',
                'documents.download',
            ],
        ];
    }

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = self::permissions();

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::rolePermissions() as $role => $permissionsForRole) {
            $roleModel = SpatieRole::findOrCreate($role, 'web');
            $roleModel->syncPermissions($permissionsForRole);
        }
    }
}
