<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialCustomerOrderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_orders_are_revenue_and_profit_is_revenue_less_expenses(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Finance Customer']);
        $this->createOrder($customer, 'CO-FIN-COMPLETE', 'completed', 1250);
        $this->createOrder($customer, 'CO-FIN-PENDING', 'pending', 500);
        $this->createOrder($customer, 'CO-FIN-PRODUCTION', 'production', 600);
        $this->createOrder($customer, 'CO-FIN-CANCELLED', 'cancelled', 700);
        Expense::query()->create([
            'expense_category' => 'Utilities',
            'description' => 'Test expense',
            'amount' => 250,
            'expense_date' => '2026-10-10',
        ]);

        $finance = $this->user('finance');

        $this->actingAs($finance)
            ->get(route('finance.dashboard'))
            ->assertOk()
            ->assertViewHas('totalRevenue', 1250.0)
            ->assertViewHas('totalExpense', 250.0)
            ->assertViewHas('netProfit', 1000.0);

        $this->actingAs($finance)
            ->get(route('finance.report'))
            ->assertOk()
            ->assertViewHas('totalRevenue', 1250.0)
            ->assertViewHas('totalExpenses', 250.0)
            ->assertViewHas('netProfit', 1000.0);
    }

    public function test_finance_access_rules_are_preserved(): void
    {
        $this->actingAs($this->user('finance'))
            ->get(route('finance.dashboard'))
            ->assertOk();

        $this->actingAs($this->user('staff'))
            ->get(route('finance.dashboard'))
            ->assertForbidden();
    }

    public function test_finance_can_edit_expenses_but_other_roles_cannot(): void
    {
        $expense = Expense::query()->create([
            'expense_category' => 'Packaging',
            'description' => 'Original expense',
            'amount' => 100,
            'expense_date' => '2026-10-01',
        ]);

        $this->actingAs($this->user('finance'))
            ->put(route('finance.expenses.update', $expense), [
                'expense_category' => 'Logistics',
                'description' => 'Updated expense',
                'amount' => 125.50,
                'expense_date' => '2026-10-10',
            ])
            ->assertRedirect(route('finance.expenses'));

        $this->assertDatabaseHas('expenses', [
            'expense_id' => $expense->getKey(),
            'expense_category' => 'Logistics',
            'description' => 'Updated expense',
            'amount' => 125.50,
        ]);

        $this->actingAs($this->user('staff'))
            ->get(route('finance.expenses.edit', $expense))
            ->assertForbidden();
        $this->actingAs($this->user('admin'))
            ->get(route('finance.expenses.edit', $expense))
            ->assertForbidden();
    }

    public function test_financial_report_filters_revenue_and_expenses_by_period(): void
    {
        $customer = Customer::query()->create(['customer_name' => 'Period Customer']);
        $inside = $this->createOrder($customer, 'CO-PERIOD-IN', 'completed', 300);
        $inside->update(['order_date' => '2026-10-10']);
        $outside = $this->createOrder($customer, 'CO-PERIOD-OUT', 'completed', 900);
        $outside->update(['order_date' => '2026-09-01']);
        Expense::query()->create(['expense_category' => 'Packaging', 'description' => 'Inside', 'amount' => 40, 'expense_date' => '2026-10-10']);
        Expense::query()->create(['expense_category' => 'Packaging', 'description' => 'Outside', 'amount' => 60, 'expense_date' => '2026-09-01']);

        $this->actingAs($this->user('finance'))
            ->get(route('finance.report', ['date_from' => '2026-10-01', 'date_to' => '2026-10-31']))
            ->assertOk()
            ->assertViewHas('totalRevenue', 300.0)
            ->assertViewHas('totalExpenses', 40.0)
            ->assertViewHas('netProfit', 260.0);
    }

    private function createOrder(Customer $customer, string $orderNo, string $status, float $total): CustomerOrder
    {
        return CustomerOrder::query()->create([
            'customer_id' => $customer->id,
            'order_no' => $orderNo,
            'order_date' => '2026-10-10',
            'status' => $status,
            'total_amount' => $total,
        ]);
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
