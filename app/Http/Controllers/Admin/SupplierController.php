<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('supplier_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.suppliers.index', [
            'suppliers' => $suppliers,
            'search' => $search,
            'supplierRoutePrefix' => $this->supplierRoutePrefix($request),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.suppliers.create', [
            'supplierRoutePrefix' => $this->supplierRoutePrefix($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);
        ActivityLog::record($request->user(), 'created', 'Supplier', "Created supplier {$supplier->supplier_name}.");

        return redirect()
            ->route($this->supplierRoutePrefix($request).'.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function show(Request $request, Supplier $supplier): View
    {
        return view('admin.suppliers.show', [
            'supplier' => $supplier,
            'supplierRoutePrefix' => $this->supplierRoutePrefix($request),
        ]);
    }

    public function edit(Request $request, Supplier $supplier): View
    {
        return view('admin.suppliers.edit', [
            'supplier' => $supplier,
            'supplierRoutePrefix' => $this->supplierRoutePrefix($request),
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name,'.$supplier->id,
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        if ($supplier->isReferencedByPurchaseRequests() && $validated['supplier_name'] !== $supplier->supplier_name) {
            return back()
                ->withErrors(['supplier_name' => 'Supplier name cannot be changed because it is referenced by existing purchase requests.'])
                ->withInput();
        }

        $supplier->update($validated);
        ActivityLog::record($request->user(), 'updated', 'Supplier', "Updated supplier {$supplier->supplier_name}.");

        return redirect()
            ->route($this->supplierRoutePrefix($request).'.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Request $request, Supplier $supplier): RedirectResponse
    {
        if ($supplier->isReferencedByPurchaseRequests()) {
            return redirect()
                ->route($this->supplierRoutePrefix($request).'.suppliers.index')
                ->with('error', 'Supplier cannot be deleted because it is referenced by existing purchase requests.');
        }

        $supplier->delete();
        ActivityLog::record($request->user(), 'deleted', 'Supplier', "Deleted supplier {$supplier->supplier_name}.");

        return redirect()
            ->route($this->supplierRoutePrefix($request).'.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    private function supplierRoutePrefix(Request $request): string
    {
        return $request->routeIs('staff.suppliers.*') ? 'staff' : 'admin';
    }
}
