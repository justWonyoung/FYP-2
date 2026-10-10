<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_a_purchase_request_using_an_active_customer_order(): void
    {
        $staff = $this->createUser('staff');
        $order = $this->createOrder('CO-REAL-001', 'pending');

        $this->actingAs($staff)->get(route('staff.pr.create'))
            ->assertOk()->assertSee('CO-REAL-001 - Integration Customer');

        $this->actingAs($staff)->post(route('staff.pr.store'), [
            'customer_order_no' => $order->order_no,
            'supplier_name' => 'Integration Supplier',
            'material_item' => 'Shea Butter',
            'quantity' => 5,
            'unit' => 'kg',
            'estimated_cost' => 100,
        ])->assertRedirect(route('staff.dashboard'));

        $this->assertDatabaseHas('purchase_requests', [
            'customer_order_no' => 'CO-REAL-001',
            'request_no' => 'PR-001',
        ]);
    }

    public function test_nonexistent_customer_order_is_rejected(): void
    {
        $staff = $this->createUser('staff');

        $this->actingAs($staff)->from(route('staff.pr.create'))
            ->post(route('staff.pr.store'), [
                'customer_order_no' => 'CO-NOT-FOUND',
                'supplier_name' => 'Integration Supplier',
                'material_item' => 'Shea Butter',
                'quantity' => 5,
                'unit' => 'kg',
                'estimated_cost' => 100,
            ])->assertRedirect(route('staff.pr.create'))
            ->assertSessionHasErrors('customer_order_no');

        $this->assertDatabaseCount('purchase_requests', 0);
    }

    public function test_cancelled_customer_orders_are_not_selectable_or_accepted(): void
    {
        $staff = $this->createUser('staff');
        $cancelled = $this->createOrder('CO-CANCELLED-001', 'cancelled');

        $this->actingAs($staff)->get(route('staff.pr.create'))
            ->assertOk()->assertDontSee('CO-CANCELLED-001');

        $this->actingAs($staff)->from(route('staff.pr.create'))
            ->post(route('staff.pr.store'), [
                'customer_order_no' => $cancelled->order_no,
                'supplier_name' => 'Integration Supplier',
                'material_item' => 'Shea Butter',
                'quantity' => 5,
                'unit' => 'kg',
                'estimated_cost' => 100,
            ])->assertRedirect(route('staff.pr.create'))
            ->assertSessionHasErrors('customer_order_no');
    }

    private function createOrder(string $orderNo, string $status): CustomerOrder
    {
        $customer = Customer::query()->create(['customer_name' => 'Integration Customer']);

        return CustomerOrder::query()->create([
            'customer_id' => $customer->id,
            'order_no' => $orderNo,
            'order_date' => '2026-10-10',
            'status' => $status,
            'total_amount' => 250,
        ]);
    }

    private function createUser(string $role): User
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
