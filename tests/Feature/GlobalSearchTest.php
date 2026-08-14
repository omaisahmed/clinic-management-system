<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Models\User;
use Modules\Patients\Models\Patient;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        (new DatabaseSeeder)->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::where('email', 'admin@admin.com')->firstOrFail());
    }

    public function test_search_page_renders_empty_state_without_term(): void
    {
        $this->get(route('search.index'))
            ->assertOk()
            ->assertSee('Enter a search term');
    }

    public function test_search_finds_patients_and_renders_results(): void
    {
        $patient = Patient::query()->firstOrFail();

        $this->get(route('search.index', ['q' => $patient->first_name]))
            ->assertOk()
            ->assertSee('Patients')
            ->assertSee($patient->full_name);
    }

    public function test_search_page_renders_no_results_state(): void
    {
        $this->get(route('search.index', ['q' => 'zzz-no-match-12345']))
            ->assertOk()
            ->assertSee('No results found');
    }
}
