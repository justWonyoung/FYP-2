<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\Production;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionCustomerOrderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_production_from_an_order_item(): void
    {
        [$order, $item] = $this->orderWithItem('CO-PROD-001', 'Body Lotion');

        $this->actingAs($this->user('staff'))
            ->get(route('production.create'))
            ->assertOk()
            ->assertSee('CO-PROD-001 - Production Customer')
            ->assertSee('Products are limited to the selected customer order.');

        $this->actingAs($this->user('staff'))
            ->post(route('production.store'), [
                'batch_number' => 'BATCH-TEST-001',
                'customer_order_no' => $order->order_no,
                'product_name' => $item->product_name,
                'planned_output' => 10,
                'unit' => 'pcs',
                'start_date' => '2026-10-10',
            ])
            ->assertRedirect(route('production.index'));

        $this->assertDatabaseHas('productions', [
            'batch_number' => 'BATCH-TEST-001',
            'customer_order_no' => $order->order_no,
            'product_name' => $item->product_name,
        ]);
    }

    public function test_invalid_order_and_product_are_rejected(): void
    {
        [$order] = $this->orderWithItem('CO-PROD-002', 'Face Cream');
        $staff = $this->user('staff');
        $payload = [
            'batch_number' => 'BATCH-TEST-002',
            'customer_order_no' => $order->order_no,
            'product_name' => 'Unrelated Product',
            'planned_output' => 10,
            'unit' => 'pcs',
            'start_date' => '2026-10-10',
        ];

        $this->actingAs($staff)->from(route('production.create'))
            ->post(route('production.store'), $payload)
            ->assertRedirect(route('production.create'))
            ->assertSessionHasErrors('product_name');

        $payload['batch_number'] = 'BATCH-TEST-003';
        $payload['customer_order_no'] = 'CO-NOT-FOUND';
        $payload['product_name'] = 'Face Cream';
        $this->actingAs($staff)->from(route('production.create'))
            ->post(route('production.store'), $payload)
            ->assertRedirect(route('production.create'))
            ->assertSessionHasErrors('customer_order_no');
    }

    public function test_cancelled_orders_are_not_selectable_or_accepted(): void
    {
        [$order, $item] = $this->orderWithItem('CO-PROD-CANCELLED', 'Cancelled Product', 'cancelled');
        $staff = $this->user('staff');

        $this->actingAs($staff)->get(route('production.create'))
            ->assertOk()
            ->assertDontSee($order->order_no);

        $this->actingAs($staff)->from(route('production.create'))
            ->post(route('production.store'), [
                'batch_number' => 'BATCH-TEST-004',
                'customer_order_no' => $order->order_no,
                'product_name' => $item->product_name,
                'planned_output' => 10,
                'unit' => 'pcs',
                'start_date' => '2026-10-10',
            ])
            ->assertRedirect(route('production.create'))
            ->assertSessionHasErrors('customer_order_no');
    }

    private function orderWithItem(string $orderNo, string $product, string $status = 'pending'): array
    {
        $customer = Customer::query()->create(['customer_name' => 'Production Customer']);
        $order = CustomerOrder::query()->create([
            'customer_id' => $customer->id,
            'order_no' => $orderNo,
            'order_date' => '2026-10-10',
            'status' => $status,
            'total_amount' => 100,
        ]);
        $item = $order->items()->create([
            'product_name' => $product,
            'quantity' => 10,
            'price' => 10,
            'subtotal' => 100,
        ]);

        return [$order, $item];
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
