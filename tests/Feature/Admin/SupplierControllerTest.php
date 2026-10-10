<?php

namespace Tests\Feature\Admin;

use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_from_supplier_management(): void
    {
        $response = $this->get(route('staff.suppliers.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_finance_user_is_forbidden_from_admin_supplier_management(): void
    {
        $response = $this->actingAs($this->createUser('finance'))
            ->get(route('staff.suppliers.index'));

        $response->assertForbidden();
    }

    public function test_staff_can_create_update_and_delete_a_supplier(): void
    {
        $admin = $this->createUser('staff');

        $this->actingAs($admin)
            ->post(route('staff.suppliers.store'), [
                'supplier_name' => 'Azalea Chemical Sdn Bhd',
                'phone' => '03-1234 5678',
                'email' => 'contact@azalea.test',
                'address' => 'Shah Alam, Selangor',
            ])
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('success', 'Supplier added successfully.');

        $this->assertDatabaseHas('suppliers', [
            'supplier_name' => 'Azalea Chemical Sdn Bhd',
            'phone' => '03-1234 5678',
            'email' => 'contact@azalea.test',
            'address' => 'Shah Alam, Selangor',
        ]);

        $supplier = Supplier::query()->where('supplier_name', 'Azalea Chemical Sdn Bhd')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('staff.suppliers.update', $supplier), [
                'supplier_name' => 'Azalea Chemical Supplies Sdn Bhd',
                'phone' => '03-9876 5432',
                'email' => 'sales@azalea.test',
                'address' => 'Klang, Selangor',
            ])
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('success', 'Supplier updated successfully.');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'supplier_name' => 'Azalea Chemical Supplies Sdn Bhd',
            'phone' => '03-9876 5432',
            'email' => 'sales@azalea.test',
            'address' => 'Klang, Selangor',
        ]);

        $this->actingAs($admin)
            ->delete(route('staff.suppliers.destroy', $supplier))
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('success', 'Supplier deleted successfully.');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_supplier_name_is_required_and_unique(): void
    {
        Supplier::query()->create(['supplier_name' => 'Azalea Chemical Sdn Bhd']);

        $response = $this->actingAs($this->createUser('staff'))
            ->from(route('staff.suppliers.create'))
            ->post(route('staff.suppliers.store'), [
                'supplier_name' => '',
                'email' => 'not-an-email-address',
            ]);

        $response->assertRedirect(route('staff.suppliers.create'))
            ->assertSessionHasErrors([
                'supplier_name' => 'The supplier name field is required.',
                'email' => 'The email field must be a valid email address.',
            ]);

        $this->actingAs($this->createUser('staff'))
            ->from(route('staff.suppliers.create'))
            ->post(route('staff.suppliers.store'), [
                'supplier_name' => 'Azalea Chemical Sdn Bhd',
            ])
            ->assertRedirect(route('staff.suppliers.create'))
            ->assertSessionHasErrors([
                'supplier_name' => 'The supplier name has already been taken.',
            ]);
    }

    public function test_staff_cannot_delete_a_supplier_referenced_by_purchase_requests(): void
    {
        $admin = $this->createUser('staff');
        $supplier = Supplier::query()->create(['supplier_name' => 'Referenced Supplier']);

        PurchaseRequest::query()->create([
            'request_no' => 'PR-001',
            'customer_order_no' => 'CO-001',
            'supplier_name' => $supplier->supplier_name,
            'requested_by' => $admin->getKey(),
            'material_item' => 'Test Material',
            'quantity' => 1,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'kg',
            'estimated_cost' => 10,
            'finance_status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->delete(route('staff.suppliers.destroy', $supplier))
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('error', 'Supplier cannot be deleted because it is referenced by existing purchase requests.');

        $this->assertModelExists($supplier);
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
