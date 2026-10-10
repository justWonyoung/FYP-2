<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAlignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_keeps_supervisory_pages_but_cannot_use_operational_or_finance_writes(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.reports'))->assertOk();
        $this->actingAs($admin)->get(route('admin.activity-logs.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.purchase.requests'))->assertOk();

        $this->actingAs($admin)->get(route('staff.customers.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('staff.orders.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('staff.suppliers.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('staff.pr.create'))->assertForbidden();
        $this->actingAs($admin)->get(route('inventory.receive'))->assertForbidden();
        $this->actingAs($admin)->get(route('production.create'))->assertForbidden();
        $this->actingAs($admin)->get(route('finance.expenses'))->assertForbidden();
        $this->actingAs($admin)->get(route('finance.purchase.show', 999999))->assertForbidden();
    }

    public function test_staff_keeps_operational_access_but_cannot_use_admin_or_finance_pages(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)->get(route('staff.customers.index'))->assertOk();
        $this->actingAs($staff)->get(route('staff.orders.index'))->assertOk();
        $this->actingAs($staff)->get(route('staff.suppliers.index'))->assertOk();
        $this->actingAs($staff)->get(route('staff.pr.create'))->assertOk();
        $this->actingAs($staff)->get(route('inventory.receive'))->assertOk();
        $this->actingAs($staff)->get(route('production.create'))->assertOk();

        $this->actingAs($staff)->get(route('admin.reports'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.activity-logs.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('finance.dashboard'))->assertForbidden();
    }

    public function test_finance_keeps_finance_access_but_cannot_use_operational_or_admin_pages(): void
    {
        $finance = $this->user('finance');

        $this->actingAs($finance)->get(route('finance.dashboard'))->assertOk();
        $this->actingAs($finance)->get(route('finance.expenses'))->assertOk();
        $this->actingAs($finance)->get(route('finance.report'))->assertOk();

        $this->actingAs($finance)->get(route('staff.customers.index'))->assertForbidden();
        $this->actingAs($finance)->get(route('staff.suppliers.index'))->assertForbidden();
        $this->actingAs($finance)->get(route('inventory.receive'))->assertForbidden();
        $this->actingAs($finance)->get(route('production.create'))->assertForbidden();
        $this->actingAs($finance)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($finance)->get(route('admin.purchase.requests'))->assertForbidden();
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
