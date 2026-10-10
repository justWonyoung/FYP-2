<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_search_view_create_update_and_delete_an_unused_customer(): void
    {
        $staff = $this->createUser('staff');
        $customer = Customer::query()->create([
            'customer_name' => 'Searchable Customer',
            'phone' => '0111111111',
            'email' => 'searchable@example.test',
            'address' => 'Kuala Lumpur',
        ]);
        Customer::query()->create(['customer_name' => 'Other Customer']);

        $this->actingAs($staff)->get(route('staff.customers.index', ['search' => 'searchable@example.test']))
            ->assertOk()->assertSee('Searchable Customer')->assertDontSee('Other Customer');
        $this->actingAs($staff)->get(route('staff.customers.show', $customer))
            ->assertOk()->assertSee('searchable@example.test');

        $this->actingAs($staff)->post(route('staff.customers.store'), [
            'customer_name' => 'Created Customer', 'email' => 'created@example.test',
        ])->assertRedirect(route('staff.customers.index'));
        $created = Customer::query()->where('customer_name', 'Created Customer')->firstOrFail();

        $this->actingAs($staff)->put(route('staff.customers.update', $created), [
            'customer_name' => 'Updated Customer', 'email' => 'updated@example.test',
        ])->assertRedirect(route('staff.customers.index'));
        $this->assertDatabaseHas('customers', ['id' => $created->id, 'customer_name' => 'Updated Customer']);

        $this->actingAs($staff)->delete(route('staff.customers.destroy', $created))
            ->assertRedirect(route('staff.customers.index'))
            ->assertSessionHas('success', 'Customer deleted successfully.');
        $this->assertModelMissing($created);
    }

    public function test_customer_validation_and_changed_email_duplicate_protection_work(): void
    {
        $staff = $this->createUser('staff');
        $existing = Customer::query()->create(['customer_name' => 'Existing', 'email' => 'same@example.test']);
        $editable = Customer::query()->create(['customer_name' => 'Editable', 'email' => 'editable@example.test']);

        $this->actingAs($staff)->from(route('staff.customers.create'))
            ->post(route('staff.customers.store'), ['customer_name' => '', 'email' => 'invalid'])
            ->assertRedirect(route('staff.customers.create'))
            ->assertSessionHasErrors(['customer_name', 'email']);

        $this->actingAs($staff)->from(route('staff.customers.create'))
            ->post(route('staff.customers.store'), ['customer_name' => 'Duplicate', 'email' => 'same@example.test'])
            ->assertRedirect(route('staff.customers.create'))
            ->assertSessionHasErrors(['email']);

        $this->actingAs($staff)->from(route('staff.customers.edit', $editable))->put(route('staff.customers.update', $editable), [
            'customer_name' => 'Editable Updated', 'email' => 'same@example.test',
        ])->assertRedirect(route('staff.customers.edit', $editable))->assertSessionHasErrors(['email']);

        $this->actingAs($staff)->put(route('staff.customers.update', $existing), [
            'customer_name' => 'Existing Updated', 'email' => 'same@example.test',
        ])->assertRedirect(route('staff.customers.index'));
    }

    public function test_customer_detail_shows_related_orders_and_order_delete_is_blocked(): void
    {
        $staff = $this->createUser('staff');
        $customer = Customer::query()->create(['customer_name' => 'Ordered Customer']);
        $order = CustomerOrder::query()->create([
            'customer_id' => $customer->id, 'order_no' => 'CO-CUSTOMER-001',
            'order_date' => '2026-10-10', 'status' => 'pending', 'total_amount' => 125,
        ]);

        $this->actingAs($staff)->get(route('staff.customers.show', $customer))
            ->assertOk()->assertSee('CO-CUSTOMER-001')->assertSee('RM 125.00')->assertSee('View Order');
        $this->actingAs($staff)->delete(route('staff.customers.destroy', $customer))
            ->assertRedirect(route('staff.customers.index'))
            ->assertSessionHas('error', 'Customer cannot be deleted because it is referenced by existing customer orders.');
        $this->assertModelExists($customer);
        $this->assertModelExists($order);
    }

    public function test_finance_and_admin_are_forbidden_from_customer_management(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Protected Customer']);

        foreach (['index', 'create', 'show', 'edit'] as $action) {
            $url = match ($action) {
                'index' => route('staff.customers.index'),
                'create' => route('staff.customers.create'),
                'show' => route('staff.customers.show', $customer),
                'edit' => route('staff.customers.edit', $customer),
            };
            $this->actingAs($this->createUser('finance'))->get($url)->assertForbidden();
        }

        $this->actingAs($this->createUser('admin'))->get(route('staff.customers.index'))->assertForbidden();
    }

    private function createUser(string $role): User
    {
        return User::query()->create([
            'full_name' => ucfirst($role).' User', 'username' => $role.'_'.fake()->unique()->numerify('####'),
            'email' => fake()->unique()->safeEmail(), 'password' => 'password', 'role' => $role, 'status' => 'active',
        ]);
    }
}
