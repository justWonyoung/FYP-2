<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_and_customer_creation_are_logged(): void
    {
        $staff = $this->user('staff', 'activity-staff@example.test');

        $this->post(route('login'), [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertRedirect('/home');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $staff->getKey(),
            'action' => 'logged_in',
            'module' => 'Authentication',
        ]);

        $this->actingAs($staff)->post(route('staff.customers.store'), [
            'customer_name' => 'Audit Customer',
            'email' => 'audit-customer@example.test',
        ])->assertRedirect(route('staff.customers.index'));

        $customer = Customer::query()->where('customer_name', 'Audit Customer')->firstOrFail();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $staff->getKey(),
            'action' => 'created',
            'module' => 'Customer',
            'description' => "Created customer {$customer->customer_name}.",
        ]);
    }

    public function test_admin_can_view_activity_logs_and_staff_and_finance_cannot(): void
    {
        $admin = $this->user('admin', 'activity-admin@example.test');
        $staff = $this->user('staff', 'activity-staff@example.test');
        $finance = $this->user('finance', 'activity-finance@example.test');
        ActivityLog::record($admin, 'created', 'Supplier', 'Created supplier Audit Supplier.');

        $this->actingAs($admin)->get(route('admin.activity-logs.index'))
            ->assertOk()
            ->assertSee('Audit Supplier');

        $this->actingAs($staff)->get(route('admin.activity-logs.index'))->assertForbidden();
        $this->actingAs($finance)->get(route('admin.activity-logs.index'))->assertForbidden();
    }

    private function user(string $role, string $email): User
    {
        return User::query()->create([
            'full_name' => ucfirst($role).' User',
            'username' => $role.'_'.fake()->unique()->numerify('####'),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
