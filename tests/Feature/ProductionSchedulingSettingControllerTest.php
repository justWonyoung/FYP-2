<?php

namespace Tests\Feature;

use App\Models\ProductionSchedulingSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSchedulingSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_the_production_capacity_page(): void
    {
        $this->actingAs($this->user('staff'))
            ->get(route('production.capacity.index'))
            ->assertOk()
            ->assertSee('Production Capacity')
            ->assertSee('No active daily capacity has been configured.');
    }

    public function test_staff_can_create_the_first_active_capacity_setting(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)
            ->put(route('production.capacity.update'), [
                'daily_capacity' => 800,
                'unit' => 'pcs',
            ])
            ->assertRedirect(route('production.capacity.index'));

        $setting = ProductionSchedulingSetting::query()->sole();

        $this->assertSame('800.00', $setting->daily_capacity);
        $this->assertSame('pcs', $setting->unit);
        $this->assertTrue($setting->is_active);
    }

    public function test_staff_updates_the_existing_active_capacity_setting(): void
    {
        $setting = ProductionSchedulingSetting::query()->create([
            'daily_capacity' => 800,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $this->actingAs($this->user('staff'))
            ->put(route('production.capacity.update'), [
                'daily_capacity' => 950.50,
                'unit' => 'bottles',
            ])
            ->assertRedirect(route('production.capacity.index'));

        $this->assertSame(1, ProductionSchedulingSetting::query()->count());

        $setting->refresh();

        $this->assertSame('950.50', $setting->daily_capacity);
        $this->assertSame('bottles', $setting->unit);
        $this->assertTrue($setting->is_active);
    }

    public function test_staff_cannot_save_zero_capacity(): void
    {
        $this->actingAs($this->user('staff'))
            ->from(route('production.capacity.index'))
            ->put(route('production.capacity.update'), [
                'daily_capacity' => 0,
                'unit' => 'pcs',
            ])
            ->assertRedirect(route('production.capacity.index'))
            ->assertSessionHasErrors('daily_capacity');

        $this->assertDatabaseCount('production_scheduling_settings', 0);
    }

    public function test_staff_cannot_save_negative_capacity(): void
    {
        $this->actingAs($this->user('staff'))
            ->from(route('production.capacity.index'))
            ->put(route('production.capacity.update'), [
                'daily_capacity' => -1,
                'unit' => 'pcs',
            ])
            ->assertRedirect(route('production.capacity.index'))
            ->assertSessionHasErrors('daily_capacity');

        $this->assertDatabaseCount('production_scheduling_settings', 0);
    }

    public function test_admin_can_view_capacity_but_cannot_update_it(): void
    {
        ProductionSchedulingSetting::query()->create([
            'daily_capacity' => 800,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $admin = $this->user('admin');

        $this->actingAs($admin)
            ->get(route('production.capacity.index'))
            ->assertOk()
            ->assertSee('800.00 pcs')
            ->assertSee('read-only for Admin users');

        $this->actingAs($admin)
            ->put(route('production.capacity.update'), [
                'daily_capacity' => 900,
                'unit' => 'pcs',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('production_scheduling_settings', [
            'daily_capacity' => 800,
            'unit' => 'pcs',
            'is_active' => true,
        ]);
    }

    public function test_finance_cannot_view_or_update_capacity(): void
    {
        $finance = $this->user('finance');

        $this->actingAs($finance)
            ->get(route('production.capacity.index'))
            ->assertForbidden();

        $this->actingAs($finance)
            ->put(route('production.capacity.update'), [
                'daily_capacity' => 800,
                'unit' => 'pcs',
            ])
            ->assertForbidden();
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
