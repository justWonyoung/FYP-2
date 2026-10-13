<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Expense;
use App\Models\Material;
use App\Models\MaterialReceiving;
use App\Models\MaterialUsage;
use App\Models\MaterialWaste;
use App\Models\Production;
use App\Models\ProductionYield;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a self-contained supervisor demonstration dataset.
 *
 * Run this seeder only against a newly migrated dbfm_demo database. It is
 * deliberately not called by DatabaseSeeder, so normal application seeding
 * and any existing database remain unaffected.
 */
class DemoDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            [$admin, $staff, $finance] = $this->createUsers();

            [$aurora, $seriAnggun, $mutiara] = $this->createCustomers();
            $this->createSuppliers();
            $materials = $this->createMaterials();

            $orders = $this->createCustomerOrders($aurora, $seriAnggun, $mutiara);
            $requests = $this->createPurchaseRequests($staff, $orders);

            $this->createReceivingHistory($requests['approved_received']);
            $productions = $this->createProductionRecords($orders, $materials);
            $this->createExpenses();
            $this->createActivityLogs($admin, $staff, $finance, $orders, $requests, $productions);
        });
    }

    /**
     * @return array{0: User, 1: User, 2: User}
     */
    private function createUsers(): array
    {
        $password = Hash::make('DemoPass2026!');

        $admin = User::create([
            'full_name' => 'Aina Rahman',
            'username' => 'demo_admin',
            'email' => 'admin@dbfm-demo.test',
            'phone_number' => '012-880 1001',
            'password' => $password,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $staff = User::create([
            'full_name' => 'Nur Syafiqah Ismail',
            'username' => 'demo_staff',
            'email' => 'staff@dbfm-demo.test',
            'phone_number' => '012-880 1002',
            'password' => $password,
            'role' => 'staff',
            'status' => 'active',
        ]);

        $finance = User::create([
            'full_name' => 'Daniel Tan',
            'username' => 'demo_finance',
            'email' => 'finance@dbfm-demo.test',
            'phone_number' => '012-880 1003',
            'password' => $password,
            'role' => 'finance',
            'status' => 'active',
        ]);

        return [$admin, $staff, $finance];
    }

    /**
     * @return array{0: Customer, 1: Customer, 2: Customer}
     */
    private function createCustomers(): array
    {
        $aurora = Customer::create([
            'customer_name' => 'Aurora Wellness Sdn. Bhd.',
            'phone' => '03-2718 4100',
            'email' => 'procurement@aurorawellness.my',
            'address' => '21, Jalan Teknologi 3/4, 47810 Petaling Jaya, Selangor',
        ]);

        $seriAnggun = Customer::create([
            'customer_name' => 'Seri Anggun Skincare',
            'phone' => '03-8962 2290',
            'email' => 'orders@serianggun.my',
            'address' => '8, Jalan Puteri 2/5, Bandar Puteri, 47100 Puchong, Selangor',
        ]);

        $mutiara = Customer::create([
            'customer_name' => 'Mutiara Beauty Studio',
            'phone' => '04-228 7721',
            'email' => 'hello@mutiarabeauty.my',
            'address' => '17, Lebuh Victoria, 10300 George Town, Pulau Pinang',
        ]);

        return [$aurora, $seriAnggun, $mutiara];
    }

    private function createSuppliers(): void
    {
        Supplier::create([
            'supplier_name' => 'Prima Ingredients Malaysia',
            'phone' => '03-7832 6190',
            'email' => 'sales@primaingredients.my',
            'address' => '12, Jalan PJU 1A/41, Ara Damansara, 47301 Petaling Jaya, Selangor',
        ]);

        Supplier::create([
            'supplier_name' => 'EcoPack Solutions',
            'phone' => '03-5122 8810',
            'email' => 'orders@ecopack.my',
            'address' => '34, Jalan Industri 2/3, Taman Perindustrian, 47130 Puchong, Selangor',
        ]);

        Supplier::create([
            'supplier_name' => 'PureLab Chemicals',
            'phone' => '03-7806 4408',
            'email' => 'support@purelabchemicals.my',
            'address' => '5, Jalan Astaka U8/84, Bukit Jelutong, 40150 Shah Alam, Selangor',
        ]);
    }

    /**
     * @return array<string, Material>
     */
    private function createMaterials(): array
    {
        $materials = [
            'purified_water' => Material::create([
                'material_name' => 'Purified Water',
                'material_type' => 'Raw Material',
                'unit' => 'L',
                'current_stock' => 50,
                'minimum_stock' => 15,
            ]),
            'niacinamide' => Material::create([
                'material_name' => 'Niacinamide 10%',
                'material_type' => 'Raw Material',
                'unit' => 'kg',
                'current_stock' => 3.60,
                'minimum_stock' => 2,
            ]),
            'dropper_bottle' => Material::create([
                'material_name' => 'Glass Dropper Bottle 30ml',
                'material_type' => 'Packaging',
                'unit' => 'pcs',
                'current_stock' => 160,
                'minimum_stock' => 100,
            ]),
            'centella_extract' => Material::create([
                'material_name' => 'Centella Asiatica Extract',
                'material_type' => 'Raw Material',
                'unit' => 'kg',
                'current_stock' => 0.80,
                'minimum_stock' => 2,
            ]),
            'carton_box' => Material::create([
                'material_name' => 'Cosmetic Carton Box',
                'material_type' => 'Packaging',
                'unit' => 'pcs',
                'current_stock' => 0,
                'minimum_stock' => 50,
            ]),
        ];

        return $materials;
    }

    /**
     * @return array<string, CustomerOrder>
     */
    private function createCustomerOrders(Customer $aurora, Customer $seriAnggun, Customer $mutiara): array
    {
        $pending = CustomerOrder::create([
            'customer_id' => $aurora->id,
            'order_no' => 'CO-DEMO-001',
            'order_date' => '2026-10-10',
            'status' => 'pending',
            'total_amount' => 3600,
        ]);
        CustomerOrderItem::create([
            'customer_order_id' => $pending->id,
            'product_name' => 'Cica Barrier Cream 50g',
            'quantity' => 80,
            'price' => 45,
            'subtotal' => 3600,
        ]);

        $production = CustomerOrder::create([
            'customer_id' => $seriAnggun->id,
            'order_no' => 'CO-DEMO-002',
            'order_date' => '2026-10-06',
            'status' => 'production',
            'total_amount' => 3600,
        ]);
        CustomerOrderItem::create([
            'customer_order_id' => $production->id,
            'product_name' => 'Hydrating Toner 100ml',
            'quantity' => 120,
            'price' => 30,
            'subtotal' => 3600,
        ]);

        $completed = CustomerOrder::create([
            'customer_id' => $mutiara->id,
            'order_no' => 'CO-DEMO-003',
            'order_date' => '2026-09-22',
            'status' => 'completed',
            'total_amount' => 8060,
        ]);
        CustomerOrderItem::create([
            'customer_order_id' => $completed->id,
            'product_name' => 'Brightening Serum 30ml',
            'quantity' => 100,
            'price' => 55,
            'subtotal' => 5500,
        ]);
        CustomerOrderItem::create([
            'customer_order_id' => $completed->id,
            'product_name' => 'Gentle Cleansing Gel 100ml',
            'quantity' => 80,
            'price' => 32,
            'subtotal' => 2560,
        ]);

        $cancelled = CustomerOrder::create([
            'customer_id' => $aurora->id,
            'order_no' => 'CO-DEMO-004',
            'order_date' => '2026-10-03',
            'status' => 'cancelled',
            'total_amount' => 2880,
        ]);
        CustomerOrderItem::create([
            'customer_order_id' => $cancelled->id,
            'product_name' => 'Mineral Sunscreen SPF50 50ml',
            'quantity' => 60,
            'price' => 48,
            'subtotal' => 2880,
        ]);

        return compact('pending', 'production', 'completed', 'cancelled');
    }

    /**
     * @param array<string, CustomerOrder> $orders
     * @return array<string, PurchaseRequest>
     */
    private function createPurchaseRequests(User $staff, array $orders): array
    {
        $pending_finance = PurchaseRequest::create([
            'request_no' => 'PR-DEMO-001',
            'customer_order_no' => $orders['pending']->order_no,
            'supplier_name' => 'EcoPack Solutions',
            'requested_by' => $staff->user_id,
            'material_item' => 'Cosmetic Carton Box',
            'quantity' => 100,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'pcs',
            'estimated_cost' => 250,
            'finance_status' => 'pending',
            'approval_status' => 'pending',
        ]);

        $finance_approved = PurchaseRequest::create([
            'request_no' => 'PR-DEMO-002',
            'customer_order_no' => $orders['production']->order_no,
            'supplier_name' => 'EcoPack Solutions',
            'requested_by' => $staff->user_id,
            'material_item' => 'Glass Dropper Bottle 30ml',
            'quantity' => 150,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'pcs',
            'estimated_cost' => 450,
            'finance_status' => 'approved',
            'approval_status' => 'pending',
            'finance_remark' => 'Budget verified and recommended for Admin approval.',
        ]);

        $approved_received = PurchaseRequest::create([
            'request_no' => 'PR-DEMO-003',
            'customer_order_no' => $orders['completed']->order_no,
            'supplier_name' => 'Prima Ingredients Malaysia',
            'requested_by' => $staff->user_id,
            'material_item' => 'Niacinamide 10%',
            'quantity' => 5,
            'received_quantity' => 5,
            'delivery_status' => 'received',
            'unit' => 'kg',
            'estimated_cost' => 680,
            'finance_status' => 'approved',
            'approval_status' => 'approved',
            'finance_remark' => 'Budget verified by Finance.',
            'admin_remark' => 'Approved for completed customer order fulfilment.',
        ]);

        $finance_rejected = PurchaseRequest::create([
            'request_no' => 'PR-DEMO-004',
            'customer_order_no' => $orders['pending']->order_no,
            'supplier_name' => 'PureLab Chemicals',
            'requested_by' => $staff->user_id,
            'material_item' => 'Centella Asiatica Extract',
            'quantity' => 3,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'kg',
            'estimated_cost' => 540,
            'finance_status' => 'rejected',
            'approval_status' => 'pending',
            'finance_remark' => 'Rejected by Finance: supplier quotation needs revision.',
        ]);

        $admin_rejected = PurchaseRequest::create([
            'request_no' => 'PR-DEMO-005',
            'customer_order_no' => $orders['production']->order_no,
            'supplier_name' => 'EcoPack Solutions',
            'requested_by' => $staff->user_id,
            'material_item' => 'Premium Gift Sleeve',
            'quantity' => 120,
            'received_quantity' => 0,
            'delivery_status' => 'pending',
            'unit' => 'pcs',
            'estimated_cost' => 720,
            'finance_status' => 'approved',
            'approval_status' => 'rejected',
            'finance_remark' => 'Budget verified by Finance.',
            'admin_remark' => 'Rejected by Admin: not required for this production run.',
        ]);

        return compact('pending_finance', 'finance_approved', 'approved_received', 'finance_rejected', 'admin_rejected');
    }

    private function createReceivingHistory(PurchaseRequest $request): void
    {
        MaterialReceiving::create([
            'purchase_request_id' => $request->purchase_request_id,
            'material_name' => $request->material_item,
            'supplier_name' => $request->supplier_name,
            'quantity_received' => 5,
            'unit' => 'kg',
            'received_date' => '2026-09-24',
            'remarks' => 'Full delivery inspected and accepted in good condition.',
        ]);
    }

    /**
     * @param array<string, CustomerOrder> $orders
     * @param array<string, Material> $materials
     * @return array<string, Production>
     */
    private function createProductionRecords(array $orders, array $materials): array
    {
        $in_progress = Production::create([
            'batch_number' => 'BATCH-DEMO-001',
            'customer_order_no' => $orders['production']->order_no,
            'product_name' => 'Hydrating Toner 100ml',
            'planned_output' => 120,
            'actual_output' => 0,
            'unit' => 'pcs',
            'status' => 'in_progress',
            'start_date' => '2026-10-08',
        ]);
        MaterialUsage::create([
            'production_id' => $in_progress->production_id,
            'material_id' => $materials['purified_water']->material_id,
            'quantity_used' => 5,
        ]);

        $good_yield = Production::create([
            'batch_number' => 'BATCH-DEMO-002',
            'customer_order_no' => $orders['completed']->order_no,
            'product_name' => 'Brightening Serum 30ml',
            'planned_output' => 100,
            'actual_output' => 98,
            'unit' => 'pcs',
            'status' => 'completed',
            'start_date' => '2026-09-23',
            'completion_date' => '2026-09-25',
        ]);
        MaterialUsage::create([
            'production_id' => $good_yield->production_id,
            'material_id' => $materials['niacinamide']->material_id,
            'quantity_used' => 1.20,
        ]);
        MaterialUsage::create([
            'production_id' => $good_yield->production_id,
            'material_id' => $materials['purified_water']->material_id,
            'quantity_used' => 18,
        ]);
        MaterialWaste::create([
            'production_id' => $good_yield->production_id,
            'material_id' => $materials['niacinamide']->material_id,
            'quantity_wasted' => 0.20,
            'waste_reason' => 'Minor transfer residue during filling.',
        ]);
        ProductionYield::create([
            'production_id' => $good_yield->production_id,
            'planned_quantity' => 100,
            'actual_quantity' => 98,
            'yield_percentage' => 98,
        ]);

        $lower_yield = Production::create([
            'batch_number' => 'BATCH-DEMO-003',
            'customer_order_no' => $orders['completed']->order_no,
            'product_name' => 'Gentle Cleansing Gel 100ml',
            'planned_output' => 80,
            'actual_output' => 68,
            'unit' => 'pcs',
            'status' => 'completed',
            'start_date' => '2026-09-24',
            'completion_date' => '2026-09-26',
        ]);
        MaterialUsage::create([
            'production_id' => $lower_yield->production_id,
            'material_id' => $materials['purified_water']->material_id,
            'quantity_used' => 12,
        ]);
        MaterialUsage::create([
            'production_id' => $lower_yield->production_id,
            'material_id' => $materials['centella_extract']->material_id,
            'quantity_used' => 0.30,
        ]);
        MaterialWaste::create([
            'production_id' => $lower_yield->production_id,
            'material_id' => $materials['purified_water']->material_id,
            'quantity_wasted' => 0.50,
            'waste_reason' => 'Viscosity adjustment and filling loss.',
        ]);
        ProductionYield::create([
            'production_id' => $lower_yield->production_id,
            'planned_quantity' => 80,
            'actual_quantity' => 68,
            'yield_percentage' => 85,
        ]);

        return compact('in_progress', 'good_yield', 'lower_yield');
    }

    private function createExpenses(): void
    {
        Expense::create([
            'expense_category' => 'Raw Materials',
            'description' => 'Quality testing and laboratory consumables for September production.',
            'amount' => 850,
            'expense_date' => '2026-09-24',
        ]);
        Expense::create([
            'expense_category' => 'Packaging',
            'description' => 'Labels, safety seals, and carton printing.',
            'amount' => 420,
            'expense_date' => '2026-09-26',
        ]);
        Expense::create([
            'expense_category' => 'Labor / Utility',
            'description' => 'Production utilities and temporary packing labour.',
            'amount' => 650,
            'expense_date' => '2026-09-30',
        ]);
        Expense::create([
            'expense_category' => 'Logistics',
            'description' => 'Courier collection and delivery arrangement for completed orders.',
            'amount' => 180,
            'expense_date' => '2026-10-01',
        ]);
    }

    /**
     * @param array<string, CustomerOrder> $orders
     * @param array<string, PurchaseRequest> $requests
     * @param array<string, Production> $productions
     */
    private function createActivityLogs(
        User $admin,
        User $staff,
        User $finance,
        array $orders,
        array $requests,
        array $productions,
    ): void {
        ActivityLog::create([
            'user_id' => $staff->user_id,
            'action' => 'logged_in',
            'module' => 'Authentication',
            'description' => 'Successful login.',
        ]);
        ActivityLog::create([
            'user_id' => $staff->user_id,
            'action' => 'created',
            'module' => 'Customer Order',
            'description' => "Created {$orders['production']->order_no} for Seri Anggun Skincare.",
        ]);
        ActivityLog::create([
            'user_id' => $staff->user_id,
            'action' => 'created',
            'module' => 'Purchase Request',
            'description' => "Submitted {$requests['pending_finance']->request_no} for Finance review.",
        ]);
        ActivityLog::create([
            'user_id' => $finance->user_id,
            'action' => 'approved',
            'module' => 'Purchase Request',
            'description' => "Finance approved {$requests['finance_approved']->request_no}.",
        ]);
        ActivityLog::create([
            'user_id' => $admin->user_id,
            'action' => 'approved',
            'module' => 'Purchase Request',
            'description' => "Admin approved {$requests['approved_received']->request_no}.",
        ]);
        ActivityLog::create([
            'user_id' => $staff->user_id,
            'action' => 'created',
            'module' => 'Production',
            'description' => "Created production batch {$productions['in_progress']->batch_number}.",
        ]);
        ActivityLog::create([
            'user_id' => $staff->user_id,
            'action' => 'completed',
            'module' => 'Production',
            'description' => "Completed production batch {$productions['good_yield']->batch_number} with a 98% yield.",
        ]);
        ActivityLog::create([
            'user_id' => $finance->user_id,
            'action' => 'created',
            'module' => 'Finance',
            'description' => 'Recorded September production and logistics expenses.',
        ]);
    }
}
