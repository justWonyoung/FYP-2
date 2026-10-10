<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\Expense;
use App\Models\Production;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_report_summarizes_current_operational_data(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Analytics Customer']);
        $this->order($customer, 'CO-ANALYTICS-1', 'completed', 1500);
        $this->order($customer, 'CO-ANALYTICS-2', 'pending', 800);
        $this->order($customer, 'CO-ANALYTICS-3', 'production', 900);
        $this->order($customer, 'CO-ANALYTICS-4', 'cancelled', 300);
        Expense::query()->create([
            'expense_category' => 'Utilities', 'description' => 'Analytics expense',
            'amount' => 200, 'expense_date' => '2026-10-10',
        ]);
        Production::query()->create([
            'batch_number' => 'BATCH-ANALYTICS-1', 'customer_order_no' => 'CO-ANALYTICS-1',
            'product_name' => 'Analytics Product', 'planned_output' => 10,
            'actual_output' => 0, 'unit' => 'pcs', 'status' => 'in_progress',
            'start_date' => '2026-10-10',
        ]);

        $this->actingAs($this->user('admin'))
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertViewHas('totalOrders', 4)
            ->assertViewHas('completedOrderRevenue', 1500.0)
            ->assertViewHas('pendingPurchaseRequests', 0)
            ->assertViewHas('inProgressProduction', 1)
            ->assertViewHas('netProfit', 1300.0);
    }

    public function test_staff_cannot_access_admin_analytics(): void
    {
        $this->actingAs($this->user('staff'))
            ->get(route('admin.reports'))
            ->assertForbidden();
    }

    private function order(Customer $customer, string $orderNo, string $status, float $total): CustomerOrder
    {
        return CustomerOrder::query()->create([
            'customer_id' => $customer->id, 'order_no' => $orderNo,
            'order_date' => '2026-10-10', 'status' => $status, 'total_amount' => $total,
        ]);
    }

    private function user(string $role): User
    {
        return User::query()->create([
            'full_name' => ucfirst($role).' User', 'username' => $role.'_'.fake()->unique()->numerify('####'),
            'email' => fake()->unique()->safeEmail(), 'password' => 'password',
            'role' => $role, 'status' => 'active',
        ]);
    }
}
