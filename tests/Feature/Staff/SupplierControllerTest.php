<?php

namespace Tests\Feature\Staff;

use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_from_staff_supplier_management(): void
    {
        $this->get(route('staff.suppliers.index'))
            ->assertRedirect(route('login'));
    }

    public function test_staff_can_search_view_create_update_and_delete_an_unused_supplier(): void
    {
        $staff = $this->createUser('staff');
        $matchingSupplier = Supplier::query()->create([
            'supplier_name' => 'Searchable Supplier',
            'phone' => '0111111111',
            'email' => 'searchable@example.test',
        ]);
        Supplier::query()->create(['supplier_name' => 'Other Supplier']);

        $this->actingAs($staff)
            ->get(route('staff.suppliers.index', ['search' => 'searchable@example.test']))
            ->assertOk()
            ->assertSee('Searchable Supplier')
            ->assertDontSee('Other Supplier');

        $this->actingAs($staff)
            ->get(route('staff.suppliers.show', $matchingSupplier))
            ->assertOk()
            ->assertSee('searchable@example.test');

        $this->actingAs($staff)
            ->post(route('staff.suppliers.store'), [
                'supplier_name' => 'Staff Created Supplier',
                'phone' => '0123456789',
                'email' => 'staff-created@example.test',
                'address' => 'Kuala Lumpur',
            ])
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('success', 'Supplier added successfully.');

        $supplier = Supplier::query()->where('supplier_name', 'Staff Created Supplier')->firstOrFail();

        $this->actingAs($staff)
            ->put(route('staff.suppliers.update', $supplier), [
                'supplier_name' => 'Staff Updated Supplier',
                'phone' => '0198765432',
                'email' => 'staff-updated@example.test',
                'address' => 'Shah Alam',
            ])
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('success', 'Supplier updated successfully.');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'supplier_name' => 'Staff Updated Supplier',
        ]);

        $this->actingAs($staff)
            ->delete(route('staff.suppliers.destroy', $supplier))
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('success', 'Supplier deleted successfully.');

        $this->assertModelMissing($supplier);
    }

    public function test_staff_receives_validation_errors_for_missing_or_duplicate_supplier_name(): void
    {
        $staff = $this->createUser('staff');
        Supplier::query()->create(['supplier_name' => 'Existing Supplier']);

        $this->actingAs($staff)
            ->from(route('staff.suppliers.create'))
            ->post(route('staff.suppliers.store'), [
                'supplier_name' => '',
                'email' => 'invalid-email',
            ])
            ->assertRedirect(route('staff.suppliers.create'))
            ->assertSessionHasErrors([
                'supplier_name' => 'The supplier name field is required.',
                'email' => 'The email field must be a valid email address.',
            ]);

        $this->actingAs($staff)
            ->from(route('staff.suppliers.create'))
            ->post(route('staff.suppliers.store'), ['supplier_name' => 'Existing Supplier'])
            ->assertRedirect(route('staff.suppliers.create'))
            ->assertSessionHasErrors(['supplier_name' => 'The supplier name has already been taken.']);
    }

    public function test_finance_is_forbidden_from_staff_supplier_routes(): void
    {
        $finance = $this->createUser('finance');
        $supplier = Supplier::query()->create(['supplier_name' => 'Protected Supplier']);

        $this->actingAs($finance)->get(route('staff.suppliers.index'))->assertForbidden();
        $this->actingAs($finance)->get(route('staff.suppliers.create'))->assertForbidden();
        $this->actingAs($finance)->get(route('staff.suppliers.show', $supplier))->assertForbidden();
        $this->actingAs($finance)->get(route('staff.suppliers.edit', $supplier))->assertForbidden();
        $this->actingAs($finance)->post(route('staff.suppliers.store'), ['supplier_name' => 'Forbidden Supplier'])->assertForbidden();
        $this->actingAs($finance)->put(route('staff.suppliers.update', $supplier), ['supplier_name' => 'Forbidden Update'])->assertForbidden();
        $this->actingAs($finance)->delete(route('staff.suppliers.destroy', $supplier))->assertForbidden();
    }

    public function test_staff_cannot_delete_a_supplier_referenced_by_purchase_requests(): void
    {
        $staff = $this->createUser('staff');
        $supplier = Supplier::query()->create(['supplier_name' => 'Referenced Supplier']);
        PurchaseRequest::query()->create([
            'request_no' => 'PR-001',
            'customer_order_no' => 'CO-001',
            'supplier_name' => $supplier->supplier_name,
            'requested_by' => $staff->getKey(),
            'material_item' => 'Test Material',
            'quantity' => 1,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'kg',
            'estimated_cost' => 10,
            'finance_status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $this->actingAs($staff)
            ->delete(route('staff.suppliers.destroy', $supplier))
            ->assertRedirect(route('staff.suppliers.index'))
            ->assertSessionHas('error', 'Supplier cannot be deleted because it is referenced by existing purchase requests.');

        $this->assertModelExists($supplier);
    }

    public function test_staff_cannot_rename_a_supplier_referenced_by_purchase_requests(): void
    {
        $staff = $this->createUser('staff');
        $supplier = Supplier::query()->create(['supplier_name' => 'Referenced Supplier']);
        PurchaseRequest::query()->create([
            'request_no' => 'PR-001',
            'customer_order_no' => 'CO-001',
            'supplier_name' => $supplier->supplier_name,
            'requested_by' => $staff->getKey(),
            'material_item' => 'Test Material',
            'quantity' => 1,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'kg',
            'estimated_cost' => 10,
            'finance_status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $this->actingAs($staff)
            ->from(route('staff.suppliers.edit', $supplier))
            ->put(route('staff.suppliers.update', $supplier), [
                'supplier_name' => 'Renamed Supplier',
                'phone' => '0123456789',
                'email' => 'referenced@example.test',
                'address' => 'Kuala Lumpur',
            ])
            ->assertRedirect(route('staff.suppliers.edit', $supplier))
            ->assertSessionHasErrors([
                'supplier_name' => 'Supplier name cannot be changed because it is referenced by existing purchase requests.',
            ]);

        $this->assertSame('Referenced Supplier', $supplier->fresh()->supplier_name);
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
