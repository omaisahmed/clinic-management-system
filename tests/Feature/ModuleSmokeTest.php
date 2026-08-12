<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ModuleSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        (new \Database\Seeders\DatabaseSeeder())->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::where('email', 'admin@admin.com')->firstOrFail());
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function indexRoutes(): array
    {
        return [
            ['/dashboard', 'dashboard'],
            ['/patients', 'patients'],
            ['/appointments', 'appointments'],
            ['/queue', 'queue'],
            ['/visits', 'visits'],
            ['/medicines', 'medicines'],
            ['/prescriptions', 'prescriptions'],
            ['/lab-tests', 'lab tests'],
            ['/documents', 'documents'],
            ['/billing', 'billing'],
            ['/payments', 'payments'],
            ['/expenses', 'expenses'],
            ['/reports', 'reports'],
            ['/reports/revenue', 'reports revenue'],
            ['/reports/patients-report', 'reports patients'],
            ['/reports/inventory', 'reports inventory'],
            ['/audit-logs', 'audit logs'],
        ];
    }

    #[DataProvider('indexRoutes')]
    public function test_module_index_renders(string $path, string $name): void
    {
        $response = $this->get($path);

        $response->assertStatus(200);
    }

    public function test_patient_profile_tabs_render(): void
    {
        $patient = \Modules\Patients\Models\Patient::query()->firstOrFail();

        foreach (['overview', 'medical', 'timeline', 'appointments', 'visits', 'prescriptions', 'lab-tests', 'documents', 'billing'] as $tab) {
            $response = $this->get(route('patients.show', [$patient, 'tab' => $tab]));

            $response->assertStatus(200);
        }
    }

    public function test_patient_contact_create_and_destroy(): void
    {
        $patient = \Modules\Patients\Models\Patient::query()->firstOrFail();

        $response = $this->post(route('patients.contacts.store', $patient), [
            'name' => 'Jane Doe',
            'phone' => '+1 555 0100',
            'relationship' => 'Sister',
        ]);

        $response->assertRedirect(route('patients.show', $patient));

        $contact = $patient->contacts()->where('name', 'Jane Doe')->firstOrFail();

        $this->delete(route('patients.contacts.destroy', [$patient, $contact]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseMissing('patient_contacts', ['id' => $contact->id]);
    }
}
