<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Production;
use App\Models\PurchaseRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = CustomerOrder::query()
            ->with('customer')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('order_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search): void {
                            $query->where('customer_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('date_from'), function ($query) use ($request): void {
                $query->whereDate('order_date', '>=', $request->date('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request): void {
                $query->whereDate('order_date', '<=', $request->date('date_to'));
            })
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('staff.orders.index', compact('orders'));
    }

    public function show(CustomerOrder $order): View
    {
        $order->load(['customer', 'items']);

        return view('staff.orders.show', ['customerOrder' => $order]);
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('customer_name')->get();

        return view('staff.orders.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedOrder($request);

        $order = DB::transaction(function () use ($validated): CustomerOrder {
            $lastOrder = CustomerOrder::query()->lockForUpdate()->orderByDesc('id')->first();
            $nextNumber = $lastOrder === null ? 1 : ((int) preg_replace('/\D/', '', $lastOrder->order_no)) + 1;

            $order = CustomerOrder::create([
                'customer_id' => $validated['customer_id'],
                'order_no' => 'CO-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT),
                'order_date' => $validated['order_date'],
                'status' => $validated['status'],
                'total_amount' => $this->storeItems(null, $validated['items']),
            ]);

            $this->storeItems($order, $validated['items']);

            return $order;
        });

        return redirect()->route('staff.orders.index')->with('success', 'Customer order '.$order->order_no.' created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(CustomerOrder $order): View|RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return redirect()->route('staff.orders.show', $order)
                ->with('error', 'Cancelled customer orders cannot be edited.');
        }

        $order->load('items');
        $customers = Customer::query()->orderBy('customer_name')->get();

        return view('staff.orders.edit', ['customerOrder' => $order, 'customers' => $customers]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerOrder $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return redirect()->route('staff.orders.show', $order)
                ->with('error', 'Cancelled customer orders cannot be edited.');
        }

        $validated = $this->validatedOrder($request);

        if ($order->status === 'completed' && $validated['status'] !== 'completed') {
            return back()->withInput()->withErrors([
                'status' => 'Completed customer orders must remain completed.',
            ]);
        }

        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled' && $this->hasActiveOperations($order)) {
            return back()->withInput()->withErrors([
                'status' => 'This customer order cannot be cancelled while linked purchasing or production operations are active.',
            ]);
        }

        DB::transaction(function () use ($order, $validated): void {
            $totalAmount = $this->storeItems(null, $validated['items']);
            $order->update([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'status' => $validated['status'],
                'total_amount' => $totalAmount,
            ]);

            $order->items()->delete();
            $this->storeItems($order, $validated['items']);
        });

        return redirect()->route('staff.orders.show', $order)->with('success', 'Customer order updated successfully.');
    }

    /**
     * @return array{customer_id: int, order_date: string, status: string, items: array<int, array{product_name: string, quantity: int, price: numeric}>}
     */
    private function validatedOrder(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,production,completed,cancelled'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    /**
     * @param  array<int, array{product_name: string, quantity: int, price: numeric}>  $items
     */
    private function storeItems(?CustomerOrder $order, array $items): float
    {
        $totalAmount = 0.0;

        foreach ($items as $item) {
            $subtotal = round(((int) $item['quantity']) * ((float) $item['price']), 2);
            $totalAmount += $subtotal;

            if ($order !== null) {
                CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }
        }

        return round($totalAmount, 2);
    }

    private function hasActiveOperations(CustomerOrder $order): bool
    {
        $hasActivePurchaseRequest = PurchaseRequest::query()
            ->where('customer_order_no', $order->order_no)
            ->where(function ($query): void {
                $query->where('finance_status', 'pending')
                    ->orWhere(function ($query): void {
                        $query->where('finance_status', 'approved')
                            ->where('approval_status', 'pending');
                    })
                    ->orWhere(function ($query): void {
                        $query->where('finance_status', 'approved')
                            ->where('approval_status', 'approved')
                            ->whereIn('delivery_status', ['pending', 'partial']);
                    });
            })
            ->exists();

        if ($hasActivePurchaseRequest) {
            return true;
        }

        return Production::query()
            ->where('customer_order_no', $order->order_no)
            ->where('status', 'in_progress')
            ->exists();
    }
}
