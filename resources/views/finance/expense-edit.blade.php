@extends('layouts.app')

@php
    $pageTitle = 'Edit Expense | DBFM';
    $moduleTitle = 'Edit Expense';
    $moduleSubtitle = 'Update an existing finance expense record';
@endphp

@section('content')
<div class="page-header"><h1>Edit Expense</h1><p>Update the expense category, description, amount, or date.</p></div>
<div class="card stat-card p-4" style="max-width: 720px;">
    <form action="{{ route('finance.expenses.update', $expense) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3"><label class="form-label fw-bold">Expense Category</label>
            <select name="expense_category" class="form-select" required>
                @foreach(['Raw Materials','Packaging','Labor / Utility','Logistics'] as $category)
                    <option value="{{ $category }}" @selected(old('expense_category', $expense->expense_category) === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3"><label class="form-label fw-bold">Description</label><input name="description" class="form-control" value="{{ old('description', $expense->description) }}" required></div>
        <div class="mb-3"><label class="form-label fw-bold">Amount (RM)</label><input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required></div>
        <div class="mb-4"><label class="form-label fw-bold">Expense Date</label><input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date) }}" required></div>
        <button class="btn btn-primary">Update Expense</button>
        <a href="{{ route('finance.expenses') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection
