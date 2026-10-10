<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $customers = Customer::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('customer_name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('staff.customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        return view('staff.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedCustomer($request);

        $customer = Customer::create($validated);
        ActivityLog::record($request->user(), 'created', 'Customer', "Created customer {$customer->customer_name}.");

        return redirect()->route('staff.customers.index')->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'orders' => fn ($query) => $query->orderByDesc('order_date')->orderByDesc('id'),
        ]);

        return view('staff.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('staff.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $this->validatedCustomer($request, $customer);

        $customer->update($validated);
        ActivityLog::record($request->user(), 'updated', 'Customer', "Updated customer {$customer->customer_name}.");

        return redirect()->route('staff.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return redirect()
                ->route('staff.customers.index')
                ->with('error', 'Customer cannot be deleted because it is referenced by existing customer orders.');
        }

        $customer->delete();
        ActivityLog::record($request->user(), 'deleted', 'Customer', "Deleted customer {$customer->customer_name}.");

        return redirect()
            ->route('staff.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * @return array{customer_name: string, phone?: string|null, email?: string|null, address?: string|null}
     */
    private function validatedCustomer(Request $request, ?Customer $customer = null): array
    {
        $emailRules = ['nullable', 'email', 'max:255'];

        if ($request->filled('email') && ($customer === null || $request->input('email') !== $customer->email)) {
            $emailRules[] = Rule::unique('customers', 'email');
        }

        return $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => $emailRules,
            'address' => ['nullable', 'string'],
        ]);
    }
}
