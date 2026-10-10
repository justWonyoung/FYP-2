@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <div class="card stat-card p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-plus me-2"></i>New Purchase Request</h5>

        <form action="{{ route('staff.pr.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="customer_order_no">Linked Customer Order</label>
                    <select id="customer_order_no" name="customer_order_no" class="form-select" required @disabled($customerOrders->isEmpty())>
                        <option value="">Select customer order</option>
                        @foreach($customerOrders as $customerOrder)
                            <option value="{{ $customerOrder->order_no }}" @selected(old('customer_order_no') === $customerOrder->order_no)>
                                {{ $customerOrder->order_no }} - {{ $customerOrder->customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                    @if($customerOrders->isEmpty())<div class="form-text text-danger">No active customer orders are available.</div>@endif
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
