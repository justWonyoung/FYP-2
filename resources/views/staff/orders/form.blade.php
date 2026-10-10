@php
$items = old('items', $customerOrder?->items?->map(fn ($item) => ['product_name' => $item->product_name, 'quantity' => $item->quantity, 'price' => $item->price])->all() ?? [['product_name' => '', 'quantity' => 1, 'price' => '']]);
@endphp

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if($customers->isEmpty())<div class="alert alert-warning">Add a customer before creating a customer order. <a href="{{ route('staff.customers.create') }}">Add customer</a></div>@endif

<div class="section-card">
    <form method="POST" action="{{ $action }}">
        @csrf
        @if($method !== 'POST') @method($method) @endif
        <div class="row g-4 mb-4">
            <div class="col-md-4"><label class="form-label" for="customer_id">Customer</label><select id="customer_id" name="customer_id" class="form-select" required @disabled($customers->isEmpty())><option value="">Select customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id', $customerOrder?->customer_id) == $customer->id)>{{ $customer->customer_name }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label" for="order_date">Order Date</label><input id="order_date" type="date" name="order_date" class="form-control" value="{{ old('order_date', $customerOrder?->order_date?->toDateString() ?? now()->toDateString()) }}" required></div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                @if($customerOrder?->status === 'completed')
                    <input id="status" class="form-control" value="Completed" readonly>
                    <input type="hidden" name="status" value="completed">
                    <div class="form-text">Completed orders must remain completed.</div>
                @else
                    <select id="status" name="status" class="form-select" required>@foreach(['pending' => 'Pending', 'production' => 'Production', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $customerOrder?->status ?? 'pending') === $value)>{{ $label }}</option>@endforeach</select>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3"><div class="section-title">Order Items</div><button type="button" id="add-item" class="btn btn-sm btn-outline-secondary">Add Item</button></div>
        <div id="order-items">@foreach($items as $index => $item)<div class="row g-3 align-items-end item-row mb-3"><div class="col-md-5"><label class="form-label">Product Name</label><input name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item['product_name'] }}" required></div><div class="col-md-2"><label class="form-label">Quantity</label><input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item['quantity'] }}" required></div><div class="col-md-3"><label class="form-label">Unit Price (RM)</label><input type="number" min="0" step="0.01" name="items[{{ $index }}][price]" class="form-control" value="{{ $item['price'] }}" required></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger w-100 remove-item">Remove</button></div></div>@endforeach</div>
        <div class="mt-4"><button class="btn btn-primary" @disabled($customers->isEmpty())>{{ $method === 'POST' ? 'Create Order' : 'Update Order' }}</button><a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a></div>
    </form>
</div>

<template id="item-template"><div class="row g-3 align-items-end item-row mb-3"><div class="col-md-5"><label class="form-label">Product Name</label><input class="form-control product-name" required></div><div class="col-md-2"><label class="form-label">Quantity</label><input type="number" min="1" class="form-control quantity" value="1" required></div><div class="col-md-3"><label class="form-label">Unit Price (RM)</label><input type="number" min="0" step="0.01" class="form-control price" required></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger w-100 remove-item">Remove</button></div></div></template>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const items = document.getElementById('order-items');
    const renumberItems = function () {
        items.querySelectorAll('.item-row').forEach(function (row, index) {
            row.querySelector('input[name*="product_name"]').name = `items[${index}][product_name]`;
            row.querySelector('input[name*="quantity"]').name = `items[${index}][quantity]`;
            row.querySelector('input[name*="price"]').name = `items[${index}][price]`;
        });
    };
    document.getElementById('add-item').addEventListener('click', function () {
        const row = document.getElementById('item-template').content.cloneNode(true);
        row.querySelector('.product-name').name = 'items[new][product_name]';
        row.querySelector('.quantity').name = 'items[new][quantity]';
        row.querySelector('.price').name = 'items[new][price]';
        items.appendChild(row);
        renumberItems();
    });
    items.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item') && items.querySelectorAll('.item-row').length > 1) {
            event.target.closest('.item-row').remove();
            renumberItems();
        }
    });
});
</script>
