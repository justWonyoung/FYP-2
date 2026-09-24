@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card stat-card p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-plus me-2"></i>New Purchase Request</h5>

        <form action="{{ route('pr.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Linked Customer Order</label>
                    <select name="customer_order_no" class="form-select" required>
                        <option value="CO-089 - Siti Enterprise (Body Lotion 500ml x200)">CO-089 - Siti Enterprise (Body Lotion 500ml x200)</option>
                        <option value="CO-090 - Beauty Secret (Face Cream 50g x100)">CO-090 - Beauty Secret (Face Cream 50g x100)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Supplier</label>
                    <select name="supplier_name" class="form-select" required>
                        <option value="Azalea Chemical Sdn Bhd">Azalea Chemical Sdn Bhd</option>
                        <option value="Pack Mart Suppliers">Pack Mart Suppliers</option>
                        <option value="Aroma Fragrance Sdn Bhd">Aroma Fragrance Sdn Bhd</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Material / Item Name</label>
                    <input type="text" name="material_item" class="form-control" placeholder="e.g. Shea Butter" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Unit</label>
                    <select name="unit" class="form-select" required>
                        <option value="kg">kg</option>
                        <option value="L">L</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Quantity</label>
                    <input type="number" step="0.01" name="quantity" class="form-control" placeholder="10" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Estimated Total Cost (RM)</label>
                    <input type="number" step="0.01" name="estimated_cost" class="form-control" placeholder="2400.00" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-1"></i> Submit Purchase Request</button>
            </div>
        </form>
    </div>
</div>
@endsection