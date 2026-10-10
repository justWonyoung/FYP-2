<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\Production;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_update_a_customer_order_with_items(): void
    {
        $staff = $this->createUser('staff');
        $customer = Customer::query()->create(['customer_name' => 'Siti Enterprise']);

        $this->actingAs($staff)
            ->post(route('staff.orders.store'), [
                'customer_id' => $customer->id,
                'order_date' => '2026-10-10',
                'status' => 'pending',
                'items' => [
                    ['product_name' => 'Body Lotion 500ml', 'quantity' => 200, 'price' => 12.50],
                    ['product_name' => 'Face Cream 50g', 'quantity' => 100, 'price' => 15.00],
                ],
            ])
            ->assertRedirect(route('staff.orders.index'))
            ->assertSessionHas('success', 'Customer order CO-001 created successfully.');

        $this->assertDatabaseHas('customer_orders', [
            'customer_id' => $customer->id,
            'order_no' => 'CO-001',
            'status' => 'pending',
            'total_amount' => 4000,
        ]);

        $order = CustomerOrder::query()->firstOrFail();

        $this->actingAs($staff)
            ->put(route('staff.orders.update', $order), [
                'customer_id' => $customer->id,
                'order_date' => '2026-10-11',
                'status' => 'production',
                'items' => [
                    ['product_name' => 'Body Lotion 500ml', 'quantity' => 250, 'price' => 12.50],
                ],
            ])
            ->assertRedirect(route('staff.orders.show', $order))
            ->assertSessionHas('success', 'Customer order updated successfully.');

        $this->assertDatabaseHas('customer_orders', ['id' => $order->id, 'status' => 'production', 'total_amount' => 3125]);
        $this->assertDatabaseCount('customer_order_items', 1);
        $this->assertDatabaseHas('customer_order_items', ['customer_order_id' => $order->id, 'quantity' => 250, 'subtotal' => 3125]);
    }

    public function test_order_validation_requires_customer_and_item_details(): void
    {
        $response = $this->actingAs($this->createUser('staff'))
            ->from(route('staff.orders.create'))
            ->post(route('staff.orders.store'), []);

        $response->assertRedirect(route('staff.orders.create'))
            ->assertSessionHasErrors(['customer_id', 'order_date', 'items'])
            ->assertSessionDoesntHaveErrors('status');
    }

    public function test_new_customer_orders_always_start_as_pending(): void
    {
        $staff = $this->createUser('staff');
        $customer = Customer::query()->create(['customer_name' => 'Pending Customer']);

        $this->actingAs($staff)
            ->post(route('staff.orders.store'), [
                'customer_id' => $customer->id,
                'order_date' => '2026-10-10',
                'status' => 'completed',
                'items' => [
                    ['product_name' => 'Pending Product', 'quantity' => 1, 'price' => 10],
                ],
            ])
            ->assertRedirect(route('staff.orders.index'));

        $this->assertDatabaseHas('customer_orders', [
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
    }

    public function test_create_form_hides_status_control_and_shows_pending(): void
    {
        $this->actingAs($this->createUser('staff'))
            ->get(route('staff.orders.create'))
            ->assertOk()
            ->assertSee('New customer orders always start as Pending.')
            ->assertDontSee('name="status"');
    }

    public function test_finance_user_is_forbidden_from_customer_order_management(): void
    {
        $response = $this->actingAs($this->createUser('finance'))
            ->get(route('staff.orders.index'));

        $response->assertForbidden();
    }

    public function test_staff_can_view_customer_order_details_with_item_totals(): void
    {
        $customer = Customer::query()->create([
            'customer_name' => 'Traceable Customer',
            'phone' => '0123456789',
            'email' => 'traceable@example.test',
            'address' => 'Testing Address',
        ]);
        $order = $this->createOrder($customer, 'CO-DETAIL', 'pending');
        $order->items()->create([
            'product_name' => 'Traceable Product',
            'quantity' => 3,
            'price' => 12.50,
            'subtotal' => 37.50,
        ]);

        $this->actingAs($this->createUser('staff'))
            ->get(route('staff.orders.show', $order))
            ->assertOk()
            ->assertSee('CO-DETAIL')
            ->assertSee('Traceable Customer')
            ->assertSee('Traceable Product')
            ->assertSee('RM 37.50');
    }

    public function test_order_list_filters_by_order_number_customer_status_date_and_paginates(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Filter Customer']);
        $otherCustomer = Customer::query()->create(['customer_name' => 'Other Customer']);

        foreach (range(1, 11) as $number) {
            $this->createOrder(
                $number === 11 ? $otherCustomer : $customer,
                'CO-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                $number === 11 ? 'completed' : 'pending',
                $number === 1 ? '2026-10-01' : '2026-10-10'
            );
        }

        $staff = $this->createUser('staff');

        $this->actingAs($staff)
            ->get(route('staff.orders.index', ['search' => 'CO-005']))
            ->assertOk()
            ->assertSee('CO-005')
            ->assertDontSee('CO-006');

        $this->actingAs($staff)
            ->get(route('staff.orders.index', ['search' => 'Other Customer']))
            ->assertOk()
            ->assertSee('CO-011')
            ->assertDontSee('CO-010');

        $this->actingAs($staff)
            ->get(route('staff.orders.index', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('CO-011')
            ->assertDontSee('CO-010');

        $this->actingAs($staff)
            ->get(route('staff.orders.index', ['date_from' => '2026-10-01', 'date_to' => '2026-10-01']))
            ->assertOk()
            ->assertSee('CO-001')
            ->assertDontSee('CO-002');

        $this->actingAs($staff)
            ->get(route('staff.orders.index', ['status' => 'pending', 'page' => 2]))
            ->assertOk()
            ->assertSee('CO-001')
            ->assertSee('status=pending');
    }

    public function test_staff_can_change_an_order_status_when_no_active_operations_are_linked(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Status Customer']);
        $order = $this->createOrder($customer, 'CO-STATUS', 'pending');

        $this->actingAs($this->createUser('staff'))
            ->put(route('staff.orders.update', $order), $this->orderPayload($customer, 'production'))
            ->assertRedirect(route('staff.orders.show', $order))
            ->assertSessionHas('success', 'Customer order updated successfully.');

        $this->assertDatabaseHas('customer_orders', ['id' => $order->id, 'status' => 'production']);
    }

    public function test_cancellation_is_blocked_when_active_purchase_or_production_operations_are_linked(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Protected Customer']);
        $purchaseOrder = $this->createOrder($customer, 'CO-PURCHASE', 'pending');
        $productionOrder = $this->createOrder($customer, 'CO-PRODUCTION', 'pending');

        PurchaseRequest::query()->create([
            'request_no' => 'PR-PROTECTED',
            'customer_order_no' => $purchaseOrder->order_no,
            'supplier_name' => 'Protected Supplier',
            'material_item' => 'Protected Material',
            'quantity' => 1,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'pcs',
            'estimated_cost' => 10,
            'finance_status' => 'pending',
            'approval_status' => 'pending',
        ]);
        Production::query()->create([
            'batch_number' => 'BATCH-PROTECTED',
            'customer_order_no' => $productionOrder->order_no,
            'product_name' => 'Protected Product',
            'planned_output' => 10,
            'actual_output' => 0,
            'unit' => 'pcs',
            'status' => 'in_progress',
            'start_date' => '2026-10-10',
        ]);

        $staff = $this->createUser('staff');

        foreach ([$purchaseOrder, $productionOrder] as $order) {
            $this->actingAs($staff)
                ->from(route('staff.orders.edit', $order))
                ->put(route('staff.orders.update', $order), $this->orderPayload($customer, 'cancelled'))
                ->assertRedirect(route('staff.orders.edit', $order))
                ->assertSessionHasErrors('status');

            $this->assertDatabaseHas('customer_orders', ['id' => $order->id, 'status' => 'pending']);
        }
    }

    public function test_completed_orders_cannot_be_reverted_and_cancelled_orders_cannot_be_edited(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Lifecycle Customer']);
        $completedOrder = $this->createOrder($customer, 'CO-COMPLETE', 'completed');
        $cancelledOrder = $this->createOrder($customer, 'CO-CANCELLED', 'cancelled');
        $staff = $this->createUser('staff');

        $this->actingAs($staff)
            ->from(route('staff.orders.edit', $completedOrder))
            ->put(route('staff.orders.update', $completedOrder), $this->orderPayload($customer, 'pending'))
            ->assertRedirect(route('staff.orders.edit', $completedOrder))
            ->assertSessionHasErrors('status');

        $this->actingAs($staff)
            ->get(route('staff.orders.edit', $cancelledOrder))
            ->assertRedirect(route('staff.orders.show', $cancelledOrder))
            ->assertSessionHas('error', 'Cancelled customer orders cannot be edited.');

        $this->actingAs($staff)
            ->put(route('staff.orders.update', $cancelledOrder), $this->orderPayload($customer, 'pending'))
            ->assertRedirect(route('staff.orders.show', $cancelledOrder))
            ->assertSessionHas('error', 'Cancelled customer orders cannot be edited.');
    }

    private function createOrder(Customer $customer, string $orderNo, string $status, string $orderDate = '2026-10-10'): CustomerOrder
    {
        return CustomerOrder::query()->create([
            'customer_id' => $customer->id,
            'order_no' => $orderNo,
            'order_date' => $orderDate,
            'status' => $status,
            'total_amount' => 37.50,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(Customer $customer, string $status): array
    {
        return [
            'customer_id' => $customer->id,
            'order_date' => '2026-10-10',
            'status' => $status,
            'items' => [
                ['product_name' => 'Lifecycle Product', 'quantity' => 3, 'price' => 12.50],
            ],
        ];
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
