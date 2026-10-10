<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_users_cannot_log_in_but_active_users_can(): void
    {
        User::query()->create([
            'full_name' => 'Inactive User', 'username' => 'inactive-user',
            'email' => 'inactive@example.test', 'password' => Hash::make('password'),
            'role' => 'staff', 'status' => 'inactive',
        ]);
        User::query()->create([
            'full_name' => 'Active User', 'username' => 'active-user',
            'email' => 'active@example.test', 'password' => Hash::make('password'),
            'role' => 'staff', 'status' => 'active',
        ]);

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'inactive@example.test', 'password' => 'password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->post(route('login'), ['email' => 'active@example.test', 'password' => 'password'])
            ->assertRedirect('/home');
    }

    public function test_development_email_and_public_registration_routes_are_unavailable(): void
    {
        $this->get('/test-email')->assertNotFound();
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }

    public function test_role_access_boundaries_remain_enforced(): void
    {
        $staff = $this->user('staff');
        $finance = $this->user('finance');

        $this->actingAs($staff)->get(route('admin.reports'))->assertForbidden();
        $this->actingAs($staff)->get(route('finance.dashboard'))->assertForbidden();
        $this->actingAs($finance)->get(route('staff.suppliers.index'))->assertForbidden();
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
