<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DebugDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        (new \Database\Seeders\DatabaseSeeder())->run();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::where('email', 'admin@admin.com')->firstOrFail());
    }

    public function test_dashboard_debug(): void
    {
        $response = $this->get('/dashboard');

        $html = $response->getContent();

        file_put_contents('C:\Users\Omais\AppData\Local\Temp\opencode\dashboard.html', $html);
    }
}
