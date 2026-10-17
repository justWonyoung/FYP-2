<?php

namespace Tests\Feature;

use App\Models\ProductionSchedulingSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSchedulingRecommendationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_the_read_only_scheduling_recommendation(): void
    {
        ProductionSchedulingSetting::query()->create([
            'daily_capacity' => 800,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $this->actingAs($this->user('staff'))
            ->get(route('production.schedule-recommendations.index'))
            ->assertOk()
            ->assertSee('Production Scheduling Recommendation')
            ->assertSee('Recommendation only.')
            ->assertSee('Material Planning: Not evaluated');
    }

    public function test_admin_can_view_the_read_only_scheduling_recommendation(): void
    {
        $this->actingAs($this->user('admin'))
            ->get(route('production.schedule-recommendations.index'))
            ->assertOk()
            ->assertSee('Production Scheduling Recommendation');
    }

    public function test_finance_cannot_view_the_scheduling_recommendation(): void
    {
        $this->actingAs($this->user('finance'))
            ->get(route('production.schedule-recommendations.index'))
            ->assertForbidden();
    }

    public function test_page_displays_a_capacity_configuration_warning_when_no_active_setting_exists(): void
    {
        $this->actingAs($this->user('staff'))
            ->get(route('production.schedule-recommendations.index'))
            ->assertOk()
            ->assertSee('No active production capacity is configured.')
            ->assertSee('Open Production Capacity');
    }

    private function user(string $role): User
    {
        return User::query()->create([
            'full_name' => ucfirst($role).' User',
            'username' => $role.'_'.fake()->unique()->numerify('####'),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
